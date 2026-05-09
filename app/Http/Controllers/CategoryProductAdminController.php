<?php

namespace App\Http\Controllers;

use App\Infrastructure\Persistence\Eloquent\Catalog\ProductCategory;
use App\Infrastructure\Persistence\Eloquent\Catalog\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CategoryProductAdminController extends Controller
{
    public function add_category_product(): View
    {
        return view('admin.add_category_product', [
            'category' => $this->rootLegacyCategories(),
        ]);
    }

    public function save_category_product(Request $request)
    {
        $request->merge([
            'slug_category_product' => $request->input('slug_category_product')
                ?: Str::slug((string) $request->input('category_product_name')),
        ]);

        $validated = Validator::make($request->all(), [
            'category_product_name' => ['required', 'string', 'max:255'],
            'slug_category_product' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('product_categories', 'slug'),
            ],
            'category_product_desc' => ['nullable', 'string'],
            'category_product_keywords' => ['nullable', 'string'],
            'category_parent' => [
                'nullable',
                'integer',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    $parentId = (int) $value;

                    if ($parentId > 0 && ! ProductCategory::query()->whereKey($parentId)->exists()) {
                        $fail('Danh mục cha không hợp lệ.');
                    }
                },
            ],
            'category_product_status' => ['required', 'in:0,1'],
        ])->validate();

        $parentId = (int) ($validated['category_parent'] ?? 0);
        $nextOrder = ((int) ProductCategory::query()->max('display_order')) + 1;

        ProductCategory::query()->create([
            'name' => $validated['category_product_name'],
            'slug' => $validated['slug_category_product'] ?: Str::slug($validated['category_product_name']),
            'parent_id' => $parentId > 0 ? $parentId : null,
            'description' => $validated['category_product_desc'] ?? null,
            'is_active' => (int) $validated['category_product_status'] === 0,
            'display_order' => $nextOrder,
            'meta' => [
                'meta_keywords' => $validated['category_product_keywords'] ?? null,
            ],
        ]);

        return redirect('/admin/categories-management')->with('message', 'Thêm danh mục sản phẩm thành công.');
    }

    public function edit_category_product(int $category_id): View
    {
        $category = ProductCategory::query()->find($category_id);

        return view('admin.edit_category_product', [
            'edit_category_product' => $category
                ? collect([$this->toLegacyCategoryRecord($category)])
                : collect(),
            'category' => $this->allLegacyCategories(),
        ]);
    }

    public function update_category_product(Request $request, int $category_id): RedirectResponse
    {
        $category = ProductCategory::query()->find($category_id);

        if (! $category) {
            return redirect('/admin/categories-management')->with('error', 'Không tìm thấy danh mục cần cập nhật.');
        }

        $name = trim((string) $request->input('category_product_name'));
        $submittedSlug = trim((string) $request->input('slug_category_product'));
        $nameChanged = $name !== '' && $name !== (string) $category->name;

        if ($submittedSlug === '' || ($nameChanged && $submittedSlug === (string) $category->slug)) {
            $submittedSlug = Str::slug($name);
        }

        $request->merge(['slug_category_product' => $submittedSlug]);

        $validated = Validator::make($request->all(), [
            'category_product_name' => ['required', 'string', 'max:255'],
            'slug_category_product' => [
                'required',
                'string',
                'max:255',
                Rule::unique('product_categories', 'slug')->ignore($category->id),
            ],
            'category_product_desc' => ['nullable', 'string'],
            'category_product_keywords' => ['nullable', 'string'],
            'category_parent' => [
                'nullable',
                'integer',
                Rule::notIn([$category->id]),
            ],
        ])->validate();

        $parentId = (int) ($validated['category_parent'] ?? 0);
        if ($parentId > 0 && ! ProductCategory::query()->whereKey($parentId)->exists()) {
            return redirect()->back()
                ->withErrors(['category_parent' => 'Danh mục cha không hợp lệ.'])
                ->withInput();
        }

        $meta = $category->meta ?? [];
        $meta['meta_keywords'] = $validated['category_product_keywords'] ?? null;

        $category->update([
            'name' => $validated['category_product_name'],
            'slug' => $validated['slug_category_product'],
            'parent_id' => $parentId > 0 ? $parentId : null,
            'description' => $validated['category_product_desc'] ?? null,
            'meta' => $meta,
        ]);

        return redirect('/admin/categories-management')->with('message', 'Cập nhật danh mục sản phẩm thành công.');
    }

    public function delete_category_product(int $category_id): RedirectResponse
    {
        $category = ProductCategory::query()->find($category_id);

        if (! $category) {
            return redirect()->back()->with('error', 'Không tìm thấy danh mục cần xóa.');
        }

        $hasProducts = Product::query()->where('primary_category_id', $category->id)->exists()
            || $category->products()->exists();

        if ($hasProducts) {
            return redirect()->back()->with('error', 'Không thể xóa danh mục đang có sản phẩm.');
        }

        $category->delete();

        return redirect('/admin/categories-management')->with('message', 'Xóa danh mục sản phẩm thành công.');
    }

    public function active_category_product(int $category_id): RedirectResponse
    {
        return $this->setCategoryStatus($category_id, true, 'Đã kích hoạt danh mục sản phẩm.');
    }

    public function unactive_category_product(int $category_id): RedirectResponse
    {
        return $this->setCategoryStatus($category_id, false, 'Đã ẩn danh mục sản phẩm.');
    }

    protected function setCategoryStatus(int $categoryId, bool $isActive, string $message): RedirectResponse
    {
        $category = ProductCategory::query()->find($categoryId);

        if (! $category) {
            return redirect()->back()->with('error', 'Không tìm thấy danh mục cần cập nhật trạng thái.');
        }

        $category->update(['is_active' => $isActive]);

        return redirect()->back()->with('message', $message);
    }

    protected function rootLegacyCategories()
    {
        return ProductCategory::query()
            ->whereNull('parent_id')
            ->orderBy('display_order')
            ->orderBy('name')
            ->get()
            ->map(fn (ProductCategory $category) => $this->toLegacyCategoryRecord($category))
            ->values();
    }

    protected function allLegacyCategories()
    {
        return ProductCategory::query()
            ->orderBy('display_order')
            ->orderBy('name')
            ->get()
            ->map(fn (ProductCategory $category) => $this->toLegacyCategoryRecord($category))
            ->values();
    }

    protected function toLegacyCategoryRecord(ProductCategory $category): object
    {
        return (object) [
            'category_id' => $category->id,
            'category_name' => $category->name,
            'category_parent' => $category->parent_id ?? 0,
            'slug_category_product' => $category->slug,
            'category_desc' => $category->description,
            'meta_keywords' => data_get($category->meta, 'meta_keywords'),
            'category_order' => $category->display_order ?? 0,
            'category_status' => $category->is_active ? 0 : 1,
        ];
    }
}
