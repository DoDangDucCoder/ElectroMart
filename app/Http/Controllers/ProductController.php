<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use App\Models\Slider;
use App\Models\CatePost;
use App\Models\ProductAttribute;
use App\Application\Catalog\Services\ProductService;
use App\Application\Catalog\Services\ProductReviewService;
use App\Domain\Catalog\Repositories\BrandRepositoryInterface;
use App\Domain\Catalog\Repositories\CategoryRepositoryInterface;
use App\Infrastructure\Persistence\Eloquent\Catalog\Brand;
use App\Infrastructure\Persistence\Eloquent\Catalog\Product;
use App\Infrastructure\Persistence\Eloquent\Catalog\ProductCategory;

class ProductController extends Controller
{
    public function __construct(
        private readonly ProductService $productService,
        private readonly ProductReviewService $productReviewService,
        private readonly CategoryRepositoryInterface $categoryRepository,
        private readonly BrandRepositoryInterface $brandRepository
    ) {
    }

    public function AuthLogin()
    {

        // if (Session::get('login_normal')) {

        //     $admin_id = Session::get('admin_id');
        // } else {
        //     $admin_id = Auth::id();
        // }
        // if ($admin_id) {
        //     return Redirect::to('dashboard');
        // } else {
        //     return Redirect::to('admin')->send();
        // }
    }
    public function file_browser(Request $request)
    {
        $paths = glob(public_path('uploads/ckeditor/*'));

        $fileNames = array();

        foreach ($paths as $path) {
            array_push($fileNames, basename($path));
        }
        $data = array(
            'fileNames' => $fileNames
        );

        return view('admin.images.file_browser')->with($data);
    }
    public function reply_comment(Request $request): JsonResponse
    {
        $parentId = (int) $request->input('comment_id');
        $productId = (int) $request->input('comment_product_id');
        $content = trim((string) $request->input('comment'));

        if (! $parentId || ! $productId || $content === '') {
            return response()->json(['message' => 'Invalid data'], 422);
        }

        $adminName = Auth::user()->name ?? 'Admin';
        $reply = $this->productReviewService->replyAsAdmin($productId, $parentId, $content, $adminName);

        if (! $reply) {
            return response()->json(['message' => 'Unable to reply comment'], 400);
        }

        return response()->json(['message' => 'ok']);
    }

