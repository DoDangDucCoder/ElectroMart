<?php

namespace App\Console\Commands;

use App\Support\AssetUrl;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\Process;

class OptimizeProductImagesCommand extends Command
{
    protected $signature = 'products:optimize-images
        {--dry-run : Audit only; this is also the default when --commit is absent}
        {--commit : Generate optimized image variants}
        {--limit= : Limit number of source images to audit/process}
        {--product-id= : Process images for a single product id}
        {--min-bytes=204800 : Skip source images smaller than this size unless --force is used}
        {--quality=82 : WebP quality from 1 to 100}
        {--force : Recreate variants even when they already exist}
        {--without-gallery : Only process products.thumbnail_path}
        {--ffmpeg= : Absolute path to ffmpeg when it is not on PATH}';

    protected $description = 'Audit and generate optimized WebP variants for product images without changing source files or database rows.';

    private const REPORT_HEADERS = [
        'source_path',
        'source_type',
        'product_id',
        'source_bytes',
        'source_width',
        'source_height',
        'variant',
        'target_width',
        'optimized_path',
        'optimized_bytes',
        'saved_bytes',
        'status',
        'message',
    ];

    private array $rows = [];

    private array $summary = [
        'dry_run' => true,
        'commit' => false,
        'driver' => null,
        'sources_audited' => 0,
        'variants_planned' => 0,
        'variants_created' => 0,
        'variants_existing' => 0,
        'variants_skipped' => 0,
        'variants_error' => 0,
        'estimated_source_bytes' => 0,
        'optimized_bytes' => 0,
        'saved_bytes' => 0,
        'reports' => [],
    ];

