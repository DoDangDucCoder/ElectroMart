<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AuditLegacyHoldoutDataCommand extends Command
{
    protected $signature = 'legacy:audit-holdouts
        {--show-samples : Show sample unmapped legacy rows}
        {--show-proposed : Show proposed target rows for mappable legacy rows}
        {--limit=50 : Maximum sample/proposed rows to show per holdout table}
        {--fail-on-issues : Return a non-zero exit code when unmapped holdout rows are found}';

    protected $description = 'Audit legacy holdout data mapping readiness without mutating records';

    public function handle(): int
    {
        $rows = [
            $this->commentReadiness(),
            $this->ratingReadiness(),
            $this->statisticalReadiness(),
        ];

        $this->table(
            ['Legacy Table', 'Legacy Rows', 'Modern Target', 'Target Rows', 'Ready Rows', 'Blocked Rows', 'Primary Blocker'],
            $rows
        );

        if ($this->option('show-samples')) {
            $this->showSamples();
        }

        if ($this->option('show-proposed')) {
            $this->showProposedMappings();
        }

        $blocked = collect($rows)->sum(fn (array $row) => (int) $row['blocked_rows']);

        if ($blocked > 0) {
            $this->warn("Legacy holdout audit found {$blocked} blocked row(s).");

            return $this->option('fail-on-issues') ? self::FAILURE : self::SUCCESS;
        }

        $this->info('Legacy holdout audit passed.');

        return self::SUCCESS;
    }

    private function commentReadiness(): array
    {
        if (! $this->hasTables(['tbl_comment', 'product_reviews'])) {
            return $this->missingTableRow('tbl_comment', 'product_reviews');
        }

        $legacyRows = DB::table('tbl_comment')->count();
        $targetRows = DB::table('product_reviews')->count();
        $readyRows = $this->countRowsWithMatchingProduct('tbl_comment', 'comment_product_id');

        return [
            'legacy_table' => 'tbl_comment',
            'legacy_rows' => $legacyRows,
            'modern_target' => 'product_reviews',
            'target_rows' => $targetRows,
            'ready_rows' => $readyRows,
            'blocked_rows' => max(0, $legacyRows - $readyRows),
            'primary_blocker' => $legacyRows === $readyRows ? 'none' : 'missing matching products.id',
        ];
    }

    private function ratingReadiness(): array
    {
        if (! $this->hasTables(['tbl_rating', 'product_reviews'])) {
            return $this->missingTableRow('tbl_rating', 'product_reviews');
        }

        $legacyRows = DB::table('tbl_rating')->count();
        $targetRows = DB::table('product_reviews')->count();
        $readyRows = $this->countRowsWithMatchingProduct('tbl_rating', 'product_id');

        return [
            'legacy_table' => 'tbl_rating',
            'legacy_rows' => $legacyRows,
            'modern_target' => 'product_reviews',
            'target_rows' => $targetRows,
            'ready_rows' => $readyRows,
            'blocked_rows' => max(0, $legacyRows - $readyRows),
            'primary_blocker' => $legacyRows === $readyRows ? 'none' : 'missing matching products.id',
        ];
    }

    private function statisticalReadiness(): array
    {
        if (! $this->hasTables(['tbl_statistical', 'analytics_daily'])) {
            return $this->missingTableRow('tbl_statistical', 'analytics_daily');
        }

        $legacyRows = DB::table('tbl_statistical')->count();
        $targetRows = DB::table('analytics_daily')->count();
        $readyRows = DB::table('tbl_statistical')
            ->whereNotNull('order_date')
            ->where('order_date', '<>', '')
            ->count();

        return [
            'legacy_table' => 'tbl_statistical',
            'legacy_rows' => $legacyRows,
            'modern_target' => 'analytics_daily',
            'target_rows' => $targetRows,
            'ready_rows' => $readyRows,
            'blocked_rows' => max(0, $legacyRows - $readyRows),
            'primary_blocker' => $legacyRows === $readyRows ? 'none' : 'missing order_date',
        ];
    }

    private function countRowsWithMatchingProduct(string $legacyTable, string $productColumn): int
    {
        if (! Schema::hasTable('products')) {
            return 0;
        }

        return DB::table($legacyTable.' as legacy')
            ->join('products as products', 'products.id', '=', 'legacy.'.$productColumn)
            ->count();
    }

    private function showSamples(): void
    {
        $samples = [];

        if ($this->hasTables(['tbl_comment', 'products'])) {
            $samples = array_merge($samples, $this->missingProductSamples('tbl_comment', 'comment_id', 'comment_product_id'));
        }

        if ($this->hasTables(['tbl_rating', 'products'])) {
            $samples = array_merge($samples, $this->missingProductSamples('tbl_rating', 'rating_id', 'product_id'));
        }

        if (Schema::hasTable('tbl_statistical')) {
            $statisticalSamples = DB::table('tbl_statistical')
                ->where(fn ($query) => $query->whereNull('order_date')->orWhere('order_date', ''))
                ->limit($this->sampleLimit())
                ->get()
                ->map(fn (object $row) => [
                    'legacy_table' => 'tbl_statistical',
                    'legacy_id' => $row->id_statistical ?? 'unknown',
                    'legacy_product_id' => '',
                    'blocker' => 'missing order_date',
                ])
                ->all();

            $samples = array_merge($samples, $statisticalSamples);
        }

        if ($samples === []) {
            $this->line('No blocked sample rows found.');

            return;
        }

        $this->table(['Legacy Table', 'Legacy ID', 'Legacy Product ID', 'Legacy Product', 'Modern Product', 'Blocker'], $samples);
    }

    private function missingProductSamples(string $legacyTable, string $idColumn, string $productColumn): array
    {
        return DB::table($legacyTable.' as legacy')
            ->leftJoin('tbl_product as legacy_product', 'legacy_product.product_id', '=', 'legacy.'.$productColumn)
            ->leftJoin('products as products', 'products.id', '=', 'legacy.'.$productColumn)
            ->whereNull('products.id')
            ->select([
                'legacy.'.$idColumn.' as legacy_id',
                'legacy.'.$productColumn.' as legacy_product_id',
                'legacy_product.product_id as existing_legacy_product_id',
                'products.id as existing_modern_product_id',
            ])
            ->limit($this->sampleLimit())
            ->get()
            ->map(fn (object $row) => [
                'legacy_table' => $legacyTable,
                'legacy_id' => $row->legacy_id,
                'legacy_product_id' => $row->legacy_product_id,
                'legacy_product' => $row->existing_legacy_product_id ? 'exists' : 'missing',
                'modern_product' => $row->existing_modern_product_id ? 'exists' : 'missing',
                'blocker' => $row->existing_legacy_product_id
                    ? 'missing matching products.id'
                    : 'orphan legacy product_id',
            ])
            ->all();
    }

    private function showProposedMappings(): void
    {
        $this->line('Proposed tbl_comment -> product_reviews rows:');
        $this->table(
            ['Legacy ID', 'Product ID', 'Author', 'Rating', 'Status', 'Parent ID', 'Content Preview'],
            $this->proposedCommentRows()
        );

        $this->line('Proposed tbl_rating -> product_reviews rows:');
        $this->table(
            ['Legacy ID', 'Product ID', 'Rating', 'Status', 'Content Preview'],
            $this->proposedRatingRows()
        );

        $this->line('Proposed tbl_statistical -> analytics_daily rows:');
        $this->table(
            ['Legacy ID', 'Date', 'Orders', 'Sales', 'Profit', 'Items Sold'],
            $this->proposedStatisticalRows()
        );
    }

    private function proposedCommentRows(): array
    {
        if (! $this->hasTables(['tbl_comment', 'products'])) {
            return [];
        }

        return DB::table('tbl_comment as legacy')
            ->join('products as products', 'products.id', '=', 'legacy.comment_product_id')
            ->orderBy('legacy.comment_id')
            ->limit($this->sampleLimit())
            ->get()
            ->map(fn (object $row) => [
                'legacy_id' => $row->comment_id,
                'product_id' => $row->comment_product_id,
                'author' => $row->comment_name,
                'rating' => 5,
                'status' => ((int) $row->comment_status) === 0 ? 'approved' : 'pending',
                'parent_id' => ((int) $row->comment_parent_comment) > 0 ? $row->comment_parent_comment : '',
                'content_preview' => mb_substr((string) $row->comment, 0, 80),
            ])
            ->all();
    }

    private function proposedRatingRows(): array
    {
        if (! $this->hasTables(['tbl_rating', 'products'])) {
            return [];
        }

        return DB::table('tbl_rating as legacy')
            ->join('products as products', 'products.id', '=', 'legacy.product_id')
            ->orderBy('legacy.rating_id')
            ->limit($this->sampleLimit())
            ->get()
            ->map(fn (object $row) => [
                'legacy_id' => $row->rating_id,
                'product_id' => $row->product_id,
                'rating' => $row->rating,
                'status' => 'approved',
                'content_preview' => 'Legacy rating import',
            ])
            ->all();
    }

    private function proposedStatisticalRows(): array
    {
        if (! Schema::hasTable('tbl_statistical')) {
            return [];
        }

        return DB::table('tbl_statistical')
            ->whereNotNull('order_date')
            ->where('order_date', '<>', '')
            ->orderBy('order_date')
            ->limit($this->sampleLimit())
            ->get()
            ->map(fn (object $row) => [
                'legacy_id' => $row->id_statistical,
                'date' => $row->order_date,
                'orders' => $row->total_order,
                'sales' => $row->sales,
                'profit' => $row->profit,
                'items_sold' => $row->quantity,
            ])
            ->all();
    }

    private function sampleLimit(): int
    {
        return max(1, (int) $this->option('limit'));
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

    private function missingTableRow(string $legacyTable, string $modernTarget): array
    {
        return [
            'legacy_table' => $legacyTable,
            'legacy_rows' => Schema::hasTable($legacyTable) ? DB::table($legacyTable)->count() : 'missing',
            'modern_target' => $modernTarget,
            'target_rows' => Schema::hasTable($modernTarget) ? DB::table($modernTarget)->count() : 'missing',
            'ready_rows' => 0,
            'blocked_rows' => 0,
            'primary_blocker' => 'missing table',
        ];
    }
}
