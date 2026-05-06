<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class SupplementGalleryImagesCommand extends Command
{
    protected $signature = 'products:supplement-gallery-images
        {--dry-run : Audit only; this is also the default when --commit is absent}
        {--commit : Download valid gallery images and write product_media rows}
        {--limit= : Limit number of products to audit/process}
        {--product-id= : Process a single product id}
        {--target-total=4 : Desired total image count per product, including the main image}
        {--max-images=8 : Maximum total image count per product}';

    protected $description = 'Audit and supplement missing product gallery images for TGDD/imported demo products.';

    private const USAGE_NOTE = 'Ảnh mượn phục vụ học tập/local demo; khi kinh doanh thật cần thay bằng ảnh tự chụp/ảnh nhà cung cấp/ảnh được cấp quyền chính thức.';

    private const REPORT_HEADERS = [
        'product_id',
        'product_name',
        'brand',
        'current_image_count',
        'added_image_count',
        'final_image_count',
        'source_used',
        'added_urls',
        'skipped_urls',
        'skip_reason',
        'status',
    ];

    private array $auditRows = [];

    private array $errors = [];

    private array $summary = [
        'dry_run' => true,
        'commit' => false,
        'products_audited' => 0,
        'products_missing_gallery' => 0,
        'products_with_images_added' => 0,
        'products_with_planned_images' => 0,
        'products_review_image' => 0,
        'products_partial' => 0,
        'products_error' => 0,
        'images_added' => 0,
        'planned_images' => 0,
        'backup_path' => null,
        'reports' => [],
    ];

    private array $manifestRows = [];

    public function handle(): int
    {
        if (! $this->validateSchema()) {
            return self::FAILURE;
        }

        $commit = (bool) $this->option('commit') && ! (bool) $this->option('dry-run');
        $dryRun = ! $commit;
        $limit = $this->option('limit') !== null ? max(1, (int) $this->option('limit')) : null;
        $productId = $this->option('product-id') !== null ? (int) $this->option('product-id') : null;
        $targetTotal = max(2, (int) $this->option('target-total'));
        $maxImages = max($targetTotal, (int) $this->option('max-images'));

        $this->summary['dry_run'] = $dryRun;
        $this->summary['commit'] = $commit;
        $this->manifestRows = $this->loadManifestRows();

        if ($commit) {
            $this->summary['backup_path'] = $this->backupRelatedTables();
        }

        $products = $this->candidateProducts($productId, $limit);

        foreach ($products as $product) {
            $this->summary['products_audited']++;

            try {
                $this->processProduct($product, $dryRun, $targetTotal, $maxImages);
            } catch (\Throwable $exception) {
                $this->recordError($product, $exception);
            }
        }

        $this->writeReports();
        $this->line(json_encode($this->summary, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

        return $this->summary['products_error'] > 0 ? self::FAILURE : self::SUCCESS;
    }

    private function validateSchema(): bool
    {
        foreach (['products', 'product_media'] as $table) {
            if (! Schema::hasTable($table)) {
                $this->error("Missing required table: {$table}");

                return false;
            }
        }

        foreach (['id', 'name', 'slug', 'sku', 'thumbnail_path', 'meta'] as $column) {
            if (! Schema::hasColumn('products', $column)) {
                $this->error("Missing required products column: {$column}");

                return false;
            }
        }

        foreach (['product_id', 'file_path', 'disk', 'collection', 'display_order', 'meta'] as $column) {
            if (! Schema::hasColumn('product_media', $column)) {
                $this->error("Missing required product_media column: {$column}");

                return false;
            }
        }

        return true;
    }

    private function candidateProducts(?int $productId, ?int $limit): Collection
    {
        $galleryCount = DB::table('product_media')
            ->select('product_id', DB::raw("SUM(CASE WHEN collection = 'gallery' THEN 1 ELSE 0 END) as gallery_count"))
            ->groupBy('product_id');

        $query = DB::table('products')
            ->leftJoinSub($galleryCount, 'gallery_counts', 'gallery_counts.product_id', '=', 'products.id')
            ->leftJoin('brands', 'brands.id', '=', 'products.brand_id')
            ->select([
                'products.id',
                'products.name',
                'products.slug',
                'products.sku',
                'products.thumbnail_path',
                'products.meta',
                'brands.name as brand_name',
                DB::raw('COALESCE(gallery_counts.gallery_count, 0) as gallery_count'),
            ])
            ->whereNull('products.deleted_at')
            ->where(function ($query): void {
                $query->where('products.slug', 'like', 'tgdd-%')
                    ->orWhere('products.meta', 'like', '%thegioididong.com%')
                    ->orWhere('products.meta', 'like', '%cdn.tgdd.vn%')
                    ->orWhere('products.meta', 'like', '%cdnv2.tgdd.vn%')
                    ->orWhere('products.meta', 'like', '%tgdd-pass-5-5%')
                    ->orWhereRaw('COALESCE(gallery_counts.gallery_count, 0) < 3');
            })
            ->whereRaw('COALESCE(gallery_counts.gallery_count, 0) < 3')
            ->orderBy('products.id');

        if ($productId !== null && $productId > 0) {
            $query->where('products.id', $productId);
        }

        if ($limit !== null) {
            $query->limit($limit);
        }

        return $query->get();
    }

    private function processProduct(object $product, bool $dryRun, int $targetTotal, int $maxImages): void
    {
        $currentGalleryCount = (int) $product->gallery_count;
        $hasMainImage = $this->isValidLocalImagePath($product->thumbnail_path);
        $currentTotal = $currentGalleryCount + ($hasMainImage ? 1 : 0);

        if ($currentGalleryCount >= 3 || $currentTotal >= $targetTotal) {
            $this->addAuditRow($product, $currentTotal, 0, $currentTotal, '', [], [], 'already_has_enough_gallery', 'OK');

            return;
        }

        $this->summary['products_missing_gallery']++;

        if (! $hasMainImage) {
            $this->summary['products_review_image']++;
            $this->addAuditRow($product, $currentTotal, 0, $currentTotal, '', [], [], 'missing_or_invalid_main_image', 'REVIEW_IMAGE');

            return;
        }

        $needed = min($maxImages - $currentTotal, max(0, $targetTotal - $currentTotal));
        if ($needed <= 0) {
            $this->addAuditRow($product, $currentTotal, 0, $currentTotal, '', [], [], 'target_already_reached', 'OK');

            return;
        }

        $existingFingerprints = $this->existingImageFingerprints($product);
        $candidates = $this->candidateImagesForProduct($product);
        $skipped = [];
        $selected = [];

        foreach ($candidates as $candidate) {
            if (count($selected) >= $needed) {
                break;
            }

            $skipReason = $this->preflightSkipReason($product, $candidate, $existingFingerprints, $selected);
            if ($skipReason !== null) {
                $skipped[] = $candidate['url'].' ['.$skipReason.']';

                continue;
            }

            if ($dryRun) {
                $selected[] = $candidate + ['saved_path' => '', 'hash' => ''];

                continue;
            }

            $downloaded = $this->downloadAndValidate($product, $candidate, $existingFingerprints, count($selected) + 1);
            if (! $downloaded['ok']) {
                $skipped[] = $candidate['url'].' ['.$downloaded['reason'].']';

                continue;
            }

            $selected[] = $candidate + $downloaded;
            $existingFingerprints['urls'][] = $candidate['url'];
            $existingFingerprints['hashes'][] = $downloaded['hash'];
            $existingFingerprints['paths'][] = $downloaded['saved_path'];
        }

        if (! $dryRun && $selected !== []) {
            DB::transaction(function () use ($product, $selected): void {
                $nextOrder = (int) DB::table('product_media')
                    ->where('product_id', $product->id)
                    ->max('display_order');

                foreach ($selected as $index => $image) {
                    DB::table('product_media')->insert([
                        'product_id' => $product->id,
                        'file_path' => $image['saved_path'],
                        'disk' => 'public',
                        'collection' => 'gallery',
                        'display_order' => $nextOrder + $index + 1,
                        'meta' => json_encode([
                            'source_url' => $image['source_page'] ?? null,
                            'image_source_url' => $image['url'],
                            'source_domain' => $image['source_domain'] ?? $this->host($image['url']),
                            'usage_note' => self::USAGE_NOTE,
                            'confidence' => 'high',
                            'fetched_at' => Carbon::now()->toIso8601String(),
                            'hash' => $image['hash'],
                            'width' => $image['width'],
                            'height' => $image['height'],
                        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            });
        }

        $addedCount = count($selected);
        $finalTotal = $currentTotal + $addedCount;
        $sourceUsed = collect($selected)->pluck('source_domain')->filter()->unique()->implode('|');
        $addedUrls = collect($selected)->pluck('url')->all();
        $status = $addedCount === 0 ? 'REVIEW_IMAGE' : ($finalTotal >= $targetTotal ? 'OK' : 'PARTIAL');

        if ($status === 'REVIEW_IMAGE') {
            $this->summary['products_review_image']++;
        } elseif ($status === 'PARTIAL') {
            $this->summary['products_partial']++;
        }

        if ($addedCount > 0) {
            if ($dryRun) {
                $this->summary['products_with_planned_images']++;
                $this->summary['planned_images'] += $addedCount;
            } else {
                $this->summary['products_with_images_added']++;
                $this->summary['images_added'] += $addedCount;
            }
        }

        $this->addAuditRow(
            $product,
            $currentTotal,
            $addedCount,
            $finalTotal,
            $sourceUsed,
            $addedUrls,
            $skipped,
            $addedCount === 0 ? 'no_valid_gallery_image_found' : '',
            $status
        );
    }

    private function candidateImagesForProduct(object $product): array
    {
        $fromManifest = $this->manifestCandidates($product);
        $sourceUrl = $this->sourceUrl($product);

        if ($sourceUrl === null) {
            return $fromManifest;
        }

        return collect($fromManifest)
            ->merge($this->sourcePageCandidates($product, $sourceUrl))
            ->unique('url')
            ->values()
            ->all();
    }

    private function manifestCandidates(object $product): array
    {
        return collect($this->manifestRows)
            ->filter(function (array $row) use ($product): bool {
                $role = strtolower((string) ($row['image_role'] ?? ''));
                $result = strtolower((string) ($row['result'] ?? ''));
                $path = $this->normalizePath($row['saved_path'] ?? ($row['local_image_path'] ?? ''));

                return $role === 'gallery'
                    && $result !== 'removed_wrong_image'
                    && $path !== ''
                    && $this->rowMatchesProduct($row, $product)
                    && $this->isValidLocalImagePath($path);
            })
            ->map(fn (array $row): array => [
                'url' => (string) ($row['image_source_url'] ?? $row['source_url'] ?? ''),
                'local_path' => $this->normalizePath($row['saved_path'] ?? ($row['local_image_path'] ?? '')),
                'source_page' => (string) ($row['source_url'] ?? ''),
                'source_domain' => (string) ($row['source_domain'] ?? $this->host((string) ($row['image_source_url'] ?? ''))),
                'source' => 'manifest',
            ])
            ->filter(fn (array $row): bool => $row['url'] !== '' || $row['local_path'] !== '')
            ->values()
            ->all();
    }

    private function sourcePageCandidates(object $product, string $sourceUrl): array
    {
        try {
            $response = Http::timeout(20)
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) DangDucLocalDemo/1.0',
                    'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                ])
                ->get($sourceUrl);

            if (! $response->successful()) {
                return [];
            }

            $html = html_entity_decode(str_replace('\\/', '/', (string) $response->body()), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        } catch (\Throwable) {
            return [];
        }

        preg_match_all('#https?://[^"\'\s<>]+(?:cdn|tgdd|thegioididong|dienmayxanh)[^"\'\s<>]+\.(?:jpg|jpeg|png|webp)(?:\?[^"\'\s<>]*)?#iu', $html, $matches);

        return collect($matches[0] ?? [])
            ->map(fn (string $url): string => $this->normalizeUrl($url))
            ->filter()
            ->reject(fn (string $url): bool => $this->looksLikeNonProductAsset($url))
            ->filter(fn (string $url): bool => $this->highConfidenceImageUrl($product, $url, $sourceUrl))
            ->unique()
            ->take(12)
            ->map(fn (string $url): array => [
                'url' => $url,
                'local_path' => '',
                'source_page' => $sourceUrl,
                'source_domain' => $this->host($sourceUrl),
                'source' => 'source_page',
            ])
            ->values()
            ->all();
    }

    private function downloadAndValidate(object $product, array $candidate, array $existingFingerprints, int $index): array
    {
        if (($candidate['local_path'] ?? '') !== '') {
            return $this->validateExistingLocalCandidate($candidate, $existingFingerprints);
        }

        try {
            $response = Http::timeout(25)
                ->withHeaders(['User-Agent' => 'Mozilla/5.0 DangDucLocalDemo/1.0'])
                ->get($candidate['url']);
        } catch (\Throwable) {
            return ['ok' => false, 'reason' => 'download_failed'];
        }

        if (! $response->successful() || strlen((string) $response->body()) < 2048) {
            return ['ok' => false, 'reason' => 'download_not_successful_or_too_small'];
        }

        $body = (string) $response->body();
        $image = @getimagesizefromstring($body);
        if (! $image) {
            return ['ok' => false, 'reason' => 'not_readable_image'];
        }

        [$width, $height] = [$image[0], $image[1]];
        if ($width < 400 || $height < 400) {
            return ['ok' => false, 'reason' => 'image_dimensions_too_small'];
        }

        $hash = hash('sha256', $body);
        if (in_array($hash, $existingFingerprints['hashes'], true)) {
            return ['ok' => false, 'reason' => 'duplicate_hash'];
        }

        $extension = $this->extensionFromResponse($response->header('Content-Type', ''), $candidate['url']);
        $directory = 'uploads/products/'.$product->id.'/gallery';
        File::ensureDirectoryExists(public_path($directory));

        $filename = Str::slug($product->slug ?: $product->name).'-gallery-'.($index + (int) DB::table('product_media')->where('product_id', $product->id)->count()).'.'.$extension;
        $relativePath = $directory.'/'.$filename;

        if (in_array($relativePath, $existingFingerprints['paths'], true) || File::exists(public_path($relativePath))) {
            return ['ok' => false, 'reason' => 'duplicate_filename'];
        }

        file_put_contents(public_path($relativePath), $body);

        return [
            'ok' => true,
            'reason' => '',
            'saved_path' => $relativePath,
            'hash' => $hash,
            'width' => $width,
            'height' => $height,
        ];
    }

    private function validateExistingLocalCandidate(array $candidate, array $existingFingerprints): array
    {
        $path = $this->normalizePath($candidate['local_path'] ?? '');
        $absolutePath = public_path($path);
        if (! File::exists($absolutePath)) {
            return ['ok' => false, 'reason' => 'local_candidate_missing'];
        }

        $body = (string) file_get_contents($absolutePath);
        $image = @getimagesizefromstring($body);
        if (! $image || $image[0] < 400 || $image[1] < 400) {
            return ['ok' => false, 'reason' => 'local_candidate_invalid_dimensions'];
        }

        $hash = hash('sha256', $body);
        if (in_array($hash, $existingFingerprints['hashes'], true)) {
            return ['ok' => false, 'reason' => 'duplicate_hash'];
        }

        return [
            'ok' => true,
            'reason' => '',
            'saved_path' => $path,
            'hash' => $hash,
            'width' => $image[0],
            'height' => $image[1],
        ];
    }

    private function preflightSkipReason(object $product, array $candidate, array $existingFingerprints, array $selected): ?string
    {
        $url = $candidate['url'] ?? '';
        $path = $this->normalizePath($candidate['local_path'] ?? '');

        if ($url !== '' && in_array($url, $existingFingerprints['urls'], true)) {
            return 'duplicate_url';
        }

        if ($path !== '' && in_array($path, $existingFingerprints['paths'], true)) {
            return 'duplicate_path';
        }

        if ($url !== '' && $this->looksLikeNonProductAsset($url)) {
            return 'non_product_asset_url';
        }

        if ($url !== '' && ! $this->highConfidenceImageUrl($product, $url, (string) ($candidate['source_page'] ?? ''))) {
            return 'low_model_match_confidence';
        }

        $selectedUrls = collect($selected)->pluck('url')->filter()->all();
        if ($url !== '' && in_array($url, $selectedUrls, true)) {
            return 'duplicate_selected_url';
        }

        return null;
    }

    private function existingImageFingerprints(object $product): array
    {
        $paths = collect([$this->normalizePath($product->thumbnail_path)])
            ->merge(DB::table('product_media')->where('product_id', $product->id)->pluck('file_path')->map(fn ($path) => $this->normalizePath($path)))
            ->filter()
            ->unique()
            ->values()
            ->all();

        $hashes = [];
        foreach ($paths as $path) {
            $absolutePath = public_path($path);
            if (File::exists($absolutePath)) {
                $hashes[] = hash_file('sha256', $absolutePath);
            }
        }

        $urls = DB::table('product_media')
            ->where('product_id', $product->id)
            ->pluck('meta')
            ->map(fn ($meta) => json_decode((string) $meta, true))
            ->filter()
            ->flatMap(fn (array $meta) => array_filter([$meta['image_source_url'] ?? null, $meta['source_url'] ?? null]))
            ->unique()
            ->values()
            ->all();

        return [
            'paths' => $paths,
            'hashes' => array_values(array_unique($hashes)),
            'urls' => $urls,
        ];
    }

    private function loadManifestRows(): array
    {
        $rows = [];
        foreach ([
            base_path('docs/import/product-images-manifest.json'),
            base_path('docs/import/tgdd-demo-products-manifest.json'),
        ] as $file) {
            if (! File::exists($file)) {
                continue;
            }

            $decoded = json_decode((string) file_get_contents($file), true);
            if (is_array($decoded)) {
                $rows = array_merge($rows, $decoded);
            }
        }

        return $rows;
    }

    private function rowMatchesProduct(array $row, object $product): bool
    {
        $sku = (string) ($row['sku'] ?? '');
        if ($sku !== '' && $sku === (string) $product->sku) {
            return true;
        }

        $name = (string) ($row['name'] ?? $row['product_name'] ?? '');

        return $name !== '' && Str::slug($name) === Str::slug((string) $product->name);
    }

    private function sourceUrl(object $product): ?string
    {
        $meta = json_decode((string) $product->meta, true);
        $url = is_array($meta) ? trim((string) ($meta['source_url'] ?? '')) : '';

        if ($url === '' || ! preg_match('#^https?://#i', $url)) {
            return null;
        }

        return $url;
    }

    private function isValidLocalImagePath(?string $path): bool
    {
        $path = $this->normalizePath($path);
        if ($path === '' || preg_match('#^https?://#i', $path) || str_contains($path, '../')) {
            return false;
        }

        $absolutePath = public_path($path);
        if (! File::exists($absolutePath) || ! File::isFile($absolutePath) || File::size($absolutePath) <= 0) {
            return false;
        }

        return @getimagesize($absolutePath) !== false;
    }

    private function highConfidenceImageUrl(object $product, string $imageUrl, string $sourcePage): bool
    {
        $url = strtolower($imageUrl);
        if (str_contains($url, '/products/images/')) {
            return true;
        }

        $tokens = $this->modelTokens((string) $product->name);
        if ($tokens === []) {
            return false;
        }

        $haystack = Str::slug($imageUrl.' '.$sourcePage);
        $matched = 0;
        foreach ($tokens as $token) {
            if (str_contains($haystack, Str::slug($token))) {
                $matched++;
            }
        }

        return $matched >= min(2, count($tokens));
    }

    private function modelTokens(string $name): array
    {
        preg_match_all('/[A-Za-z0-9]+(?:-[A-Za-z0-9]+)*/', $name, $matches);

        return collect($matches[0] ?? [])
            ->map(fn (string $token): string => trim($token))
            ->filter(fn (string $token): bool => strlen($token) >= 2)
            ->filter(fn (string $token): bool => preg_match('/\d/', $token) === 1 || preg_match('/[A-Z]{2,}/', $token) === 1)
            ->unique()
            ->values()
            ->all();
    }

    private function looksLikeNonProductAsset(string $url): bool
    {
        $url = strtolower($url);

        foreach (['sprite', 'logo', 'icon', 'badge', 'banner', 'payment', 'delivery', 'protected', 'placeholder', 'no-image', 'loading', 'bg-online-ontop', 'ic-image', '/contentmwg/images/'] as $needle) {
            if (str_contains($url, $needle)) {
                return true;
            }
        }

        return false;
    }

    private function extensionFromResponse(string $contentType, string $url): string
    {
        $contentType = strtolower($contentType);
        if (str_contains($contentType, 'webp')) {
            return 'webp';
        }
        if (str_contains($contentType, 'png')) {
            return 'png';
        }

        $extension = strtolower(pathinfo(parse_url($url, PHP_URL_PATH) ?: '', PATHINFO_EXTENSION));

        return in_array($extension, ['jpg', 'jpeg', 'png', 'webp'], true) ? $extension : 'jpg';
    }

    private function normalizeUrl(string $url): string
    {
        $url = html_entity_decode(trim($url), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $url = strtok($url, ' ') ?: $url;

        return rtrim($url, '.,;)"\'');
    }

    private function normalizePath(?string $path): string
    {
        $path = str_replace('\\', '/', trim((string) $path));
        $path = preg_replace('#^/?public/#', '', $path) ?? $path;

        return ltrim($path, '/');
    }

    private function host(string $url): string
    {
        return strtolower((string) parse_url($url, PHP_URL_HOST));
    }

    private function addAuditRow(
        object $product,
        int $currentImageCount,
        int $addedImageCount,
        int $finalImageCount,
        string $sourceUsed,
        array $addedUrls,
        array $skippedUrls,
        string $skipReason,
        string $status
    ): void {
        $this->auditRows[] = [
            'product_id' => $product->id,
            'product_name' => $product->name,
            'brand' => $product->brand_name ?? '',
            'current_image_count' => $currentImageCount,
            'added_image_count' => $addedImageCount,
            'final_image_count' => $finalImageCount,
            'source_used' => $sourceUsed,
            'added_urls' => implode('|', $addedUrls),
            'skipped_urls' => implode('|', $skippedUrls),
            'skip_reason' => $skipReason,
            'status' => $status,
        ];
    }

    private function recordError(object $product, \Throwable $exception): void
    {
        $this->summary['products_error']++;
        $this->errors[] = [
            'product_id' => $product->id ?? null,
            'product_name' => $product->name ?? null,
            'message' => $exception->getMessage(),
            'class' => $exception::class,
        ];

        $this->addAuditRow($product, 0, 0, 0, '', [], [], $exception->getMessage(), 'ERROR');
    }

    private function backupRelatedTables(): string
    {
        $timestamp = Carbon::now()->format('Ymd-His');
        $backupPath = dirname(base_path()).'/_backups/Laravel11_Shop_Website_Full/'.$timestamp.'-before-supplement-gallery';

        File::ensureDirectoryExists($backupPath);
        File::put($backupPath.'/products.json', json_encode(DB::table('products')->get(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        File::put($backupPath.'/product_media.json', json_encode(DB::table('product_media')->get(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        File::put($backupPath.'/README-before-supplement-gallery.txt', "Backup before products:supplement-gallery-images --commit.\nOnly products and product_media are backed up because the command only adds product_media rows and image files.\n");

        return $backupPath;
    }

    private function writeReports(): void
    {
        $dir = base_path('docs/import');
        File::ensureDirectoryExists($dir);

        $auditCsv = $dir.'/supplement-gallery-audit.csv';
        $auditJson = $dir.'/supplement-gallery-audit.json';
        $summaryJson = $dir.'/supplement-gallery-summary.json';
        $errorsJson = $dir.'/supplement-gallery-errors.json';

        $handle = fopen($auditCsv, 'wb');
        fputcsv($handle, self::REPORT_HEADERS);
        foreach ($this->auditRows as $row) {
            fputcsv($handle, array_map(fn (string $header) => $row[$header] ?? '', self::REPORT_HEADERS));
        }
        fclose($handle);

        $this->summary['reports'] = [
            'audit_csv' => $auditCsv,
            'audit_json' => $auditJson,
            'summary_json' => $summaryJson,
            'errors_json' => $errorsJson,
        ];

        File::put($auditJson, json_encode($this->auditRows, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        File::put($summaryJson, json_encode($this->summary, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        File::put($errorsJson, json_encode($this->errors, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }
}
