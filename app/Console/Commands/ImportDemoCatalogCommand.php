<?php

namespace App\Console\Commands;

use App\Infrastructure\Persistence\Eloquent\Catalog\Brand;
use App\Infrastructure\Persistence\Eloquent\Catalog\Product;
use App\Infrastructure\Persistence\Eloquent\Catalog\ProductCategory;
use App\Support\AssetUrl;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class ImportDemoCatalogCommand extends Command
{
    private const SOURCE_DUMMYJSON = 'dummyjson';
    private const DUMMYJSON_PRODUCTS_URL = 'https://dummyjson.com/products';
    private const USD_TO_VND_RATE = 25000;

    protected $signature = 'demo:import-catalog
                            {--source=dummyjson : Demo data source. Currently only dummyjson is supported}
                            {--limit=0 : Maximum number of filtered products to import; 0 imports all}
                            {--categories=smartphones,laptops,tablets,mobile-accessories : Comma-separated DummyJSON category slugs}
                            {--dry-run : Preview creates and updates without writing to the database}';

    protected $description = 'Import demo catalog data from public product APIs';

    private array $productColumns = [];

    private array $categoryDefinitions = [
        'smartphones' => [
            'name' => 'Điện thoại thông minh',
            'slug' => 'dien-thoai-thong-minh',
            'description' => 'Dữ liệu mẫu điện thoại thông minh được nhập từ DummyJSON.',
        ],
        'laptops' => [
            'name' => 'Laptop',
            'slug' => 'laptop',
            'description' => 'Dữ liệu mẫu laptop được nhập từ DummyJSON.',
        ],
        'tablets' => [
            'name' => 'Máy tính bảng',
            'slug' => 'may-tinh-bang',
            'description' => 'Dữ liệu mẫu máy tính bảng được nhập từ DummyJSON.',
        ],
        'mobile-accessories' => [
            'name' => 'Phụ kiện di động',
            'slug' => 'phu-kien-di-dong',
            'description' => 'Dữ liệu mẫu phụ kiện di động được nhập từ DummyJSON.',
        ],
    ];

    public function handle(): int
    {
        $source = Str::lower(trim((string) $this->option('source')));

        if ($source !== self::SOURCE_DUMMYJSON) {
            $this->error("Unsupported source [{$source}]. Currently only [dummyjson] is supported.");

            return self::FAILURE;
        }

        $categories = $this->parseCategoriesOption();
        $limit = max(0, (int) $this->option('limit'));
        $dryRun = (bool) $this->option('dry-run');

        $this->productColumns = Schema::hasTable('products') ? Schema::getColumnListing('products') : [];

        try {
            $products = $this->fetchDummyJsonProducts()
                ->filter(fn (array $product) => in_array((string) ($product['category'] ?? ''), $categories, true))
                ->values();
        } catch (\Throwable $e) {
            $this->error('Unable to fetch demo catalog data: '.$e->getMessage());

            return self::FAILURE;
        }

        if ($limit > 0) {
            $products = $products->take($limit)->values();
        }

        if ($products->isEmpty()) {
            $this->warn('No products matched the selected categories.');

            return self::SUCCESS;
        }

        try {
            $stats = $dryRun
                ? $this->previewImport($products)
                : $this->importProducts($products);
        } catch (\Throwable $e) {
            $this->error('Demo catalog import failed: '.$e->getMessage());

            return self::FAILURE;
        }

        $this->renderSummary($stats, $dryRun);

        return self::SUCCESS;
    }

    private function fetchDummyJsonProducts(): Collection
    {
        $response = Http::timeout(20)
            ->acceptJson()
            ->get(self::DUMMYJSON_PRODUCTS_URL, ['limit' => 0]);

        if ($response->failed()) {
            throw new \RuntimeException('DummyJSON returned HTTP '.$response->status().'.');
        }

        $payload = $response->json();

        if (! is_array($payload) || ! isset($payload['products']) || ! is_array($payload['products'])) {
            throw new \RuntimeException('DummyJSON response did not contain a products array.');
        }

        return collect($payload['products'])
            ->filter(fn ($product) => is_array($product))
            ->values();
    }

    private function importProducts(Collection $products): array
    {
        $stats = $this->emptyStats($products->count());

        DB::transaction(function () use ($products, &$stats): void {
            $brandCache = [];
            $categoryCache = [];

            foreach ($products as $sourceProduct) {
                $sourceCategory = (string) ($sourceProduct['category'] ?? '');
                $category = $categoryCache[$sourceCategory]
                    ??= $this->resolveCategory($sourceCategory, $stats);

                $brandName = $this->brandName($sourceProduct);
                $brandKey = Str::lower($brandName);
                $brand = $brandCache[$brandKey]
                    ??= $this->resolveBrand($brandName, $stats);

                $payload = $this->productPayload($sourceProduct, $brand, $category);
                $product = $this->findImportedProduct($payload['sku'], $payload['slug']);

                if ($product) {
                    $product->fill($payload);
                    $product->save();
                    $stats['products_updated']++;
                } else {
                    $product = Product::create($payload);
                    $stats['products_created']++;
                }

                $this->syncProductCategory($product, $category);
                $this->syncProductMedia($product, $sourceProduct, $stats);
            }
        });

        return $stats;
    }

    private function previewImport(Collection $products): array
    {
        $stats = $this->emptyStats($products->count());

        $products
            ->pluck('category')
            ->filter()
            ->unique()
            ->each(function (string $sourceCategory) use (&$stats): void {
                $definition = $this->categoryDefinition($sourceCategory);
                $category = ProductCategory::query()->where('slug', $definition['slug'])->first();

                if (! $category) {
                    $stats['categories_created']++;
                } elseif (($category->meta['source'] ?? null) === self::SOURCE_DUMMYJSON) {
                    $stats['categories_updated']++;
                } else {
                    $stats['categories_linked']++;
                }
            });

        $products
            ->map(fn (array $product) => $this->brandName($product))
            ->unique(fn (string $brandName) => Str::lower($brandName))
            ->each(function (string $brandName) use (&$stats): void {
                $brand = $this->findBrand($brandName);

                if ($brand) {
                    $stats['brands_linked']++;
                } else {
                    $stats['brands_created']++;
                }
            });

        foreach ($products as $sourceProduct) {
            $sku = $this->internalSku($sourceProduct);
            $slug = $this->internalProductSlug($sourceProduct);
            $product = $this->findImportedProduct($sku, $slug);

            if ($product) {
                $stats['products_updated']++;
            } else {
                $stats['products_created']++;
            }

            foreach ($this->mediaUrls($sourceProduct) as $mediaUrl) {
                if (! $product) {
                    $stats['media_created']++;
                    continue;
                }

                $exists = DB::table('product_media')
                    ->where('product_id', $product->id)
                    ->where('file_path', $mediaUrl)
                    ->exists();

                if ($exists) {
                    $stats['media_updated']++;
                } else {
                    $stats['media_created']++;
                }
            }
        }

        return $stats;
    }

    private function resolveBrand(string $brandName, array &$stats): Brand
    {
        $brand = $this->findBrand($brandName);

        if ($brand) {
            $stats['brands_linked']++;

            return $brand;
        }

        $stats['brands_created']++;

        return Brand::create([
            'name' => $brandName,
            'slug' => $this->uniqueBrandSlug($brandName),
            'code' => null,
            'description' => "Thương hiệu {$brandName} được tạo tự động từ dữ liệu mẫu DummyJSON.",
            'logo_path' => null,
            'is_active' => true,
            'display_order' => $this->nextDisplayOrder('brands'),
            'meta' => [
                'source' => self::SOURCE_DUMMYJSON,
            ],
        ]);
    }

    private function resolveCategory(string $sourceCategory, array &$stats): ProductCategory
    {
        $definition = $this->categoryDefinition($sourceCategory);

        /** @var ProductCategory|null $category */
        $category = ProductCategory::query()
            ->where('slug', $definition['slug'])
            ->first();

        $payload = [
            'name' => $definition['name'],
            'slug' => $definition['slug'],
            'code' => null,
            'parent_id' => null,
            'thumbnail_path' => null,
            'description' => $definition['description'],
            'is_active' => true,
            'display_order' => $this->nextDisplayOrder('product_categories'),
            'meta_title' => $definition['name'],
            'meta_description' => $definition['description'],
            'meta' => [
                'source' => self::SOURCE_DUMMYJSON,
                'source_category' => $sourceCategory,
            ],
        ];

        if (! $category) {
            $stats['categories_created']++;

            return ProductCategory::create($payload);
        }

        if (($category->meta['source'] ?? null) === self::SOURCE_DUMMYJSON) {
            $payload['display_order'] = $category->display_order;
            $category->fill($payload);
            $category->save();
            $stats['categories_updated']++;
        } else {
            $stats['categories_linked']++;
        }

        return $category;
    }

    private function productPayload(array $sourceProduct, Brand $brand, ProductCategory $category): array
    {
        $title = trim((string) ($sourceProduct['title'] ?? 'DummyJSON Product '.$sourceProduct['id']));
        $sourceCategory = (string) ($sourceProduct['category'] ?? '');
        $sourceCategoryName = $this->categoryDefinition($sourceCategory)['name'];
        $price = $this->usdToVnd($sourceProduct['price'] ?? 0);
        $discount = min(100.0, max(0.0, round((float) ($sourceProduct['discountPercentage'] ?? 0), 2)));
        $salePrice = $discount > 0 ? $this->roundVnd($price * (1 - ($discount / 100))) : null;
        $finalPrice = $salePrice ?? $price;

        $payload = [
            'brand_id' => $brand->id,
            'primary_category_id' => $category->id,
            'name' => $title,
            'slug' => $this->internalProductSlug($sourceProduct),
            'sku' => $this->internalSku($sourceProduct),
            'barcode' => data_get($sourceProduct, 'meta.barcode'),
            'unit' => 'Chiếc',
            'price' => $price,
            'sale_price' => $salePrice,
            'cost_price' => $this->roundVnd($finalPrice * 0.72),
            'stock_quantity' => max(0, (int) ($sourceProduct['stock'] ?? 0)),
            'stock_alert_threshold' => 5,
            'is_active' => true,
            'is_featured' => ((float) ($sourceProduct['rating'] ?? 0)) >= 4.5,
            'display_order' => (int) ($sourceProduct['id'] ?? 0),
            'thumbnail_path' => $this->thumbnailUrl($sourceProduct) ?? AssetUrl::PRODUCT_FALLBACK,
            'description' => $this->description($sourceProduct, $sourceCategoryName),
            'content' => $this->content($sourceProduct, $sourceCategoryName),
            'views_count' => $this->viewsCount($sourceProduct),
            'meta' => $this->productMeta($sourceProduct),
            'meta_title' => $title.' | Dữ liệu mẫu',
            'meta_description' => Str::limit(strip_tags($this->description($sourceProduct, $sourceCategoryName)), 180, ''),
        ];

        if ($this->productHasColumn('sale_type')) {
            $payload['sale_type'] = $discount > 0 ? 'percent' : null;
        }

        if ($this->productHasColumn('sale_value')) {
            $payload['sale_value'] = $discount > 0 ? $discount : null;
        }

        if ($this->productHasColumn('is_hot')) {
            $payload['is_hot'] = ((float) ($sourceProduct['rating'] ?? 0)) >= 4.7;
        }

        return $payload;
    }

    private function syncProductCategory(Product $product, ProductCategory $category): void
    {
        $product->categories()->syncWithoutDetaching([$category->id]);

        if ((int) $product->primary_category_id !== (int) $category->id) {
            $product->primary_category_id = $category->id;
            $product->save();
        }
    }

    private function syncProductMedia(Product $product, array $sourceProduct, array &$stats): void
    {
        foreach ($this->mediaUrls($sourceProduct) as $index => $mediaUrl) {
            $now = now();
            $payload = [
                'disk' => 'public',
                'collection' => $index === 0 ? 'thumbnail' : 'gallery',
                'display_order' => $index,
                'meta' => json_encode([
                    'source' => self::SOURCE_DUMMYJSON,
                    'source_id' => $sourceProduct['id'] ?? null,
                ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
                'updated_at' => $now,
            ];

            $existing = DB::table('product_media')
                ->where('product_id', $product->id)
                ->where('file_path', $mediaUrl)
                ->first();

            if ($existing) {
                DB::table('product_media')
                    ->where('id', $existing->id)
                    ->update($payload);

                $stats['media_updated']++;
                continue;
            }

            DB::table('product_media')->insert($payload + [
                'product_id' => $product->id,
                'file_path' => $mediaUrl,
                'created_at' => $now,
            ]);

            $stats['media_created']++;
        }
    }

    private function renderSummary(array $stats, bool $dryRun): void
    {
        $this->info($dryRun ? 'Dry run complete. No data was written.' : 'Demo catalog import completed.');
        $this->line('Products considered: '.$stats['products_total']);

        $this->table(
            ['Resource', 'Create', 'Update', 'Link/Keep'],
            [
                ['Brands', $stats['brands_created'], $stats['brands_updated'], $stats['brands_linked']],
                ['Categories', $stats['categories_created'], $stats['categories_updated'], $stats['categories_linked']],
                ['Products', $stats['products_created'], $stats['products_updated'], 0],
                ['Media', $stats['media_created'], $stats['media_updated'], 0],
            ]
        );
    }

    private function parseCategoriesOption(): array
    {
        $categories = collect(explode(',', (string) $this->option('categories')))
            ->map(fn (string $category) => trim($category))
            ->filter()
            ->unique()
            ->values();

        if ($categories->isEmpty()) {
            return array_keys($this->categoryDefinitions);
        }

        return $categories->all();
    }

    private function categoryDefinition(string $sourceCategory): array
    {
        if (isset($this->categoryDefinitions[$sourceCategory])) {
            return $this->categoryDefinitions[$sourceCategory];
        }

        $name = Str::of($sourceCategory)
            ->replace('-', ' ')
            ->title()
            ->toString();

        return [
            'name' => $name,
            'slug' => 'dummyjson-'.$sourceCategory,
            'description' => "Dữ liệu mẫu nhóm {$name} được nhập từ DummyJSON.",
        ];
    }

    private function findBrand(string $brandName): ?Brand
    {
        $slug = Str::slug($brandName);

        return Brand::query()
            ->where('slug', $slug)
            ->orWhereRaw('LOWER(name) = ?', [Str::lower($brandName)])
            ->first();
    }

    private function findImportedProduct(string $sku, string $slug): ?Product
    {
        return Product::query()
            ->where('sku', $sku)
            ->orWhere('slug', $slug)
            ->first();
    }

    private function brandName(array $sourceProduct): string
    {
        $brand = trim((string) ($sourceProduct['brand'] ?? ''));

        return $brand !== '' ? $brand : 'DummyJSON';
    }

    private function uniqueBrandSlug(string $brandName): string
    {
        $baseSlug = Str::slug($brandName) ?: 'dummyjson-brand';
        $slug = $baseSlug;
        $counter = 2;

        while (Brand::query()->where('slug', $slug)->exists()) {
            $slug = $baseSlug.'-'.$counter;
            $counter++;
        }

        return $slug;
    }

    private function internalProductSlug(array $sourceProduct): string
    {
        $sourceId = (string) ($sourceProduct['id'] ?? Str::random(8));
        $titleSlug = Str::slug((string) ($sourceProduct['title'] ?? 'product')) ?: 'product';

        return Str::limit("dummyjson-{$sourceId}-{$titleSlug}", 250, '');
    }

    private function internalSku(array $sourceProduct): string
    {
        $sourceId = (string) ($sourceProduct['id'] ?? Str::random(8));
        $sourceSku = strtoupper((string) ($sourceProduct['sku'] ?? ''));
        $sourceSku = preg_replace('/[^A-Z0-9]+/', '-', $sourceSku) ?: 'NO-SKU';

        return Str::limit("DJSON-{$sourceId}-{$sourceSku}", 250, '');
    }

    private function thumbnailUrl(array $sourceProduct): ?string
    {
        $thumbnail = trim((string) ($sourceProduct['thumbnail'] ?? ''));

        return $this->isHttpUrl($thumbnail) ? $thumbnail : null;
    }

    private function mediaUrls(array $sourceProduct): array
    {
        $urls = collect([$this->thumbnailUrl($sourceProduct)])
            ->merge(is_array($sourceProduct['images'] ?? null) ? $sourceProduct['images'] : [])
            ->map(fn ($url) => trim((string) $url))
            ->filter(fn (string $url) => $this->isHttpUrl($url))
            ->unique()
            ->values();

        return $urls->all();
    }

    private function description(array $sourceProduct, string $sourceCategoryName): string
    {
        $title = trim((string) ($sourceProduct['title'] ?? 'Sản phẩm mẫu'));
        $description = trim((string) ($sourceProduct['description'] ?? ''));

        return "Sản phẩm mẫu {$title} thuộc nhóm {$sourceCategoryName}. "
            .'Thông tin được lấy từ DummyJSON để phục vụ dữ liệu demo. '
            .$description;
    }

    private function content(array $sourceProduct, string $sourceCategoryName): string
    {
        $title = $this->escape((string) ($sourceProduct['title'] ?? 'Sản phẩm mẫu'));
        $description = $this->escape((string) ($sourceProduct['description'] ?? ''));
        $categoryName = $this->escape($sourceCategoryName);
        $warranty = $this->escape((string) ($sourceProduct['warrantyInformation'] ?? 'Thông tin bảo hành mẫu'));
        $shipping = $this->escape((string) ($sourceProduct['shippingInformation'] ?? 'Thông tin vận chuyển mẫu'));
        $returnPolicy = $this->escape((string) ($sourceProduct['returnPolicy'] ?? 'Chính sách đổi trả mẫu'));

        return <<<HTML
<h2>{$title}</h2>
<p>{$description}</p>
<ul>
    <li>Nhóm sản phẩm: {$categoryName}</li>
    <li>Bảo hành: {$warranty}</li>
    <li>Vận chuyển: {$shipping}</li>
    <li>Đổi trả: {$returnPolicy}</li>
</ul>
HTML;
    }

    private function productMeta(array $sourceProduct): array
    {
        return [
            'source' => self::SOURCE_DUMMYJSON,
            'source_id' => $sourceProduct['id'] ?? null,
            'source_category' => $sourceProduct['category'] ?? null,
            'source_sku' => $sourceProduct['sku'] ?? null,
            'tags' => $sourceProduct['tags'] ?? [],
            'rating' => $sourceProduct['rating'] ?? null,
            'warranty_information' => $sourceProduct['warrantyInformation'] ?? null,
            'shipping_information' => $sourceProduct['shippingInformation'] ?? null,
            'availability_status' => $sourceProduct['availabilityStatus'] ?? null,
            'return_policy' => $sourceProduct['returnPolicy'] ?? null,
            'minimum_order_quantity' => $sourceProduct['minimumOrderQuantity'] ?? null,
            'weight' => $sourceProduct['weight'] ?? null,
            'dimensions' => $sourceProduct['dimensions'] ?? null,
            'original_usd_price' => $sourceProduct['price'] ?? null,
        ];
    }

    private function viewsCount(array $sourceProduct): int
    {
        $rating = (float) ($sourceProduct['rating'] ?? 0);
        $sourceId = (int) ($sourceProduct['id'] ?? 0);

        return max(0, (int) round($rating * 100) + $sourceId);
    }

    private function usdToVnd(mixed $usd): int
    {
        return $this->roundVnd(max(0, (float) $usd) * self::USD_TO_VND_RATE);
    }

    private function roundVnd(float $amount): int
    {
        return (int) round($amount / 1000) * 1000;
    }

    private function nextDisplayOrder(string $table): int
    {
        return ((int) DB::table($table)->max('display_order')) + 1;
    }

    private function productHasColumn(string $column): bool
    {
        return in_array($column, $this->productColumns, true);
    }

    private function isHttpUrl(string $url): bool
    {
        return Str::startsWith($url, ['http://', 'https://'])
            && filter_var($url, FILTER_VALIDATE_URL) !== false;
    }

    private function escape(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    private function emptyStats(int $productsTotal): array
    {
        return [
            'products_total' => $productsTotal,
            'brands_created' => 0,
            'brands_updated' => 0,
            'brands_linked' => 0,
            'categories_created' => 0,
            'categories_updated' => 0,
            'categories_linked' => 0,
            'products_created' => 0,
            'products_updated' => 0,
            'media_created' => 0,
            'media_updated' => 0,
        ];
    }
}
