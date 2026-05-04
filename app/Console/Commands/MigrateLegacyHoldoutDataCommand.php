<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MigrateLegacyHoldoutDataCommand extends Command
{
    protected $signature = 'legacy:migrate-holdouts
        {--dry-run : Show what would be migrated without writing data}
        {--skip-orphans : Skip orphan legacy rows that cannot be mapped}
        {--confirm-legacy-holdout-migration : Confirm this one-time holdout migration is intentionally needed}';

    protected $description = 'Migrate reviewed legacy holdout rows into modern tables with explicit safety guards';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');

        if (! $dryRun && app()->environment('production')) {
            $this->error('This legacy holdout migration command is disabled in production.');

            return self::FAILURE;
        }

        if (! $dryRun && ! $this->option('confirm-legacy-holdout-migration')) {
            $this->warn('This command writes reviewed legacy holdout data into modern tables.');
            $this->line('Use --confirm-legacy-holdout-migration only after backup and explicit approval.');

            return self::FAILURE;
        }

        $blocked = $this->blockedRows();
        if (! $dryRun && $blocked > 0 && ! $this->option('skip-orphans')) {
            $this->error("{$blocked} orphan legacy row(s) cannot be mapped. Re-run with --skip-orphans only if skipping them is intentional.");

            return self::FAILURE;
        }

        $plan = $this->migrationPlan();
        $this->table(['Target', 'Action', 'Rows'], $plan);

        if ($dryRun) {
            $this->info('Dry-run complete. No data was written.');

            return self::SUCCESS;
        }

        DB::transaction(function (): void {
            $this->migrateComments();
            $this->migrateRatings();
            $this->migrateStatistics();
        });

        $this->info('Legacy holdout migration completed.');

        return self::SUCCESS;
    }

    private function migrationPlan(): array
    {
        return [
            [
                'target' => 'product_reviews',
                'action' => 'legacy comments ready',
                'rows' => $this->readyRowsWithMatchingProduct('tbl_comment', 'comment_product_id'),
            ],
            [
                'target' => 'product_reviews',
                'action' => 'legacy ratings ready',
                'rows' => $this->readyRowsWithMatchingProduct('tbl_rating', 'product_id'),
            ],
            [
                'target' => 'product_reviews',
                'action' => 'legacy ratings blocked',
                'rows' => $this->blockedRatingRows(),
            ],
            [
                'target' => 'analytics_daily',
                'action' => 'legacy statistics ready',
                'rows' => $this->readyStatisticalRows(),
            ],
        ];
    }

    private function migrateComments(): void
    {
        if (! $this->hasTables(['tbl_comment', 'product_reviews', 'products'])) {
            return;
        }

        $comments = DB::table('tbl_comment as legacy')
            ->join('products as products', 'products.id', '=', 'legacy.comment_product_id')
            ->orderBy('legacy.comment_id')
            ->select('legacy.*')
            ->get();

        $legacyIdToReviewId = [];

        foreach ($comments as $comment) {
            $createdAt = $this->parseDate($comment->comment_date);
            $title = 'Legacy comment '.$comment->comment_id;

            $this->updateOrInsertPreservingCreatedAt(
                'product_reviews',
                ['title' => $title],
                [
                    'product_id' => (int) $comment->comment_product_id,
                    'customer_id' => null,
                    'author_name' => $comment->comment_name,
                    'author_email' => null,
                    'rating' => 5,
                    'content' => $comment->comment,
                    'parent_id' => null,
                    'status' => ((int) $comment->comment_status) === 0 ? 'approved' : 'pending',
                    'is_public' => ((int) $comment->comment_status) === 0,
                    'approved_at' => ((int) $comment->comment_status) === 0 ? $createdAt : null,
                    'meta' => json_encode([
                        'legacy' => [
                            'table' => 'tbl_comment',
                            'id' => (int) $comment->comment_id,
                        ],
                    ], JSON_UNESCAPED_UNICODE),
                    'created_at' => $createdAt,
                    'updated_at' => now(),
                    'deleted_at' => null,
                ]
            );

            $legacyIdToReviewId[(int) $comment->comment_id] = (int) DB::table('product_reviews')
                ->where('title', $title)
                ->value('id');
        }

        foreach ($comments as $comment) {
            $parentLegacyId = (int) $comment->comment_parent_comment;
            if ($parentLegacyId <= 0 || ! isset($legacyIdToReviewId[$parentLegacyId])) {
                continue;
            }

            DB::table('product_reviews')
                ->where('title', 'Legacy comment '.$comment->comment_id)
                ->update([
                    'parent_id' => $legacyIdToReviewId[$parentLegacyId],
                    'updated_at' => now(),
                ]);
        }
    }

    private function migrateRatings(): void
    {
        if (! $this->hasTables(['tbl_rating', 'product_reviews', 'products'])) {
            return;
        }

        DB::table('tbl_rating as legacy')
            ->join('products as products', 'products.id', '=', 'legacy.product_id')
            ->orderBy('legacy.rating_id')
            ->select('legacy.*')
            ->get()
            ->each(function (object $rating): void {
                $this->updateOrInsertPreservingCreatedAt(
                    'product_reviews',
                    ['title' => 'Legacy rating '.$rating->rating_id],
                    [
                        'product_id' => (int) $rating->product_id,
                        'customer_id' => null,
                        'author_name' => 'Legacy rating',
                        'author_email' => null,
                        'rating' => max(1, min(5, (int) $rating->rating)),
                        'content' => 'Legacy rating import',
                        'parent_id' => null,
                        'status' => 'approved',
                        'is_public' => true,
                        'approved_at' => now(),
                        'meta' => json_encode([
                            'legacy' => [
                                'table' => 'tbl_rating',
                                'id' => (int) $rating->rating_id,
                            ],
                        ], JSON_UNESCAPED_UNICODE),
                        'created_at' => now(),
                        'updated_at' => now(),
                        'deleted_at' => null,
                    ]
                );
            });
    }

    private function migrateStatistics(): void
    {
        if (! $this->hasTables(['tbl_statistical', 'analytics_daily'])) {
            return;
        }

        DB::table('tbl_statistical')
            ->whereNotNull('order_date')
            ->where('order_date', '<>', '')
            ->orderBy('order_date')
            ->get()
            ->each(function (object $row): void {
                $this->updateOrInsertPreservingCreatedAt(
                    'analytics_daily',
                    ['date' => $row->order_date],
                    [
                        'orders_count' => (int) $row->total_order,
                        'sales' => (float) $row->sales,
                        'profit' => (float) $row->profit,
                        'items_sold' => (int) $row->quantity,
                        'meta' => json_encode([
                            'legacy' => [
                                'table' => 'tbl_statistical',
                                'id' => (int) $row->id_statistical,
                            ],
                        ], JSON_UNESCAPED_UNICODE),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            });
    }

    private function blockedRows(): int
    {
        return $this->blockedRatingRows();
    }

    private function blockedRatingRows(): int
    {
        if (! $this->hasTables(['tbl_rating', 'products'])) {
            return 0;
        }

        return DB::table('tbl_rating as legacy')
            ->leftJoin('products as products', 'products.id', '=', 'legacy.product_id')
            ->whereNull('products.id')
            ->count();
    }

    private function readyRowsWithMatchingProduct(string $legacyTable, string $productColumn): int
    {
        if (! $this->hasTables([$legacyTable, 'products'])) {
            return 0;
        }

        return DB::table($legacyTable.' as legacy')
            ->join('products as products', 'products.id', '=', 'legacy.'.$productColumn)
            ->count();
    }

    private function readyStatisticalRows(): int
    {
        if (! Schema::hasTable('tbl_statistical')) {
            return 0;
        }

        return DB::table('tbl_statistical')
            ->whereNotNull('order_date')
            ->where('order_date', '<>', '')
            ->count();
    }

    private function hasTables(array $tables): bool
    {
        foreach ($tables as $table) {
            if (! Schema::hasTable($table)) {
                return false;
            }
        }

        return true;
    }

    private function updateOrInsertPreservingCreatedAt(string $table, array $attributes, array $values): void
    {
        if (DB::table($table)->where($attributes)->exists()) {
            unset($values['created_at']);
        }

        DB::table($table)->updateOrInsert($attributes, $values);
    }

    private function parseDate(?string $value): Carbon
    {
        try {
            return Carbon::parse($value);
        } catch (\Throwable) {
            return now();
        }
    }
}
