<?php

namespace App\Http\Controllers;

use App\Infrastructure\Persistence\Eloquent\Catalog\ProductCategory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

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
        $perPage = max(1, min((int) $request->integer('per_page', 10), 50));

        $search = trim((string) $request->input('q', ''));
        $statusFilter = (string) $request->input('status', 'all');
        $levelFilter = (string) $request->input('level', 'all');
        $menuFilter = (string) $request->input('menu', 'all');

        $allCategoriesRaw = ProductCategory::query()
            ->withCount('products')
            ->orderBy('display_order')
            ->orderBy('name')
            ->get();

        $totalAll = $allCategoriesRaw->count();
        $totalActive = $allCategoriesRaw->where('is_active', true)->count();
        $totalHidden = $totalAll - $totalActive;
        $totalParents = $allCategoriesRaw->whereNull('parent_id')->count();
        $totalChildren = $totalAll - $totalParents;

        $stats = [
            'total' => $totalAll,
            'active' => $totalActive,
            'hidden' => $totalHidden,
            'parents' => $totalParents,
            'children' => $totalChildren,
            'active_pct' => $totalAll > 0 ? round($totalActive / $totalAll * 100, 1) : 0,
            'parent_pct' => $totalAll > 0 ? round($totalParents / $totalAll * 100, 1) : 0,
            'children_pct' => $totalAll > 0 ? round($totalChildren / $totalAll * 100, 1) : 0,
            'hidden_pct' => $totalAll > 0 ? round($totalHidden / $totalAll * 100, 1) : 0,
        ];

        $tree = $this->buildCategoryTree($allCategoriesRaw);

        $levelMap = [];
        $this->computeLevels($tree, 1, $levelMap);

        $query = ProductCategory::query()
            ->withCount('products')
            ->orderBy('display_order')
            ->orderBy('name');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('slug', 'like', '%' . $search . '%');
            });
        }

        if ($statusFilter === 'active') {
            $query->where('is_active', true);
        } elseif ($statusFilter === 'hidden') {
            $query->where('is_active', false);
        }

        if ($levelFilter === 'parent') {
            $query->whereNull('parent_id');
        } elseif ($levelFilter === 'child') {
            $query->whereNotNull('parent_id');
        }

        $categories = $query->paginate($perPage)->withQueryString();

        $rows = $categories->getCollection()->map(function (ProductCategory $cat) use ($levelMap, $allCategoriesRaw) {
            $level = $levelMap[$cat->id] ?? 1;
            $isMenu = (bool) ($cat->meta['show_in_menu'] ?? true);
            $isFeatured = (bool) ($cat->meta['is_featured'] ?? false);

            return (object) [
                'id' => $cat->id,
                'name' => $cat->name,
                'slug' => $cat->slug,
                'level' => $level,
                'parent_id' => $cat->parent_id,
                'product_count' => $cat->products_count ?? 0,
                'is_active' => (bool) $cat->is_active,
                'is_menu' => $isMenu,
                'is_featured' => $isFeatured,
                'thumbnail' => $cat->thumbnail_path,
                'updated_at' => $cat->updated_at,
                'description' => $cat->description,
                'parent_name' => $cat->parent_id
                    ? optional($allCategoriesRaw->firstWhere('id', $cat->parent_id))->name
                    : null,
            ];
        });

        $categories->setCollection($rows);

        return view('admin.category.index', [
            'categories' => $categories,
            'stats' => $stats,
            'tree' => $tree,
            'filters' => [
                'q' => $search,
                'status' => $statusFilter,
                'level' => $levelFilter,
                'menu' => $menuFilter,
                'per_page' => $perPage,
            ],
            'rawCategoryCount' => $totalAll,
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

    protected function buildCategoryTree(Collection $categories): array
    {
        $grouped = $categories->groupBy(fn (ProductCategory $c) => $c->parent_id ?? 0);

        $build = function ($parentId) use (&$build, $grouped) {
            return $grouped->get($parentId, collect())->map(function (ProductCategory $cat) use (&$build) {
                $childCount = ($cat->products_count ?? 0);

                return [
                    'id' => $cat->id,
                    'name' => $cat->name,
                    'slug' => $cat->slug,
                    'is_active' => (bool) $cat->is_active,
                    'thumbnail' => $cat->thumbnail_path,
                    'description' => $cat->description,
                    'product_count' => $childCount,
                    'updated_at' => $cat->updated_at,
                    'children' => $build($cat->id),
                ];
            })->values()->all();
        };

        return $build(0);
    }

    protected function computeLevels(array $tree, int $level, array &$map): void
    {
        foreach ($tree as $node) {
            $map[$node['id']] = $level;
            if (!empty($node['children'])) {
                $this->computeLevels($node['children'], $level + 1, $map);
            }
        }
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
