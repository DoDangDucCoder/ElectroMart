<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class NormalizeStorefrontCategoriesCommand extends Command
{
    protected $signature = 'storefront:normalize-categories {--apply : Write category and product changes}';

    protected $description = 'Create storefront filter categories and assign matching generic products safely.';

    public function handle(): int
    {
        if (! Schema::hasTable('product_categories') || ! Schema::hasTable('products')) {
            $this->error('Missing product_categories or products table.');

            return self::FAILURE;
        }

        $apply = (bool) $this->option('apply');
        $now = Carbon::now();
        $genericIds = DB::table('product_categories')
            ->whereIn('slug', ['phu-kien', 'linh-kien'])
            ->pluck('id', 'slug');

        $categories = $this->categoryDefinitions($genericIds);
        $categoryIds = [];

        DB::transaction(function () use ($apply, $now, $categories, &$categoryIds): void {
            foreach ($categories as $slug => $category) {
                $existing = DB::table('product_categories')->where('slug', $slug)->first();
                $categoryIds[$slug] = $existing?->id;

                if (! $apply) {
                    continue;
                }

                if ($existing) {
                    DB::table('product_categories')->where('id', $existing->id)->update([
                        'name' => $category['name'],
                        'parent_id' => $category['parent_id'],
                        'description' => $category['description'],
                        'is_active' => true,
                        'display_order' => $category['display_order'],
                        'updated_at' => $now,
                    ]);
                    $categoryIds[$slug] = $existing->id;
                } else {
                    $categoryIds[$slug] = DB::table('product_categories')->insertGetId([
                        'name' => $category['name'],
                        'slug' => $slug,
                        'code' => Str::upper(Str::replace('-', '_', $slug)),
                        'parent_id' => $category['parent_id'],
                        'description' => $category['description'],
                        'is_active' => true,
                        'display_order' => $category['display_order'],
                        'meta' => json_encode(['source' => 'storefront_normalization']),
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }
            }
        });

        if (! $apply) {
            foreach ($categories as $slug => $category) {
                $categoryIds[$slug] ??= DB::table('product_categories')->where('slug', $slug)->value('id') ?: -1;
            }
        }

        $candidates = $this->candidateProducts();
        $assignments = $this->buildAssignments($candidates, $categoryIds);
        $summary = $assignments->groupBy('target_slug')->map->count()->sortKeys();

        if ($apply) {
            $this->backupAssignments($assignments);
            foreach ($assignments as $assignment) {
                DB::table('products')->where('id', $assignment['product_id'])->update([
                    'primary_category_id' => $assignment['target_id'],
                    'updated_at' => $now,
                ]);

                if (Schema::hasTable('product_category_product')) {
                    DB::table('product_category_product')->updateOrInsert(
                        [
                            'product_id' => $assignment['product_id'],
                            'product_category_id' => $assignment['target_id'],
                        ],
                        [
                            'created_at' => $now,
                            'updated_at' => $now,
                        ]
                    );
                }
            }
        }

        $this->table(['category', 'products'], $summary->map(fn ($count, $slug) => [$slug, $count])->values()->all());
        $this->info($apply ? 'Applied storefront category normalization.' : 'Dry run only. Re-run with --apply to write changes.');

        return self::SUCCESS;
    }

    private function categoryDefinitions($genericIds): array
    {
        $accessoryId = $genericIds['phu-kien'] ?? null;
        $componentId = $genericIds['linh-kien'] ?? $accessoryId;

        return [
            'pc' => ['name' => 'PC', 'parent_id' => null, 'description' => 'Máy tính để bàn, PC gaming và workstation.', 'display_order' => 30],
            'man-hinh' => ['name' => 'Màn hình', 'parent_id' => $accessoryId, 'description' => 'Màn hình máy tính và thiết bị hiển thị.', 'display_order' => 31],
            'card-man-hinh' => ['name' => 'Card màn hình', 'parent_id' => $componentId, 'description' => 'VGA, GPU và card đồ họa rời.', 'display_order' => 32],
            'gaming-gear' => ['name' => 'Gaming Gear', 'parent_id' => $accessoryId, 'description' => 'Chuột, bàn phím, tai nghe và phụ kiện gaming.', 'display_order' => 33],
            'ram' => ['name' => 'RAM', 'parent_id' => $componentId, 'description' => 'Bộ nhớ RAM cho PC và laptop.', 'display_order' => 34],
            'ssd' => ['name' => 'SSD', 'parent_id' => $componentId, 'description' => 'Ổ cứng SSD và thiết bị lưu trữ tốc độ cao.', 'display_order' => 35],
            'network' => ['name' => 'Thiết bị mạng', 'parent_id' => $accessoryId, 'description' => 'Router, Wi-Fi mesh và thiết bị mạng.', 'display_order' => 36],
            'thiet-bi-van-phong' => ['name' => 'Thiết bị văn phòng', 'parent_id' => $accessoryId, 'description' => 'Máy in và thiết bị hỗ trợ văn phòng.', 'display_order' => 37],
        ];
    }

    private function candidateProducts()
    {
        $genericIds = DB::table('product_categories')
            ->whereIn('slug', ['phu-kien', 'linh-kien'])
            ->pluck('id')
            ->all();

        return DB::table('products')
            ->select('id', 'name', 'slug', 'description', 'primary_category_id')
            ->where('is_active', true)
            ->where(function ($query) use ($genericIds) {
                $query->whereNull('primary_category_id');
                if ($genericIds !== []) {
                    $query->orWhereIn('primary_category_id', $genericIds);
                }
            })
            ->orderBy('id')
            ->get();
    }

    private function buildAssignments($products, array $categoryIds)
    {
        $rules = [
            'card-man-hinh' => ['card màn hình', 'vga', 'gpu', 'geforce', 'radeon', 'rtx', 'gtx'],
            'ram' => [' ram ', 'ddr4', 'ddr5', 'memory'],
            'ssd' => [' ssd ', 'nvme'],
            'man-hinh' => ['màn hình', 'monitor', 'ultragear'],
            'network' => ['router', 'wifi', 'wi-fi', 'mesh', 'access point'],
            'thiet-bi-van-phong' => ['máy in', 'printer'],
            'gaming-gear' => ['gaming gear', 'chuột', 'mouse', 'bàn phím', 'keyboard', 'tai nghe', 'headset'],
            'pc' => ['pc gaming', 'máy bộ', 'desktop', 'workstation', 'mini pc'],
        ];

        return collect($products)->map(function ($product) use ($rules, $categoryIds) {
            $haystack = Str::lower(' '.$product->name.' '.$product->slug.' '.$product->description.' ');

            foreach ($rules as $slug => $terms) {
                foreach ($terms as $term) {
                    if (Str::contains($haystack, Str::lower($term))) {
                        $targetId = $categoryIds[$slug] ?? null;
                        if (! $targetId || (int) $targetId === (int) $product->primary_category_id) {
                            return null;
                        }

                        return [
                            'product_id' => (int) $product->id,
                            'name' => $product->name,
                            'old_category_id' => $product->primary_category_id,
                            'target_slug' => $slug,
                            'target_id' => (int) $targetId,
                        ];
                    }
                }
            }

            return null;
        })->filter()->values();
    }

    private function backupAssignments($assignments): void
    {
        if ($assignments->isEmpty()) {
            return;
        }

        $dir = storage_path('app/catalog-category-normalization');
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        file_put_contents(
            $dir.'/'.now()->format('Ymd_His').'_assignments.json',
            json_encode($assignments->all(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );
    }
}