    public function allow_comment(Request $request): JsonResponse
    {
        $commentId = (int) $request->input('comment_id');
        $status = (int) $request->input('comment_status') === 0 ? 'approved' : 'pending';

        $review = $this->productReviewService->updateStatus($commentId, $status);

        if (! $review) {
            return response()->json(['message' => 'Comment not found'], 404);
        }

        return response()->json(['message' => 'ok', 'status' => $review->comment_status]);
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

    public function send_comment(Request $request): JsonResponse
    {
        $productId = (int) $request->input('product_id');
        $author = trim((string) $request->input('comment_name'));
        $content = trim((string) $request->input('comment_content'));

        if (! $productId || $author === '' || $content === '') {
            return response()->json(['message' => 'Invalid data'], 422);
        }

        $this->productReviewService->createCustomerComment($productId, $author, $content);

        return response()->json(['message' => 'ok']);
    }
    public function load_comment(Request $request): JsonResponse
    {
        $productId = (int) $request->input('product_id');
        $threads = $this->productReviewService->getApprovedThread($productId);

        $output = '';

        foreach ($threads as $review) {
            $output .= '
            <div class="row style_comment">

                                        <div class="col-md-2">
                                            <img width="100%" src="' . url('frontend/images/batman-icon.png') . '" class="img img-responsive img-thumbnail">
                                        </div>
                                        <div class="col-md-10">
                                            <p style="color:green;">@' . e($review->comment_name) . '</p>
                                            <p style="color:#000;">' . e($review->comment_date) . '</p>
                                            <p>' . e($review->comment) . '</p>
                                        </div>
                                    </div><p></p>
                                    ';

            foreach ($review->children as $reply) {
                $output .= ' <div class="row style_comment" style="margin:5px 40px;background: aquamarine;">

                                        <div class="col-md-2">
                                            <img width="80%" src="' . url('frontend/images/businessman.jpg') . '" class="img img-responsive img-thumbnail">
                                        </div>
                                        <div class="col-md-10">
                                            <p style="color:blue;">@' . e($reply->comment_name) . '</p>
                                            <p style="color:#000;">' . e($reply->comment) . '</p>
                                            <p></p>
                                        </div>
                                    </div><p></p>';
            }
        }

        return response($output);
    }
    public function local_storage()
    {
        return view('pages.local_storage.storage');
    }

    public function quickview(Request $request): JsonResponse
    {
        $productId = (int) $request->input('product_id');
        $product = $this->productService->findProductById($productId);

        if (! $product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $galleryHtml = '';
        $mediaItems = $product->media ?? collect();

        foreach ($mediaItems as $media) {
            $imagePath = $media->file_path ?? $product->product_image;

            if (! $imagePath) {
                continue;
            }

            $galleryHtml .= '<p><img width="100%" src="'.asset($imagePath).'"></p>';
        }

        if ($galleryHtml === '') {
            $fallbackImage = $product->product_image ? asset($product->product_image) : asset('frontend/images/no-image.png');
            $galleryHtml = '<p><img width="100%" src="'.$fallbackImage.'"></p>';
        }

        $displayPrice = number_format($product->product_price, 0, ',', '.').' VND';
        $primaryImage = $product->product_image ? asset($product->product_image) : asset('frontend/images/no-image.png');

        $output = [
            'product_gallery' => $galleryHtml,
            'product_name' => $product->product_name,
            'product_id' => $product->product_id,
            'product_desc' => $product->product_desc,
            'product_content' => $product->product_content,
            'product_price' => $displayPrice,
            'product_image' => '<p><img width="100%" src="'.$primaryImage.'"></p>',
            'product_button' => '<input type="button" value="Mua ngay" class="btn btn-primary btn-sm add-to-cart-quickview" id="buy_quickview" data-id_product="'.$product->product_id.'"  name="add-to-cart">',
            'product_quickview_value' => '

        <input type="hidden" value="'.$product->product_id.'" class="cart_product_id_'.$product->product_id.'">
                                            
        <input type="hidden" value="'.$product->product_name.'" class="cart_product_name_'.$product->product_id.'">
                                          
        <input type="hidden" value="'.$product->product_quantity.'" class="cart_product_quantity_'.$product->product_id.'">
                                            
        <input type="hidden" value="'.$product->product_image.'" class="cart_product_image_'.$product->product_id.'">

        <input type="hidden" value="'.$product->product_price.'" class="cart_product_price_'.$product->product_id.'">

        <input type="hidden" value="1" class="cart_product_qty_'.$product->product_id.'">',
        ];

        return response()->json($output);
    }
    public function add_product()
    {
        $this->AuthLogin();
        $cate_product = $this->categoryRepository->all();
        $brand_product = $this->brandRepository->all();

        return view('admin.add_product')
            ->with('cate_product', $cate_product)
            ->with('brand_product', $brand_product);
    }
    public function all_product()
    {
        $this->AuthLogin();
        $all_product = $this->productService->paginateForAdmin(['only_active' => false], 20);
        $manager_product  = view('admin.all_product')->with('all_product', $all_product);
        return view('admin_layout')->with('admin.all_product', $manager_product);
    }
    public function save_product(Request $request)
    {
        $this->AuthLogin();

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
        $this->AuthLogin();

        $product = $this->productService->findProductById($product_id);

        if (! $product) {
            abort(404);
        }

        $this->productService->setProductStatus($product, false);

        Session::put('message', 'Không kích hoạt sản phẩm thành công');

        return Redirect::to('all-product');
    }
    public function active_product(int $product_id)
    {
        $this->AuthLogin();

        $product = $this->productService->findProductById($product_id);

        if (! $product) {
            abort(404);
        }

        $this->productService->setProductStatus($product, true);

        Session::put('message', 'Kích hoạt sản phẩm thành công');

        return Redirect::to('all-product');
    }
    public function edit_product(int $product_id)
    {
        $this->AuthLogin();

        $product = $this->productService->findProductById($product_id);

        if (! $product) {
            abort(404);
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
        $this->AuthLogin();

        $product = $this->productService->findProductById($product_id);

        if (! $product) {
            abort(404);
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

        return Redirect::to('all-product');
    }
    public function delete_document(Request $request): JsonResponse
    {
        $productId = (int) $request->input('product_id');

        $product = $this->productService->findProductById($productId);

        if (! $product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $this->productService->removeProductDocument($product);

        return response()->json(['message' => 'ok']);
    }
    public function delete_product(int $product_id)
    {
        $this->AuthLogin();

        $product = $this->productService->findProductById($product_id);

        if (! $product) {
            abort(404);
        }

        $this->productService->deleteProduct($product);

        Session::put('message', 'Xóa sản phẩm thành công');

        return Redirect::to('all-product');
    }
    protected function validateProductRequest(Request $request, ?int $productId = null): array
    {
        return $request->validate([
            'product_name' => ['required', 'string', 'max:255'],
            'product_slug' => ['nullable', 'string', 'max:255', Rule::unique('products', 'slug')->ignore($productId)],
            'product_price' => ['required'],
            'price_cost' => ['nullable'],
            'product_quantity' => ['required', 'integer', 'min:0'],
            'product_desc' => ['nullable', 'string'],
            'product_content' => ['nullable', 'string'],
            'product_cate' => ['required', 'integer', 'exists:product_categories,id'],
            'product_brand' => ['nullable', 'integer', 'exists:brands,id'],
            'product_status' => ['required', 'in:0,1'],
            'product_tags' => ['nullable', 'string'],
            'product_image' => ['nullable', 'image', 'max:4096'],
            'document' => ['nullable', 'file', 'max:5120'],
        ]);
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
            'sale_price' => null,
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
    //End Admin Page
    public function details_product($product_slug, Request $request)
    {
        $product = $this->productService->findProductBySlug($product_slug);

        if (! $product) {
            abort(404);
        }

        $slider = Slider::orderBy('slider_id', 'DESC')->where('slider_status', '1')->take(4)->get();
        $category_post = CatePost::orderBy('cate_post_id', 'DESC')->get();

        $cate_product = ProductCategory::where('is_active', true)
            ->orderBy('display_order')
            ->orderByDesc('id')
            ->get();

        $brand_product = Brand::where('is_active', true)
            ->orderBy('display_order')
            ->orderByDesc('id')
            ->get();

        $this->productService->incrementProductViews($product);

        $primaryCategory = $product->primaryCategory ?? $product->categories->first();
        $product_cate = $primaryCategory?->category_name;
        $cate_slug = $primaryCategory?->slug_category_product;

        $meta_desc = $product->product_desc;
        $meta_keywords = $product->product_slug;
        $meta_title = $product->product_name;
        $url_canonical = $request->url();
        $share_images = $product->product_image ? asset($product->product_image) : asset('frontend/images/no-image.png');

        $gallery = $product->media ?? collect();
        $product_attribute = ProductAttribute::where('product_id', $product->product_id)->get();
        $related_product = $this->productService->getRelatedProducts($product, 6);
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
            ->with('rating', $rating)
            ->with('share_images', $share_images);
    }
    public function tag(Request $request, $product_tag)
    {
        $category_post = CatePost::orderBy('cate_post_id', 'DESC')->get();
        $slider = Slider::orderBy('slider_id', 'DESC')->where('slider_status', '1')->take(4)->get();

        $cate_product = ProductCategory::where('is_active', true)
            ->orderBy('display_order')
            ->orderByDesc('id')
            ->get();

        $brand_product = Brand::where('is_active', true)
            ->orderBy('display_order')
            ->orderByDesc('id')
            ->get();

        $tag = str_replace('-', ' ', $product_tag);
        $pro_tag = $this->productService->getProductsByTag($tag, 30);

        $meta_desc = 'Tags tìm kiếm::' . $product_tag;
        $meta_keywords = 'Tags tìm kiếm:' . $product_tag;
        $meta_title = 'Tags tìm kiếm:' . $product_tag;
        $url_canonical = $request->url();

        return view('pages.sanpham.tag')
            ->with('slider', $slider)
            ->with('category_post', $category_post)
            ->with('category', $cate_product)
            ->with('brand', $brand_product)
            ->with('meta_desc', $meta_desc)
            ->with('meta_keywords', $meta_keywords)
            ->with('meta_title', $meta_title)
            ->with('url_canonical', $url_canonical)
            ->with('product_tag', $product_tag)
            ->with('pro_tag', $pro_tag);
    }
    public function insert_rating(Request $request): JsonResponse
    {
        $productId = (int) $request->input('product_id');
        $rating = (int) $request->input('index');

        if (! $productId || ! $rating) {
            return response()->json('error', 422);
        }

        $this->productReviewService->createRating($productId, $rating);

        return response()->json('done');
    }
    public function ckeditor_image(Request $request)
    {
        if ($request->hasFile('upload')) {

            $originName = $request->file('upload')->getClientOriginalName();
            $fileName = pathinfo($originName, PATHINFO_FILENAME);
            $extension = $request->file('upload')->getClientOriginalExtension();
            $fileName = $fileName . '_' . time() . '.' . $extension;
            $request->file('upload')->move('uploads/ckeditor', $fileName);

            $CKEditorFuncNum = $request->input('CKEditorFuncNum');
            $url = asset('uploads/ckeditor/' . $fileName);
            $msg = 'Tß║úi ß║únh th├ánh c├┤ng';
            $response = "<script>window.parent.CKEDITOR.tools.callFunction($CKEditorFuncNum, '$url', '$msg')</script>";
            @header('Content-type: text/html; charset=utf-8');
            echo $response;
        }
    }
}


























