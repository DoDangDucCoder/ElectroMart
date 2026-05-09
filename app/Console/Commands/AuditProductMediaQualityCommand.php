<?php

namespace App\Console\Commands;

use App\Support\ProductMediaQuality;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class AuditProductMediaQualityCommand extends Command
{
    protected $signature = 'products:audit-media-quality {--min-gallery=4 : Minimum gallery images required for storefront visibility}';

    protected $description = 'Audit product main/gallery media quality and write storefront visibility reports.';

    public function handle(): int
    {
        $minGallery = max(1, (int) $this->option('min-gallery'));
        $audit = ProductMediaQuality::audit($minGallery);
        $reportDir = base_path('docs/import');

        File::ensureDirectoryExists($reportDir);

        $auditPath = $reportDir.'/product-media-quality-audit.csv';
        $summaryPath = $reportDir.'/product-media-quality-summary.json';
        $errorsPath = $reportDir.'/product-media-quality-errors.json';

        $this->writeCsv($auditPath, $audit['rows']->all());
        File::put($summaryPath, json_encode($audit['summary'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        File::put($errorsPath, json_encode($audit['errors'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

        $this->info('Product media quality reports written.');
        $this->line('Audit: '.$auditPath);
        $this->line('Summary: '.$summaryPath);
        $this->line('Errors: '.$errorsPath);
        $this->table(
            ['Metric', 'Value'],
            collect($audit['summary'])->map(fn ($value, $key) => [$key, $value])->values()->all()
        );

        return self::SUCCESS;
    }

    private function writeCsv(string $path, array $rows): void
    {
        $handle = fopen($path, 'wb');

        if ($handle === false) {
            throw new \RuntimeException('Unable to write CSV report: '.$path);
        }

        $headers = [
            'product_id',
            'slug',
            'name',
            'is_active',
            'main_image',
            'has_main_image',
            'existing_gallery_images',
            'has_4_gallery_images',
            'has_5_gallery_images',
            'broken_image_files',
            'is_visible',
            'hidden_reason',
        ];

        fputcsv($handle, $headers);

        foreach ($rows as $row) {
            fputcsv($handle, array_map(
                fn (string $header) => is_bool($row[$header] ?? null) ? ((bool) $row[$header] ? '1' : '0') : ($row[$header] ?? ''),
                $headers
            ));
        }

        fclose($handle);
    }
}
