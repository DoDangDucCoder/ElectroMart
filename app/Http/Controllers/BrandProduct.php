<?php

namespace App\Http\Controllers;

use App\Application\Catalog\Services\BrandService;
use App\Application\Catalog\Services\CategoryService;
use App\Application\Catalog\Services\ProductService;
use App\Application\Cms\Services\BlogCategoryService;
use App\Http\Controllers\Concerns\LegacyStorefrontContext;
use App\Infrastructure\Persistence\Eloquent\Catalog\Brand;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class BrandProduct extends Controller
{
    use LegacyStorefrontContext;
    public function __construct(
        private readonly BrandService $brandService,
        private readonly ProductService $productService,
        private readonly CategoryService $categoryService,
        private readonly BlogCategoryService $blogCategoryService,
    ) {
    }

    public function add_brand_product(): View
    {
        return view('admin.brand.create');
    }

    public function all_brand_product(Request $request): View
    {
        $perPage = (int) $request->input('per_page', 15);
        $search = $request->input('search');

        $brands = $this->brandService->paginate(
            array_filter([
                'search' => $search,
                'is_active' => $request->input('is_active'),
            ]),
            $perPage
        );

        return view('admin.brand.index', [
            'brands' => $brands,
            'search' => $search,
        ]);
    }

    public function save_brand_product(Request $request): RedirectResponse
    {
        $request->merge([
            'brand_slug' => $request->input('brand_slug')
                ?: Str::slug((string) $request->input('brand_product_name')),
        ]);

        $validated = $request->validate([
            'brand_product_name' => ['required', 'string', 'max:255'],
            'brand_slug' => ['required', 'string', 'max:255', Rule::unique((new Brand())->getTable(), 'slug')],
            'brand_product_desc' => ['nullable', 'string'],
            'brand_product_status' => ['nullable', 'in:0,1'],
            'display_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $this->brandService->create([
            'name' => $validated['brand_product_name'],
            'slug' => $validated['brand_slug'] ?? null,
            'description' => $validated['brand_product_desc'] ?? null,
            'is_active' => ((int) ($validated['brand_product_status'] ?? 0)) === 0,
            'display_order' => $validated['display_order'] ?? 0,
        ]);

        return redirect()->to('/all-brand-product')->with('message', 'Thêm thương hiệu sản phẩm thành công');
    }

    public function unactive_brand_product(int $brandId): RedirectResponse
    {
        $brand = $this->brandService->findById($brandId);

        if (! $brand) {
            return redirect()->to('/all-brand-product')->with('error', 'Không tìm thấy thương hiệu cần cập nhật trạng thái');
        }

        $this->brandService->updateStatus($brand, false);

        return redirect()->to('/all-brand-product')->with('message', 'Đã tắt hiển thị thương hiệu');
    }

    public function active_brand_product(int $brandId): RedirectResponse
    {
        $brand = $this->brandService->findById($brandId);

        if (! $brand) {
            return redirect()->to('/all-brand-product')->with('error', 'Không tìm thấy thương hiệu cần cập nhật trạng thái');
        }

        $this->brandService->updateStatus($brand, true);

        return redirect()->to('/all-brand-product')->with('message', 'Đã kích hoạt hiển thị thương hiệu');
    }

    public function edit_brand_product(int $brandId): View|RedirectResponse
    {
        $brand = $this->brandService->findById($brandId);

        if (! $brand) {
            return redirect()->to('/all-brand-product')->with('error', 'Không tìm thấy thương hiệu cần chỉnh sửa');
        }

        return view('admin.brand.edit', [
            'brand' => $brand,
        ]);
    }

    public function update_brand_product(Request $request, int $brandId): RedirectResponse
    {
        $brand = $this->brandService->findById($brandId);

        if (! $brand) {
            return redirect()->to('/all-brand-product')->with('error', 'Không tìm thấy thương hiệu cần cập nhật');
        }

        $validated = $request->validate([
            'brand_product_name' => ['required', 'string', 'max:255'],
            'brand_product_slug' => ['nullable', 'string', 'max:255'],
            'brand_product_desc' => ['nullable', 'string'],
            'brand_product_status' => ['nullable', 'in:0,1'],
            'display_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $this->brandService->update($brand, [
            'name' => $validated['brand_product_name'],
            'slug' => $validated['brand_product_slug'] ?? null,
            'description' => $validated['brand_product_desc'] ?? null,
            'is_active' => ((int) ($validated['brand_product_status'] ?? 0)) === 0,
            'display_order' => $validated['display_order'] ?? $brand->display_order,
        ]);

        return redirect()->to('/all-brand-product')->with('message', 'Cập nhật thương hiệu sản phẩm thành công');
    }

    public function delete_brand_product(int $brandId): RedirectResponse
    {
        $brand = $this->brandService->findById($brandId);

        if (! $brand) {
            return redirect()->to('/all-brand-product')->with('error', 'Không tìm thấy thương hiệu cần xóa');
        }

        $this->brandService->delete($brand);

        return redirect()->to('/all-brand-product')->with('message', 'Xóa thương hiệu sản phẩm thành công');
    }

    public function show_brand_home(Request $request, string $brandSlug): View
    {
        $categoryPost = $this->blogCategoryService->listAll(['is_active' => true]);
        $slider = $this->legacyActiveSliders();

        $productCategories = $this->categoryService->listAll(['is_active' => true]);
        $brandList = $this->brandService->listActive();

        $brand = $this->brandService->findActiveBySlug($brandSlug);

        abort_if(! $brand, 404);

        $products = $this->productService->getProductsByBrandSlug($brandSlug, [], 9);
        $products->appends($request->query());

        $metaDesc = $brand->brand_desc;
        $metaKeywords = $brand->brand_desc;
        $metaTitle = $brand->brand_name;

        return view('pages.brand.show_brand', [
            'category' => $productCategories,
            'brand' => $brandList,
            'brand_by_id' => $products,
            'selected_brand' => $brand,
            'meta_desc' => $metaDesc,
            'meta_keywords' => $metaKeywords,
            'meta_title' => $metaTitle,
            'url_canonical' => $request->url(),
            'slider' => $slider,
            'category_post' => $categoryPost,
        ]);
    }

}
