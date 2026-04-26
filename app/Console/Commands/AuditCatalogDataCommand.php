<?php

namespace App\Console\Commands;

use App\Support\AssetUrl;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AuditCatalogDataCommand extends Command
{
    protected $signature = 'catalog:audit-data {--fail-on-issues : Return a non-zero exit code when blocking anomalies are found}';

    protected $description = 'Audit catalog data for hidden runtime issues without mutating records';

    public function handle(): int
    {
        $checks = $this->checks();
        $rows = [];
        $blockingIssues = 0;

        foreach ($checks as $name => $check) {
            $count = $check['count']();
            $blocking = (bool) ($check['blocking'] ?? true);

            if ($blocking) {
                $blockingIssues += $count;
            }

            $rows[] = [
                'check' => $name,
                'count' => $count,
                'blocking' => $blocking ? 'yes' : 'no',
            ];
        }

        $this->table(['Check', 'Count', 'Blocking'], $rows);

        if ($blockingIssues > 0) {
            $this->warn("Catalog audit found {$blockingIssues} blocking issue(s).");

            return $this->option('fail-on-issues') ? self::FAILURE : self::SUCCESS;
        }

        $this->info('Catalog audit passed.');

        return self::SUCCESS;
    }

    private function checks(): array
    {
        return [
            'products_empty_slug' => ['count' => fn () => $this->countEmpty('products', 'slug')],
            'products_duplicate_slug' => ['count' => fn () => $this->countDuplicates('products', 'slug')],
            'products_bad_price' => ['count' => fn () => $this->countProductsBadPrice()],
            'products_sale_price_not_less_than_price' => ['count' => fn () => $this->countProductsBadSalePrice()],
            'products_orphan_brand' => ['count' => fn () => $this->countProductsOrphanBrand()],
            'products_orphan_primary_category' => ['count' => fn () => $this->countProductsOrphanPrimaryCategory()],
            'product_category_pivot_orphans' => ['count' => fn () => $this->countPivotOrphans()],
            'product_media_orphans' => ['count' => fn () => $this->countProductMediaOrphans()],
            'products_invalid_meta' => ['count' => fn () => $this->countInvalidJson('products', 'meta')],
            'brands_empty_slug' => ['count' => fn () => $this->countEmpty('brands', 'slug')],
            'brands_duplicate_slug' => ['count' => fn () => $this->countDuplicates('brands', 'slug')],
            'categories_empty_slug' => ['count' => fn () => $this->countEmpty('product_categories', 'slug')],
            'categories_duplicate_slug' => ['count' => fn () => $this->countDuplicates('product_categories', 'slug')],
            'product_images_unresolvable' => ['count' => fn () => $this->countUnresolvableProductImages()],
            'product_media_unresolvable' => ['count' => fn () => $this->countUnresolvableProductMedia()],
            'external_product_thumbnails' => [
                'count' => fn () => $this->countExternalProductThumbnails(),
                'blocking' => false,
            ],
        ];
    }

    private function countEmpty(string $table, string $column): int
    {
        if (! $this->hasColumn($table, $column)) {
            return 0;
        }

        return DB::table($table)
            ->where(fn ($query) => $query->whereNull($column)->orWhere($column, ''))
            ->count();
    }

    private function countDuplicates(string $table, string $column): int
    {
        if (! $this->hasColumn($table, $column)) {
            return 0;
        }

        return DB::query()
            ->fromSub(
                DB::table($table)
                    ->select($column)
                    ->whereNotNull($column)
                    ->where($column, '<>', '')
                    ->groupBy($column)
                    ->havingRaw('COUNT(*) > 1'),
                'duplicates'
            )
            ->count();
    }

    private function countProductsBadPrice(): int
    {
        if (! $this->hasColumn('products', 'price')) {
            return 0;
        }

        return DB::table('products')
            ->where(fn ($query) => $query->whereNull('price')->orWhere('price', '<', 0))
            ->count();
    }

    private function countProductsBadSalePrice(): int
    {
        if (! $this->hasColumn('products', 'sale_price') || ! $this->hasColumn('products', 'price')) {
            return 0;
        }

        return DB::table('products')
            ->whereNotNull('sale_price')
            ->whereColumn('sale_price', '>=', 'price')
            ->count();
    }

    private function countProductsOrphanBrand(): int
    {
        if (! Schema::hasTable('products') || ! Schema::hasTable('brands') || ! $this->hasColumn('products', 'brand_id')) {
            return 0;
        }

        return DB::table('products as p')
            ->leftJoin('brands as b', 'b.id', '=', 'p.brand_id')
            ->whereNotNull('p.brand_id')
            ->whereNull('b.id')
            ->count();
    }

    private function countProductsOrphanPrimaryCategory(): int
    {
        if (! Schema::hasTable('products') || ! Schema::hasTable('product_categories') || ! $this->hasColumn('products', 'primary_category_id')) {
            return 0;
        }

        return DB::table('products as p')
            ->leftJoin('product_categories as c', 'c.id', '=', 'p.primary_category_id')
            ->whereNotNull('p.primary_category_id')
            ->whereNull('c.id')
            ->count();
    }

    private function countPivotOrphans(): int
    {
        if (! Schema::hasTable('product_category_product')) {
            return 0;
        }

        return DB::table('product_category_product as pc')
            ->leftJoin('products as p', 'p.id', '=', 'pc.product_id')
            ->leftJoin('product_categories as c', 'c.id', '=', 'pc.product_category_id')
            ->where(fn ($query) => $query->whereNull('p.id')->orWhereNull('c.id'))
            ->count();
    }

    private function countProductMediaOrphans(): int
    {
        if (! Schema::hasTable('product_media') || ! Schema::hasTable('products')) {
            return 0;
        }

        return DB::table('product_media as m')
            ->leftJoin('products as p', 'p.id', '=', 'm.product_id')
            ->whereNull('p.id')
            ->count();
    }

    private function countInvalidJson(string $table, string $column): int
    {
        if (! $this->hasColumn($table, $column)) {
            return 0;
        }

        return DB::table($table)
            ->whereNotNull($column)
            ->pluck($column)
            ->filter(function ($value) {
                if (is_array($value) || is_object($value)) {
                    return false;
                }

                json_decode((string) $value, true);

                return json_last_error() !== JSON_ERROR_NONE;
            })
            ->count();
    }

    private function countUnresolvableProductImages(): int
    {
        if (! $this->hasColumn('products', 'thumbnail_path')) {
            return 0;
        }

        return DB::table('products')
            ->pluck('thumbnail_path')
            ->filter(fn ($path) => $this->isUnresolvablePath($path))
            ->count();
    }

    private function countUnresolvableProductMedia(): int
    {
        if (! $this->hasColumn('product_media', 'file_path')) {
            return 0;
        }

        return DB::table('product_media')
            ->pluck('file_path')
            ->filter(fn ($path) => $this->isUnresolvablePath($path))
            ->count();
    }

    private function countExternalProductThumbnails(): int
    {
        if (! $this->hasColumn('products', 'thumbnail_path')) {
            return 0;
        }

        return DB::table('products')
            ->where('thumbnail_path', 'like', 'http://%')
            ->orWhere('thumbnail_path', 'like', 'https://%')
            ->count();
    }

    private function isUnresolvablePath(mixed $path): bool
    {
        $path = trim((string) $path);

        if ($path === '' || AssetUrl::isAbsoluteUrl($path)) {
            return false;
        }

        return ! file_exists(public_path(ltrim($path, '/\\')));
    }

    private function hasColumn(string $table, string $column): bool
    {
        try {
            return Schema::hasTable($table) && Schema::hasColumn($table, $column);
        } catch (\Throwable) {
            return false;
        }
    }
}
