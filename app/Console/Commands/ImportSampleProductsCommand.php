<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class ImportSampleProductsCommand extends Command
{
    protected $signature = 'products:import-sample-products
        {--dry-run : Validate source data only; this is the default unless --commit is supplied}
        {--commit : Write valid products, media rows, and local image files}
        {--all : Process every product candidate currently available from the approved source sitemaps}
        {--limit= : Limit valid products processed for a test run}
        {--target=100 : Target number of valid products to process when --limit is not supplied}
        {--max-pages=600 : Maximum product detail pages to inspect}
        {--source= : Optional source key: phongvu, xgear, anphatpc, cellphones}';

    protected $description = 'Import licensed sample products from approved Vietnamese electronics retailers.';

    private const USAGE_NOTE = 'Dữ liệu/ảnh dùng cho học tập và dữ liệu mẫu nội bộ; khi public hoặc kinh doanh thật cần thay bằng dữ liệu/ảnh được cấp quyền chính thức.';

    private const AUDIT_HEADERS = [
        'source_name',
        'source_url',
        'product_name',
        'brand',
        'category',
        'price',
        'original_price',
        'main_image',
        'gallery_images',
        'valid_image_count',
        'status',
        'skipped_reason',
    ];

    private const REPORTS = [
        'audit_csv' => 'docs/import/sample-products-import-audit.csv',
        'audit_json' => 'docs/import/sample-products-import-audit.json',
        'summary_json' => 'docs/import/sample-products-import-summary.json',
        'errors_json' => 'docs/import/sample-products-import-errors.json',
    ];

    private array $auditRows = [];

    private array $errors = [];

    private array $lastRequestAt = [];

    private array $skipReasons = [];

    private array $summary = [
        'dry_run' => true,
        'commit' => false,
        'all' => false,
        'target' => 100,
        'max_pages' => 600,
        'candidate_urls_queued' => 0,
        'products_checked' => 0,
        'valid_products' => 0,
        'products_imported' => 0,
        'products_updated' => 0,
        'products_duplicate' => 0,
        'products_skipped' => 0,
        'products_error' => 0,
        'products_missing_images' => 0,
        'products_image_error' => 0,
        'products_missing_required_info' => 0,
        'top_skip_reasons' => [],
        'images_validated' => 0,
        'images_saved' => 0,
        'backup_path' => null,
        'reports' => self::REPORTS,
        'sources' => [],
    ];

    public function handle(): int
    {
        if (! $this->validateSchema()) {
            return self::FAILURE;
        }

        $commit = (bool) $this->option('commit') && ! (bool) $this->option('dry-run');
        $dryRun = ! $commit;
        $all = (bool) $this->option('all');
        $target = $all ? null : $this->targetCount();
        $maxPages = $all ? null : max($target, (int) $this->option('max-pages'));
        $sources = $this->selectedSources();

        if ($sources === []) {
            $this->error('No approved source matched the --source option.');

            return self::FAILURE;
        }

        $this->summary['dry_run'] = $dryRun;
        $this->summary['commit'] = $commit;
        $this->summary['all'] = $all;
        $this->summary['target'] = $target;
        $this->summary['max_pages'] = $maxPages;
        $this->summary['sources'] = array_map(fn (array $source): string => $source['name'], $sources);

        if ($dryRun) {
            $this->info('Running in dry-run mode. No database rows or image files will be written.');
        } else {
            $this->summary['backup_path'] = $this->backupRelatedTables();
            $this->info('Backup created at: '.$this->summary['backup_path']);
        }

        $queues = $this->productUrlQueues($sources, $maxPages);
        $candidates = $this->interleaveQueues($queues);
        $this->summary['candidate_urls_queued'] = count($candidates);

        foreach ($candidates as $candidate) {
            if ($target !== null && $this->summary['valid_products'] >= $target) {
                break;
            }

            if ($maxPages !== null && $this->summary['products_checked'] >= $maxPages) {
                break;
            }

            $source = $sources[$candidate['source_key']];
            $this->summary['products_checked']++;

            try {
                $this->processCandidate($source, $candidate['url'], $dryRun);
            } catch (\Throwable $exception) {
                $this->recordError($source, $candidate['url'], $exception);
            }
        }

        $this->writeReports();
        $this->line(json_encode($this->summary, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

        return self::SUCCESS;
    }

    private function validateSchema(): bool
    {
        $required = [
            'products' => ['id', 'brand_id', 'primary_category_id', 'name', 'slug', 'sku', 'price', 'sale_price', 'sale_type', 'sale_value', 'stock_quantity', 'thumbnail_path', 'description', 'content', 'meta', 'meta_title', 'meta_description'],
            'product_media' => ['id', 'product_id', 'file_path', 'disk', 'collection', 'display_order', 'meta'],
            'product_categories' => ['id', 'name', 'slug', 'code', 'description', 'is_active', 'display_order', 'meta'],
            'brands' => ['id', 'name', 'slug', 'code', 'description', 'is_active', 'display_order', 'meta'],
            'product_category_product' => ['product_id', 'product_category_id'],
        ];

        foreach ($required as $table => $columns) {
            if (! Schema::hasTable($table)) {
                $this->error("Missing required table: {$table}");

                return false;
            }

            foreach ($columns as $column) {
                if (! Schema::hasColumn($table, $column)) {
                    $this->error("Missing required column: {$table}.{$column}");

                    return false;
                }
            }
        }

        return true;
    }

    private function targetCount(): int
    {
        if ($this->option('limit') !== null && (int) $this->option('limit') > 0) {
            return (int) $this->option('limit');
        }

        return max(1, (int) $this->option('target'));
    }

    private function selectedSources(): array
    {
        $sources = $this->sources();
        $only = trim((string) $this->option('source'));

        if ($only === '') {
            return $sources;
        }

        return array_filter($sources, fn (array $source, string $key): bool => $key === $only, ARRAY_FILTER_USE_BOTH);
    }

    private function sources(): array
    {
        return [
            'phongvu' => [
                'name' => 'Phong Vũ',
                'host' => 'phongvu.vn',
                'sitemaps' => ['https://phongvu.vn/sitemap-collection-products.xml'],
                'sitemap_url' => '~sitemap-collection-products~i',
                'crawl_delay' => 0.7,
                'product_url' => '~^https://phongvu\.vn/(?!.*(?:/search|/cart|/checkout|/login|/account)).*--s\d+~i',
            ],
            'xgear' => [
                'name' => 'XGear',
                'host' => 'xgear.net',
                'sitemaps' => ['https://xgear.net/sitemap.xml'],
                'sitemap_url' => '~sitemap_products_\d+\.xml$~i',
                'crawl_delay' => 1.0,
                'product_url' => '~^https://xgear\.net/products/[^?#]+~i',
            ],
            'anphatpc' => [
                'name' => 'An Phát PC',
                'host' => 'www.anphatpc.com.vn',
                'sitemaps' => ['https://www.anphatpc.com.vn/sitemap_product.xml'],
                'sitemap_url' => '~sitemap_product\.xml$~i',
                'crawl_delay' => 5.0,
                'product_url' => '~^https://www\.anphatpc\.com\.vn/[^?#]+\.html$~i',
            ],
            'cellphones' => [
                'name' => 'CellphoneS',
                'host' => 'cellphones.com.vn',
                'sitemaps' => ['https://cellphones.com.vn/sitemap/sitemap_index.xml?v=google'],
                'sitemap_url' => '~product-sitemap\d*\.xml~i',
                'crawl_delay' => 0.7,
                'product_url' => '~^https://cellphones\.com\.vn/[^?#]+\.html$~i',
            ],
        ];
    }

    private function productUrlQueues(array $sources, ?int $maxPages): array
    {
        $queues = [];
        $perSourceLimit = $maxPages === null ? null : max(40, (int) ceil($maxPages / max(1, count($sources))) + 40);

        foreach ($sources as $key => $source) {
            $queues[$key] = $this->productUrlsFromSitemaps($source, $perSourceLimit);
            $this->line($source['name'].': queued '.count($queues[$key]).' candidate product URLs.');
        }

        return $queues;
    }

    private function productUrlsFromSitemaps(array $source, ?int $limit): array
    {
        $queue = collect($source['sitemaps'])->map(fn (string $url): array => ['url' => $url, 'depth' => 0])->all();
        $seenSitemaps = [];
        $products = [];

        while ($queue !== [] && ($limit === null || count($products) < $limit)) {
            $current = array_shift($queue);
            $url = $this->normalizeUrl($current['url']);

            if (! $url || isset($seenSitemaps[$url])) {
                continue;
            }

            $seenSitemaps[$url] = true;
            $xml = $this->fetch($url, $source, true);
            if ($xml === null) {
                continue;
            }

            foreach ($this->locsFromSitemap($xml) as $loc) {
                $loc = $this->normalizeUrl($loc);
                if (! $loc) {
                    continue;
                }

                if ($this->isProductUrl($source, $loc)) {
                    $products[$loc] = $loc;

                    if ($limit !== null && count($products) >= $limit) {
                        break;
                    }

                    continue;
                }

                if (($current['depth'] ?? 0) < 2 && $this->isAllowedSitemapUrl($source, $loc)) {
                    $queue[] = ['url' => $loc, 'depth' => ((int) ($current['depth'] ?? 0)) + 1];
                }
            }
        }

        return array_values($products);
    }

    private function interleaveQueues(array $queues): array
    {
        $rows = [];

        while (array_filter($queues) !== []) {
            foreach ($queues as $sourceKey => $queue) {
                if ($queue === []) {
                    continue;
                }

                $url = array_shift($queues[$sourceKey]);
                $rows[] = ['source_key' => $sourceKey, 'url' => $url];
            }
        }

        return $rows;
    }

    private function processCandidate(array $source, string $url, bool $dryRun): void
    {
        $html = $this->fetch($url, $source);

        if ($html === null) {
            $this->skip($source, $url, '', '', '', null, null, '', [], 0, 'ERROR', 'source_page_fetch_failed');

            return;
        }

        $product = $this->parseProduct($source, $url, $html);

        if (! $product['ok']) {
            $this->skip(
                $source,
                $url,
                $product['name'] ?? '',
                $product['brand'] ?? '',
                $product['category'] ?? '',
                $product['price'] ?? null,
                $product['original_price'] ?? null,
                '',
                [],
                0,
                'ERROR',
                $product['reason']
            );

            return;
        }

        $duplicate = $this->findExistingProduct($product);
        if ($duplicate !== null) {
            $this->summary['products_duplicate']++;
            $this->recordSkipReason('product_already_exists');
            $this->addAuditRow($source, $url, $product, '', [], 0, 'DUPLICATE', 'product_already_exists:id='.$duplicate->id);

            return;
        }

        $validated = $this->validatedImages($source, $product);

        if (count($validated) < 5) {
            $this->summary['products_skipped']++;
            $this->summary['products_missing_images']++;
            $this->recordSkipReason('valid_image_count_less_than_5');
            $this->addAuditRow($source, $url, $product, $validated[0]['source_url'] ?? '', array_slice(array_column($validated, 'source_url'), 1), count($validated), 'SKIPPED_NOT_ENOUGH_IMAGES', 'valid_image_count_less_than_5');

            return;
        }

        $selected = array_slice($validated, 0, 5);
        $this->summary['valid_products']++;

        if ($dryRun) {
            $this->addAuditRow($source, $url, $product, $selected[0]['source_url'], array_slice(array_column($selected, 'source_url'), 1), count($selected), 'OK', '');

            return;
        }

        $result = $this->commitProduct($source, $product, $selected);
        $this->addAuditRow($source, $url, $product, $result['main_image'], $result['gallery_images'], count($selected), $result['status'], '');
    }

    private function parseProduct(array $source, string $url, string $html): array
    {
        $jsonLdProduct = $this->jsonLdNode($html, 'Product') ?? [];
        $breadcrumb = $this->breadcrumbNames($html);

        $name = $this->cleanText($jsonLdProduct['name'] ?? '')
            ?: $this->metaContent($html, 'og:title')
            ?: $this->matchFirst($html, '#<h1[^>]*>(.*?)</h1>#isu');
        $name = $this->cleanText($name);

        $price = $this->extractPrice($jsonLdProduct, $html);
        $originalPrice = $this->extractOriginalPrice($jsonLdProduct, $html, $price);
        $brand = $this->extractBrand($jsonLdProduct, $html, $name);
        $category = $this->extractCategory($source, $url, $name, $breadcrumb);
        $description = $this->extractDescription($jsonLdProduct, $html);
        $specs = $this->extractSpecifications($html, $description);
        $images = $this->extractImageCandidates($source, $url, $html, $jsonLdProduct, $name);

        if ($name === '') {
            return ['ok' => false, 'reason' => 'missing_product_name'];
        }

        if ($price === null || $price <= 0) {
            return ['ok' => false, 'reason' => 'missing_product_price', 'name' => $name, 'brand' => $brand, 'category' => $category];
        }

        if ($brand === '') {
            return ['ok' => false, 'reason' => 'missing_product_brand', 'name' => $name, 'price' => $price, 'category' => $category];
        }

        if ($category === '') {
            return ['ok' => false, 'reason' => 'missing_product_category', 'name' => $name, 'brand' => $brand, 'price' => $price];
        }

        if ($description === '') {
            return ['ok' => false, 'reason' => 'missing_product_description', 'name' => $name, 'brand' => $brand, 'category' => $category, 'price' => $price];
        }

        if ($specs === []) {
            return ['ok' => false, 'reason' => 'missing_product_specifications', 'name' => $name, 'brand' => $brand, 'category' => $category, 'price' => $price];
        }

        if (count($images) < 5) {
            return ['ok' => false, 'reason' => 'not_enough_candidate_images', 'name' => $name, 'brand' => $brand, 'category' => $category, 'price' => $price, 'original_price' => $originalPrice];
        }

        return [
            'ok' => true,
            'source_name' => $source['name'],
            'source_key' => $this->sourceKeyByName($source['name']),
            'source_url' => $url,
            'name' => $name,
            'brand' => $brand,
            'category' => $category,
            'price' => $price,
            'original_price' => $originalPrice,
            'description' => $description,
            'specifications' => $specs,
            'images' => $images,
            'slug' => $this->uniqueImportSlug($source, $name, $url),
            'sku' => $this->skuFor($source, $url),
        ];
    }

    private function validatedImages(array $source, array $product): array
    {
        $validated = [];
        $seenUrls = [];
        $seenHashes = [];

        foreach ($product['images'] as $imageUrl) {
            if (count($validated) >= 8) {
                break;
            }

            $imageUrl = $this->normalizeImageUrl($source, $imageUrl);
            if ($imageUrl === null || isset($seenUrls[$imageUrl]) || $this->looksLikeNonProductAsset($imageUrl)) {
                continue;
            }

            $seenUrls[$imageUrl] = true;

            $downloaded = $this->downloadImageToMemory($source, $imageUrl);
            if (! $downloaded['ok']) {
                $this->recordSkipReason($downloaded['reason']);

                continue;
            }

            if (isset($seenHashes[$downloaded['hash']])) {
                continue;
            }

            $seenHashes[$downloaded['hash']] = true;
            $validated[] = $downloaded + ['source_url' => $imageUrl];
            $this->summary['images_validated']++;
        }

        return $validated;
    }

    private function commitProduct(array $source, array $product, array $images): array
    {
        $savedFiles = [];

        try {
            return DB::transaction(function () use ($source, $product, $images, &$savedFiles): array {
                $brandId = $this->ensureBrand($product['brand'], $source);
                $categoryId = $this->ensureCategory($product['category'], $source);
                $now = now();
                $productId = DB::table('products')->insertGetId($this->productPayload($product, $brandId, $categoryId, null, $now));
                $slug = Str::slug($product['slug']);

                $main = $this->saveImageFile($productId, $slug, 'main', 0, $images[0]);
                $savedFiles[] = public_path($main);

                DB::table('products')->where('id', $productId)->update([
                    'thumbnail_path' => $main,
                    'updated_at' => $now,
                ]);

                DB::table('product_media')->insert([
                    'product_id' => $productId,
                    'file_path' => $main,
                    'disk' => 'public',
                    'collection' => 'thumbnail',
                    'display_order' => 0,
                    'meta' => $this->mediaMeta($source, $product, $images[0]),
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);

                $galleryPaths = [];
                foreach (array_slice($images, 1, 4) as $index => $image) {
                    $path = $this->saveImageFile($productId, $slug, 'gallery', $index + 1, $image);
                    $savedFiles[] = public_path($path);
                    $galleryPaths[] = $path;

                    DB::table('product_media')->insert([
                        'product_id' => $productId,
                        'file_path' => $path,
                        'disk' => 'public',
                        'collection' => 'gallery',
                        'display_order' => $index + 1,
                        'meta' => $this->mediaMeta($source, $product, $image),
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }

                DB::table('product_category_product')->updateOrInsert(
                    ['product_id' => $productId, 'product_category_id' => $categoryId],
                    ['created_at' => $now, 'updated_at' => $now]
                );

                $this->summary['products_imported']++;
                $this->summary['images_saved'] += 5;

                return [
                    'status' => 'IMPORTED',
                    'main_image' => $main,
                    'gallery_images' => $galleryPaths,
                ];
            });
        } catch (\Throwable $exception) {
            foreach ($savedFiles as $file) {
                if (File::exists($file)) {
                    File::delete($file);
                }
            }

            throw $exception;
        }
    }

    private function productPayload(array $product, int $brandId, int $categoryId, ?string $thumbnailPath, Carbon $now): array
    {
        $salePrice = $product['price'];
        $basePrice = $product['original_price'] && $product['original_price'] > $salePrice
            ? $product['original_price']
            : $salePrice;

        $saleValue = max(0, $basePrice - $salePrice);

        return [
            'brand_id' => $brandId,
            'primary_category_id' => $categoryId,
            'name' => $product['name'],
            'slug' => $product['slug'],
            'sku' => $product['sku'],
            'unit' => 'cái',
            'price' => $basePrice,
            'sale_type' => $saleValue > 0 ? 'fixed' : null,
            'sale_value' => $saleValue > 0 ? $saleValue : null,
            'sale_price' => $saleValue > 0 ? $salePrice : null,
            'cost_price' => null,
            'stock_quantity' => 20,
            'stock_alert_threshold' => 3,
            'is_active' => false,
            'is_featured' => false,
            'is_hot' => false,
            'display_order' => 90,
            'thumbnail_path' => $thumbnailPath,
            'description' => Str::limit($product['description'], 1000, ''),
            'content' => $this->contentHtml($product),
            'views_count' => 0,
            'meta' => json_encode([
                'source_name' => $product['source_name'],
                'source_url' => $product['source_url'],
                'sample_import' => 'licensed-retailer-sample-products',
                'usage_note' => self::USAGE_NOTE,
                'original_price' => $product['original_price'],
                'specifications' => $product['specifications'],
                'imported_at' => Carbon::now()->toIso8601String(),
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'meta_title' => $product['name'],
            'meta_description' => Str::limit($product['description'], 250, ''),
            'created_at' => $now,
            'updated_at' => $now,
        ];
    }

    private function contentHtml(array $product): string
    {
        $items = collect($product['specifications'])
            ->take(20)
            ->map(function ($value, $key): string {
                if (is_int($key)) {
                    return '<li>'.e((string) $value).'</li>';
                }

                return '<li><strong>'.e((string) $key).':</strong> '.e((string) $value).'</li>';
            })
            ->implode('');

        return '<p>'.e($product['description']).'</p>'.($items !== '' ? '<ul>'.$items.'</ul>' : '');
    }

    private function ensureBrand(string $name, array $source): int
    {
        $slug = Str::slug($name);
        $existing = DB::table('brands')->where('slug', $slug)->first();

        if ($existing) {
            return $existing->id;
        }

        return DB::table('brands')->insertGetId([
            'name' => $name,
            'slug' => $slug,
            'code' => $this->uniqueCode('BR', $slug),
            'description' => 'Brand created by products:import-sample-products for licensed learning sample data.',
            'is_active' => true,
            'display_order' => 90,
            'meta' => json_encode([
                'source_name' => $source['name'],
                'sample_import' => 'licensed-retailer-sample-products',
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function ensureCategory(string $name, array $source): int
    {
        $slug = Str::slug($name);
        $existing = DB::table('product_categories')->where('slug', $slug)->first();

        if ($existing) {
            return $existing->id;
        }

        return DB::table('product_categories')->insertGetId([
            'name' => $name,
            'slug' => $slug,
            'code' => $this->uniqueCode('CT', $slug),
            'description' => 'Category created by products:import-sample-products for licensed learning sample data.',
            'is_active' => true,
            'display_order' => 90,
            'meta' => json_encode([
                'source_name' => $source['name'],
                'sample_import' => 'licensed-retailer-sample-products',
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function saveImageFile(int $productId, string $slug, string $role, int $index, array $image): string
    {
        $extension = $image['extension'] ?: 'jpg';
        $directory = $role === 'main'
            ? "uploads/products/{$productId}"
            : "uploads/products/{$productId}/gallery";

        File::ensureDirectoryExists(public_path($directory));

        $filename = $role === 'main'
            ? "{$slug}-main.{$extension}"
            : "{$slug}-gallery-{$index}.{$extension}";
        $path = "{$directory}/{$filename}";

        File::put(public_path($path), $image['body']);

        if (! File::exists(public_path($path)) || @getimagesize(public_path($path)) === false) {
            throw new \RuntimeException('saved_image_not_readable:'.$path);
        }

        return $path;
    }

    private function mediaMeta(array $source, array $product, array $image): string
    {
        return json_encode([
            'source_name' => $source['name'],
            'source_url' => $product['source_url'],
            'image_source_url' => $image['source_url'],
            'usage_note' => self::USAGE_NOTE,
            'hash' => $image['hash'],
            'width' => $image['width'],
            'height' => $image['height'],
            'fetched_at' => Carbon::now()->toIso8601String(),
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    private function findExistingProduct(array $product): ?object
    {
        $slug = $product['slug'];
        $sku = $product['sku'];
        $sourceUrl = $product['source_url'];
        $name = $product['name'];

        return DB::table('products')
            ->whereNull('deleted_at')
            ->where(function ($query) use ($slug, $sku, $sourceUrl, $name): void {
                $query->where('slug', $slug)
                    ->orWhere('sku', $sku)
                    ->orWhere('name', $name)
                    ->orWhere('meta', 'like', '%'.$sourceUrl.'%');
            })
            ->first();
    }

    private function backupRelatedTables(): string
    {
        $timestamp = Carbon::now()->format('Ymd-His');
        $backupPath = dirname(base_path()).'/_backups/Laravel11_Shop_Website_Full/'.$timestamp.'-before-import-sample-products';

        File::ensureDirectoryExists($backupPath);

        foreach (['products', 'product_media', 'product_categories', 'categories', 'brands', 'product_category_product'] as $table) {
            if (! Schema::hasTable($table)) {
                continue;
            }

            File::put(
                $backupPath.'/'.$table.'.json',
                json_encode(DB::table($table)->get(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
            );
        }

        File::put($backupPath.'/README-before-import-sample-products.txt', "Backup before products:import-sample-products --commit.\nNo old data is deleted by the import command.\n");

        return $backupPath;
    }

    private function fetch(string $url, array $source, bool $isSitemap = false): ?string
    {
        try {
            $this->throttle($source);
            $response = Http::timeout($isSitemap ? 30 : 25)
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) DangDucLocalLearningImporter/1.0',
                    'Accept' => $isSitemap ? 'application/xml,text/xml,*/*;q=0.8' : 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                ])
                ->get($url);

            if (! $response->successful()) {
                return null;
            }

            return html_entity_decode(str_replace('\\/', '/', (string) $response->body()), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        } catch (\Throwable) {
            return null;
        }
    }

    private function downloadImageToMemory(array $source, string $url): array
    {
        try {
            $this->throttle($source);
            $response = Http::timeout(25)
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) DangDucLocalLearningImporter/1.0',
                    'Accept' => 'image/avif,image/webp,image/png,image/jpeg,*/*;q=0.8',
                ])
                ->get($url);

            $body = (string) $response->body();
            if (! $response->successful() || strlen($body) < 2048) {
                return ['ok' => false, 'reason' => 'download_failed_or_too_small'];
            }

            $size = @getimagesizefromstring($body);
            if ($size === false) {
                return ['ok' => false, 'reason' => 'not_readable_image'];
            }

            if ($size[0] < 400 || $size[1] < 400) {
                return ['ok' => false, 'reason' => 'image_dimensions_less_than_400'];
            }

            $extension = $this->extensionFromImage($size['mime'] ?? '', $url);
            if (! in_array($extension, ['jpg', 'jpeg', 'png', 'webp'], true)) {
                return ['ok' => false, 'reason' => 'unsupported_image_extension'];
            }

            return [
                'ok' => true,
                'body' => $body,
                'hash' => hash('sha256', $body),
                'width' => $size[0],
                'height' => $size[1],
                'extension' => $extension === 'jpeg' ? 'jpg' : $extension,
            ];
        } catch (\Throwable) {
            return ['ok' => false, 'reason' => 'image_request_exception'];
        }
    }

    private function throttle(array $source): void
    {
        $host = $source['host'];
        $delay = (float) ($source['crawl_delay'] ?? 0);

        if ($delay <= 0 || ! isset($this->lastRequestAt[$host])) {
            $this->lastRequestAt[$host] = microtime(true);

            return;
        }

        $elapsed = microtime(true) - $this->lastRequestAt[$host];
        if ($elapsed < $delay) {
            usleep((int) (($delay - $elapsed) * 1_000_000));
        }

        $this->lastRequestAt[$host] = microtime(true);
    }

    private function locsFromSitemap(string $xml): array
    {
        preg_match_all('#<loc>\s*(.*?)\s*</loc>#isu', $xml, $matches);

        return array_map(fn (string $url): string => trim(html_entity_decode($url, ENT_QUOTES | ENT_HTML5, 'UTF-8')), $matches[1] ?? []);
    }

    private function isProductUrl(array $source, string $url): bool
    {
        return preg_match($source['product_url'], $url) === 1
            && ! str_contains($url, '?')
            && ! str_contains($url, '/search')
            && ! str_contains($url, '/cart')
            && ! str_contains($url, '/checkout')
            && ! str_contains($url, '/account');
    }

    private function isAllowedSitemapUrl(array $source, string $url): bool
    {
        $path = strtolower((string) parse_url($url, PHP_URL_PATH));

        if (! str_ends_with($path, '.xml')) {
            return false;
        }

        if (! isset($source['sitemap_url'])) {
            return true;
        }

        return preg_match($source['sitemap_url'], $url) === 1;
    }

    private function jsonLdNode(string $html, string $type): ?array
    {
        foreach ($this->jsonLdPayloads($html) as $payload) {
            $node = $this->findTypedNode($payload, $type);
            if ($node !== null) {
                return $node;
            }
        }

        return null;
    }

    private function jsonLdPayloads(string $html): array
    {
        preg_match_all('#<script[^>]+type=["\']application/ld\+json["\'][^>]*>(.*?)</script>#isu', $html, $matches);
        $payloads = [];

        foreach ($matches[1] ?? [] as $json) {
            $json = trim(html_entity_decode($json, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
            $json = preg_replace('#^\s*<!--|-->\s*$#', '', $json) ?? $json;
            $decoded = json_decode($json, true);

            if (is_array($decoded)) {
                $payloads[] = $decoded;
            }
        }

        return $payloads;
    }

    private function findTypedNode(array $payload, string $type): ?array
    {
        if ($this->nodeHasType($payload, $type)) {
            return $payload;
        }

        if (isset($payload['@graph']) && is_array($payload['@graph'])) {
            foreach ($payload['@graph'] as $node) {
                if (is_array($node) && $this->nodeHasType($node, $type)) {
                    return $node;
                }
            }
        }

        if (array_is_list($payload)) {
            foreach ($payload as $node) {
                if (is_array($node)) {
                    $found = $this->findTypedNode($node, $type);
                    if ($found !== null) {
                        return $found;
                    }
                }
            }
        }

        return null;
    }

    private function nodeHasType(array $node, string $type): bool
    {
        $nodeType = $node['@type'] ?? null;

        if (is_string($nodeType)) {
            return Str::lower($nodeType) === Str::lower($type);
        }

        if (is_array($nodeType)) {
            return in_array($type, $nodeType, true);
        }

        return false;
    }

    private function breadcrumbNames(string $html): array
    {
        $node = $this->jsonLdNode($html, 'BreadcrumbList');

        if (! $node || ! isset($node['itemListElement']) || ! is_array($node['itemListElement'])) {
            return [];
        }

        return collect($node['itemListElement'])
            ->map(function ($item): string {
                if (! is_array($item)) {
                    return '';
                }

                if (isset($item['name'])) {
                    return $this->cleanText((string) $item['name']);
                }

                if (isset($item['item']['name'])) {
                    return $this->cleanText((string) $item['item']['name']);
                }

                return '';
            })
            ->filter()
            ->values()
            ->all();
    }

    private function extractPrice(array $jsonLdProduct, string $html): ?int
    {
        $offer = $jsonLdProduct['offers'] ?? [];
        if (array_is_list($offer)) {
            $offer = $offer[0] ?? [];
        }

        $price = is_array($offer) ? ($offer['price'] ?? $offer['lowPrice'] ?? null) : null;
        $parsed = $this->parseMoney($price);

        if ($parsed !== null) {
            return $parsed;
        }

        foreach ([
            '#<meta[^>]+property=["\']product:price:amount["\'][^>]+content=["\']([^"\']+)["\']#isu',
            '#<meta[^>]+content=["\']([^"\']+)["\'][^>]+property=["\']product:price:amount["\']#isu',
            '#itemprop=["\']price["\'][^>]+content=["\']([^"\']+)["\']#isu',
            '#class=["\'][^"\']*price-now[^"\']*["\'][^>]*>\s*([^<]+)#isu',
        ] as $pattern) {
            $value = $this->matchFirst($html, $pattern);
            $parsed = $this->parseMoney($value);
            if ($parsed !== null) {
                return $parsed;
            }
        }

        foreach ([
            '#"price"\s*:\s*"?([0-9][0-9\.,]{4,})"?#iu',
            '#price:\s*"?([0-9][0-9\.,]{4,})"?#iu',
            '#data-price\s*=\s*"([0-9][0-9\.,]{4,})"#iu',
            '#class=["\'][^"\']*(?:price|product-price)[^"\']*["\'][^>]*>\s*([^<]+)#iu',
        ] as $pattern) {
            $value = $this->matchFirst($html, $pattern);
            $parsed = $this->parseMoney($value);
            if ($parsed !== null) {
                return $parsed;
            }
        }

        return null;
    }

    private function extractOriginalPrice(array $jsonLdProduct, string $html, ?int $salePrice): ?int
    {
        if ($salePrice === null) {
            return null;
        }

        $candidates = [];
        preg_match_all('#(?:compare_at_price|original_price|old_price|oldPrice|listPrice|price_before_discount|giá gốc|giá niêm yết)[^0-9]{0,80}([0-9][0-9\.,]{4,})#iu', $html, $matches);

        foreach ($matches[1] ?? [] as $value) {
            $parsed = $this->parseMoney($value);
            if ($parsed !== null && $parsed > $salePrice && $parsed <= $salePrice * 3) {
                $candidates[] = $parsed;
            }
        }

        $offers = $jsonLdProduct['offers'] ?? [];
        if (is_array($offers) && isset($offers['highPrice'])) {
            $parsed = $this->parseMoney($offers['highPrice']);
            if ($parsed !== null && $parsed > $salePrice && $parsed <= $salePrice * 3) {
                $candidates[] = $parsed;
            }
        }

        return $candidates === [] ? null : min($candidates);
    }

    private function extractBrand(array $jsonLdProduct, string $html, string $name): string
    {
        $brand = $jsonLdProduct['brand'] ?? '';

        if (is_array($brand)) {
            $brand = $brand['name'] ?? $brand['description'] ?? '';
        }

        $brand = $this->cleanText((string) $brand);

        if ($brand !== '') {
            return $brand;
        }

        $brand = $this->matchFirst($html, '#"brand"\s*:\s*\{\s*"@type"\s*:\s*"Brand"\s*,\s*"name"\s*:\s*"([^"]+)"#isu')
            ?? $this->matchFirst($html, '#brand[^<>"\']{0,40}(?:name|description)["\']?\s*[:=]\s*["\']([^"\']+)#isu');

        $brand = $this->cleanText((string) $brand);

        return $brand !== '' ? $brand : $this->inferBrandFromName($name);
    }

    private function extractCategory(array $source, string $url, string $name, array $breadcrumb): string
    {
        $filtered = collect($breadcrumb)
            ->reject(fn (string $item): bool => Str::lower($item) === 'trang chủ')
            ->reject(fn (string $item): bool => $this->sameProductName($item, $name))
            ->values();

        if ($filtered->isNotEmpty()) {
            return $filtered->last();
        }

        $haystack = Str::lower($url.' '.$name);

        foreach ($this->categoryKeywords() as $keyword => $category) {
            if (str_contains($haystack, $keyword)) {
                return $category;
            }
        }

        return '';
    }

    private function extractDescription(array $jsonLdProduct, string $html): string
    {
        $description = $this->cleanText($jsonLdProduct['description'] ?? '');

        if ($description !== '') {
            return $description;
        }

        return $this->cleanText($this->metaContent($html, 'description') ?: $this->metaContent($html, 'og:description') ?: '');
    }

    private function extractSpecifications(string $html, string $description): array
    {
        $specs = [];

        preg_match_all('#<tr[^>]*>\s*<(?:td|th)[^>]*>(.*?)</(?:td|th)>\s*<(?:td|th)[^>]*>(.*?)</(?:td|th)>#isu', $html, $rows, PREG_SET_ORDER);
        foreach ($rows as $row) {
            $key = $this->cleanText($row[1]);
            $value = $this->cleanText($row[2]);

            if ($key !== '' && $value !== '' && ! $this->looksLikeUiText($key) && mb_strlen($key) <= 80 && mb_strlen($value) <= 500) {
                $specs[$key] = $value;
            }
        }

        if ($specs !== []) {
            return $specs;
        }

        if (mb_strlen($description) >= 60) {
            return ['Thông số/Mô tả nguồn' => Str::limit($description, 900, '')];
        }

        return [];
    }

    private function extractImageCandidates(array $source, string $url, string $html, array $jsonLdProduct, string $name): array
    {
        $images = [];
        $jsonImages = $jsonLdProduct['image'] ?? [];

        if (is_string($jsonImages)) {
            $images[] = $jsonImages;
        } elseif (is_array($jsonImages)) {
            $images = array_merge($images, collect($jsonImages)->flatten()->filter(fn ($value): bool => is_string($value))->all());
        }

        preg_match_all('#https?://[^"\'\s<>]+?\.(?:jpg|jpeg|png|webp)(?:\?[^"\'\s<>]*)?#iu', $html, $matches);
        $images = array_merge($images, $matches[0] ?? []);

        $skuHint = (string) ($jsonLdProduct['sku'] ?? $jsonLdProduct['mpn'] ?? '');

        return collect($images)
            ->map(fn (string $image): ?string => $this->normalizeImageUrl($source, $image))
            ->filter()
            ->reject(fn (string $image): bool => $this->looksLikeNonProductAsset($image))
            ->filter(fn (string $image): bool => $this->imageBelongsToSourceProduct($source, $image, $name, $skuHint, $url))
            ->unique()
            ->values()
            ->all();
    }

    private function imageBelongsToSourceProduct(array $source, string $imageUrl, string $name, string $skuHint, string $productUrl): bool
    {
        $url = Str::lower($imageUrl);
        $sourceKey = $this->sourceKeyByName($source['name']);

        if ($sourceKey === 'xgear') {
            return str_contains($url, 'cdn.hstatic.net/products/');
        }

        if ($sourceKey === 'anphatpc') {
            if (! str_contains($url, '/media/product/')) {
                return false;
            }

            return $skuHint === '' || str_contains($url, Str::lower($skuHint));
        }

        if ($sourceKey === 'cellphones') {
            return str_contains($url, '/media/catalog/product/');
        }

        if ($sourceKey === 'phongvu') {
            if (! (str_contains($url, 'storage.googleapis.com') || str_contains($url, 'lh3.googleusercontent.com'))) {
                return false;
            }

            $tokens = $this->modelTokens($name);
            if ($tokens === []) {
                return true;
            }

            $haystack = Str::slug($imageUrl.' '.$productUrl);
            $matches = collect($tokens)->filter(fn (string $token): bool => str_contains($haystack, Str::slug($token)))->count();

            return $matches >= min(2, count($tokens));
        }

        return false;
    }

    private function normalizeImageUrl(array $source, string $url): ?string
    {
        $url = html_entity_decode(trim($url), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $url = str_replace(['\\u002F', '\\/'], '/', $url);
        $url = trim($url, " \t\n\r\0\x0B'\"),;\\");

        if (! filter_var($url, FILTER_VALIDATE_URL)) {
            return null;
        }

        $sourceKey = $this->sourceKeyByName($source['name']);

        if ($sourceKey === 'cellphones') {
            $url = preg_replace('#cdn2\.cellphones\.com\.vn/(?:[0-9]+x|insecure/[^/]+/q:[0-9]+/plain/https://cellphones\.com\.vn)/#', 'cdn2.cellphones.com.vn/x/', $url) ?? $url;
        }

        if ($sourceKey === 'anphatpc') {
            $url = preg_replace('#/media/product/(?:[0-9]+_)#', '/media/product/', $url) ?? $url;
        }

        return $url;
    }

    private function normalizeUrl(?string $url): ?string
    {
        $url = html_entity_decode(trim((string) $url), ENT_QUOTES | ENT_HTML5, 'UTF-8');

        return filter_var($url, FILTER_VALIDATE_URL) ? $url : null;
    }

    private function looksLikeNonProductAsset(string $url): bool
    {
        $url = Str::lower($url);

        foreach (['logo', 'favicon', 'icon', 'sprite', 'banner', 'placeholder', 'placehoder', 'loading', 'empty_cart', 'gift-filled', 'payment', 'delivery', 'shipping', 'warranty', 'badge', 'chibi', 'wysiwyg/web/logo', '/media/category/', '/media/banner/', '/static/'] as $needle) {
            if (str_contains($url, $needle)) {
                return true;
            }
        }

        return false;
    }

    private function inferBrandFromName(string $name): string
    {
        $brands = [
            'Apple', 'Samsung', 'Asus', 'Acer', 'Dell', 'HP', 'Lenovo', 'MSI', 'Gigabyte', 'LG', 'Sony',
            'Keychron', 'Akko', 'Logitech', 'Razer', 'Corsair', 'HyperX', 'JBL', 'Anker', 'Baseus',
            'TP-Link', 'Tenda', 'Ugreen', 'Xiaomi', 'Oppo', 'Vivo', 'Canon', 'Epson', 'Brother',
            'Kingston', 'Adata', 'Seagate', 'Western Digital', 'ViewSonic', 'AOC', 'Philips', 'Noctua',
            'Jonsbo', 'Darkflash', 'ASRock', 'Tryx', 'Ergotek', 'Aptek',
        ];

        foreach ($brands as $brand) {
            if (Str::contains(Str::lower($name), Str::lower($brand))) {
                return $brand;
            }
        }

        return '';
    }

    private function categoryKeywords(): array
    {
        return [
            'laptop' => 'Laptop',
            'macbook' => 'Laptop',
            'ban-phim' => 'Bàn phím',
            'keyboard' => 'Bàn phím',
            'chuot' => 'Chuột',
            'mouse' => 'Chuột',
            'tai-nghe' => 'Tai nghe',
            'headphone' => 'Tai nghe',
            'loa' => 'Loa',
            'speaker' => 'Loa',
            'man-hinh' => 'Màn hình',
            'monitor' => 'Màn hình',
            'vga' => 'Card màn hình',
            'rtx' => 'Card màn hình',
            'arc-' => 'Card màn hình',
            'case' => 'Vỏ case',
            'vo-case' => 'Vỏ case',
            'tan-nhiet' => 'Tản nhiệt',
            'router' => 'Thiết bị mạng',
            'switch' => 'Thiết bị mạng',
            'may-in' => 'Máy in',
            'dien-thoai' => 'Điện thoại',
            'iphone' => 'Điện thoại',
            'samsung-galaxy' => 'Điện thoại',
            'ipad' => 'Máy tính bảng',
            'tablet' => 'Máy tính bảng',
            'ssd' => 'Ổ cứng SSD',
            'ram' => 'RAM',
            'tui-deo' => 'Phụ kiện',
            'cap-' => 'Cáp kết nối',
        ];
    }

    private function modelTokens(string $name): array
    {
        preg_match_all('/[A-Za-z0-9]+(?:-[A-Za-z0-9]+)*/', $name, $matches);

        return collect($matches[0] ?? [])
            ->map(fn (string $token): string => trim($token))
            ->filter(fn (string $token): bool => strlen($token) >= 3)
            ->filter(fn (string $token): bool => preg_match('/\d/', $token) === 1 || preg_match('/[A-Z]{2,}/', $token) === 1)
            ->unique()
            ->values()
            ->all();
    }

    private function parseMoney(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        $digits = preg_replace('/[^0-9]/', '', (string) $value);
        if ($digits === null || $digits === '') {
            return null;
        }

        $amount = (int) $digits;

        return $amount >= 10000 && $amount <= 500000000 ? $amount : null;
    }

    private function metaContent(string $html, string $name): ?string
    {
        $quoted = preg_quote($name, '#');

        return $this->matchFirst($html, '#<meta[^>]+(?:name|property)=["\']'.$quoted.'["\'][^>]+content=["\']([^"\']+)["\'][^>]*>#isu')
            ?? $this->matchFirst($html, '#<meta[^>]+content=["\']([^"\']+)["\'][^>]+(?:name|property)=["\']'.$quoted.'["\'][^>]*>#isu');
    }

    private function matchFirst(string $text, string $pattern): ?string
    {
        return preg_match($pattern, $text, $match) === 1 ? $match[1] : null;
    }

    private function cleanText(mixed $value): string
    {
        $text = html_entity_decode(strip_tags((string) $value), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = preg_replace('/\s+/u', ' ', $text) ?? $text;

        return trim($text);
    }

    private function looksLikeUiText(string $text): bool
    {
        $text = Str::lower($text);

        foreach (['giỏ hàng', 'thanh toán', 'đăng nhập', 'khuyến mãi', 'giao hàng', 'bảo hành', 'trả góp'] as $needle) {
            if (str_contains($text, $needle)) {
                return true;
            }
        }

        return false;
    }

    private function sameProductName(string $breadcrumb, string $name): bool
    {
        return Str::slug($breadcrumb) === Str::slug($name)
            || str_contains(Str::slug($name), Str::slug($breadcrumb));
    }

    private function uniqueImportSlug(array $source, string $name, string $url): string
    {
        return Str::slug($this->sourceKeyByName($source['name']).'-'.$name.'-'.Str::substr(md5($url), 0, 8));
    }

    private function skuFor(array $source, string $url): string
    {
        return Str::upper(Str::substr($this->sourceKeyByName($source['name']), 0, 3).'-'.Str::substr(md5($url), 0, 12));
    }

    private function uniqueCode(string $prefix, string $slug): string
    {
        return Str::upper(Str::substr($prefix.md5($slug), 0, 12));
    }

    private function sourceKeyByName(string $name): string
    {
        return match ($name) {
            'Phong Vũ' => 'phongvu',
            'XGear' => 'xgear',
            'An Phát PC' => 'anphatpc',
            'CellphoneS' => 'cellphones',
            default => Str::slug($name),
        };
    }

    private function extensionFromImage(string $mime, string $url): string
    {
        $mime = Str::lower($mime);

        if (str_contains($mime, 'webp')) {
            return 'webp';
        }

        if (str_contains($mime, 'png')) {
            return 'png';
        }

        if (str_contains($mime, 'jpeg') || str_contains($mime, 'jpg')) {
            return 'jpg';
        }

        $extension = Str::lower(pathinfo(parse_url($url, PHP_URL_PATH) ?: '', PATHINFO_EXTENSION));

        return in_array($extension, ['jpg', 'jpeg', 'png', 'webp'], true) ? $extension : 'jpg';
    }

    private function skip(array $source, string $url, string $name, string $brand, string $category, ?int $price, ?int $originalPrice, string $mainImage, array $galleryImages, int $validImageCount, string $status, string $reason): void
    {
        $this->recordSkipReason($reason);

        if ($status === 'ERROR') {
            $this->summary['products_error']++;
            $this->errors[] = [
                'source_name' => $source['name'],
                'source_url' => $url,
                'product_name' => $name,
                'status' => $status,
                'reason' => $reason,
            ];
        } else {
            $this->summary['products_skipped']++;
        }

        if ($this->isMissingImageReason($reason)) {
            $this->summary['products_missing_images']++;
        }

        if ($this->isImageErrorReason($reason)) {
            $this->summary['products_image_error']++;
        }

        if ($this->isMissingRequiredReason($reason)) {
            $this->summary['products_missing_required_info']++;
        }

        $this->auditRows[] = [
            'source_name' => $source['name'],
            'source_url' => $url,
            'product_name' => $name,
            'brand' => $brand,
            'category' => $category,
            'price' => $price,
            'original_price' => $originalPrice,
            'main_image' => $mainImage,
            'gallery_images' => implode('|', $galleryImages),
            'valid_image_count' => $validImageCount,
            'status' => $status,
            'skipped_reason' => $reason,
        ];
    }

    private function addAuditRow(array $source, string $url, array $product, string $mainImage, array $galleryImages, int $validImageCount, string $status, string $reason): void
    {
        $this->auditRows[] = [
            'source_name' => $source['name'],
            'source_url' => $url,
            'product_name' => $product['name'],
            'brand' => $product['brand'],
            'category' => $product['category'],
            'price' => $product['price'],
            'original_price' => $product['original_price'],
            'main_image' => $mainImage,
            'gallery_images' => implode('|', $galleryImages),
            'valid_image_count' => $validImageCount,
            'status' => $status,
            'skipped_reason' => $reason,
        ];
    }

    private function recordError(array $source, string $url, \Throwable $exception): void
    {
        $this->skip($source, $url, '', '', '', null, null, '', [], 0, 'ERROR', $exception::class.': '.$exception->getMessage());
    }

    private function writeReports(): void
    {
        File::ensureDirectoryExists(base_path('docs/import'));
        $this->summary['top_skip_reasons'] = $this->topSkipReasons();

        $auditCsv = base_path(self::REPORTS['audit_csv']);
        $handle = fopen($auditCsv, 'wb');
        fputcsv($handle, self::AUDIT_HEADERS);

        foreach ($this->auditRows as $row) {
            fputcsv($handle, array_map(fn (string $header) => $row[$header] ?? '', self::AUDIT_HEADERS));
        }

        fclose($handle);

        File::put(base_path(self::REPORTS['audit_json']), json_encode($this->auditRows, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        File::put(base_path(self::REPORTS['summary_json']), json_encode($this->summary, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        File::put(base_path(self::REPORTS['errors_json']), json_encode($this->errors, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }

    private function recordSkipReason(string $reason): void
    {
        $reason = trim($reason);

        if ($reason === '') {
            return;
        }

        $this->skipReasons[$reason] = ($this->skipReasons[$reason] ?? 0) + 1;
    }

    private function topSkipReasons(): array
    {
        arsort($this->skipReasons);

        return collect($this->skipReasons)
            ->take(15)
            ->map(fn (int $count, string $reason): array => [
                'reason' => $reason,
                'count' => $count,
            ])
            ->values()
            ->all();
    }

    private function isMissingImageReason(string $reason): bool
    {
        return in_array($reason, [
            'not_enough_candidate_images',
            'valid_image_count_less_than_5',
        ], true);
    }

    private function isImageErrorReason(string $reason): bool
    {
        return str_contains($reason, 'image_')
            || str_contains($reason, 'download_')
            || str_contains($reason, 'unsupported_image_extension')
            || str_contains($reason, 'not_readable_image');
    }

    private function isMissingRequiredReason(string $reason): bool
    {
        return str_starts_with($reason, 'missing_product_');
    }
}