    public function handle(): int
    {
        if (! $this->validateSchema()) {
            return self::FAILURE;
        }

        $commit = (bool) $this->option('commit') && ! (bool) $this->option('dry-run');
        $dryRun = ! $commit;
        $limit = $this->option('limit') !== null ? max(1, (int) $this->option('limit')) : null;
        $productId = $this->option('product-id') !== null ? (int) $this->option('product-id') : null;
        $minBytes = max(0, (int) $this->option('min-bytes'));
        $quality = max(1, min(100, (int) $this->option('quality')));
        $force = (bool) $this->option('force');
        $ffmpeg = $this->resolveFfmpegPath();

        $this->summary['dry_run'] = $dryRun;
        $this->summary['commit'] = $commit;
        $this->summary['driver'] = $ffmpeg ? 'ffmpeg' : null;

        if ($commit && $ffmpeg === null) {
            $this->error('ffmpeg was not found. Install ffmpeg or pass --ffmpeg=PATH. Dry-run still works.');

            return self::FAILURE;
        }

        foreach ($this->imageSources($productId, $limit) as $source) {
            $this->processSource($source, $dryRun, $minBytes, $quality, $force, $ffmpeg);
        }

        $this->writeReports();
        $this->line(json_encode($this->summary, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        return $this->summary['variants_error'] > 0 ? self::FAILURE : self::SUCCESS;
    }

    private function validateSchema(): bool
    {
        if (! Schema::hasTable('products')) {
            $this->error('Missing required table: products');

            return false;
        }

        foreach (['id', 'thumbnail_path'] as $column) {
            if (! Schema::hasColumn('products', $column)) {
                $this->error("Missing required products column: {$column}");

                return false;
            }
        }

        if (! (bool) $this->option('without-gallery')) {
            if (! Schema::hasTable('product_media')) {
                $this->error('Missing required table: product_media');

                return false;
            }

            foreach (['product_id', 'file_path'] as $column) {
                if (! Schema::hasColumn('product_media', $column)) {
                    $this->error("Missing required product_media column: {$column}");

                    return false;
                }
            }
        }

        return true;
    }

    private function imageSources(?int $productId, ?int $limit): Collection
    {
        $sources = collect();

        $products = DB::table('products')
            ->select([
                'id as product_id',
                DB::raw("'thumbnail' as source_type"),
                'thumbnail_path as source_path',
            ])
            ->whereNotNull('thumbnail_path')
            ->where('thumbnail_path', '!=', '');

        if ($productId !== null) {
            $products->where('id', $productId);
        }

        $sources = $sources->merge($products->get());

        if (! (bool) $this->option('without-gallery')) {
            $media = DB::table('product_media')
                ->select([
                    'product_id',
                    DB::raw("'gallery' as source_type"),
                    'file_path as source_path',
                ])
                ->whereNotNull('file_path')
                ->where('file_path', '!=', '');

            if ($productId !== null) {
                $media->where('product_id', $productId);
            }

            $sources = $sources->merge($media->get());
        }

        return $sources
            ->map(fn ($source) => (object) [
                'product_id' => (int) $source->product_id,
                'source_type' => (string) $source->source_type,
                'source_path' => $this->normalizeLocalPath($source->source_path),
            ])
            ->filter(fn ($source) => $source->source_path !== null)
            ->unique('source_path')
            ->when($limit !== null, fn (Collection $items) => $items->take($limit))
            ->values();
    }

    private function processSource(object $source, bool $dryRun, int $minBytes, int $quality, bool $force, ?string $ffmpeg): void
    {
        $this->summary['sources_audited']++;

        $absolutePath = public_path($source->source_path);
        $sourceBytes = is_file($absolutePath) ? (int) filesize($absolutePath) : 0;
        $imageInfo = is_file($absolutePath) ? @getimagesize($absolutePath) : false;
        $width = is_array($imageInfo) ? (int) ($imageInfo[0] ?? 0) : 0;
        $height = is_array($imageInfo) ? (int) ($imageInfo[1] ?? 0) : 0;

        if (! is_file($absolutePath) || $imageInfo === false || $width <= 0 || $height <= 0) {
            $this->recordRowsForAllVariants($source, $sourceBytes, $width, $height, 'skipped', 'Source image is missing or unreadable.');

            return;
        }

        if (! $force && $sourceBytes < $minBytes) {
            $this->recordRowsForAllVariants($source, $sourceBytes, $width, $height, 'skipped_small', 'Source image is below min-bytes.');

            return;
        }

        $this->summary['estimated_source_bytes'] += $sourceBytes;

        foreach (AssetUrl::PRODUCT_IMAGE_VARIANTS as $variant => $targetWidth) {
            $this->summary['variants_planned']++;

            $optimizedPath = AssetUrl::optimizedProductPath($source->source_path, $variant);

            if ($optimizedPath === null) {
                $this->recordRow($source, $sourceBytes, $width, $height, $variant, $targetWidth, null, null, 'skipped', 'Unsupported source image extension.');

                continue;
            }

            $optimizedAbsolutePath = public_path($optimizedPath);

            if (! $force && is_file($optimizedAbsolutePath)) {
                $optimizedBytes = (int) filesize($optimizedAbsolutePath);
                $this->summary['variants_existing']++;
                $this->summary['optimized_bytes'] += $optimizedBytes;
                $this->summary['saved_bytes'] += max(0, $sourceBytes - $optimizedBytes);
                $this->recordRow($source, $sourceBytes, $width, $height, $variant, $targetWidth, $optimizedPath, $optimizedBytes, 'exists', 'Optimized variant already exists.');

                continue;
            }

            if ($dryRun) {
                $this->recordRow($source, $sourceBytes, $width, $height, $variant, $targetWidth, $optimizedPath, null, 'planned', 'Would generate optimized WebP variant.');

                continue;
            }

            $created = $this->generateWithFfmpeg($ffmpeg, $absolutePath, $optimizedAbsolutePath, min($targetWidth, $width), $quality);

            if (! $created || ! is_file($optimizedAbsolutePath)) {
                $this->summary['variants_error']++;
                $this->recordRow($source, $sourceBytes, $width, $height, $variant, $targetWidth, $optimizedPath, null, 'error', 'ffmpeg failed to create variant.');

                continue;
            }

            $optimizedBytes = (int) filesize($optimizedAbsolutePath);
            $this->summary['variants_created']++;
            $this->summary['optimized_bytes'] += $optimizedBytes;
            $this->summary['saved_bytes'] += max(0, $sourceBytes - $optimizedBytes);
            $this->recordRow($source, $sourceBytes, $width, $height, $variant, $targetWidth, $optimizedPath, $optimizedBytes, 'created', 'Optimized variant created.');
        }
    }

    private function recordRowsForAllVariants(object $source, int $sourceBytes, int $width, int $height, string $status, string $message): void
    {
        foreach (AssetUrl::PRODUCT_IMAGE_VARIANTS as $variant => $targetWidth) {
            $this->recordRow($source, $sourceBytes, $width, $height, $variant, $targetWidth, AssetUrl::optimizedProductPath($source->source_path, $variant), null, $status, $message);
        }
    }

    private function recordRow(
        object $source,
        int $sourceBytes,
        int $width,
        int $height,
        string $variant,
        int $targetWidth,
        ?string $optimizedPath,
        ?int $optimizedBytes,
        string $status,
        string $message
    ): void {
        if (str_starts_with($status, 'skipped')) {
            $this->summary['variants_skipped']++;
        }

        $this->rows[] = [
            'source_path' => $source->source_path,
            'source_type' => $source->source_type,
            'product_id' => $source->product_id,
            'source_bytes' => $sourceBytes,
            'source_width' => $width,
            'source_height' => $height,
            'variant' => $variant,
            'target_width' => $targetWidth,
            'optimized_path' => $optimizedPath,
            'optimized_bytes' => $optimizedBytes,
            'saved_bytes' => $optimizedBytes !== null ? max(0, $sourceBytes - $optimizedBytes) : null,
            'status' => $status,
            'message' => $message,
        ];
    }

    private function generateWithFfmpeg(?string $ffmpeg, string $sourcePath, string $targetPath, int $targetWidth, int $quality): bool
    {
        if ($ffmpeg === null) {
            return false;
        }

        File::ensureDirectoryExists(dirname($targetPath));

        $process = new Process([
            $ffmpeg,
            '-y',
            '-hide_banner',
            '-loglevel',
            'error',
            '-i',
            $sourcePath,
            '-vf',
            'scale='.$targetWidth.':-2',
            '-frames:v',
            '1',
            '-c:v',
            'libwebp',
            '-quality',
            (string) $quality,
            $targetPath,
        ]);
        $process->setTimeout(120);
        $process->run();

        return $process->isSuccessful();
    }

    private function resolveFfmpegPath(): ?string
    {
        $configured = trim((string) ($this->option('ffmpeg') ?? ''));

        if ($configured !== '' && is_file($configured)) {
            return $configured;
        }

        return (new ExecutableFinder())->find('ffmpeg');
    }

    private function normalizeLocalPath(?string $path): ?string
    {
        $path = trim((string) $path);

        if ($path === '' || AssetUrl::isAbsoluteUrl($path)) {
            return null;
        }

        $path = str_replace('\\', '/', ltrim($path, '/\\'));

        if (str_contains($path, '/_optimized/')) {
            return null;
        }

        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        if (! in_array($extension, ['jpg', 'jpeg', 'png', 'webp'], true)) {
            return null;
        }

        return $path;
    }

    private function writeReports(): void
    {
        $directory = base_path('docs/import');
        File::ensureDirectoryExists($directory);

        $stamp = Carbon::now()->format('Ymd-His').'-'.substr(str_replace('.', '', (string) microtime(true)), -6);
        $csvPath = $directory.'/optimize-product-images-audit-'.$stamp.'.csv';
        $jsonPath = $directory.'/optimize-product-images-audit-'.$stamp.'.json';
        $summaryPath = $directory.'/optimize-product-images-summary-'.$stamp.'.json';

        $handle = fopen($csvPath, 'wb');
        if ($handle !== false) {
            fputcsv($handle, self::REPORT_HEADERS);

            foreach ($this->rows as $row) {
                fputcsv($handle, array_map(fn ($header) => $row[$header] ?? null, self::REPORT_HEADERS));
            }

            fclose($handle);
        }

        File::put($jsonPath, json_encode($this->rows, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        $this->summary['reports'] = [
            'csv' => str_replace('\\', '/', $csvPath),
            'json' => str_replace('\\', '/', $jsonPath),
            'summary' => str_replace('\\', '/', $summaryPath),
        ];
        File::put($summaryPath, json_encode($this->summary, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }
}
