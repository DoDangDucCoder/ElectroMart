<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SmokeCleanupCommand extends Command
{
    protected $signature = 'smoke:cleanup
        {--confirm : Actually delete the smoke/demo records. Without this option the command only reports what would be deleted}
        {--include-sessions : Also clear database sessions. This logs out active users}
        {--include-analytics=1 : Include smoke analytics/search/view rows. Use 0 to skip analytics cleanup}';

    protected $description = 'Safely inspect and clean known smoke/demo records, with dry-run enabled by default';

    private const SMOKE_ORDER_CODES = [
        'Q35EVOB8TU',
        '2HZS1FX0MY',
        'BWLPDTAOQE',
        'LURRYASU9S',
    ];

    private const SMOKE_CONTACT_EMAIL = 'contact-smoke-20260504@example.com';

    private const SMOKE_ADDRESS_IDS = [3, 5, 6];

    private const SMOKE_PRODUCT_ID = 6;

    private const SMOKE_PRODUCT_TERMS = [
        'E2E Test Electronic Product',
        'e2e-test-electronic-product',
    ];

    public function handle(): int
    {
        $confirmed = (bool) $this->option('confirm');
        $includeAnalytics = $this->includeAnalytics();
        $includeSessions = (bool) $this->option('include-sessions');

        $this->line($confirmed ? '<fg=red>CONFIRM MODE: records will be deleted.</>' : '<fg=yellow>DRY-RUN MODE: no records will be deleted.</>');
        $this->line('Analytics cleanup: '.($includeAnalytics ? 'included' : 'skipped'));
        $this->line('Session cleanup: '.($includeSessions ? 'included' : 'skipped'));
        $this->newLine();

        $plan = $this->buildPlan($includeAnalytics, $includeSessions);
        $this->reportPlan($plan);
        $this->reportMissingExpectedRecords($plan);

        if (! $confirmed) {
            $this->newLine();
            $this->info('Dry-run complete. Re-run with --confirm to delete the listed records.');

            return self::SUCCESS;
        }

        DB::transaction(function () use ($plan): void {
            $this->deletePlannedRecords($plan);
        });

        $this->newLine();
        $this->info('Smoke/demo cleanup completed.');
        $this->reportPlan($this->buildPlan($this->includeAnalytics(), (bool) $this->option('include-sessions')), 'Remaining matching records');

        return self::SUCCESS;
    }

    private function includeAnalytics(): bool
    {
        $value = strtolower((string) $this->option('include-analytics'));

        return ! in_array($value, ['0', 'false', 'no', 'off'], true);
    }

    /**
     * @return array<string, array{table: string, ids: array<int|string>, reason: string}>
     */
    private function buildPlan(bool $includeAnalytics, bool $includeSessions): array
    {
        $orders = $this->tableIds('orders', function () {
            return DB::table('orders')
                ->whereIn('code', self::SMOKE_ORDER_CODES)
                ->pluck('id');
        });

        $orderIds = $orders;
        $customerAddressIdsFromOrders = $this->tableIds('orders', function () {
            return DB::table('orders')
                ->whereIn('code', self::SMOKE_ORDER_CODES)
                ->whereNotNull('customer_address_id')
                ->pluck('customer_address_id');
        });

        $smokeProducts = $this->smokeProductIds();
        $smokeProductIds = $smokeProducts->all();

        $plan = [];

        if ($includeAnalytics) {
            $plan['product_search_queries'] = [
                'table' => 'product_search_queries',
                'ids' => $this->productSearchQueryIds(),
                'reason' => 'Known local smoke/API search rows.',
            ];
            $plan['product_recommendation_impressions'] = [
                'table' => 'product_recommendation_impressions',
                'ids' => $this->productRecommendationImpressionIds($smokeProductIds),
                'reason' => 'Local smoke recommendation impressions and impressions for smoke product.',
            ];
            $plan['product_views'] = [
                'table' => 'product_views',
                'ids' => $this->productViewIds($smokeProductIds),
                'reason' => 'Smoke product view tracking rows.',
            ];
        }

        $plan['payments'] = [
            'table' => 'payments',
            'ids' => $this->childIds('payments', 'order_id', $orderIds),
            'reason' => 'Payment rows belonging to smoke orders.',
        ];
        $plan['order_shipping'] = [
            'table' => 'order_shipping',
            'ids' => $this->childIds('order_shipping', 'order_id', $orderIds),
            'reason' => 'Shipping rows belonging to smoke orders.',
        ];
        $plan['order_items'] = [
            'table' => 'order_items',
            'ids' => $this->childIds('order_items', 'order_id', $orderIds),
            'reason' => 'Line items belonging to smoke orders.',
        ];
        $plan['orders'] = [
            'table' => 'orders',
            'ids' => $orderIds,
            'reason' => 'Known smoke order codes.',
        ];
        $plan['customer_addresses'] = [
            'table' => 'customer_addresses',
            'ids' => $this->customerAddressIds($customerAddressIdsFromOrders),
            'reason' => 'Checkout smoke addresses by stable order relation plus known smoke address ids.',
        ];
        $plan['product_category_product'] = [
            'table' => 'product_category_product',
            'ids' => $this->pivotIds('product_category_product', 'product_id', $smokeProductIds),
            'reason' => 'Category pivot rows for smoke product.',
        ];
        $plan['products'] = [
            'table' => 'products',
            'ids' => $smokeProductIds,
            'reason' => 'Smoke product identified by id/name/slug.',
        ];
        $plan['contact_messages'] = [
            'table' => 'contact_messages',
            'ids' => $this->tableIds('contact_messages', function () {
                return DB::table('contact_messages')
                    ->where('email', self::SMOKE_CONTACT_EMAIL)
                    ->pluck('id');
            }),
            'reason' => 'Known contact form smoke email.',
        ];
        $plan['carts'] = [
            'table' => 'carts',
            'ids' => $this->emptyCartIds(),
            'reason' => 'Empty smoke cart residue only.',
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

    private function smokeProductIds(): Collection
    {
        if (! Schema::hasTable('products')) {
            return collect();
        }

        return DB::table('products')
            ->where(function ($query): void {
                $query->where('id', self::SMOKE_PRODUCT_ID);

                foreach (self::SMOKE_PRODUCT_TERMS as $term) {
                    $query->orWhere('name', 'like', '%'.$term.'%')
                        ->orWhere('slug', 'like', '%'.$term.'%');
                }
            })
            ->pluck('id')
            ->unique()
            ->values();
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
                $query->whereIn('id', [1, 2])
                    ->orWhere(function ($nested): void {
                        $nested->where('ip_address', '127.0.0.1')
                            ->whereDate('created_at', '2026-05-04')
                            ->whereIn('source', ['web', 'api']);
                    });
            })
            ->pluck('id')
            ->all();
    }

    /**
     * @param array<int|string> $smokeProductIds
     * @return array<int|string>
     */
    private function productRecommendationImpressionIds(array $smokeProductIds): array
    {
        if (! Schema::hasTable('product_recommendation_impressions')) {
            return [];
        }

        return DB::table('product_recommendation_impressions')
            ->where(function ($query) use ($smokeProductIds): void {
                if ($smokeProductIds !== []) {
                    $query->whereIn('product_id', $smokeProductIds);
                }

                $query->orWhere(function ($nested): void {
                    $nested->where('ip_address', '127.0.0.1')
                        ->whereDate('created_at', '2026-05-04')
                        ->whereIn('context', [
                            'products_page',
                            'product_detail',
                            'search_page',
                            'api_recommendations',
                            'chatbot_fallback',
                        ]);
                });
            })
            ->pluck('id')
            ->unique()
            ->values()
            ->all();
    }

    /**
     * @param array<int|string> $smokeProductIds
     * @return array<int|string>
     */
    private function productViewIds(array $smokeProductIds): array
    {
        if (! Schema::hasTable('product_views')) {
            return [];
        }

        return DB::table('product_views')
            ->where(function ($query) use ($smokeProductIds): void {
                if ($smokeProductIds !== []) {
                    $query->whereIn('product_id', $smokeProductIds);
                }

                $query->orWhere(function ($nested): void {
                    $nested->where('ip_address', '127.0.0.1')
                        ->whereDate('created_at', '2026-05-04');
                });
            })
            ->pluck('id')
            ->unique()
            ->values()
            ->all();
    }

    /**
     * @param array<int|string> $orderAddressIds
     * @return array<int|string>
     */
    private function customerAddressIds(array $orderAddressIds): array
    {
        if (! Schema::hasTable('customer_addresses')) {
            return [];
        }

        return DB::table('customer_addresses')
            ->where(function ($query) use ($orderAddressIds): void {
                $query->whereIn('id', array_unique(array_merge(self::SMOKE_ADDRESS_IDS, $orderAddressIds)))
                    ->orWhere('address_line1', 'like', '%Smoke%')
                    ->orWhere('address_line2', 'like', '%Smoke%')
                    ->orWhere('recipient_name', 'like', '%Smoke%');
            })
            ->where('is_default', false)
            ->pluck('id')
            ->unique()
            ->values()
            ->all();
    }

    /**
     * @return array<int|string>
     */
    private function emptyCartIds(): array
    {
        if (! Schema::hasTable('carts')) {
            return [];
        }

        $query = DB::table('carts')
            ->where('items_count', 0)
            ->where('subtotal', 0)
            ->where('total', 0);

        if (Schema::hasTable('cart_items')) {
            $query->whereNotExists(function ($subQuery): void {
                $subQuery->selectRaw('1')
                    ->from('cart_items')
                    ->whereColumn('cart_items.cart_id', 'carts.id');
            });
        }

        return $query->pluck('id')->all();
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
            ->all();
    }

    /**
     * @param array<int|string> $ids
     * @return array<int|string>
     */
    private function pivotIds(string $table, string $foreignKey, array $ids): array
    {
        if (! Schema::hasTable($table) || $ids === []) {
            return [];
        }

        return DB::table($table)
            ->whereIn($foreignKey, $ids)
            ->pluck('id')
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
     * @param array<string, array{table: string, ids: array<int|string>, reason: string}> $plan
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
     * @param array<string, array{table: string, ids: array<int|string>, reason: string}> $plan
     */
    private function reportMissingExpectedRecords(array $plan): void
    {
        $missingOrderCodes = [];

        if (Schema::hasTable('orders')) {
            $existingCodes = DB::table('orders')
                ->whereIn('code', self::SMOKE_ORDER_CODES)
                ->pluck('code')
                ->all();
            $missingOrderCodes = array_values(array_diff(self::SMOKE_ORDER_CODES, $existingCodes));
        }

        $missing = [];

        if ($missingOrderCodes !== []) {
            $missing[] = 'Missing smoke order codes: '.implode(', ', $missingOrderCodes);
        }

        if (($plan['products']['ids'] ?? []) === []) {
            $missing[] = 'Smoke product not found by id/name/slug.';
        }

        if (($plan['contact_messages']['ids'] ?? []) === []) {
            $missing[] = 'Smoke contact message not found by email.';
        }

        if ($missing === []) {
            return;
        }

        $this->newLine();
        $this->warn('Expected records missing or already cleaned:');
        foreach ($missing as $message) {
            $this->line('- '.$message);
        }
    }

    /**
     * @param array<string, array{table: string, ids: array<int|string>, reason: string}> $plan
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
