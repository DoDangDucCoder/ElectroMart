<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class CleanupOrphanedProducts extends Command
{
    protected $signature = 'app:cleanup-orphaned-products
        {--apply : Persist changes. Without this option the command only reports a dry-run}
        {--default-category=phu-kien : Fallback product_categories.slug for unmatched products}
        {--hide-unmatched : Hide products that cannot be classified instead of assigning the fallback category}';

    protected $description = 'Safely repair products whose category references no longer exist';

    private array $categoryIds = [];

    public function handle(): int
    {
        $this->loadCategoryIds();

        $defaultSlug = (string) $this->option('default-category');
        $defaultCategoryId = $this->categoryIds[$defaultSlug] ?? null;

        if (! $defaultCategoryId) {
            $this->error("Default category '{$defaultSlug}' does not exist.");

            return self::FAILURE;
        }

        $orphans = DB::table('products as p')
            ->leftJoin('product_categories as c', 'p.primary_category_id', '=', 'c.id')
            ->whereNotNull('p.primary_category_id')
            ->whereNull('c.id')
            ->select('p.id', 'p.name', 'p.slug', 'p.primary_category_id', 'p.is_active')
            ->orderBy('p.id')
            ->get();

        if ($orphans->isEmpty()) {
            $this->info('No orphaned products found.');

            return self::SUCCESS;
        }

        $actions = $orphans->map(fn ($product) => $this->buildAction($product, $defaultCategoryId));
        $summary = $actions->groupBy(fn ($action) => $action['target_slug'] ?? 'hide')->map->count();

        $this->warn("Found {$orphans->count()} orphaned products.");
        $this->table(['target', 'count'], $summary->map(fn ($count, $target) => [$target, $count])->values()->all());

        if (! $this->option('apply')) {
            $this->info('Dry-run only. Re-run with --apply to update products and pivot categories.');

            return self::SUCCESS;
        }

        $backupPath = $this->backup($orphans->pluck('id')->all());

        DB::transaction(function () use ($actions): void {
            foreach ($actions as $action) {
                if ($action['target_id'] === null) {
                    DB::table('products')->where('id', $action['product_id'])->update([
                        'is_active' => false,
                        'updated_at' => now(),
                    ]);

                    continue;
                }

                DB::table('products')->where('id', $action['product_id'])->update([
                    'primary_category_id' => $action['target_id'],
                    'updated_at' => now(),
                ]);

                DB::table('product_category_product')
                    ->where('product_id', $action['product_id'])
                    ->whereNotIn('product_category_id', DB::table('product_categories')->select('id'))
                    ->delete();

                $this->syncPivot($action['product_id'], $action['target_id']);
            }
        });

        $this->info("Backup written to {$backupPath}");
        $this->info('Orphaned products repaired without deleting products.');

        return self::SUCCESS;
    }

    private function loadCategoryIds(): void
    {
        $this->categoryIds = DB::table('product_categories')->pluck('id', 'slug')->all();
    }

    private function buildAction(object $product, int $defaultCategoryId): array
    {
        $targetSlug = $this->classify((string) $product->name);

        if ($targetSlug === null && $this->option('hide-unmatched')) {
            return [
                'product_id' => (int) $product->id,
                'old_category_id' => (int) $product->primary_category_id,
                'target_id' => null,
                'target_slug' => null,
            ];
        }

        $targetSlug ??= (string) $this->option('default-category');

        return [
            'product_id' => (int) $product->id,
            'old_category_id' => (int) $product->primary_category_id,
            'target_id' => $this->categoryIds[$targetSlug] ?? $defaultCategoryId,
            'target_slug' => $targetSlug,
        ];
    }

    private function classify(string $name): ?string
    {
        $text = Str::lower($name);

        return match (true) {
            Str::contains($text, ['iphone', 'ipad']) => 'iphone',
            Str::contains($text, ['samsung galaxy', 'galaxy tab', 'galaxy watch', 'galaxy buds']) => 'samsung-galaxy',
            Str::contains($text, ['oppo', 'xiaomi', 'redmi', 'realme', 'vivo', 'huawei', 'android']) => 'android-khac',
            Str::contains($text, ['laptop gaming', 'nitro', 'tuf gaming', 'rog', 'legion', 'predator', 'victus', 'msi gaming']) => 'laptop-gaming',
            Str::contains($text, ['laptop', 'macbook', 'thinkpad', 'inspiron', 'vivobook', 'zenbook', 'surface']) => 'laptop-van-phong',
            Str::contains($text, ['sạc', 'sac', 'cáp', 'cap', 'charger', 'adapter', 'pin dự phòng', 'power bank']) => 'sac-cap',
            Str::contains($text, ['ốp', 'op lung', 'kính cường lực', 'kinh cuong luc', 'case điện thoại']) => 'op-lung-kinh-cuong-luc',
            Str::contains($text, ['cpu', 'mainboard', 'ram', 'ssd', 'hdd', 'vga', 'card ', 'nguồn', 'nguon', 'tản nhiệt', 'tan nhiet', 'linh kiện']) => 'linh-kien',
            default => null,
        };
    }

    private function syncPivot(int $productId, int $categoryId): void
    {
        $ids = collect([$categoryId]);
        $parentId = DB::table('product_categories')->where('id', $categoryId)->value('parent_id');

        if ($parentId) {
            $ids->push((int) $parentId);
        }

        foreach ($ids->unique() as $id) {
            DB::table('product_category_product')->updateOrInsert([
                'product_id' => $productId,
                'product_category_id' => $id,
            ], [
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    private function backup(array $productIds): string
    {
        $dir = storage_path('app/backups');
        File::ensureDirectoryExists($dir);

        $payload = [
            'created_at' => now()->toDateTimeString(),
            'products' => DB::table('products')->whereIn('id', $productIds)->get(),
            'product_category_product' => DB::table('product_category_product')->whereIn('product_id', $productIds)->get(),
        ];

        $path = $dir.'/orphaned-products-'.now()->format('Ymd-His').'.json';
        File::put($path, json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        return $path;
    }
}
