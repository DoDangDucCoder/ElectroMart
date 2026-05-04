<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class PurgeDemoCatalogCommand extends Command
{
    protected $signature = 'catalog:purge-demo
                            {--source=dummyjson : Demo catalog source marker to purge}
                            {--dry-run : Show affected row counts without deleting}
                            {--confirm-replacement-ready : Confirm a replacement catalog is ready before deleting demo rows}';

    protected $description = 'Purge demo catalog rows such as DummyJSON products, brands, categories, and media';

    public function handle(): int
    {
        foreach (['products', 'brands', 'product_categories'] as $requiredTable) {
            if (! Schema::hasTable($requiredTable)) {
                $this->error("Required table [{$requiredTable}] does not exist.");

                return self::FAILURE;
            }
        }

        $source = Str::lower(trim((string) $this->option('source'))) ?: 'dummyjson';
        $dryRun = (bool) $this->option('dry-run');
        $confirmed = (bool) $this->option('confirm-replacement-ready');

        $productIds = $this->demoProductIds($source);
        $brandIds = $this->demoIds('brands', $source);
        $categoryIds = $this->demoIds('product_categories', $source);

        $counts = [
            'products' => $productIds->count(),
            'brands' => $brandIds->count(),
            'product_categories' => $categoryIds->count(),
            'product_media' => $this->productMediaCount($productIds, $source),
            'product_category_product' => $this->productCategoryPivotCount($productIds, $categoryIds),
        ];

        $this->table(['Table', 'Rows'], collect($counts)->map(
            static fn (int $count, string $table) => [$table, $count]
        )->values()->all());

        if ($dryRun) {
            $this->info('Dry run only. No catalog rows were deleted.');

            return self::SUCCESS;
        }

        if (! $confirmed) {
            $this->warn('Demo catalog purge is destructive.');
            $this->line('Import or prepare the replacement catalog first, then rerun with --confirm-replacement-ready.');

            return self::FAILURE;
        }

        DB::transaction(function () use ($productIds, $brandIds, $categoryIds, $source): void {
            if (Schema::hasTable('product_category_product')) {
                DB::table('product_category_product')
                    ->whereIn('product_id', $productIds)
                    ->orWhereIn('product_category_id', $categoryIds)
                    ->delete();
            }

            if (Schema::hasTable('product_media')) {
                DB::table('product_media')
                    ->whereIn('product_id', $productIds)
                    ->orWhere($this->metaSourcePredicate($source))
                    ->delete();
            }

            DB::table('products')
                ->whereIn('id', $productIds)
                ->delete();

            DB::table('brands')
                ->whereIn('id', $brandIds)
                ->delete();

            DB::table('product_categories')
                ->whereIn('id', $categoryIds)
                ->delete();
        });

        $this->info('Demo catalog rows were purged.');

        return self::SUCCESS;
    }

    private function demoProductIds(string $source)
    {
        return DB::table('products')
            ->where(function ($query) use ($source): void {
                $query->where($this->metaSourcePredicate($source))
                    ->orWhere('slug', 'like', $source.'-%');

                if ($source === 'dummyjson') {
                    $query->orWhere('sku', 'like', 'DJSON-%');
                }
            })
            ->pluck('id');
    }

    private function demoIds(string $table, string $source)
    {
        return DB::table($table)
            ->where($this->metaSourcePredicate($source))
            ->pluck('id');
    }

    private function productMediaCount($productIds, string $source): int
    {
        if (! Schema::hasTable('product_media')) {
            return 0;
        }

        return DB::table('product_media')
            ->whereIn('product_id', $productIds)
            ->orWhere($this->metaSourcePredicate($source))
            ->count();
    }

    private function productCategoryPivotCount($productIds, $categoryIds): int
    {
        if (! Schema::hasTable('product_category_product')) {
            return 0;
        }

        return DB::table('product_category_product')
            ->whereIn('product_id', $productIds)
            ->orWhereIn('product_category_id', $categoryIds)
            ->count();
    }

    private function metaSourcePredicate(string $source): \Closure
    {
        return static function ($query) use ($source): void {
            $query->whereRaw(
                "CASE WHEN JSON_VALID(meta) THEN JSON_UNQUOTE(JSON_EXTRACT(meta, '$.source')) ELSE NULL END = ?",
                [$source]
            );
        };
    }
}
