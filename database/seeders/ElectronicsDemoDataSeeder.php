<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ElectronicsDemoDataSeeder extends Seeder
{
    private const DEMO_PASSWORD = 'password123';

    public function run(): void
    {
        DB::transaction(function (): void {
            $brands = $this->seedBrands();
            $categories = $this->seedCategories();
            $products = $this->seedProducts($brands, $categories);
            $this->seedVariants($products);
            $customers = $this->seedCustomers();
            $this->seedCoupons();
            $this->seedBlogContent();
            $this->seedOrders($customers, $products);
        });
    }

    private function seedBrands(): array
    {
        $now = now();
        $brands = [
            ['NovaTech', 'Thiết bị cá nhân hiện đại cho học tập, làm việc và giải trí.'],
            ['VoltEdge', 'Phụ kiện sạc, nguồn và thiết bị kết nối bền bỉ.'],
            ['PixelCore', 'Màn hình, laptop và thiết bị sáng tạo nội dung.'],
            ['AeroByte', 'Laptop mỏng nhẹ, router và phụ kiện di động.'],
            ['Zenova', 'Thiết bị văn phòng gọn gàng, dễ sử dụng.'],
            ['HyperLink', 'Router, mesh Wi-Fi và phụ kiện mạng cho gia đình.'],
            ['MaxGear', 'Gaming gear, bàn phím, chuột và tai nghe hiệu năng cao.'],
            ['SonicWave', 'Tai nghe, loa và thiết bị âm thanh cá nhân.'],
            ['BrightView', 'Màn hình, camera và thiết bị hiển thị.'],
            ['CoreMate', 'Linh kiện PC và phụ kiện nâng cấp máy tính.'],
            ['TerraChip', 'SSD, RAM và linh kiện lưu trữ cho người dùng phổ thông.'],
            ['LumaDesk', 'Thiết bị hỗ trợ góc làm việc tại nhà và văn phòng nhỏ.'],
        ];

        foreach ($brands as $index => [$name, $description]) {
            $slug = Str::slug($name);
            DB::table('brands')->updateOrInsert(
                ['slug' => $slug],
                [
                    'name' => $name,
                    'slug' => $slug,
                    'code' => Str::upper(Str::replace('-', '', $slug)),
                    'logo_path' => 'uploads/brands/demo-'.$slug.'.svg',
                    'description' => $description,
                    'is_active' => true,
                    'display_order' => 50 + $index,
                    'meta' => $this->json([
                        'source' => 'electronics_demo',
                        'fictional' => true,
                    ]),
                    'deleted_at' => null,
                    'updated_at' => $now,
                    'created_at' => DB::table('brands')->where('slug', $slug)->value('created_at') ?? $now,
                ]
            );
        }

        return DB::table('brands')
            ->whereIn('slug', collect($brands)->map(fn ($brand) => Str::slug($brand[0]))->all())
            ->pluck('id', 'slug')
            ->all();
    }

    private function seedCategories(): array
    {
        $now = now();
        $this->restoreAccidentallyTaggedSeedCategories();
        $this->normalizeLegacyDemoCategorySlugs();
        $this->deleteObsoleteDemoCategoryGroups();
        $parents = [];

        foreach ($parents as $index => [$name, $slug, $description]) {
            DB::table('product_categories')->updateOrInsert(
                ['slug' => $slug],
                [
                    'name' => $name,
                    'slug' => $slug,
                    'code' => 'DEMO_'.Str::upper(Str::replace('-', '_', $slug)),
                    'parent_id' => null,
                    'thumbnail_path' => 'uploads/categories/demo-'.$slug.'.jpg',
                    'description' => $description,
                    'is_active' => true,
                    'display_order' => 100 + $index,
                    'meta_title' => $name.' chính hãng và dữ liệu demo',
                    'meta_description' => $description,
                    'meta' => $this->json(['source' => 'electronics_demo']),
                    'deleted_at' => null,
                    'updated_at' => $now,
                    'created_at' => DB::table('product_categories')->where('slug', $slug)->value('created_at') ?? $now,
                ]
            );
        }

        $parentIds = DB::table('product_categories')
            ->whereIn('slug', collect($parents)->pluck(1)->all())
            ->pluck('id', 'slug')
            ->all();

        $children = [
            ['Ultrabook Demo', 'demo-ultrabook', 'demo-laptop', 'Laptop mỏng nhẹ cho di chuyển nhiều.'],
            ['Laptop Gaming Demo', 'demo-laptop-gaming', 'demo-laptop', 'Laptop cấu hình cao cho chơi game và đồ họa.'],
            ['Workstation Mobile Demo', 'demo-workstation-mobile', 'demo-laptop', 'Laptop hiệu năng cao cho kỹ thuật và sáng tạo.'],
            ['Smartphone Phổ Thông Demo', 'demo-smartphone-pho-thong', 'demo-smartphone', 'Điện thoại dễ dùng, pin tốt, giá hợp lý.'],
            ['Smartphone Cao Cấp Demo', 'demo-smartphone-cao-cap', 'demo-smartphone', 'Điện thoại hiệu năng cao, camera tốt.'],
            ['Tablet Học Tập Demo', 'demo-tablet-hoc-tap', 'demo-tablet', 'Máy tính bảng cho ghi chú và học online.'],
            ['Màn Hình Văn Phòng Demo', 'demo-man-hinh-van-phong', 'demo-monitor', 'Màn hình tối ưu cho công việc hằng ngày.'],
            ['Màn Hình Gaming Demo', 'demo-man-hinh-gaming', 'demo-monitor', 'Màn hình tần số quét cao cho game.'],
            ['Keyboard Demo', 'demo-keyboard', 'demo-gaming-gear', 'Bàn phím cơ và bàn phím văn phòng.'],
            ['Mouse Demo', 'demo-mouse', 'demo-gaming-gear', 'Chuột không dây, chuột gaming và chuột văn phòng.'],
            ['Headphone Demo', 'demo-headphone', 'demo-audio', 'Tai nghe chụp tai và tai nghe không dây.'],
            ['Speaker Demo', 'demo-speaker', 'demo-audio', 'Loa bluetooth, loa để bàn và soundbar mini.'],
            ['Router Demo', 'demo-router', 'demo-networking', 'Router và mesh Wi-Fi cho gia đình.'],
            ['Camera Demo', 'demo-camera', 'demo-office-devices', 'Camera an ninh và webcam.'],
            ['Printer Demo', 'demo-printer', 'demo-office-devices', 'Máy in cho gia đình và văn phòng nhỏ.'],
            ['Storage Demo', 'demo-storage', 'demo-pc-components', 'SSD, ổ cứng di động và thẻ nhớ.'],
            ['RAM Demo', 'demo-ram', 'demo-pc-components', 'RAM laptop và desktop.'],
            ['Charger Cable Demo', 'demo-charger-cable', 'demo-accessories', 'Củ sạc, cáp sạc và dock chuyển đổi.'],
            ['Smartwatch Demo', 'demo-smartwatch', 'demo-accessories', 'Đồng hồ thông minh theo dõi sức khỏe.'],
            ['Laptop Stand Demo', 'demo-laptop-stand', 'demo-accessories', 'Đế kê laptop và phụ kiện góc làm việc.'],
        ];

        foreach ($children as $index => [$name, $slug, $parentSlug, $description]) {
            DB::table('product_categories')->updateOrInsert(
                ['slug' => $slug],
                [
                    'name' => $name,
                    'slug' => $slug,
                    'code' => 'DEMO_'.Str::upper(Str::replace('-', '_', $slug)),
                    'parent_id' => null,
                    'thumbnail_path' => 'uploads/categories/demo-'.$slug.'.jpg',
                    'description' => $description,
                    'is_active' => true,
                    'display_order' => 150 + $index,
                    'meta_title' => $name.' - danh mục demo',
                    'meta_description' => $description,
                    'meta' => $this->json(['source' => 'electronics_demo']),
                    'deleted_at' => null,
                    'updated_at' => $now,
                    'created_at' => DB::table('product_categories')->where('slug', $slug)->value('created_at') ?? $now,
                ]
            );
        }

        return DB::table('product_categories')
            ->whereIn('slug', array_merge(collect($parents)->pluck(1)->all(), collect($children)->pluck(1)->all()))
            ->pluck('id', 'slug')
            ->all();
    }

    private function seedProducts(array $brands, array $categories): array
    {
        $now = now();
        $definitions = $this->productDefinitions();
        $brandSlugs = array_keys($brands);
        $productIdsBySku = [];

        foreach ($definitions as $index => $definition) {
            $brandSlug = $brandSlugs[$index % count($brandSlugs)];
            $categoryId = $categories[$definition['category']] ?? reset($categories);
            $brandId = $brands[$brandSlug];
            $sku = 'DEMO-'.Str::upper($definition['code']).'-'.Str::padLeft((string) ($index + 1), 3, '0');
            $slug = 'demo-'.Str::slug($definition['name']).'-'.Str::lower($definition['code']).'-'.Str::padLeft((string) ($index + 1), 3, '0');
            $saleType = $definition['discount'] > 0 ? 'percent' : null;
            $salePrice = $definition['discount'] > 0
                ? $this->roundVnd($definition['price'] * (1 - ($definition['discount'] / 100)))
                : null;
            $stock = $this->stockForIndex($index);
            $imageSlug = $this->imageSlug($definition['category']);

            DB::table('products')->updateOrInsert(
                ['sku' => $sku],
                [
                    'brand_id' => $brandId,
                    'primary_category_id' => $categoryId,
                    'name' => $definition['name'],
                    'slug' => $slug,
                    'sku' => $sku,
                    'barcode' => '893'.Str::padLeft((string) (202605040000 + $index), 10, '0'),
                    'unit' => 'Chiếc',
                    'price' => $definition['price'],
                    'sale_type' => $saleType,
                    'sale_value' => $definition['discount'] > 0 ? $definition['discount'] : null,
                    'sale_price' => $salePrice,
                    'cost_price' => $this->roundVnd(($salePrice ?? $definition['price']) * 0.72),
                    'stock_quantity' => $stock,
                    'stock_alert_threshold' => 5 + ($index % 8),
                    'is_active' => true,
                    'is_featured' => $index % 9 === 0,
                    'is_hot' => $index % 13 === 0,
                    'display_order' => 200 + $index,
                    'thumbnail_path' => 'uploads/products/demo-'.$imageSlug.'-'.Str::padLeft((string) (($index % 18) + 1), 2, '0').'.jpg',
                    'description' => $definition['description'],
                    'content' => $this->productContent($definition),
                    'views_count' => 40 + (($index * 17) % 950),
                    'meta' => $this->json($definition['meta'] + [
                        'source' => 'electronics_demo',
                        'warranty' => $definition['warranty'],
                        'package' => ['Thiết bị', 'Cáp kết nối', 'Sách hướng dẫn nhanh'],
                    ]),
                    'meta_title' => $definition['name'].' | Dữ liệu demo điện tử',
                    'meta_description' => Str::limit($definition['description'], 180, ''),
                    'deleted_at' => null,
                    'updated_at' => $now,
                    'created_at' => DB::table('products')->where('sku', $sku)->value('created_at') ?? $now,
                ]
            );

            $productId = DB::table('products')->where('sku', $sku)->value('id');
            $productIdsBySku[$sku] = $productId;

            DB::table('product_category_product')->updateOrInsert(
                [
                    'product_id' => $productId,
                    'product_category_id' => $categoryId,
                ],
                [
                    'product_id' => $productId,
                    'product_category_id' => $categoryId,
                    'updated_at' => $now,
                    'created_at' => DB::table('product_category_product')
                        ->where('product_id', $productId)
                        ->where('product_category_id', $categoryId)
                        ->value('created_at') ?? $now,
                ]
            );
        }

        return $productIdsBySku;
    }

    private function normalizeLegacyDemoCategorySlugs(): void
    {
        $renames = [
            'smartphone' => 'demo-smartphone-pho-thong',
            'tablet' => 'demo-tablet-hoc-tap',
            'monitor' => 'demo-man-hinh-van-phong',
            'gaming-gear' => 'demo-gaming-gear',
            'pc-components' => 'demo-pc-components',
            'audio' => 'demo-audio',
            'accessories' => 'demo-accessories',
            'networking' => 'demo-networking',
            'office-devices' => 'demo-office-devices',
            'ultrabook' => 'demo-ultrabook',
            'workstation-mobile' => 'demo-workstation-mobile',
            'smartphone-pho-thong' => 'demo-smartphone-pho-thong',
            'smartphone-cao-cap' => 'demo-smartphone-cao-cap',
            'tablet-hoc-tap' => 'demo-tablet-hoc-tap',
            'man-hinh-van-phong' => 'demo-man-hinh-van-phong',
            'man-hinh-gaming' => 'demo-man-hinh-gaming',
            'keyboard' => 'demo-keyboard',
            'mouse' => 'demo-mouse',
            'headphone' => 'demo-headphone',
            'speaker' => 'demo-speaker',
            'router' => 'demo-router',
            'camera' => 'demo-camera',
            'printer' => 'demo-printer',
            'storage' => 'demo-storage',
            'ram' => 'demo-ram',
            'charger-cable' => 'demo-charger-cable',
            'smartwatch' => 'demo-smartwatch',
            'laptop-stand' => 'demo-laptop-stand',
        ];

        foreach ($renames as $oldSlug => $newSlug) {
            $old = DB::table('product_categories')
                ->where('slug', $oldSlug)
                ->where('meta', 'like', '%electronics_demo%')
                ->first();

            if (! $old) {
                continue;
            }

            $existingNew = DB::table('product_categories')->where('slug', $newSlug)->first();

            if ($existingNew) {
                DB::table('product_category_product')
                    ->where('product_category_id', $old->id)
                    ->update(['product_category_id' => $existingNew->id]);

                DB::table('products')
                    ->where('primary_category_id', $old->id)
                    ->update(['primary_category_id' => $existingNew->id]);

                DB::table('product_categories')
                    ->where('parent_id', $old->id)
                    ->update(['parent_id' => null]);

                $hasProducts = DB::table('product_category_product')
                    ->where('product_category_id', $old->id)
                    ->exists();

                if (! $hasProducts) {
                    DB::table('product_categories')->where('id', $old->id)->delete();
                }

                continue;
            }

            DB::table('product_categories')
                ->where('id', $old->id)
                ->update([
                    'slug' => $newSlug,
                    'code' => 'DEMO_'.Str::upper(Str::replace('-', '_', $newSlug)),
                    'parent_id' => null,
                    'updated_at' => now(),
                ]);
        }
    }

    private function restoreAccidentallyTaggedSeedCategories(): void
    {
        DB::table('product_categories')
            ->where('slug', 'laptop')
            ->where('code', 'LAPTOP')
            ->where('meta', 'like', '%electronics_demo%')
            ->update([
                'meta' => null,
                'updated_at' => now(),
            ]);
    }

    private function deleteObsoleteDemoCategoryGroups(): void
    {
        $obsoleteSlugs = [
            'demo-laptop',
            'demo-smartphone',
            'demo-tablet',
            'demo-monitor',
            'demo-gaming-gear',
            'demo-pc-components',
            'demo-audio',
            'demo-accessories',
            'demo-networking',
            'demo-office-devices',
        ];

        $obsoleteIds = DB::table('product_categories')
            ->whereIn('slug', $obsoleteSlugs)
            ->where('meta', 'like', '%electronics_demo%')
            ->pluck('id')
            ->all();

        if ($obsoleteIds === []) {
            return;
        }

        DB::table('product_categories')
            ->whereIn('parent_id', $obsoleteIds)
            ->update(['parent_id' => null]);

        $idsWithProducts = DB::table('product_category_product')
            ->whereIn('product_category_id', $obsoleteIds)
            ->pluck('product_category_id')
            ->unique()
            ->all();

        $deleteIds = array_values(array_diff($obsoleteIds, $idsWithProducts));

        if ($deleteIds !== []) {
            DB::table('product_categories')->whereIn('id', $deleteIds)->delete();
        }
    }

    private function seedVariants(array $productIdsBySku): void
    {
        $now = now();
        $selectedSkus = array_slice(array_keys($productIdsBySku), 0, 36);

        foreach ($selectedSkus as $index => $sku) {
            $product = DB::table('products')->where('sku', $sku)->first();

            if (! $product) {
                continue;
            }

            $variantSets = $this->variantSetForProduct((string) $product->name, $index);

            foreach ($variantSets as $variantIndex => $variant) {
                $variantSku = $sku.'-V'.($variantIndex + 1);
                $priceDelta = $variant['price_delta'];
                $price = max(100000, (float) $product->price + $priceDelta);
                $salePrice = $product->sale_price ? max(100000, (float) $product->sale_price + $priceDelta) : null;

                DB::table('product_variants')->updateOrInsert(
                    ['sku' => $variantSku],
                    [
                        'product_id' => $product->id,
                        'name' => $variant['name'],
                        'sku' => $variantSku,
                        'barcode' => '893V'.Str::padLeft((string) (2026050400 + ($index * 10) + $variantIndex), 9, '0'),
                        'price' => $price,
                        'sale_price' => $salePrice,
                        'cost_price' => $this->roundVnd(($salePrice ?? $price) * 0.72),
                        'stock_quantity' => max(0, (int) $product->stock_quantity - ($variantIndex * 2)),
                        'stock_alert_threshold' => $product->stock_alert_threshold,
                        'image_path' => null,
                        'attributes' => $this->json($variant['attributes']),
                        'is_active' => true,
                        'deleted_at' => null,
                        'updated_at' => $now,
                        'created_at' => DB::table('product_variants')->where('sku', $variantSku)->value('created_at') ?? $now,
                    ]
                );
            }
        }
    }

    private function seedCustomers(): array
    {
        $now = now();
        $customers = [];

        for ($i = 1; $i <= 8; $i++) {
            $email = 'demo.customer'.Str::padLeft((string) $i, 2, '0').'@example.com';
            $name = [
                'Nguyễn Minh Khoa',
                'Trần Hà Linh',
                'Lê Quốc Bảo',
                'Phạm Thu Trang',
                'Hoàng Anh Duy',
                'Đặng Ngọc Mai',
                'Vũ Thành Nam',
                'Bùi Khánh Vy',
            ][$i - 1];

            $exists = DB::table('customers')->where('email', $email)->exists();
            $payload = [
                'name' => $name,
                'email' => $email,
                'phone' => '0988'.Str::padLeft((string) $i, 6, '0'),
                'avatar_path' => null,
                'is_active' => true,
                'loyalty_points_balance' => 100 * $i,
                'email_verified_at' => $now,
                'phone_verified_at' => $i % 3 === 0 ? null : $now,
                'meta' => $this->json([
                    'source' => 'electronics_demo',
                    'preferred_language' => 'vi',
                    'marketing_consent' => $i % 2 === 0,
                ]),
                'deleted_at' => null,
                'updated_at' => $now,
                'created_at' => DB::table('customers')->where('email', $email)->value('created_at') ?? $now,
            ];

            if (! $exists) {
                $payload['password'] = Hash::make(self::DEMO_PASSWORD);
            }

            DB::table('customers')->updateOrInsert(['email' => $email], $payload);

            $customerId = DB::table('customers')->where('email', $email)->value('id');
            $customers[$email] = $customerId;

            DB::table('customer_addresses')->updateOrInsert(
                [
                    'customer_id' => $customerId,
                    'label' => 'Demo Address',
                ],
                [
                    'customer_id' => $customerId,
                    'label' => 'Demo Address',
                    'recipient_name' => $name,
                    'recipient_phone' => '0988'.Str::padLeft((string) $i, 6, '0'),
                    'email' => $email,
                    'country' => 'Vietnam',
                    'postal_code' => $i <= 4 ? '100000' : '700000',
                    'address_line1' => (20 + $i).' Đường Demo Công Nghệ',
                    'address_line2' => $i <= 4 ? 'Quận Cầu Giấy, Hà Nội' : 'Quận 3, TP. Hồ Chí Minh',
                    'is_default' => true,
                    'meta' => $this->json([
                        'source' => 'electronics_demo',
                        'address_type' => 'demo',
                    ]),
                    'deleted_at' => null,
                    'updated_at' => $now,
                    'created_at' => DB::table('customer_addresses')
                        ->where('customer_id', $customerId)
                        ->where('label', 'Demo Address')
                        ->value('created_at') ?? $now,
                ]
            );
        }

        return $customers;
    }

    private function seedCoupons(): void
    {
        $now = now();
        $coupons = [
            ['DEMO10', 'Ưu đãi demo 10%', 'percent', 10, 100000, 1000000],
            ['FREESHIP', 'Hỗ trợ phí vận chuyển demo', 'fixed', 30000, 30000, 500000],
            ['ELECTRO50K', 'Giảm 50K phụ kiện điện tử', 'fixed', 50000, 50000, 700000],
            ['LAPTOP200K', 'Giảm 200K cho laptop demo', 'fixed', 200000, 200000, 10000000],
            ['AUDIO15', 'Giảm 15% thiết bị âm thanh', 'percent', 15, 300000, 1500000],
        ];

        foreach ($coupons as [$code, $name, $type, $value, $maxDiscount, $minOrder]) {
            DB::table('coupons')->updateOrInsert(
                ['code' => $code],
                [
                    'name' => $name,
                    'code' => $code,
                    'type' => $type,
                    'value' => $value,
                    'max_discount' => $maxDiscount,
                    'min_order_total' => $minOrder,
                    'usage_limit' => 500,
                    'usage_per_customer' => 2,
                    'used_count' => DB::table('coupons')->where('code', $code)->value('used_count') ?? 0,
                    'starts_at' => $now->copy()->subDay(),
                    'expires_at' => $now->copy()->addMonths(6),
                    'is_stackable' => false,
                    'is_active' => true,
                    'applicable_segments' => null,
                    'meta' => $this->json(['source' => 'electronics_demo']),
                    'deleted_at' => null,
                    'updated_at' => $now,
                    'created_at' => DB::table('coupons')->where('code', $code)->value('created_at') ?? $now,
                ]
            );
        }
    }

    private function seedBlogContent(): void
    {
        $now = now();
        $categories = [
            ['Mẹo mua thiết bị', 'demo-meo-mua-thiet-bi', 'Kinh nghiệm chọn mua thiết bị điện tử phù hợp nhu cầu.'],
            ['Góc làm việc số', 'demo-goc-lam-viec-so', 'Gợi ý setup góc học tập và làm việc hiệu quả.'],
            ['Bảo quản thiết bị', 'demo-bao-quan-thiet-bi', 'Hướng dẫn dùng và bảo quản thiết bị bền hơn.'],
        ];

        foreach ($categories as $index => [$name, $slug, $description]) {
            DB::table('blog_categories')->updateOrInsert(
                ['slug' => $slug],
                [
                    'name' => $name,
                    'slug' => $slug,
                    'description' => $description,
                    'is_active' => true,
                    'display_order' => 50 + $index,
                    'deleted_at' => null,
                    'updated_at' => $now,
                    'created_at' => DB::table('blog_categories')->where('slug', $slug)->value('created_at') ?? $now,
                ]
            );
        }

        $categoryIds = DB::table('blog_categories')
            ->whereIn('slug', collect($categories)->pluck(1)->all())
            ->pluck('id', 'slug')
            ->all();

        $posts = [
            ['demo-chon-laptop-hoc-tap-2026', 'Cách chọn laptop học tập năm 2026', 'demo-meo-mua-thiet-bi'],
            ['demo-setup-ban-lam-viec-nho-gon', 'Setup bàn làm việc nhỏ gọn với màn hình và dock sạc', 'demo-goc-lam-viec-so'],
            ['demo-bao-quan-pin-laptop-dung-cach', 'Bảo quản pin laptop đúng cách khi học và làm việc tại nhà', 'demo-bao-quan-thiet-bi'],
            ['demo-nen-mua-tai-nghe-khong-day-nao', 'Nên chọn tai nghe không dây theo những tiêu chí nào?', 'demo-meo-mua-thiet-bi'],
            ['demo-router-mesh-cho-can-ho', 'Khi nào nên dùng router mesh cho căn hộ nhiều phòng?', 'demo-goc-lam-viec-so'],
            ['demo-ve-sinh-ban-phim-chuot', 'Vệ sinh bàn phím và chuột an toàn trong 15 phút', 'demo-bao-quan-thiet-bi'],
            ['demo-chon-man-hinh-lam-viec', 'Chọn màn hình làm việc: độ phân giải, tấm nền và cổng kết nối', 'demo-meo-mua-thiet-bi'],
        ];

        foreach ($posts as $index => [$slug, $title, $categorySlug]) {
            $excerpt = 'Bài viết demo tổng hợp kinh nghiệm thực tế, dễ áp dụng cho người dùng thiết bị điện tử.';
            DB::table('blog_posts')->updateOrInsert(
                ['slug' => $slug],
                [
                    'blog_category_id' => $categoryIds[$categorySlug] ?? null,
                    'title' => $title,
                    'slug' => $slug,
                    'excerpt' => $excerpt,
                    'content' => $this->blogContent($title),
                    'featured_image_path' => 'uploads/blog/demo-electronics-'.Str::padLeft((string) ($index + 1), 2, '0').'.jpg',
                    'is_featured' => $index < 2,
                    'status' => 'published',
                    'published_at' => $now->copy()->subDays(10 - $index),
                    'views_count' => 120 + ($index * 73),
                    'meta_title' => $title,
                    'meta_description' => $excerpt,
                    'meta' => $this->json([
                        'source' => 'electronics_demo',
                        'author' => 'DangDuc Demo',
                        'reading_time' => (4 + ($index % 3)).' phút',
                    ]),
                    'deleted_at' => null,
                    'updated_at' => $now,
                    'created_at' => DB::table('blog_posts')->where('slug', $slug)->value('created_at') ?? $now,
                ]
            );
        }
    }

    private function seedOrders(array $customers, array $products): void
    {
        $now = now();
        $productRows = DB::table('products')
            ->whereIn('id', array_values($products))
            ->orderBy('id')
            ->limit(30)
            ->get()
            ->values();
        $customerEmails = array_keys($customers);
        $statuses = ['pending', 'processing', 'completed', 'cancelled', 'completed', 'processing', 'pending', 'completed'];

        for ($i = 1; $i <= 8; $i++) {
            $code = 'DEMO-ORDER-'.Str::padLeft((string) $i, 4, '0');
            $customerEmail = $customerEmails[($i - 1) % count($customerEmails)];
            $customerId = $customers[$customerEmail];
            $addressId = DB::table('customer_addresses')
                ->where('customer_id', $customerId)
                ->where('label', 'Demo Address')
                ->value('id');
            $first = $productRows[(($i - 1) * 2) % $productRows->count()];
            $second = $productRows[((($i - 1) * 2) + 1) % $productRows->count()];
            $items = [
                [$first, 1 + ($i % 2)],
                [$second, 1],
            ];
            $subtotal = collect($items)->sum(fn ($item) => (float) ($item[0]->sale_price ?: $item[0]->price) * $item[1]);
            $shippingTotal = $i % 3 === 0 ? 30000 : 0;
            $discountTotal = $i % 4 === 0 ? 50000 : 0;
            $total = max(0, $subtotal + $shippingTotal - $discountTotal);
            $status = $statuses[$i - 1];

            DB::table('orders')->updateOrInsert(
                ['code' => $code],
                [
                    'code' => $code,
                    'customer_id' => $customerId,
                    'customer_address_id' => $addressId,
                    'status' => $status,
                    'payment_status' => in_array($status, ['completed', 'processing'], true) ? 'paid' : 'pending',
                    'fulfillment_status' => $status === 'completed' ? 'fulfilled' : 'unfulfilled',
                    'currency' => 'VND',
                    'subtotal' => $subtotal,
                    'discount_total' => $discountTotal,
                    'shipping_total' => $shippingTotal,
                    'tax_total' => 0,
                    'total' => $total,
                    'coupon_code' => $discountTotal > 0 ? 'ELECTRO50K' : null,
                    'coupon_discount' => $discountTotal,
                    'source' => 'demo',
                    'note' => 'Đơn hàng demo phục vụ học tập, không phải đơn thật.',
                    'placed_at' => $now->copy()->subDays(12 - $i),
                    'confirmed_at' => in_array($status, ['processing', 'completed'], true) ? $now->copy()->subDays(11 - $i) : null,
                    'shipped_at' => $status === 'completed' ? $now->copy()->subDays(10 - $i) : null,
                    'completed_at' => $status === 'completed' ? $now->copy()->subDays(9 - $i) : null,
                    'cancelled_at' => $status === 'cancelled' ? $now->copy()->subDays(9 - $i) : null,
                    'meta' => $this->json(['source' => 'electronics_demo', 'shipping_method' => 'cod']),
                    'deleted_at' => null,
                    'updated_at' => $now,
                    'created_at' => DB::table('orders')->where('code', $code)->value('created_at') ?? $now,
                ]
            );

            $orderId = DB::table('orders')->where('code', $code)->value('id');
            $this->seedOrderItems($orderId, $items);
            $this->seedPayment($orderId, $total, $status);
            $this->seedOrderShipping($orderId, $customerId, $shippingTotal);
        }
    }

    private function seedOrderItems(int $orderId, array $items): void
    {
        $now = now();

        foreach ($items as [$product, $quantity]) {
            $price = (float) ($product->sale_price ?: $product->price);
            $originalPrice = (float) $product->price;
            DB::table('order_items')->updateOrInsert(
                [
                    'order_id' => $orderId,
                    'sku' => $product->sku,
                ],
                [
                    'order_id' => $orderId,
                    'product_id' => $product->id,
                    'product_variant_id' => null,
                    'product_name' => $product->name,
                    'sku' => $product->sku,
                    'attributes' => null,
                    'original_price' => $originalPrice,
                    'price' => $price,
                    'sale_price' => $product->sale_price,
                    'cost_price' => $product->cost_price,
                    'sale_type' => $product->sale_type,
                    'sale_value' => $product->sale_value,
                    'sale_amount' => max(0, $originalPrice - $price),
                    'discount_amount' => max(0, $originalPrice - $price) * $quantity,
                    'quantity' => $quantity,
                    'subtotal' => $price * $quantity,
                    'tax_amount' => 0,
                    'meta' => $this->json(['source' => 'electronics_demo', 'thumbnail' => $product->thumbnail_path]),
                    'updated_at' => $now,
                    'created_at' => DB::table('order_items')
                        ->where('order_id', $orderId)
                        ->where('sku', $product->sku)
                        ->value('created_at') ?? $now,
                ]
            );
        }
    }

    private function seedPayment(int $orderId, float $amount, string $status): void
    {
        $now = now();
        DB::table('payments')->updateOrInsert(
            [
                'order_id' => $orderId,
                'provider' => 'cod',
            ],
            [
                'order_id' => $orderId,
                'provider' => 'cod',
                'reference' => 'DEMO-PAY-'.$orderId,
                'status' => in_array($status, ['completed', 'processing'], true) ? 'succeeded' : 'pending',
                'currency' => 'VND',
                'amount' => $amount,
                'fee' => 0,
                'paid_at' => in_array($status, ['completed', 'processing'], true) ? $now->copy()->subDays(2) : null,
                'meta' => $this->json(['source' => 'electronics_demo']),
                'updated_at' => $now,
                'created_at' => DB::table('payments')
                    ->where('order_id', $orderId)
                    ->where('provider', 'cod')
                    ->value('created_at') ?? $now,
            ]
        );
    }

    private function seedOrderShipping(int $orderId, int $customerId, float $shippingTotal): void
    {
        $now = now();
        $address = DB::table('customer_addresses')
            ->where('customer_id', $customerId)
            ->where('label', 'Demo Address')
            ->first();

        DB::table('order_shipping')->updateOrInsert(
            ['order_id' => $orderId],
            [
                'order_id' => $orderId,
                'recipient_name' => $address?->recipient_name,
                'recipient_phone' => $address?->recipient_phone,
                'email' => $address?->email,
                'country' => 'Vietnam',
                'city_id' => null,
                'province_id' => null,
                'ward_id' => null,
                'postal_code' => $address?->postal_code,
                'address_line1' => $address?->address_line1 ?? 'Địa chỉ demo',
                'address_line2' => $address?->address_line2,
                'shipping_method' => 'cod',
                'tracking_number' => null,
                'shipping_fee' => $shippingTotal,
                'insurance_fee' => 0,
                'meta' => $this->json(['source' => 'electronics_demo']),
                'updated_at' => $now,
                'created_at' => DB::table('order_shipping')->where('order_id', $orderId)->value('created_at') ?? $now,
            ]
        );
    }

    private function productDefinitions(): array
    {
        $templates = [
            ['category' => 'demo-ultrabook', 'code' => 'ULT', 'base' => 'Laptop mỏng nhẹ', 'prices' => [13990000, 17990000, 22990000], 'spec' => ['CPU Intel Core Ultra', 'RAM 16GB', 'SSD 512GB']],
            ['category' => 'demo-laptop-gaming', 'code' => 'GAM', 'base' => 'Laptop gaming', 'prices' => [21990000, 27990000, 35990000], 'spec' => ['GPU rời 8GB', 'Màn 144Hz', 'Tản nhiệt kép']],
            ['category' => 'demo-workstation-mobile', 'code' => 'WRK', 'base' => 'Laptop workstation', 'prices' => [32990000, 45990000, 58990000], 'spec' => ['CPU hiệu năng cao', 'RAM 32GB', 'SSD 1TB']],
            ['category' => 'demo-smartphone-pho-thong', 'code' => 'SPB', 'base' => 'Smartphone pin bền', 'prices' => [3490000, 4990000, 6990000], 'spec' => ['Pin 5000mAh', 'Màn 90Hz', 'Sạc nhanh']],
            ['category' => 'demo-smartphone-cao-cap', 'code' => 'SPP', 'base' => 'Smartphone cao cấp', 'prices' => [15990000, 21990000, 28990000], 'spec' => ['Camera chống rung', 'Màn AMOLED', 'Chip flagship']],
            ['category' => 'demo-tablet-hoc-tap', 'code' => 'TAB', 'base' => 'Tablet học tập', 'prices' => [5990000, 8490000, 11990000], 'spec' => ['Bút cảm ứng', 'Màn 11 inch', 'Pin 8000mAh']],
            ['category' => 'demo-man-hinh-van-phong', 'code' => 'MON', 'base' => 'Màn hình văn phòng', 'prices' => [2990000, 4490000, 6490000], 'spec' => ['Tấm nền IPS', 'Chống nhấp nháy', 'Cổng USB-C']],
            ['category' => 'demo-man-hinh-gaming', 'code' => 'MOG', 'base' => 'Màn hình gaming', 'prices' => [4990000, 7490000, 10990000], 'spec' => ['Tần số 165Hz', '1ms', 'Adaptive Sync']],
            ['category' => 'demo-keyboard', 'code' => 'KEY', 'base' => 'Bàn phím cơ', 'prices' => [890000, 1490000, 2490000], 'spec' => ['Switch êm', 'LED trắng', 'Keycap PBT']],
            ['category' => 'demo-mouse', 'code' => 'MOU', 'base' => 'Chuột không dây', 'prices' => [390000, 690000, 1290000], 'spec' => ['DPI tùy chỉnh', 'Pin 60 ngày', 'Kết nối 2.4GHz']],
            ['category' => 'demo-headphone', 'code' => 'HED', 'base' => 'Tai nghe chống ồn', 'prices' => [990000, 1990000, 3490000], 'spec' => ['ANC chủ động', 'Bluetooth 5.3', 'Mic lọc ồn']],
            ['category' => 'demo-speaker', 'code' => 'SPK', 'base' => 'Loa bluetooth', 'prices' => [590000, 1190000, 2390000], 'spec' => ['Chống nước IPX5', 'Pin 12 giờ', 'Âm bass sâu']],
            ['category' => 'demo-router', 'code' => 'RTR', 'base' => 'Router Wi-Fi', 'prices' => [790000, 1590000, 2990000], 'spec' => ['Wi-Fi 6', 'Mesh ready', 'Quản lý qua app']],
            ['category' => 'demo-camera', 'code' => 'CAM', 'base' => 'Camera an ninh', 'prices' => [690000, 1290000, 2290000], 'spec' => ['2K QHD', 'Đàm thoại 2 chiều', 'Nhận diện chuyển động']],
            ['category' => 'demo-printer', 'code' => 'PRN', 'base' => 'Máy in văn phòng', 'prices' => [2490000, 3990000, 5990000], 'spec' => ['In hai mặt', 'Wi-Fi Direct', 'Hộp mực tiết kiệm']],
            ['category' => 'demo-storage', 'code' => 'SSD', 'base' => 'SSD di động', 'prices' => [990000, 1690000, 2990000], 'spec' => ['USB-C', 'Tốc độ cao', 'Vỏ nhôm']],
            ['category' => 'demo-ram', 'code' => 'RAM', 'base' => 'RAM desktop', 'prices' => [690000, 1290000, 2290000], 'spec' => ['DDR5', 'Bus cao', 'Tản nhiệt nhôm']],
            ['category' => 'demo-charger-cable', 'code' => 'CHG', 'base' => 'Củ sạc nhanh', 'prices' => [290000, 490000, 890000], 'spec' => ['GaN', 'USB-C PD', 'Bảo vệ quá nhiệt']],
            ['category' => 'demo-smartwatch', 'code' => 'SWT', 'base' => 'Smartwatch sức khỏe', 'prices' => [1290000, 2490000, 4490000], 'spec' => ['Đo nhịp tim', 'GPS', 'Pin 7 ngày']],
            ['category' => 'demo-laptop-stand', 'code' => 'STD', 'base' => 'Đế kê laptop', 'prices' => [290000, 590000, 990000], 'spec' => ['Hợp kim nhôm', 'Gấp gọn', 'Chống trượt']],
        ];

        $suffixes = ['S1', 'Air', 'Pro', 'Max', 'Lite', 'Plus'];
        $definitions = [];

        foreach ($templates as $templateIndex => $template) {
            for ($i = 1; $i <= 6; $i++) {
                $price = $template['prices'][($i - 1) % count($template['prices'])];
                $suffix = $suffixes[($i - 1) % count($suffixes)];
                $name = $template['base'].' '.$suffix.' '.(2026 + (($templateIndex + $i) % 2));
                $definitions[] = [
                    'category' => $template['category'],
                    'code' => $template['code'].$i,
                    'name' => $name,
                    'price' => $price,
                    'discount' => $i % 3 === 0 ? 8 : ($i % 4 === 0 ? 12 : 0),
                    'warranty' => $price >= 10000000 ? '24 tháng' : '12 tháng',
                    'description' => $name.' là sản phẩm demo với cấu hình cân bằng, phù hợp nhu cầu mua sắm điện tử trong cửa hàng học tập.',
                    'meta' => [
                        'tags' => ['electronics-demo', $template['category'], Str::slug($template['base'])],
                        'specifications' => [
                            'overview' => $template['spec'],
                            'color' => ['Đen', 'Bạc', 'Xanh đậm'][($i + $templateIndex) % 3],
                            'release_year' => 2026,
                        ],
                    ],
                ];
            }
        }

        return $definitions;
    }

    private function productContent(array $definition): string
    {
        $specs = collect($definition['meta']['specifications']['overview'])
            ->map(fn ($item) => '<li>'.$item.'</li>')
            ->implode('');

        return '<h2>'.$definition['name'].'</h2>'
            .'<p>Sản phẩm demo được biên soạn riêng cho môi trường học tập, dùng để kiểm thử danh mục, giỏ hàng, tồn kho và đơn hàng.</p>'
            .'<h3>Điểm nổi bật</h3><ul>'.$specs.'</ul>'
            .'<p>Thông tin mô tả là dữ liệu tổng hợp, không sao chép từ website thương mại hoặc nhà sản xuất thật.</p>';
    }

    private function variantSetForProduct(string $name, int $index): array
    {
        if (str_contains(Str::lower($name), 'laptop')) {
            return [
                ['name' => 'RAM 16GB / SSD 512GB', 'price_delta' => 0, 'attributes' => ['ram' => '16GB', 'storage' => '512GB']],
                ['name' => 'RAM 32GB / SSD 1TB', 'price_delta' => 4500000, 'attributes' => ['ram' => '32GB', 'storage' => '1TB']],
            ];
        }

        if (str_contains(Str::lower($name), 'smartphone') || str_contains(Str::lower($name), 'tablet')) {
            return [
                ['name' => '128GB / Đen', 'price_delta' => 0, 'attributes' => ['storage' => '128GB', 'color' => 'Đen']],
                ['name' => '256GB / Bạc', 'price_delta' => 1800000, 'attributes' => ['storage' => '256GB', 'color' => 'Bạc']],
            ];
        }

        return [
            ['name' => 'Màu đen', 'price_delta' => 0, 'attributes' => ['color' => 'Đen']],
            ['name' => 'Màu bạc', 'price_delta' => 100000, 'attributes' => ['color' => 'Bạc']],
        ];
    }

    private function stockForIndex(int $index): int
    {
        return match (true) {
            $index % 17 === 0 => 0,
            $index % 11 === 0 => 2,
            $index % 7 === 0 => 5,
            default => 12 + (($index * 3) % 90),
        };
    }

    private function imageSlug(string $categorySlug): string
    {
        return match ($categorySlug) {
            'demo-ultrabook', 'demo-laptop-gaming', 'demo-workstation-mobile' => 'laptop',
            'demo-smartphone-pho-thong', 'demo-smartphone-cao-cap' => 'phone',
            'demo-tablet-hoc-tap' => 'tablet',
            'demo-man-hinh-van-phong', 'demo-man-hinh-gaming' => 'monitor',
            'demo-keyboard', 'demo-mouse' => 'gaming-gear',
            'demo-headphone', 'demo-speaker' => 'audio',
            'demo-router' => 'router',
            'demo-camera' => 'camera',
            'demo-printer' => 'printer',
            'demo-storage', 'demo-ram' => 'component',
            'demo-smartwatch' => 'watch',
            default => 'accessory',
        };
    }

    private function blogContent(string $title): string
    {
        return '<h2>'.$title.'</h2>'
            .'<p>Đây là bài viết demo được tạo tổng hợp cho cửa hàng điện tử. Nội dung tập trung vào cách chọn, sử dụng và bảo quản thiết bị trong bối cảnh học tập.</p>'
            .'<h3>Gợi ý nhanh</h3>'
            .'<ul><li>Xác định nhu cầu chính trước khi mua.</li><li>So sánh cấu hình, bảo hành và khả năng nâng cấp.</li><li>Ưu tiên phụ kiện an toàn, nguồn gốc rõ ràng.</li></ul>'
            .'<p>Thông tin trong bài viết không đại diện cho đánh giá sản phẩm thật.</p>';
    }

    private function roundVnd(float $value): int
    {
        return (int) (round($value / 10000) * 10000);
    }

    private function json(array $value): string
    {
        return json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}
