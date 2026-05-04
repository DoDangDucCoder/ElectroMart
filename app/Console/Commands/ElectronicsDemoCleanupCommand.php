<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ElectronicsDemoCleanupCommand extends Command
{
    protected $signature = 'electronics-demo:cleanup
        {--confirm : Actually delete electronics demo records. Without this option the command only reports what would be deleted}
        {--include-sessions : Also clear database sessions. This logs out active users}
        {--include-analytics=1 : Include demo analytics/search/view rows. Use 0 to skip analytics cleanup}';

    protected $description = 'Safely inspect and clean ElectronicsDemoDataSeeder records, with dry-run enabled by default';

    private const DEMO_BRAND_NAMES = [
        'NovaTech',
        'VoltEdge',
        'PixelCore',
        'AeroByte',
        'Zenova',
        'HyperLink',
        'MaxGear',
        'SonicWave',
        'BrightView',
        'CoreMate',
        'TerraChip',
        'LumaDesk',
    ];

    private const DEMO_CUSTOMER_EMAILS = [
        'demo.customer01@example.com',
        'demo.customer02@example.com',
        'demo.customer03@example.com',
        'demo.customer04@example.com',
        'demo.customer05@example.com',
        'demo.customer06@example.com',
        'demo.customer07@example.com',
        'demo.customer08@example.com',
    ];

    private const DEMO_COUPON_CODES = [
        'DEMO10',
        'FREESHIP',
        'ELECTRO50K',
        'LAPTOP200K',
        'AUDIO15',
    ];

    private const DEMO_ORDER_CODES = [
        'DEMO-ORDER-0001',
        'DEMO-ORDER-0002',
        'DEMO-ORDER-0003',
        'DEMO-ORDER-0004',
        'DEMO-ORDER-0005',
        'DEMO-ORDER-0006',
        'DEMO-ORDER-0007',
        'DEMO-ORDER-0008',
        'DKDXJRVUZE',
    ];

    public function handle(): int
    {
        $confirmed = (bool) $this->option('confirm');
        $includeAnalytics = $this->includeAnalytics();
        $includeSessions = (bool) $this->option('include-sessions');

        $this->line($confirmed ? '<fg=red>CONFIRM MODE: electronics demo records will be deleted.</>' : '<fg=yellow>DRY-RUN MODE: no records will be deleted.</>');
        $this->line('Analytics cleanup: '.($includeAnalytics ? 'included' : 'skipped'));
        $this->line('Session cleanup: '.($includeSessions ? 'included' : 'skipped'));
        $this->warn('This command keeps base seed data: original products, original customers, original coupons, admin users, roles, and permissions.');

        $plan = $this->buildPlan($includeAnalytics, $includeSessions);
        $this->reportPlan($plan);
        $this->reportSkipped($plan);
        $this->reportRisks($includeSessions);

        if (! $confirmed) {
            $this->newLine();
            $this->info('Dry-run complete. Re-run with --confirm to delete the listed records.');

            return self::SUCCESS;
        }

        DB::transaction(function () use ($plan): void {
            $this->deletePlannedRecords($plan);
        });

        $this->newLine();
        $this->info('Electronics demo cleanup completed.');
        $this->reportPlan($this->buildPlan($this->includeAnalytics(), (bool) $this->option('include-sessions')), 'Remaining matching records');

        return self::SUCCESS;
    }

    private function includeAnalytics(): bool
    {
        $value = strtolower((string) $this->option('include-analytics'));

        return ! in_array($value, ['0', 'false', 'no', 'off'], true);
    }

    /**
     * @return array<string, array{table: string, ids: array<int|string>, reason: string, skipped?: array<string>}>
     */
    private function buildPlan(bool $includeAnalytics, bool $includeSessions): array
    {
        $demoProductIds = $this->demoProductIds();
        $demoCustomerIds = $this->demoCustomerIds();
        $demoOrderIds = $this->demoOrderIds($demoCustomerIds);
        $demoCouponIds = $this->demoCouponIds();
        $demoBlogPostIds = $this->demoBlogPostIds();
        $demoBlogCategoryIds = $this->demoBlogCategoryIds();
        $demoCategoryIds = $this->demoCategoryIds();
        $demoBrandIds = $this->demoBrandIds();
        $safeProductIds = $this->safeProductIds($demoProductIds, $demoOrderIds);
        $safeCustomerIds = $this->safeCustomerIds($demoCustomerIds, $demoOrderIds);
        $safeCategoryIds = $this->safeCategoryIds($demoCategoryIds, $safeProductIds);
        $safeBrandIds = $this->safeBrandIds($demoBrandIds, $safeProductIds);
        $safeBlogCategoryIds = $this->safeBlogCategoryIds($demoBlogCategoryIds, $demoBlogPostIds);

        $plan = [];

        if ($includeAnalytics) {
            $plan['product_search_queries'] = [
                'table' => 'product_search_queries',
                'ids' => $this->productSearchQueryIds(),
                'reason' => 'Local/demo product search rows tied to demo terms or DEMO product code.',
            ];
            $plan['product_recommendation_impressions'] = [
                'table' => 'product_recommendation_impressions',
                'ids' => $this->childIds('product_recommendation_impressions', 'product_id', $demoProductIds),
                'reason' => 'Recommendation impressions for demo products.',
            ];
            $plan['product_views'] = [
                'table' => 'product_views',
                'ids' => $this->childIds('product_views', 'product_id', $demoProductIds),
                'reason' => 'Product view rows for demo products.',
            ];
        }

        $plan['cart_items'] = [
            'table' => 'cart_items',
            'ids' => $this->cartItemIds($safeProductIds, $safeCustomerIds),
            'reason' => 'Cart items for demo customers or demo products.',
        ];
        $plan['carts'] = [
            'table' => 'carts',
            'ids' => $this->cartIds($safeCustomerIds),
            'reason' => 'Carts owned by demo customers, plus empty carts.',
        ];
        $plan['coupon_redemptions'] = [
            'table' => 'coupon_redemptions',
            'ids' => $this->couponRedemptionIds($demoOrderIds, $demoCouponIds, $safeCustomerIds),
            'reason' => 'Coupon redemptions tied to demo coupons, demo orders, or demo customers.',
        ];
        $plan['order_returns'] = [
            'table' => 'order_returns',
            'ids' => $this->childIds('order_returns', 'order_id', $demoOrderIds),
            'reason' => 'Returns tied to demo orders.',
        ];
        $plan['vat_invoices'] = [
            'table' => 'vat_invoices',
            'ids' => $this->childIds('vat_invoices', 'order_id', $demoOrderIds),
            'reason' => 'VAT invoices tied to demo orders.',
        ];
        $plan['payments'] = [
            'table' => 'payments',
            'ids' => $this->childIds('payments', 'order_id', $demoOrderIds),
            'reason' => 'Payments tied to demo orders.',
        ];
        $plan['order_shipping'] = [
            'table' => 'order_shipping',
            'ids' => $this->childIds('order_shipping', 'order_id', $demoOrderIds),
            'reason' => 'Shipping rows tied to demo orders.',
        ];
        $plan['order_items'] = [
            'table' => 'order_items',
            'ids' => $this->childIds('order_items', 'order_id', $demoOrderIds),
            'reason' => 'Items tied to demo orders.',
        ];
        $plan['orders'] = [
            'table' => 'orders',
            'ids' => $demoOrderIds,
            'reason' => 'Electronics demo orders and the post-demo COD smoke order.',
        ];
        $plan['customer_addresses'] = [
            'table' => 'customer_addresses',
            'ids' => $this->customerAddressIds($safeCustomerIds),
            'reason' => 'Addresses owned by demo customers.',
        ];
        $plan['product_reviews'] = [
            'table' => 'product_reviews',
            'ids' => $this->productReviewIds($safeProductIds, $safeCustomerIds),
            'reason' => 'Reviews tied to demo products or demo customers.',
        ];
        $plan['customers'] = [
            'table' => 'customers',
            'ids' => $safeCustomerIds,
            'reason' => 'Demo customers by example.com demo email.',
            'skipped' => $this->skippedIds('customers', $demoCustomerIds, $safeCustomerIds),
        ];
        $plan['product_variants'] = [
            'table' => 'product_variants',
            'ids' => $this->childIds('product_variants', 'product_id', $safeProductIds),
            'reason' => 'Variants belonging to demo products.',
        ];
        $plan['product_category_product'] = [
            'table' => 'product_category_product',
            'ids' => $this->productCategoryPivotIds($safeProductIds, $safeCategoryIds),
            'reason' => 'Product/category pivots for demo products or demo categories.',
        ];
        $plan['products'] = [
            'table' => 'products',
            'ids' => $safeProductIds,
            'reason' => 'Demo products by DEMO SKU/source marker.',
            'skipped' => $this->skippedIds('products', $demoProductIds, $safeProductIds),
        ];
        $plan['coupons'] = [
            'table' => 'coupons',
            'ids' => $demoCouponIds,
            'reason' => 'Demo coupon codes from ElectronicsDemoDataSeeder.',
        ];
        $plan['blog_posts'] = [
            'table' => 'blog_posts',
            'ids' => $demoBlogPostIds,
            'reason' => 'Demo blog posts by demo slug/source marker.',
        ];
        $plan['blog_categories'] = [
            'table' => 'blog_categories',
            'ids' => $safeBlogCategoryIds,
            'reason' => 'Demo blog categories without non-demo posts.',
            'skipped' => $this->skippedIds('blog_categories', $demoBlogCategoryIds, $safeBlogCategoryIds),
        ];
        $plan['product_categories'] = [
            'table' => 'product_categories',
            'ids' => $safeCategoryIds,
            'reason' => 'Demo product categories without non-demo product references.',
            'skipped' => $this->skippedIds('product_categories', $demoCategoryIds, $safeCategoryIds),
        ];
        $plan['brands'] = [
            'table' => 'brands',
            'ids' => $safeBrandIds,
            'reason' => 'Demo brands without non-demo product references.',
            'skipped' => $this->skippedIds('brands', $demoBrandIds, $safeBrandIds),
        ];

        if ($includeSessions) {
            $plan['sessions'] = [
                'table' => 'sessions',
                'ids' => $this->tableIds('sessions', fn () => DB::table('sessions')->pluck('id')),
                'reason' => 'Optional session cleanup. This logs out active users.',
            ];
        }

        return $plan;
    }

    /**
     * @return array<int|string>
     */
    private function demoProductIds(): array
    {
        if (! Schema::hasTable('products')) {
            return [];
        }

        return DB::table('products')
            ->where(function ($query): void {
                $query->where('sku', 'like', 'DEMO-%')
                    ->orWhere('slug', 'like', 'demo-%')
                    ->orWhere('meta', 'like', '%electronics_demo%');
            })
            ->pluck('id')
            ->unique()
            ->values()
            ->all();
    }

    /**
     * @return array<int|string>
     */
    private function demoCustomerIds(): array
    {
        if (! Schema::hasTable('customers')) {
            return [];
        }

        return DB::table('customers')
            ->whereIn('email', self::DEMO_CUSTOMER_EMAILS)
            ->pluck('id')
            ->all();
    }

    /**
     * @param array<int|string> $demoCustomerIds
     * @return array<int|string>
     */
    private function demoOrderIds(array $demoCustomerIds): array
    {
        if (! Schema::hasTable('orders')) {
            return [];
        }

        return DB::table('orders')
            ->where(function ($query) use ($demoCustomerIds): void {
                $query->whereIn('code', self::DEMO_ORDER_CODES)
                    ->orWhere('code', 'like', 'DEMO-ORDER-%')
                    ->orWhere('meta', 'like', '%electronics_demo%');

                if ($demoCustomerIds !== []) {
                    $query->orWhereIn('customer_id', $demoCustomerIds);
                }
            })
            ->pluck('id')
            ->unique()
            ->values()
            ->all();
    }

    /**
     * @return array<int|string>
     */
    private function demoCouponIds(): array
    {
        if (! Schema::hasTable('coupons')) {
            return [];
        }

        return DB::table('coupons')
            ->where(function ($query): void {
                $query->whereIn('code', self::DEMO_COUPON_CODES)
                    ->orWhere('meta', 'like', '%electronics_demo%');
            })
            ->pluck('id')
            ->all();
    }

    /**
     * @return array<int|string>
     */
    private function demoBlogPostIds(): array
    {
        if (! Schema::hasTable('blog_posts')) {
            return [];
        }

        return DB::table('blog_posts')
            ->where(function ($query): void {
                $query->where('slug', 'like', 'demo-%')
                    ->orWhere('meta', 'like', '%electronics_demo%');
            })
            ->pluck('id')
            ->all();
    }

    /**
     * @return array<int|string>
     */
    private function demoBlogCategoryIds(): array
    {
        if (! Schema::hasTable('blog_categories')) {
            return [];
        }

        return DB::table('blog_categories')
            ->where('slug', 'like', 'demo-%')
            ->pluck('id')
            ->all();
    }

    /**
     * @return array<int|string>
     */
    private function demoCategoryIds(): array
    {
        if (! Schema::hasTable('product_categories')) {
            return [];
        }

        return DB::table('product_categories')
            ->where(function ($query): void {
                $query->where('code', 'like', 'DEMO_%')
                    ->orWhere('slug', 'like', 'demo-%')
                    ->orWhere('meta', 'like', '%electronics_demo%');
            })
            ->pluck('id')
            ->unique()
            ->values()
            ->all();
    }

    /**
     * @return array<int|string>
     */
    private function demoBrandIds(): array
    {
        if (! Schema::hasTable('brands')) {
            return [];
        }

        return DB::table('brands')
            ->where(function ($query): void {
                $query->whereIn('name', self::DEMO_BRAND_NAMES)
                    ->orWhere('meta', 'like', '%electronics_demo%');
            })
            ->pluck('id')
            ->unique()
            ->values()
            ->all();
    }

    /**
     * @param array<int|string> $demoProductIds
     * @param array<int|string> $demoOrderIds
     * @return array<int|string>
     */
    private function safeProductIds(array $demoProductIds, array $demoOrderIds): array
    {
        if ($demoProductIds === [] || ! Schema::hasTable('order_items')) {
            return $demoProductIds;
        }

        return collect($demoProductIds)
            ->reject(function ($productId) use ($demoOrderIds): bool {
                return DB::table('order_items')
                    ->where('product_id', $productId)
                    ->when($demoOrderIds !== [], fn ($query) => $query->whereNotIn('order_id', $demoOrderIds))
                    ->exists();
            })
            ->values()
            ->all();
    }

    /**
     * @param array<int|string> $demoCustomerIds
     * @param array<int|string> $demoOrderIds
     * @return array<int|string>
     */
    private function safeCustomerIds(array $demoCustomerIds, array $demoOrderIds): array
    {
        if ($demoCustomerIds === [] || ! Schema::hasTable('orders')) {
            return $demoCustomerIds;
        }

        return collect($demoCustomerIds)
            ->reject(function ($customerId) use ($demoOrderIds): bool {
                return DB::table('orders')
                    ->where('customer_id', $customerId)
                    ->when($demoOrderIds !== [], fn ($query) => $query->whereNotIn('id', $demoOrderIds))
                    ->exists();
            })
            ->values()
            ->all();
    }

    /**
     * @param array<int|string> $demoCategoryIds
     * @param array<int|string> $safeProductIds
     * @return array<int|string>
     */
    private function safeCategoryIds(array $demoCategoryIds, array $safeProductIds): array
    {
        if ($demoCategoryIds === []) {
            return [];
        }

        return collect($demoCategoryIds)
            ->reject(function ($categoryId) use ($demoCategoryIds, $safeProductIds): bool {
                $hasNonDemoPrimary = Schema::hasTable('products')
                    && DB::table('products')
                        ->where('primary_category_id', $categoryId)
                        ->when($safeProductIds !== [], fn ($query) => $query->whereNotIn('id', $safeProductIds))
                        ->exists();

                $hasNonDemoPivot = Schema::hasTable('product_category_product')
                    && DB::table('product_category_product')
                        ->where('product_category_id', $categoryId)
                        ->when($safeProductIds !== [], fn ($query) => $query->whereNotIn('product_id', $safeProductIds))
                        ->exists();

                $hasNonDemoChildCategory = Schema::hasTable('product_categories')
                    && DB::table('product_categories')
                        ->where('parent_id', $categoryId)
                        ->whereNotIn('id', $demoCategoryIds)
                        ->exists();

                return $hasNonDemoPrimary || $hasNonDemoPivot || $hasNonDemoChildCategory;
            })
            ->values()
            ->all();
    }

    /**
     * @param array<int|string> $demoBrandIds
     * @param array<int|string> $safeProductIds
     * @return array<int|string>
     */
    private function safeBrandIds(array $demoBrandIds, array $safeProductIds): array
    {
        if ($demoBrandIds === [] || ! Schema::hasTable('products')) {
            return $demoBrandIds;
        }

        return collect($demoBrandIds)
            ->reject(function ($brandId) use ($safeProductIds): bool {
                return DB::table('products')
                    ->where('brand_id', $brandId)
                    ->when($safeProductIds !== [], fn ($query) => $query->whereNotIn('id', $safeProductIds))
                    ->exists();
            })
            ->values()
            ->all();
    }

    /**
     * @param array<int|string> $demoBlogCategoryIds
     * @param array<int|string> $demoBlogPostIds
     * @return array<int|string>
     */
    private function safeBlogCategoryIds(array $demoBlogCategoryIds, array $demoBlogPostIds): array
    {
        if ($demoBlogCategoryIds === [] || ! Schema::hasTable('blog_posts')) {
            return $demoBlogCategoryIds;
        }

        return collect($demoBlogCategoryIds)
            ->reject(function ($categoryId) use ($demoBlogPostIds): bool {
                return DB::table('blog_posts')
                    ->where('blog_category_id', $categoryId)
                    ->when($demoBlogPostIds !== [], fn ($query) => $query->whereNotIn('id', $demoBlogPostIds))
                    ->exists();
            })
            ->values()
            ->all();
    }

    /**
     * @return array<int|string>
     */
    private function productSearchQueryIds(): array
    {
        if (! Schema::hasTable('product_search_queries')) {
            return [];
        }

        return DB::table('product_search_queries')
            ->where(function ($query): void {
                $query->where('product_code', 'like', 'DEMO-%')
                    ->orWhere('keyword', 'like', '%DEMO-%')
                    ->orWhere('keyword', 'like', '%Laptop mỏng nhẹ Air 2026%')
                    ->orWhere(function ($nested): void {
                        $nested->where('ip_address', '127.0.0.1')
                            ->whereIn('keyword', ['Laptop', 'DEMO-ULT2-002']);
                    });
            })
            ->pluck('id')
            ->unique()
            ->values()
            ->all();
    }

    /**
     * @param array<int|string> $safeProductIds
     * @param array<int|string> $safeCustomerIds
     * @return array<int|string>
     */
    private function cartItemIds(array $safeProductIds, array $safeCustomerIds): array
    {
        if (! Schema::hasTable('cart_items')) {
            return [];
        }

        return DB::table('cart_items')
            ->when($safeProductIds !== [] || $safeCustomerIds !== [], function ($query) use ($safeProductIds, $safeCustomerIds): void {
                $query->where(function ($nested) use ($safeProductIds, $safeCustomerIds): void {
                    if ($safeProductIds !== []) {
                        $nested->orWhereIn('product_id', $safeProductIds);
                    }

                    if ($safeCustomerIds !== [] && Schema::hasTable('carts')) {
                        $cartIds = DB::table('carts')->whereIn('customer_id', $safeCustomerIds)->pluck('id')->all();
                        if ($cartIds !== []) {
                            $nested->orWhereIn('cart_id', $cartIds);
                        }
                    }
                });
            })
            ->pluck('id')
            ->all();
    }

    /**
     * @param array<int|string> $safeCustomerIds
     * @return array<int|string>
     */
    private function cartIds(array $safeCustomerIds): array
    {
        if (! Schema::hasTable('carts')) {
            return [];
        }

        return DB::table('carts')
            ->where(function ($query) use ($safeCustomerIds): void {
                if ($safeCustomerIds !== []) {
                    $query->whereIn('customer_id', $safeCustomerIds);
                }

                $query->orWhere(function ($nested): void {
                    $nested->where('items_count', 0)
                        ->where('subtotal', 0)
                        ->where('total', 0);
                });
            })
            ->pluck('id')
            ->unique()
            ->values()
            ->all();
    }

    /**
     * @param array<int|string> $demoOrderIds
     * @param array<int|string> $demoCouponIds
     * @param array<int|string> $safeCustomerIds
     * @return array<int|string>
     */
    private function couponRedemptionIds(array $demoOrderIds, array $demoCouponIds, array $safeCustomerIds): array
    {
        if (! Schema::hasTable('coupon_redemptions')) {
            return [];
        }

        return DB::table('coupon_redemptions')
            ->where(function ($query) use ($demoOrderIds, $demoCouponIds, $safeCustomerIds): void {
                if ($demoOrderIds !== []) {
                    $query->orWhereIn('order_id', $demoOrderIds);
                }

                if ($demoCouponIds !== []) {
                    $query->orWhereIn('coupon_id', $demoCouponIds);
                }

                if ($safeCustomerIds !== []) {
                    $query->orWhereIn('customer_id', $safeCustomerIds);
                }
            })
            ->pluck('id')
            ->all();
    }

    /**
     * @param array<int|string> $safeProductIds
     * @param array<int|string> $safeCustomerIds
     * @return array<int|string>
     */
    private function productReviewIds(array $safeProductIds, array $safeCustomerIds): array
    {
        if (! Schema::hasTable('product_reviews')) {
            return [];
        }

        return DB::table('product_reviews')
            ->where(function ($query) use ($safeProductIds, $safeCustomerIds): void {
                if ($safeProductIds !== []) {
                    $query->orWhereIn('product_id', $safeProductIds);
                }

                if ($safeCustomerIds !== []) {
                    $query->orWhereIn('customer_id', $safeCustomerIds);
                }
            })
            ->pluck('id')
            ->all();
    }

    /**
     * @param array<int|string> $safeProductIds
     * @param array<int|string> $safeCategoryIds
     * @return array<int|string>
     */
    private function productCategoryPivotIds(array $safeProductIds, array $safeCategoryIds): array
    {
        if (! Schema::hasTable('product_category_product')) {
            return [];
        }

        return DB::table('product_category_product')
            ->where(function ($query) use ($safeProductIds, $safeCategoryIds): void {
                if ($safeProductIds !== []) {
                    $query->orWhereIn('product_id', $safeProductIds);
                }

                if ($safeCategoryIds !== []) {
                    $query->orWhereIn('product_category_id', $safeCategoryIds);
                }
            })
            ->pluck('id')
            ->unique()
            ->values()
            ->all();
    }

    /**
     * @param array<int|string> $customerIds
     * @return array<int|string>
     */
    private function customerAddressIds(array $customerIds): array
    {
        return $this->childIds('customer_addresses', 'customer_id', $customerIds);
    }

    /**
     * @param array<int|string> $parentIds
     * @return array<int|string>
     */
    private function childIds(string $table, string $foreignKey, array $parentIds): array
    {
        if (! Schema::hasTable($table) || $parentIds === []) {
            return [];
        }

        return DB::table($table)
            ->whereIn($foreignKey, $parentIds)
            ->pluck('id')
            ->unique()
            ->values()
            ->all();
    }

    /**
     * @return array<int|string>
     */
    private function tableIds(string $table, callable $callback): array
    {
        if (! Schema::hasTable($table)) {
            return [];
        }

        return collect($callback())
            ->filter(fn ($value) => $value !== null && $value !== '')
            ->unique()
            ->values()
            ->all();
    }

    /**
     * @param array<int|string> $allIds
     * @param array<int|string> $safeIds
     * @return array<string>
     */
    private function skippedIds(string $table, array $allIds, array $safeIds): array
    {
        $skipped = array_values(array_diff($allIds, $safeIds));

        if ($skipped === []) {
            return [];
        }

        return [
            $table.' ids skipped because non-demo references still exist: '.$this->formatIds($skipped),
        ];
    }

    /**
     * @param array<string, array{table: string, ids: array<int|string>, reason: string, skipped?: array<string>}> $plan
     */
    private function reportPlan(array $plan, string $title = 'Records selected'): void
    {
        $rows = [];

        foreach ($plan as $key => $entry) {
            $rows[] = [
                'step' => $key,
                'table' => $entry['table'],
                'count' => count($entry['ids']),
                'ids' => $this->formatIds($entry['ids']),
                'reason' => $entry['reason'],
            ];
        }

        $this->newLine();
        $this->info($title);
        $this->table(['Step', 'Table', 'Count', 'IDs', 'Reason'], $rows);
    }

    /**
     * @param array<string, array{table: string, ids: array<int|string>, reason: string, skipped?: array<string>}> $plan
     */
    private function reportSkipped(array $plan): void
    {
        $messages = collect($plan)
            ->flatMap(fn (array $entry) => $entry['skipped'] ?? [])
            ->values()
            ->all();

        if ($messages === []) {
            return;
        }

        $this->newLine();
        $this->warn('Skipped records:');
        foreach ($messages as $message) {
            $this->line('- '.$message);
        }
    }

    private function reportRisks(bool $includeSessions): void
    {
        $this->newLine();
        $this->warn('Risk notes:');
        $this->line('- Product, category, brand, and customer rows are skipped when non-demo references are detected.');
        $this->line('- Non-demo blog view counters are not reset.');
        $this->line('- Earlier smoke cleanup targets are not included, except DKDXJRVUZE.');

        if ($includeSessions) {
            $this->line('- --include-sessions will log out active users.');
        }
    }

    /**
     * @param array<string, array{table: string, ids: array<int|string>, reason: string, skipped?: array<string>}> $plan
     */
    private function deletePlannedRecords(array $plan): void
    {
        foreach ($plan as $entry) {
            if ($entry['ids'] === [] || ! Schema::hasTable($entry['table'])) {
                $this->line('Skipped '.$entry['table'].' (no matching records).');
                continue;
            }

            DB::table($entry['table'])
                ->whereIn('id', $entry['ids'])
                ->delete();

            $this->line('Deleted '.count($entry['ids']).' row(s) from '.$entry['table'].'.');
        }
    }

    /**
     * @param array<int|string> $ids
     */
    private function formatIds(array $ids): string
    {
        if ($ids === []) {
            return '-';
        }

        $ids = array_map(static fn ($id) => (string) $id, $ids);

        if (count($ids) <= 20) {
            return implode(', ', $ids);
        }

        return implode(', ', array_slice($ids, 0, 20)).' ... +'.(count($ids) - 20).' more';
    }
}
