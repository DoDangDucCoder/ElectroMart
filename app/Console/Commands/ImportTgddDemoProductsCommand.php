<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class ImportTgddDemoProductsCommand extends Command
{
    protected $signature = 'demo:import-tgdd-products
        {--dry-run : Scan and report without writing files or database rows}
        {--limit=36 : Maximum imported/updated demo products}
        {--max-pages=220 : Maximum TGDD pages to scan}';

    protected $description = 'Import a small TGDD-sourced demo catalog with local images and source manifests.';

    private const SOURCE_DOMAIN = 'thegioididong.com';

    private const USAGE_NOTE = 'Dữ liệu/ảnh dùng cho học tập và local demo. Khi public hoặc kinh doanh thật cần thay bằng dữ liệu/ảnh được cấp quyền sử dụng.';

    private array $manifestRows = [];

    private array $stats = [
        'source_pages_scanned' => 0,
        'products_considered' => 0,
        'products_imported' => 0,
        'products_updated' => 0,
        'skipped_missing_image' => 0,
        'skipped_low_confidence' => 0,
        'main_images_downloaded' => 0,
        'gallery_images_downloaded' => 0,
        'failed_downloads' => 0,
    ];

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $limit = max(1, (int) $this->option('limit'));
        $maxPages = max($limit, (int) $this->option('max-pages'));

        $queue = collect($this->seedPages())
            ->map(fn (array $seed) => $seed + ['depth' => 0])
            ->all();

        $seen = [];
        $done = 0;

        while ($queue !== [] && $done < $limit && $this->stats['source_pages_scanned'] < $maxPages) {
            $current = array_shift($queue);
            $url = $this->normalizeUrl($current['url']);

            if (! $url || isset($seen[$url])) {
                continue;
            }

            $seen[$url] = true;
            $html = $this->fetch($url);
            if (! $html) {
                continue;
            }

            $this->stats['source_pages_scanned']++;

            $product = $this->parseProductPage($url, $html, $current);
            if ($product) {
                $this->stats['products_considered']++;

                if ($this->importProduct($product, $dryRun)) {
                    $done++;
                }

                continue;
            }

            if (($current['depth'] ?? 0) >= 1) {
                continue;
            }

            foreach ($this->extractCandidateUrls($html) as $candidateUrl) {
                if (! isset($seen[$candidateUrl])) {
                    $queue[] = [
                        'url' => $candidateUrl,
                        'group' => $current['group'],
                        'category' => $current['category'],
                        'depth' => ((int) ($current['depth'] ?? 0)) + 1,
                    ];
                }
            }
        }

        if (! $dryRun) {
            $this->writeManifest();
        }

        $this->line(json_encode($this->stats, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

        return self::SUCCESS;
    }

    private function seedPages(): array
    {
        return [
            ['url' => 'https://www.thegioididong.com/dtdd', 'group' => 'Phone', 'category' => 'Điện thoại'],
            ['url' => 'https://www.thegioididong.com/laptop', 'group' => 'Laptop', 'category' => 'Laptop'],
            ['url' => 'https://www.thegioididong.com/phu-kien', 'group' => 'Accessories', 'category' => 'Phụ kiện'],
            ['url' => 'https://www.thegioididong.com/tai-nghe', 'group' => 'Audio', 'category' => 'Tai nghe'],
            ['url' => 'https://www.thegioididong.com/loa-laptop', 'group' => 'Audio', 'category' => 'Loa'],
            ['url' => 'https://www.thegioididong.com/thiet-bi-mang', 'group' => 'Network', 'category' => 'Thiết bị mạng'],
            ['url' => 'https://www.thegioididong.com/may-in', 'group' => 'Office', 'category' => 'Máy in'],
            ['url' => 'https://www.thegioididong.com/man-hinh-may-tinh', 'group' => 'Monitor', 'category' => 'Màn hình'],
            ['url' => 'https://www.thegioididong.com/tai-nghe/tai-nghe-bluetooth-apple-airpods-pro-3-trang-mfhp4', 'group' => 'Audio', 'category' => 'Tai nghe'],
            ['url' => 'https://www.thegioididong.com/loa-laptop/loa-bluetooth-anker-soundcore-boom-go-3i', 'group' => 'Audio', 'category' => 'Loa'],
            ['url' => 'https://www.thegioididong.com/loa-laptop/loa-bluetooth-jbl-grip', 'group' => 'Audio', 'category' => 'Loa'],
            ['url' => 'https://www.thegioididong.com/dtdd/iphone-15-plus', 'group' => 'Phone', 'category' => 'Điện thoại'],
            ['url' => 'https://www.thegioididong.com/dtdd/samsung-galaxy-z-flip7', 'group' => 'Phone', 'category' => 'Điện thoại'],
            ['url' => 'https://www.thegioididong.com/dtdd/samsung-galaxy-s26-ultra-12gb-256gb', 'group' => 'Phone', 'category' => 'Điện thoại'],
        ];
    }

    private function fetch(string $url): ?string
    {
        try {
            $response = Http::timeout(18)
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) DangDucLocalDemo/1.0',
                    'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                ])
                ->get($url);

            if (! $response->successful()) {
                return null;
            }

            return html_entity_decode((string) $response->body(), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        } catch (\Throwable) {
            return null;
        }
    }

    private function parseProductPage(string $url, string $html, array $context): ?array
    {
        if (! str_contains($html, "pageType: 'Product'") && ! str_contains($html, 'gtmViewItemV2')) {
            return null;
        }

        $name = $this->matchFirst($html, '/item_name:\s*"([^"]+)"/u')
            ?? $this->matchFirst($html, '/<h1[^>]*>(.*?)<\/h1>/isu')
            ?? $this->titleFromUrl($url);
        $name = trim(strip_tags($name));

        if ($name === '') {
            $this->stats['skipped_low_confidence']++;

            return null;
        }

        $brand = $this->cleanBrand($this->matchFirst($html, '/item_brand:\s*"([^"]+)"/u') ?? $this->guessBrand($name));
        $category = trim($this->matchFirst($html, '/item_category:\s*"([^"]+)"/u') ?? ($context['category'] ?? 'Sản phẩm'));
        $price = $this->parsePrice($html, $category);
        $slug = 'tgdd-' . Str::slug($name);
        $images = $this->extractImages($html);

        if ($images === []) {
            $this->stats['skipped_missing_image']++;

            return null;
        }

        return [
            'source_url' => $url,
            'source_domain' => self::SOURCE_DOMAIN,
            'name' => $name,
            'slug' => $slug,
            'sku' => 'TGDD-' . Str::upper(Str::substr(md5($url), 0, 10)),
            'brand' => $brand ?: 'Demo',
            'category' => $category ?: ($context['category'] ?? 'Sản phẩm'),
            'group' => $context['group'] ?? 'Demo',
            'price' => $price,
            'sale_price' => (int) round($price * 0.94 / 1000) * 1000,
            'images' => array_slice($images, 0, 5),
        ];
    }

    private function importProduct(array $product, bool $dryRun): bool
    {
        $downloaded = [];
        foreach ($product['images'] as $index => $imageUrl) {
            $role = $index === 0 ? 'main' : 'gallery';
            $path = $dryRun
                ? 'public/assets/demo/tgdd/products/' . $product['slug'] . '/' . ($role === 'main' ? 'main.jpg' : 'gallery-' . $index . '.jpg')
                : $this->downloadImage($imageUrl, $product['slug'], $role, $index);

            if (! $path) {
                $this->stats['failed_downloads']++;
                continue;
            }

            $downloaded[] = [
                'source_url' => $imageUrl,
                'local_image_path' => $path,
                'image_role' => $role,
            ];
        }

        if ($downloaded === [] || ($downloaded[0]['image_role'] ?? null) !== 'main') {
            $this->stats['skipped_missing_image']++;

            return false;
        }

        foreach ($downloaded as $row) {
            $this->manifestRows[] = [
                'source_domain' => self::SOURCE_DOMAIN,
                'source_url' => $product['source_url'],
                'product_name' => $product['name'],
                'brand' => $product['brand'],
                'category' => $product['category'],
                'local_image_path' => $row['local_image_path'],
                'image_role' => $row['image_role'],
                'confidence' => 'high',
                'usage_note' => self::USAGE_NOTE,
                'image_source_url' => $row['source_url'],
            ];
        }

        if ($dryRun) {
            return true;
        }

        $brandId = $this->ensureBrand($product['brand']);
        $categoryId = $this->ensureCategory($product['category'], $product['group']);
        $now = now();
        $existing = DB::table('products')->where('slug', $product['slug'])->first();
        $meta = [
            'source_domain' => self::SOURCE_DOMAIN,
            'source_url' => $product['source_url'],
            'usage_note' => self::USAGE_NOTE,
            'tags' => [$product['group'], $product['brand'], $product['category'], 'TGDD demo'],
            'demo_source' => 'tgdd-pass-5-5',
        ];
        $payload = [
            'brand_id' => $brandId,
            'primary_category_id' => $categoryId,
            'name' => $product['name'],
            'slug' => $product['slug'],
            'sku' => $product['sku'],
            'unit' => 'cái',
            'price' => $product['price'],
            'sale_type' => 'fixed',
            'sale_value' => max(0, $product['price'] - $product['sale_price']),
            'sale_price' => $product['sale_price'],
            'cost_price' => (int) round($product['sale_price'] * 0.82),
            'stock_quantity' => 20,
            'stock_alert_threshold' => 3,
            'is_active' => false,
            'is_featured' => true,
            'is_hot' => true,
            'thumbnail_path' => Str::after($downloaded[0]['local_image_path'], 'public/'),
            'description' => 'Dữ liệu mẫu local demo tham khảo từ TGDD cho mục đích học tập và kiểm thử giao diện.',
            'content' => '<p>' . e($product['name']) . ' là sản phẩm demo dùng để kiểm thử giao diện shop. Khi public hoặc kinh doanh thật cần thay bằng dữ liệu được cấp quyền.</p>',
            'meta' => json_encode($meta, JSON_UNESCAPED_UNICODE),
            'meta_title' => $product['name'],
            'meta_description' => 'Sản phẩm demo local tham khảo từ TGDD.',
            'updated_at' => $now,
        ];

        if ($existing) {
            DB::table('products')->where('id', $existing->id)->update($payload);
            $productId = $existing->id;
            $this->stats['products_updated']++;
        } else {
            $payload['created_at'] = $now;
            $productId = DB::table('products')->insertGetId($payload);
            $this->stats['products_imported']++;
        }

        DB::table('product_category_product')->updateOrInsert(
            ['product_id' => $productId, 'product_category_id' => $categoryId],
            ['updated_at' => $now, 'created_at' => $now]
        );

        foreach (array_slice($downloaded, 1) as $displayOrder => $image) {
            $filePath = Str::after($image['local_image_path'], 'public/');
            DB::table('product_media')->updateOrInsert(
                ['product_id' => $productId, 'file_path' => $filePath],
                [
                    'disk' => 'public',
                    'collection' => 'gallery',
                    'display_order' => $displayOrder + 1,
                    'meta' => json_encode([
                        'source_url' => $product['source_url'],
                        'image_source_url' => $image['source_url'],
                        'source_domain' => self::SOURCE_DOMAIN,
                        'usage_note' => self::USAGE_NOTE,
                        'confidence' => 'high',
                    ], JSON_UNESCAPED_UNICODE),
                    'updated_at' => $now,
                    'created_at' => $now,
                ]
            );
        }

        return true;
    }

    private function downloadImage(string $url, string $slug, string $role, int $index): ?string
    {
        try {
            $response = Http::timeout(20)
                ->withHeaders(['User-Agent' => 'Mozilla/5.0 DangDucLocalDemo/1.0'])
                ->get($url);

            if (! $response->successful() || strlen($response->body()) < 1024) {
                return null;
            }

            $contentType = strtolower($response->header('Content-Type', ''));
            $extension = str_contains($contentType, 'webp') ? 'webp' : (str_contains($contentType, 'png') ? 'png' : 'jpg');
            $dir = public_path('assets/demo/tgdd/products/' . $slug);
            if (! is_dir($dir)) {
                mkdir($dir, 0775, true);
            }

            $name = $role === 'main' ? 'main.' . $extension : 'gallery-' . $index . '.' . $extension;
            $absolutePath = $dir . DIRECTORY_SEPARATOR . $name;
            file_put_contents($absolutePath, $response->body());

            if (! file_exists($absolutePath) || filesize($absolutePath) < 1024) {
                return null;
            }

            if ($role === 'main') {
                $this->stats['main_images_downloaded']++;
            } else {
                $this->stats['gallery_images_downloaded']++;
            }

            return 'public/assets/demo/tgdd/products/' . $slug . '/' . $name;
        } catch (\Throwable) {
            return null;
        }
    }

    private function ensureBrand(string $name): int
    {
        $slug = Str::slug($name);
        $existing = DB::table('brands')->where('slug', $slug)->first();
        if ($existing) {
            return $existing->id;
        }

        return DB::table('brands')->insertGetId([
            'name' => $name,
            'slug' => $slug,
            'code' => $this->uniqueShortCode('BR', $slug),
            'is_active' => true,
            'display_order' => 90,
            'meta' => json_encode(['demo_source' => 'tgdd-pass-5-5'], JSON_UNESCAPED_UNICODE),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function ensureCategory(string $name, string $group): int
    {
        $slug = Str::slug($name);
        $existing = DB::table('product_categories')->where('slug', $slug)->first();
        if ($existing) {
            return $existing->id;
        }

        return DB::table('product_categories')->insertGetId([
            'name' => $name,
            'slug' => $slug,
            'code' => $this->uniqueShortCode('CT', $slug),
            'description' => 'Danh mục demo TGDD dùng cho local learning.',
            'is_active' => true,
            'display_order' => 90,
            'meta' => json_encode(['demo_source' => 'tgdd-pass-5-5'], JSON_UNESCAPED_UNICODE),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function writeManifest(): void
    {
        $dir = base_path('docs/import');
        if (! is_dir($dir)) {
            mkdir($dir, 0775, true);
        }

        $jsonPath = $dir . '/tgdd-demo-products-manifest.json';
        $csvPath = $dir . '/tgdd-demo-products-manifest.csv';
        $existing = file_exists($jsonPath) ? json_decode((string) file_get_contents($jsonPath), true) : [];
        $rows = collect(is_array($existing) ? $existing : [])
            ->merge($this->manifestRows)
            ->unique(fn ($row) => ($row['source_url'] ?? '') . '|' . ($row['local_image_path'] ?? '') . '|' . ($row['image_role'] ?? ''))
            ->values()
            ->all();

        file_put_contents($jsonPath, json_encode($rows, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

        $handle = fopen($csvPath, 'wb');
        $headers = ['source_domain', 'source_url', 'product_name', 'brand', 'category', 'local_image_path', 'image_role', 'confidence', 'usage_note', 'image_source_url'];
        fputcsv($handle, $headers);
        foreach ($rows as $row) {
            fputcsv($handle, array_map(fn ($header) => $row[$header] ?? '', $headers));
        }
        fclose($handle);
    }

    private function extractCandidateUrls(string $html): array
    {
        preg_match_all('#https://www\.thegioididong\.com/(dtdd|laptop|laptop-[^"\'<\s]+|phu-kien|tai-nghe|loa-laptop|thiet-bi-mang|may-in|man-hinh-may-tinh|chuot-may-tinh|ban-phim)[^"\'<\s]*#u', $html, $matches);

        return collect($matches[0] ?? [])
            ->map(fn ($url) => $this->normalizeUrl($url))
            ->filter()
            ->reject(fn ($url) => str_contains($url, '/tin-tuc') || str_contains($url, '/hoi-dap'))
            ->unique()
            ->values()
            ->all();
    }

    private function uniqueShortCode(string $prefix, string $slug): string
    {
        return Str::upper(Str::substr($prefix . md5($slug), 0, 12));
    }

    private function extractImages(string $html): array
    {
        $html = str_replace('\\/', '/', $html);
        preg_match_all('#https?://[^"\'\s<>]+(?:cdn|tgdd|thegioididong)[^"\'\s<>]+\.(?:jpg|jpeg|png|webp)(?:\?[^"\'\s<>]*)?#iu', $html, $matches);

        return collect($matches[0] ?? [])
            ->map(fn ($url) => $this->normalizeImageUrl($url))
            ->filter()
            ->reject(fn ($url) => str_contains($url, 'logo') || str_contains($url, 'icon') || str_contains($url, 'loading'))
            ->unique()
            ->take(8)
            ->values()
            ->all();
    }

    private function normalizeUrl(?string $url): ?string
    {
        $url = trim((string) $url);
        $url = html_entity_decode($url, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $url = strtok($url, '?#') ?: $url;

        return str_starts_with($url, 'https://www.thegioididong.com/') ? $url : null;
    }

    private function normalizeImageUrl(string $url): ?string
    {
        $url = html_entity_decode(trim($url), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $url = strtok($url, ' ') ?: $url;

        return filter_var($url, FILTER_VALIDATE_URL) ? $url : null;
    }

    private function parsePrice(string $html, string $category): int
    {
        $patterns = [
            '/price:\s*"?([0-9]{6,})"?/u',
            '/"price"\s*:\s*"?([0-9]{6,})"?/u',
            '/data-price\s*=\s*"([0-9]{6,})"/u',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $html, $match)) {
                return max(100000, (int) $match[1]);
            }
        }

        return match (Str::lower($category)) {
            'laptop' => 16990000,
            'điện thoại' => 12990000,
            'màn hình' => 4990000,
            'máy in' => 3990000,
            'thiết bị mạng' => 1590000,
            'tai nghe', 'loa' => 1290000,
            default => 790000,
        };
    }

    private function matchFirst(string $html, string $pattern): ?string
    {
        return preg_match($pattern, $html, $match) ? trim($match[1]) : null;
    }

    private function titleFromUrl(string $url): string
    {
        return Str::headline(str_replace('-', ' ', basename(parse_url($url, PHP_URL_PATH) ?: 'san-pham-demo')));
    }

    private function cleanBrand(string $brand): string
    {
        return trim(preg_replace('/\s*\([^)]*\)/u', '', $brand));
    }

    private function guessBrand(string $name): string
    {
        $brands = ['Apple', 'Samsung', 'ASUS', 'Acer', 'HP', 'Dell', 'Lenovo', 'Anker', 'Baseus', 'JBL', 'Xiaomi', 'OPPO', 'Vivo', 'Logitech', 'TP-Link', 'Canon', 'Epson', 'Brother'];
        foreach ($brands as $brand) {
            if (Str::contains(Str::lower($name), Str::lower($brand))) {
                return $brand;
            }
        }

        return 'Demo';
    }
}
