<?php

namespace App\Http\Controllers;

use App\Infrastructure\Persistence\Eloquent\Catalog\ProductCategory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class CategoryProductAdminController extends Controller
{
    public function add_category_product(): View
    {
        return view('admin.add_category_product', [
            'category' => $this->rootLegacyCategories(),
        ]);
    }

    public function all_category_product(Request $request): View
    {
        $perPage = max(1, min((int) $request->integer('per_page', 15), 50));

        $categories = ProductCategory::query()
            ->orderBy('display_order')
            ->orderBy('name')
            ->paginate($perPage);

        $categories->setCollection(
            $categories->getCollection()->map(
                fn (ProductCategory $category) => $this->toLegacyCategoryRecord($category)
            )
        );

        return view('admin.all_category_product', [
            'all_category_product' => $categories,
            'category_product' => $this->allLegacyCategories(),
        ]);
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
