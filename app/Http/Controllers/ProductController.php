<?php

namespace App\Http\Controllers;

use App\Application\Catalog\Services\BrandService;
use App\Application\Catalog\Services\CategoryService;
use App\Application\Catalog\Services\ProductPricingService;
use App\Application\Catalog\Services\ProductReviewService;
use App\Application\Catalog\Services\ProductService;
use App\Domain\Catalog\Repositories\BrandRepositoryInterface;
use App\Domain\Catalog\Repositories\CategoryRepositoryInterface;
use App\Http\Controllers\Concerns\LegacyStorefrontContext;
use App\Http\Requests\Storefront\Product\InsertRatingRequest;
use App\Http\Requests\Storefront\Product\LoadCommentRequest;
use App\Http\Requests\Storefront\Product\QuickviewRequest;
use App\Http\Requests\Storefront\Product\SendCommentRequest;
use App\Infrastructure\Persistence\Eloquent\Catalog\Product;
use App\Infrastructure\Persistence\Eloquent\Catalog\ProductVariant;
use App\Support\AssetUrl;
use App\Support\StorefrontLabel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    use LegacyStorefrontContext;

    public function __construct(
        private readonly ProductService $productService,
        private readonly ProductPricingService $pricingService,
        private readonly ProductReviewService $productReviewService,
        private readonly CategoryService $categoryService,
        private readonly BrandService $brandService,
        private readonly CategoryRepositoryInterface $categoryRepository,
        private readonly BrandRepositoryInterface $brandRepository
    ) {}

    public function file_browser(Request $request)
    {
        $funcNum = max(0, (int) $request->query('CKEditorFuncNum', 0));
        $paths = collect(glob(public_path('uploads/ckeditor/*')) ?: [])
            ->filter(fn ($path) => is_file($path))
            ->sortByDesc(fn ($path) => filemtime($path) ?: 0)
            ->values();

        return view('admin.images.file_browser', [
            'funcNum' => $funcNum,
            'files' => $paths->map(function (string $path) {
                $fileName = basename($path);

                return [
                    'name' => $fileName,
                    'url' => asset('uploads/ckeditor/'.$fileName),
                    'size_label' => number_format((int) ceil(filesize($path) / 1024)).' KB',
                    'updated_label' => date('d/m/Y H:i', filemtime($path) ?: time()),
                ];
            }),
        ]);
    }

    public function reply_comment(Request $request): JsonResponse
    {
        $parentId = (int) $request->input('comment_id');
        $productId = (int) $request->input('comment_product_id');
        $content = trim((string) $request->input('comment'));

        if (! $parentId || ! $productId || $content === '') {
            return response()->json(['message' => 'Dữ liệu không hợp lệ.'], 422);
        }

        $adminName = Auth::user()->name ?? 'Admin';
        $reply = $this->productReviewService->replyAsAdmin($productId, $parentId, $content, $adminName);

        if (! $reply) {
            return response()->json(['message' => 'Không thể phản hồi bình luận.'], 400);
        }

        return response()->json(['message' => 'Thành công.']);
    }

    public function allow_comment(Request $request): JsonResponse
    {
        $commentId = (int) $request->input('comment_id');
        $status = (int) $request->input('comment_status') === 0 ? 'approved' : 'pending';

        $review = $this->productReviewService->updateStatus($commentId, $status);

        if (! $review) {
            return response()->json(['message' => 'Không tìm thấy bình luận.'], 404);
        }

        return response()->json(['message' => 'Thành công.', 'status' => $review->comment_status]);
    }

    public function list_comment()
    {
        $roots = $this->productReviewService->getAdminRootComments();
        $replies = $this->productReviewService->getRepliesForParents($roots->pluck('id')->all());

        return view('admin.comment.list_comment')->with([
            'comment' => $roots,
            'comment_rep' => $replies,
        ]);
    }

    public function send_comment(SendCommentRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $productId = (int) $validated['product_id'];
        $author = trim($validated['comment_name']);
        $content = trim($validated['comment_content']);

        $this->productReviewService->createCustomerComment($productId, $author, $content);

        return response()->json(['message' => 'Thành công.']);
    }

    public function load_comment(LoadCommentRequest $request)
    {
        $productId = (int) $request->validated('product_id');

        try {
            $threads = $this->productReviewService->getApprovedThread($productId);
        } catch (\Throwable $e) {
            return response('');
        }

        return response()->view('pages.sanpham.partials.review_thread', [
            'threads' => $threads,
        ]);
    }

    public function quickview(QuickviewRequest $request): JsonResponse
    {
        $productId = (int) $request->validated('product_id');
        $product = $this->productService->findProductById($productId);

        if (! $product) {
            return response()->json(['message' => 'Không tìm thấy sản phẩm.'], 404);
        }

        $mediaItems = $product->media ?? collect();
        $fallbackPath = AssetUrl::rawOrFallback($product->product_image);
        $fallbackImage = AssetUrl::productVariant($product->product_image, 'medium');
        $cartImage = AssetUrl::productVariant($product->product_image, 'thumb');
        $output = [
            'product_name' => StorefrontLabel::neutralizeSource($product->product_name),
            'product_id' => $product->product_id,
            'product_modal_body' => view('pages.sanpham.partials.quickview_modal_body', [
                'product' => $product,
                'mediaItems' => $mediaItems,
                'fallbackPath' => $fallbackPath,
                'fallbackImage' => $fallbackImage,
            ])->render(),
            // Legacy keys for backward compatibility (e.g. for tests)
            // These are inlined to allow deletion of fragmented partial files
            'product_image' => '<p><img width="100%" src="'.$fallbackImage.'" alt="Hình ảnh sản phẩm"></p>',
            'product_button' => '<input type="button" value="Mua ngay" class="btn btn-primary btn-sm add-to-cart-quickview" id="buy_quickview" data-id_product="'.$product->product_id.'" name="add-to-cart">',
            'product_gallery' => $mediaItems->isNotEmpty()
                ? $mediaItems->map(fn ($m) => '<p><img width="100%" src="'.\App\Support\AssetUrl::productVariant($m->file_path ?? $fallbackPath, 'medium').'" alt="'.StorefrontLabel::neutralizeSource($m->gallery_name ?? 'Hình ảnh sản phẩm').'"></p>')->implode('')
                : '<p><img width="100%" src="'.$fallbackImage.'" alt="Hình ảnh sản phẩm"></p>',
            'product_quickview_value' => sprintf(
                '<input type="hidden" value="%d" class="cart_product_id_%d">'.
                '<input type="hidden" value="%s" class="cart_product_name_%d">'.
                '<input type="hidden" value="%d" class="cart_product_quantity_%d">'.
                '<input type="hidden" value="%s" class="cart_product_image_%d">'.
                '<input type="hidden" value="%s" class="cart_product_price_%d">'.
                '<input type="hidden" value="1" class="cart_product_qty_%d">',
                $product->product_id, $product->product_id,
                StorefrontLabel::neutralizeSource($product->product_name), $product->product_id,
                $product->product_quantity, $product->product_id,
                $cartImage, $product->product_id,
                $product->final_price, $product->product_id,
                $product->product_id
            ),
            'product_price' => view('partials.storefront.product-price', [
                'product' => $product,
                'currency' => 'VND',
                'showExplanation' => true,
                'compact' => true,
            ])->render(),
            'product_desc' => StorefrontLabel::neutralizeSourceHtml($product->product_desc),
            'product_content' => StorefrontLabel::neutralizeSourceHtml($product->product_content),
        ];

        return response()->json($output);
    }

    public function add_product()
    {
        $cate_product = $this->categoryRepository->all();
        $brand_product = $this->brandRepository->all();

        return view('admin.add_product')
            ->with('cate_product', $cate_product)
            ->with('brand_product', $brand_product);
    }

    public function all_product()
    {
        $all_product = $this->productService->paginateForAdmin(['only_active' => false], 20);
        $manager_product = view('admin.all_product')->with('all_product', $all_product);

        return view('admin_layout')->with('admin.all_product', $manager_product);
    }

    public function save_product(Request $request)
    {
        $validated = $this->validateProductRequest($request);

        $payload = $this->mapProductData($validated);

        $this->productService->createProduct(
            $payload,
            $request->file('product_image'),
            $request->file('document')
        );

        Session::put('message', 'Thêm sản phẩm thành công');

        return Redirect::to('add-product');
    }

    public function unactive_product(int $product_id)
    {
        $product = $this->productService->findProductById($product_id);

        if (! $product) {
            return $this->redirectMissingAdminProduct('Không tìm thấy sản phẩm cần cập nhật trạng thái.');
        }

        $this->productService->setProductStatus($product, false);

        Session::put('message', 'Không kích hoạt sản phẩm thành công');

        return Redirect::to('/admin/products-management');
    }

    public function active_product(int $product_id)
    {
        $product = $this->productService->findProductById($product_id);

        if (! $product) {
            return $this->redirectMissingAdminProduct('Không tìm thấy sản phẩm cần cập nhật trạng thái.');
        }

        $this->productService->setProductStatus($product, true);

        Session::put('message', 'Kích hoạt sản phẩm thành công');

        return Redirect::to('/admin/products-management');
    }

    public function edit_product(int $product_id)
    {
        $product = $this->productService->findProductById($product_id);

        if (! $product) {
            return $this->redirectMissingAdminProduct('Không tìm thấy sản phẩm cần chỉnh sửa.');
        }

        $cate_product = $this->categoryRepository->all();
        $brand_product = $this->brandRepository->all();

        $manager_product = view('admin.edit_product')
            ->with('edit_product', collect([$product]))
            ->with('cate_product', $cate_product)
            ->with('brand_product', $brand_product);

        return view('admin_layout')->with('admin.edit_product', $manager_product);
    }

    public function update_product(Request $request, int $product_id)
    {
        $product = $this->productService->findProductById($product_id);

        if (! $product) {
            return $this->redirectMissingAdminProduct('Không tìm thấy sản phẩm cần cập nhật.');
        }

        $validated = $this->validateProductRequest($request, $product->id);

        $payload = $this->mapProductData($validated, $product);

        $this->productService->updateProduct(
            $product,
            $payload,
            $request->file('product_image'),
            $request->file('document')
        );

        Session::put('message', 'Cập nhật sản phẩm thành công');

        return Redirect::to('/admin/products-management');
    }

    public function delete_document(Request $request): JsonResponse
    {
        $productId = (int) $request->input('product_id');

        $product = $this->productService->findProductById($productId);

        if (! $product) {
            return response()->json(['message' => 'Không tìm thấy sản phẩm.'], 404);
        }

        $this->productService->removeProductDocument($product);

        return response()->json(['message' => 'Thành công.']);
    }

    public function delete_product(int $product_id)
    {
        $product = $this->productService->findProductById($product_id);

        if (! $product) {
            return $this->redirectMissingAdminProduct('Không tìm thấy sản phẩm cần xóa.');
        }

        $this->productService->deleteProduct($product);

        Session::put('message', 'Xóa sản phẩm thành công');

        return Redirect::to('/admin/products-management');
    }

    protected function validateProductRequest(Request $request, ?int $productId = null): array
    {
        if (! $request->filled('product_slug') && $request->filled('product_name')) {
            $request->merge([
                'product_slug' => Str::slug((string) $request->input('product_name')),
            ]);
        }

        $validator = validator($request->all(), [
            'product_name'     => ['required', 'string', 'max:255'],
            'product_slug'     => ['nullable', 'string', 'max:255', Rule::unique('products', 'slug')->ignore($productId)],
            'product_price'    => ['required', 'numeric', 'min:0'],  // Fix #69: thêm numeric|min:0
            'price_cost'       => ['nullable', 'numeric', 'min:0'],
            'sale_type'        => ['nullable', Rule::in(['percent', 'fixed'])],
            'sale_value'       => ['nullable', 'numeric', 'min:0'],
            'product_quantity' => ['required', 'integer', 'min:0'],
            'product_desc'     => ['nullable', 'string'],
            'product_content'  => ['nullable', 'string'],
            'product_cate'     => ['required', 'integer', 'exists:product_categories,id'],
            'product_brand'    => ['nullable', 'integer', 'exists:brands,id'],
            'product_status'   => ['required', 'in:0,1'],
            'product_tags'     => ['nullable', 'string'],
            'product_image'    => ['nullable', 'image', 'max:4096'],
            'document'         => ['nullable', 'file', 'max:5120'],
        ]);

        $validator->after(function ($validator) use ($request) {
            $price = $this->normalizeCurrency($request->input('product_price')) ?? 0.0;
            $saleType = $this->pricingService->normalizeSaleType($request->input('sale_type'));
            $saleValue = $saleType === 'percent'
                ? $this->normalizeDecimal($request->input('sale_value'))
                : $this->normalizeCurrency($request->input('sale_value'));

            if (! $saleType && $saleValue === null) {
                return;
            }

            if (! $saleType || $saleValue === null || $saleValue <= 0) {
                $validator->errors()->add('sale_value', 'Kiểu khuyến mãi và giá trị khuyến mãi phải được nhập đầy đủ.');

                return;
            }

            if ($saleType === 'percent' && $saleValue > 100) {
                $validator->errors()->add('sale_value', 'Khuyến mãi theo phần trăm không được vượt quá 100.');
            }

            if ($saleType === 'fixed' && $saleValue >= $price) {
                $validator->errors()->add('sale_value', 'Số tiền khuyến mãi phải nhỏ hơn giá bán gốc.');
            }
        });

        return $validator->validate();
    }

    protected function mapProductData(array $data, ?Product $existing = null): array
    {
        $name = $data['product_name'] ?? ($existing?->product_name ?? '');
        $slugSource = $data['product_slug'] ?? null;
        $slug = $slugSource ? Str::slug($slugSource) : ($existing?->product_slug ?? Str::slug($name));

        $price = $this->normalizeCurrency($data['product_price'] ?? null) ?? 0.0;
        $cost = array_key_exists('price_cost', $data)
            ? $this->normalizeCurrency($data['price_cost'])
            : ($existing?->price_cost ?? null);
        $saleType = $this->pricingService->normalizeSaleType($data['sale_type'] ?? $existing?->sale_type);
        $saleValue = $saleType === 'percent'
            ? $this->normalizeDecimal($data['sale_value'] ?? ($existing?->sale_value ?? null))
            : $this->normalizeCurrency($data['sale_value'] ?? ($existing?->sale_value ?? null));
        $pricingPayload = $this->pricingService->derivePricingPayload($price, $saleType, $saleValue);

        $categoryId = isset($data['product_cate'])
            ? (int) $data['product_cate']
            : (int) ($existing?->primary_category_id ?? 0);

        $brandId = isset($data['product_brand'])
            ? (int) $data['product_brand']
            : ($existing?->brand_id ?? null);

        $statusValue = $data['product_status'] ?? ($existing?->product_status ?? 0);
        $isActive = ((int) $statusValue) === 0;

        $quantity = isset($data['product_quantity'])
            ? (int) $data['product_quantity']
            : (int) ($existing?->product_quantity ?? 0);

        $tags = $data['product_tags'] ?? ($existing?->product_tags ?? null);

        $payload = [
            'name' => $name,
            'slug' => $slug,
            'description' => $data['product_desc'] ?? $existing?->product_desc,
            'content' => $data['product_content'] ?? $existing?->product_content,
            'brand_id' => $brandId ?: null,
            'primary_category_id' => $categoryId ?: null,
            'price' => $price,
            'sale_type' => $pricingPayload['sale_type'],
            'sale_value' => $pricingPayload['sale_value'],
            'sale_price' => $pricingPayload['sale_price'],
            'cost_price' => $cost,
            'stock_quantity' => $quantity,
            'stock_alert_threshold' => $existing?->stock_alert_threshold ?? 0,
            'is_active' => $isActive,
            'display_order' => $existing?->display_order ?? 0,
            'meta_title' => $name,
            'meta_description' => $data['product_desc'] ?? $existing?->product_desc,
            'category_ids' => $categoryId ? [$categoryId] : [],
            'tags' => $tags,
        ];

        if ($existing && empty($payload['category_ids'])) {
            $payload['category_ids'] = $existing->categories?->pluck('id')->all() ?? [];
        }

        return $payload;
    }

    protected function normalizeCurrency($value): ?float
    {
        if ($value === null) {
            return null;
        }

        if (is_string($value)) {
            $trimmed = trim($value);
            if ($trimmed === '') {
                return null;
            }

            $normalized = preg_replace('/[^0-9]/', '', $trimmed);
            if ($normalized === '') {
                return null;
            }

            return (float) $normalized;
        }

        if (is_numeric($value)) {
            return (float) $value;
        }

        return null;
    }

    protected function normalizeDecimal($value): ?float
    {
        if ($value === null) {
            return null;
        }

        if (is_string($value)) {
            $trimmed = trim($value);

            if ($trimmed === '') {
                return null;
            }

            $normalized = preg_replace('/[^0-9.,]/', '', $trimmed);
            $normalized = str_replace(',', '.', $normalized);

            return $normalized === '' ? null : (float) $normalized;
        }

        if (is_numeric($value)) {
            return (float) $value;
        }

        return null;
    }

    protected function redirectMissingAdminProduct(string $message)
    {
        Session::put('error', $message);

        return Redirect::to('/admin/products-management');
    }

    //End Admin Page
    public function details_product($product_slug, Request $request)
    {
        $product = $this->productService->findProductBySlug($product_slug);

        if (! $product && $this->allowsLocalPreviewCatalog()) {
            $product = Product::with([
                'brand',
                'categories',
                'media' => fn ($query) => $query->orderBy('display_order')->orderBy('id'),
                'variants.attributeValues.attribute',
            ])
                ->withCompleteStorefrontMedia()
                ->where('slug', $product_slug)
                ->first();
        }

        if (! $product) {
            abort(404);
        }

        $slider = $this->legacyActiveSliders();
        $category_post = $this->legacyCategoryPosts();

        $cate_product = $this->categoryService->listActive();

        $brand_product = $this->brandService->listActive();

        $this->productService->recordProductView(
            $product,
            (int) Session::get('customer_id') ?: null,
            $request->ip(),
            $request->userAgent()
        );

        $primaryCategory = $product->primaryCategory ?? $product->categories->first();
        $product_cate = $primaryCategory?->category_name;
        $cate_slug = $primaryCategory?->slug_category_product;

        $meta_desc = StorefrontLabel::neutralizeSource(strip_tags((string) $product->product_desc));
        $meta_keywords = $product->product_slug;
        $meta_title = StorefrontLabel::neutralizeSource($product->product_name);
        $url_canonical = $request->url();
        $share_image = AssetUrl::productVariant($product->product_image, 'large');

        $gallery = $product->media ?? collect();
        $product_attribute = $this->legacyProductAttributes($product->product_id);
        $related_product = $this->productService->getRelatedProducts($product, 12);
        if ($this->allowsLocalPreviewCatalog() && $related_product->count() < 6) {
            $fallbackRelated = Product::with(['brand', 'categories', 'primaryCategory'])
                ->withCompleteStorefrontMedia()
                ->where('id', '!=', $product->id)
                ->whereNotNull('thumbnail_path')
                ->where('thumbnail_path', '!=', '')
                ->when($product->primary_category_id, function ($query, $categoryId) {
                    $query->orderByRaw('primary_category_id = ? desc', [$categoryId]);
                })
                ->orderByDesc('is_hot')
                ->orderByDesc('id')
                ->limit(18)
                ->get();

            $related_product = $related_product
                ->merge($fallbackRelated)
                ->unique('id')
                ->values();
        }
        $recommendedProducts = $this->productService->getRecommendedProducts(
            (int) Session::get('customer_id') ?: null,
            $request->ip(),
            $product->id,
            4,
            'product_detail'
        );
        $quickCompareProducts = $this->quickCompareProducts($product, $related_product);
        $quickCompareRows = $this->quickCompareRows($quickCompareProducts);
        $rating = $this->productReviewService->getAverageRating($product->id);

        return view('pages.sanpham.show_details')
            ->with('product_attribute', $product_attribute)
            ->with('category', $cate_product)
            ->with('brand', $brand_product)
            ->with('product_details', collect([$product]))
            ->with('relate', $related_product)
            ->with('meta_desc', $meta_desc)
            ->with('meta_keywords', $meta_keywords)
            ->with('meta_title', $meta_title)
            ->with('url_canonical', $url_canonical)
            ->with('slider', $slider)
            ->with('category_post', $category_post)
            ->with('gallery', $gallery)
            ->with('product_cate', $product_cate)
            ->with('cate_slug', $cate_slug)
            ->with('recommended_products', $recommendedProducts['products'])
            ->with('recommendation_description', $recommendedProducts['description'])
            ->with('quick_compare_products', $quickCompareProducts)
            ->with('quick_compare_rows', $quickCompareRows)
            ->with('rating', $rating)
            ->with('share_image', $share_image)
            ->with('share_images', $share_image)
            ->with('primaryCategory', $primaryCategory ?? null);
    }

    private function quickCompareProducts(Product $product, mixed $relatedProducts)
    {
        $primaryCategoryId = (int) ($product->primary_category_id ?? 0);

        return collect([$product])
            ->merge(collect($relatedProducts)
                ->filter(function ($item) use ($product, $primaryCategoryId) {
                    if ((int) $item->id === (int) $product->id) {
                        return false;
                    }

                    if ($primaryCategoryId <= 0) {
                        return true;
                    }

                    return (int) ($item->primary_category_id ?? 0) === $primaryCategoryId
                        || collect($item->categories ?? [])->contains(fn ($category) => (int) $category->id === $primaryCategoryId);
                })
                ->take(6))
            ->unique('id')
            ->values();
    }

    private function quickCompareRows($products): array
    {
        $rows = [
            'price' => ['label' => 'Giá bán', 'values' => []],
            'brand' => ['label' => 'Thương hiệu', 'values' => []],
            'category' => ['label' => 'Nhóm sản phẩm', 'values' => []],
            'stock' => ['label' => 'Tồn kho', 'values' => []],
        ];

        foreach ($products as $product) {
            $id = (int) $product->id;
            $price = (float) ($product->final_price ?? $product->product_price ?? 0);
            $rows['price']['values'][$id] = $price > 0 ? number_format($price, 0, ',', '.').'đ' : 'Liên hệ';
            $rows['brand']['values'][$id] = StorefrontLabel::neutralizeSource($product->brand_name ?: ($product->brand->brand_name ?? 'Đang cập nhật'));
            $rows['category']['values'][$id] = StorefrontLabel::category($product->primaryCategory->category_name ?? $product->category_name ?? null, $product->primaryCategory->slug_category_product ?? null, 'Sản phẩm');
            $rows['stock']['values'][$id] = ((int) ($product->product_quantity ?? 0) > 0) ? 'Còn hàng' : 'Tạm hết';

            foreach ($this->normalizedQuickCompareSpecs($product) as $key => $spec) {
                $rows[$key] ??= ['label' => $spec['label'], 'values' => []];
                $rows[$key]['values'][$id] = $spec['value'];
            }
        }

        return array_values($rows);
    }

    private function normalizedQuickCompareSpecs(Product $product): array
    {
        $meta = $product->meta ?? [];
        $specs = [];

        foreach ((array) data_get($meta, 'specifications', []) as $key => $value) {
            $label = trim((string) Str::of((string) $key)->replace(['_', '-'], ' ')->title());
            $normalizedKey = Str::slug($label, '_');
            $text = $this->stringifyQuickCompareValue($value);

            if ($normalizedKey !== '' && $text !== '') {
                $specs[$normalizedKey] = ['label' => $label, 'value' => $text];
            }
        }

        foreach ((array) data_get($meta, 'specs', []) as $spec) {
            if (! is_array($spec)) {
                continue;
            }

            $label = trim((string) ($spec['label'] ?? ''));
            $text = $this->stringifyQuickCompareValue($spec['value'] ?? null);
            $normalizedKey = Str::slug($label, '_');

            if ($normalizedKey !== '' && $text !== '') {
                $specs[$normalizedKey] = ['label' => $label, 'value' => $text];
            }
        }

        return $specs;
    }

    private function stringifyQuickCompareValue(mixed $value): string
    {
        if (is_array($value)) {
            return collect($value)
                ->map(fn ($entry) => is_scalar($entry) ? trim((string) $entry) : '')
                ->filter()
                ->implode(', ');
        }

        return is_scalar($value) ? trim((string) $value) : '';
    }

    private function allowsLocalPreviewCatalog(): bool
    {
        $appUrl = Str::lower((string) config('app.url'));

        return app()->environment(['local', 'testing'])
            || Str::contains($appUrl, ['localhost', '127.0.0.1']);
    }

    public function tag(Request $request, $product_tag)
    {
        $category_post = $this->legacyCategoryPosts();
        $slider = $this->legacyActiveSliders();

        $cate_product = $this->categoryService->listActive();

        $brand_product = $this->brandService->listActive();

        $tag = str_replace('-', ' ', $product_tag);
        $pro_tag = $this->productService->getProductsByTag($tag, 30);

        $meta_desc = 'Nhãn tìm kiếm: '.$product_tag;
        $meta_keywords = 'Nhãn tìm kiếm: '.$product_tag;
        $meta_title = 'Nhãn tìm kiếm: '.$product_tag;
        $url_canonical = $request->url();

        $tagLabel = \App\Support\StorefrontLabel::neutralizeSource($tag);
        $productCount = method_exists($pro_tag, 'total') ? $pro_tag->total() : $pro_tag->count();

        return view('pages.sanpham.listing')
            ->with('slider', $slider)
            ->with('category_post', $category_post)
            ->with('category', $cate_product)
            ->with('brand', $brand_product)
            ->with('meta_desc', $meta_desc)
            ->with('meta_keywords', $meta_keywords)
            ->with('meta_title', $meta_title)
            ->with('url_canonical', $url_canonical)
            ->with('product_tag', $product_tag)
            ->with('pro_tag', $pro_tag)
            ->with('pageMode', 'tag')
            ->with('products', $pro_tag)
            ->with('banner', [
                'breadcrumbItems' => [
                    [
                        'label' => 'Sản phẩm',
                        'url' => url('/san-pham'),
                    ],
                    [
                        'label' => $tagLabel,
                        'current' => true,
                    ],
                ],
                'kicker' => 'Nhãn sản phẩm',
                'title' => $tagLabel,
                'subtitle' => number_format($productCount).' sản phẩm đang được hiển thị theo nhãn này.',
                'results_kicker' => 'Danh sách sản phẩm',
                'results_title' => $tagLabel,
                'results_description' => 'Các sản phẩm cùng nhãn được trình bày bằng cùng một mẫu card để dễ so sánh và thêm vào giỏ hàng.',
                'empty_title' => 'Không có sản phẩm nào phù hợp với nhãn này.',
                'empty_action_text' => 'Xem tất cả sản phẩm',
                'empty_action_url' => url('/san-pham'),
            ])
            ->with('filter', [
                'keyword' => $tagLabel,
                'tag' => $tagLabel,
                'action' => url()->current(),
                'clear_url' => url('/san-pham'),
                'show_sidebar' => true,
                'show_sort' => true,
                'categories' => $cate_product,
                'brands' => $brand_product,
                'sort' => request('sort', 'popular'),
                'mode' => 'tag',
            ]);
    }

    public function insert_rating(InsertRatingRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $productId = (int) $validated['product_id'];
        $rating = (int) $validated['index'];

        $this->productReviewService->createRating($productId, $rating);

        return response()->json('Hoàn tất.');
    }

    public function ckeditor_image(Request $request)
    {
        $funcNum = max(0, (int) $request->input('CKEditorFuncNum', 0));

        $validator = validator($request->all(), [
            'upload' => ['required', 'file', 'image', 'mimes:jpg,jpeg,png,gif,webp', 'max:5120'],
        ], [
            'upload.required' => 'Vui lòng chọn hình ảnh để tải lên.',
            'upload.image' => 'Tệp tải lên phải là hình ảnh hợp lệ.',
            'upload.mimes' => 'Chỉ hỗ trợ định dạng JPG, JPEG, PNG, GIF hoặc WEBP.',
            'upload.max' => 'Kích thước ảnh không được vượt quá 5MB.',
        ]);

        if ($validator->fails()) {
            return $this->ckeditorCallbackResponse($funcNum, '', $validator->errors()->first('upload'));
        }

        $file = $request->file('upload');
        $directory = public_path('uploads/ckeditor');

        if (! File::isDirectory($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        $originName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeBaseName = Str::slug($originName) ?: 'ckeditor-image';
        $fileName = $safeBaseName.'_'.time().'.'.strtolower($file->getClientOriginalExtension());

        $file->move($directory, $fileName);

        return $this->ckeditorCallbackResponse(
            $funcNum,
            asset('uploads/ckeditor/'.$fileName),
            'Tải ảnh thành công.'
        );
    }

    protected function ckeditorCallbackResponse(int $funcNum, string $url, string $message)
    {
        $script = sprintf(
            '<script>window.parent.CKEDITOR.tools.callFunction(%d, %s, %s);</script>',
            $funcNum,
            json_encode($url, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            json_encode($message, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
        );

        return response($script, 200)->header('Content-Type', 'text/html; charset=utf-8');
    }

    protected function legacyProductAttributes(int $productId)
    {
        $table = (new ProductVariant)->getTable();

        if (! Schema::hasTable($table)) {
            return collect();
        }

        return ProductVariant::query()
            ->where('product_id', $productId)
            ->where('is_active', true)
            ->orderBy('id')
            ->get()
            ->map(function (ProductVariant $variant) {
                $imagePath = trim((string) $variant->image_path);

                return (object) [
                    'id' => $variant->id,
                    'product_id' => $variant->product_id,
                    'name' => $variant->name,
                    'price' => (float) ($variant->sale_price ?: $variant->price),
                    'quantity' => (int) $variant->stock_quantity,
                    'image' => $imagePath !== '' ? basename($imagePath) : null,
                ];
            });
    }
}
