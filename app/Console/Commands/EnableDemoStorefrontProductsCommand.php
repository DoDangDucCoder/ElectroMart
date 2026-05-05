<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class EnableDemoStorefrontProductsCommand extends Command
{
    protected $signature = 'demo:enable-storefront-products
        {--dry-run : Report planned changes without updating products}
        {--stock=100 : Stock value to set for eligible products}
        {--activate : Set is_active = 1 for eligible products}
        {--only-with-images : Only process products whose thumbnail file exists under public/}';

    protected $description = 'Enable local demo storefront products with valid local images and stock for safe local testing.';

    public function handle(): int
    {
        if (! Schema::hasTable('products')) {
            $this->error('products table does not exist.');

            return self::FAILURE;
        }

        $columns = collect(Schema::getColumnListing('products'))->flip();
        $dryRun = (bool) $this->option('dry-run');
        $activate = (bool) $this->option('activate');
        $onlyWithImages = (bool) $this->option('only-with-images');
        $stockValue = max(0, (int) $this->option('stock'));
        $now = Carbon::now();

        $stats = [
            'dry_run' => $dryRun,
            'stock_value' => $stockValue,
            'only_with_images' => $onlyWithImages,
            'activate' => $activate,
            'products_considered' => 0,
            'eligible_products' => 0,
            'products_activated' => 0,
            'products_stock_updated' => 0,
            'products_sale_price_cleared' => 0,
            'products_skipped_missing_image' => 0,
            'products_skipped_invalid_price' => 0,
            'products_skipped_invalid_thumbnail_path' => 0,
            'active_products_with_invalid_image' => 0,
            'stock_column_used' => $columns->has('stock_quantity') ? 'stock_quantity' : null,
            'image_status_changed' => false,
        ];

        $products = DB::table('products')
            ->select([
                'id',
                'name',
                'slug',
                'price',
                'sale_price',
                'stock_quantity',
                'is_active',
                'thumbnail_path',
            ])
            ->orderBy('id')
            ->get();

        foreach ($products as $product) {
            $stats['products_considered']++;

            $thumbnailPath = trim((string) ($product->thumbnail_path ?? ''));
            $hasThumbnail = $thumbnailPath !== '';
            $hasValidImage = $hasThumbnail && $this->isSafeLocalPublicImage($thumbnailPath);
            $hasValidPrice = (float) ($product->price ?? 0) > 0;

            if ((int) ($product->is_active ?? 0) === 1 && (! $hasValidImage)) {
                $stats['active_products_with_invalid_image']++;
            }

            if (! $hasThumbnail) {
                $stats['products_skipped_missing_image']++;
                continue;
            }

            if ($onlyWithImages && ! $hasValidImage) {
                $stats['products_skipped_invalid_thumbnail_path']++;
                continue;
            }

            if (! $hasValidPrice) {
                $stats['products_skipped_invalid_price']++;
                continue;
            }

            $stats['eligible_products']++;

            $updates = [];

            if ($activate && $columns->has('is_active') && (int) ($product->is_active ?? 0) !== 1) {
                $updates['is_active'] = 1;
                $stats['products_activated']++;
            }

            if ($columns->has('stock_quantity') && (int) ($product->stock_quantity ?? 0) !== $stockValue) {
                $updates['stock_quantity'] = $stockValue;
                $stats['products_stock_updated']++;
            }

            if (
                $columns->has('sale_price')
                && $product->sale_price !== null
                && (float) $product->sale_price > 0
                && (float) $product->sale_price >= (float) $product->price
            ) {
                $updates['sale_price'] = null;
                $stats['products_sale_price_cleared']++;
            }

            if ($updates !== [] && ! $dryRun) {
                if ($columns->has('updated_at')) {
                    $updates['updated_at'] = $now;
                }

                DB::table('products')
                    ->where('id', $product->id)
                    ->update($updates);
            }
        }

        $this->line(json_encode($stats, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

        return self::SUCCESS;
    }

    private function isSafeLocalPublicImage(string $path): bool
    {
        $path = str_replace('\\', '/', trim($path));

        if ($path === '' || str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return false;
        }

        $path = ltrim($path, '/');

        if (str_starts_with($path, 'public/')) {
            $path = substr($path, 7);
        }

        if (str_contains($path, '../')) {
            return false;
        }

        $absolutePath = public_path($path);
        $publicRoot = realpath(public_path());
        $resolvedPath = realpath($absolutePath);

        if ($publicRoot === false || $resolvedPath === false || ! str_starts_with($resolvedPath, $publicRoot)) {
            return false;
        }

        if (! is_file($resolvedPath) || filesize($resolvedPath) <= 0) {
            return false;
        }

        return in_array(strtolower(pathinfo($resolvedPath, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg'], true);
    }
}
