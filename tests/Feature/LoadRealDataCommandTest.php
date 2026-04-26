<?php

namespace Tests\Feature;

use App\Infrastructure\Persistence\Eloquent\Catalog\Attribute;
use App\Infrastructure\Persistence\Eloquent\Catalog\AttributeValue;
use App\Infrastructure\Persistence\Eloquent\Catalog\Brand;
use App\Infrastructure\Persistence\Eloquent\Catalog\Product;
use App\Infrastructure\Persistence\Eloquent\Catalog\ProductCategory;
use App\Infrastructure\Persistence\Eloquent\Catalog\ProductMedia;
use App\Infrastructure\Persistence\Eloquent\Catalog\ProductVariant;
use App\Infrastructure\Persistence\Eloquent\Cms\Banner;
use App\Infrastructure\Persistence\Eloquent\Cms\SiteContact;
use App\Infrastructure\Persistence\Eloquent\Cms\SiteLink;
use App\Infrastructure\Persistence\Eloquent\Cms\Video;
use App\Infrastructure\Persistence\Eloquent\Geo\Location;
use App\Infrastructure\Persistence\Eloquent\Shipping\ShippingFee;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class LoadRealDataCommandTest extends TestCase
{
    use RefreshDatabase;

    private string $dumpPath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->dumpPath = storage_path('framework/testing/shopbanhang-real-fixture.sql');
        File::ensureDirectoryExists(dirname($this->dumpPath));
        File::put($this->dumpPath, $this->buildDumpFixture());
    }

    protected function tearDown(): void
    {
        if (File::exists($this->dumpPath)) {
            File::delete($this->dumpPath);
        }

        parent::tearDown();
    }

    public function test_it_bootstraps_real_dump_into_current_schema_and_stays_idempotent(): void
    {
        $dumpOption = str_replace('\\', '/', $this->dumpPath);

        $this->artisan('data:load-real --dump='.$dumpOption)
            ->expectsOutputToContain('Real data bootstrap completed successfully.')
            ->assertExitCode(0);

        $this->assertDatabaseHas('brands', [
            'name' => 'Legacy Brand',
            'slug' => 'legacy-brand',
        ]);

        $this->assertDatabaseHas('product_categories', [
            'name' => 'Legacy Category',
            'slug' => 'legacy-category',
        ]);

        $product = Product::query()->where('slug', 'legacy-product')->firstOrFail();
        $this->assertSame(30, (int) $product->meta['legacy_product_id']);
        $this->assertSame('uploads/product/main.jpg', $product->thumbnail_path);

        $variant = ProductVariant::query()->where('sku', 'LEGACY-ATTR-5')->firstOrFail();
        $this->assertSame('uploads/attribute/variant-red.jpg', $variant->image_path);
        $this->assertSame(['Màu sắc' => 'Đỏ'], $variant->attributes);

        $attribute = Attribute::query()->where('code', 'legacy-attribute-1')->firstOrFail();
        $this->assertSame('Legacy Product Attributes', $attribute->group->name);

        $attributeValue = AttributeValue::query()->where('attribute_id', $attribute->id)->firstOrFail();
        $this->assertSame('Đỏ', $attributeValue->value);

        $this->assertDatabaseHas('product_media', [
            'product_id' => $product->id,
            'file_path' => 'uploads/product/main.jpg',
            'collection' => 'thumbnail',
        ]);

        $this->assertDatabaseHas('product_media', [
            'product_id' => $product->id,
            'file_path' => 'uploads/gallery/gallery-one.jpg',
            'collection' => 'gallery',
        ]);

        $this->assertDatabaseHas('banners', [
            'title' => 'Legacy Slider',
            'image_path' => 'uploads/slider/slider-home.jpg',
        ]);

        $this->assertDatabaseHas('videos', [
            'title' => 'Legacy Video',
            'video_url' => '-k1j6Spy__s',
        ]);

        $this->assertDatabaseHas('site_links', [
            'name' => 'Legacy Partner',
            'category' => 'partner',
            'url' => 'https://shopw.example/doi-tac',
        ]);

        $this->assertDatabaseHas('site_contacts', [
            'company_name' => 'Legacy Shop',
            'logo_path' => 'uploads/contact/logo-legacy.png',
        ]);

        $this->assertDatabaseHas('locations', [
            'code' => 'legacy-city-01',
            'type' => 'city',
        ]);

        $this->assertDatabaseHas('shipping_fees', [
            'amount' => 15000,
        ]);

        $baseline = [
            'brands' => Brand::count(),
            'categories' => ProductCategory::count(),
            'products' => Product::count(),
            'variants' => ProductVariant::count(),
            'media' => ProductMedia::count(),
            'banners' => Banner::count(),
            'videos' => Video::count(),
            'links' => SiteLink::count(),
            'contacts' => SiteContact::count(),
            'locations' => Location::count(),
            'fees' => ShippingFee::count(),
        ];

        $this->artisan('data:load-real --dump='.$dumpOption)
            ->assertExitCode(0);

        $this->assertSame($baseline['brands'], Brand::count());
        $this->assertSame($baseline['categories'], ProductCategory::count());
        $this->assertSame($baseline['products'], Product::count());
        $this->assertSame($baseline['variants'], ProductVariant::count());
        $this->assertSame($baseline['media'], ProductMedia::count());
        $this->assertSame($baseline['banners'], Banner::count());
        $this->assertSame($baseline['videos'], Video::count());
        $this->assertSame($baseline['links'], SiteLink::count());
        $this->assertSame($baseline['contacts'], SiteContact::count());
        $this->assertSame($baseline['locations'], Location::count());
        $this->assertSame($baseline['fees'], ShippingFee::count());
    }

    private function buildDumpFixture(): string
    {
        return <<<'SQL'
-- fixture with semicolons in strings and comments
CREATE TABLE `tbl_attribute` (
  `id` integer NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` varchar(200) NOT NULL,
  `status` integer NOT NULL DEFAULT 1
);
INSERT INTO `tbl_attribute` (`id`, `name`, `description`, `status`) VALUES
(1, 'Màu sắc', 'Thuộc tính; màu sắc', 1);

CREATE TABLE `tbl_brand` (
  `brand_id` integer NOT NULL,
  `brand_name` varchar(255) NOT NULL,
  `brand_slug` varchar(255) NOT NULL,
  `brand_desc` text NOT NULL,
  `brand_status` integer NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
);
INSERT INTO `tbl_brand` (`brand_id`, `brand_name`, `brand_slug`, `brand_desc`, `brand_status`, `created_at`, `updated_at`) VALUES
(20, 'Legacy Brand', 'legacy-brand', 'Brand desc', 0, NULL, NULL);

CREATE TABLE `tbl_category_product` (
  `category_id` integer NOT NULL,
  `meta_keywords` text DEFAULT NULL,
  `category_name` varchar(255) NOT NULL,
  `slug_category_product` varchar(255) NOT NULL,
  `category_desc` text NOT NULL,
  `category_parent` integer DEFAULT NULL,
  `category_status` integer NOT NULL,
  `category_order` integer DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
);
INSERT INTO `tbl_category_product` (`category_id`, `meta_keywords`, `category_name`, `slug_category_product`, `category_desc`, `category_parent`, `category_status`, `category_order`, `created_at`, `updated_at`) VALUES
(10, 'legacy,category', 'Legacy Category', 'legacy-category', 'Category desc', 0, 0, 1, NULL, NULL);

CREATE TABLE `tbl_product` (
  `product_id` integer NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `product_tags` text DEFAULT NULL,
  `product_quantity` varchar(50) NOT NULL,
  `product_sold` integer DEFAULT NULL,
  `product_slug` varchar(255) NOT NULL,
  `category_id` integer NOT NULL,
  `brand_id` integer NOT NULL,
  `product_desc` text NOT NULL,
  `product_content` text NOT NULL,
  `product_price` integer NOT NULL,
  `price_cost` varchar(100) NOT NULL,
  `product_image` varchar(255) NOT NULL,
  `product_file` varchar(100) DEFAULT NULL,
  `product_views` varchar(100) DEFAULT NULL,
  `product_status` integer NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
);
INSERT INTO `tbl_product` (`product_id`, `product_name`, `product_tags`, `product_quantity`, `product_sold`, `product_slug`, `category_id`, `brand_id`, `product_desc`, `product_content`, `product_price`, `price_cost`, `product_image`, `product_file`, `product_views`, `product_status`, `created_at`, `updated_at`) VALUES
(30, 'Legacy Phone', 'legacy phone', '30', 0, 'legacy-product', 10, 20, '<p>Legacy desc</p>', '<div>Legacy content; with HTML &nbsp; text</div>', 39900000, '25000000', 'main.jpg', NULL, '7', 0, NULL, NULL);

CREATE TABLE `tbl_product_attribute` (
  `id` integer NOT NULL,
  `product_id` integer NOT NULL,
  `attribute_id` integer NOT NULL,
  `price` varchar(50) NOT NULL,
  `image` varchar(255) NOT NULL,
  `quantity` integer NOT NULL,
  `name` varchar(200) NOT NULL
);
INSERT INTO `tbl_product_attribute` (`id`, `product_id`, `attribute_id`, `price`, `image`, `quantity`, `name`) VALUES
(5, 30, 1, '27900000', 'variant-red.jpg', 4, 'Đỏ');

CREATE TABLE `tbl_gallery` (
  `gallery_id` integer NOT NULL,
  `gallery_name` varchar(255) NOT NULL,
  `gallery_image` varchar(255) NOT NULL,
  `product_id` integer NOT NULL
);
INSERT INTO `tbl_gallery` (`gallery_id`, `gallery_name`, `gallery_image`, `product_id`) VALUES
(1, 'Legacy gallery', 'gallery-one.jpg', 30);

CREATE TABLE `tbl_slider` (
  `slider_id` integer NOT NULL,
  `slider_name` varchar(255) NOT NULL,
  `slider_status` integer NOT NULL,
  `slider_image` varchar(100) NOT NULL,
  `slider_desc` varchar(100) NOT NULL
);
INSERT INTO `tbl_slider` (`slider_id`, `slider_name`, `slider_status`, `slider_image`, `slider_desc`) VALUES
(1, 'Legacy Slider', 1, 'slider-home.jpg', 'Homepage banner');

CREATE TABLE `tbl_videos` (
  `video_id` integer NOT NULL,
  `video_title` varchar(200) NOT NULL,
  `video_slug` varchar(255) NOT NULL,
  `video_link` varchar(100) NOT NULL,
  `video_desc` mediumtext NOT NULL,
  `video_image` varchar(255) NOT NULL
);
INSERT INTO `tbl_videos` (`video_id`, `video_title`, `video_slug`, `video_link`, `video_desc`, `video_image`) VALUES
(3, 'Legacy Video', 'legacy-video', '-k1j6Spy__s', 'Legacy video desc', 'video-thumb.jpg');

CREATE TABLE `tbl_icons` (
  `id_icons` integer NOT NULL,
  `name` varchar(50) NOT NULL,
  `image` varchar(100) NOT NULL,
  `link` varchar(255) NOT NULL,
  `category` varchar(50) DEFAULT NULL
);
INSERT INTO `tbl_icons` (`id_icons`, `name`, `image`, `link`, `category`) VALUES
(15, 'Legacy Partner', 'partner-logo.png', 'https://shopw.example/doi-tac', 'doitac');

CREATE TABLE `tbl_infomation` (
  `info_id` integer NOT NULL,
  `info_contact` mediumtext NOT NULL,
  `info_map` text NOT NULL,
  `info_logo` varchar(255) NOT NULL,
  `slogan_logo` varchar(255) NOT NULL,
  `info_fanpage` text NOT NULL
);
INSERT INTO `tbl_infomation` (`info_id`, `info_contact`, `info_map`, `info_logo`, `slogan_logo`, `info_fanpage`) VALUES
(1, '<div>Legacy contact; info</div>', '<iframe></iframe>', 'logo-legacy.png', 'Legacy slogan', '<div>Fanpage</div>');

CREATE TABLE `tbl_tinhthanhpho` (
  `matp` varchar(5) NOT NULL,
  `name_city` varchar(100) NOT NULL,
  `type` varchar(30) NOT NULL
);
INSERT INTO `tbl_tinhthanhpho` (`matp`, `name_city`, `type`) VALUES
('01', 'Thành phố Hà Nội', 'Thành phố Trung ương');

CREATE TABLE `tbl_quanhuyen` (
  `maqh` varchar(5) NOT NULL,
  `name_quanhuyen` varchar(100) NOT NULL,
  `type` varchar(30) NOT NULL,
  `matp` integer NOT NULL
);
INSERT INTO `tbl_quanhuyen` (`maqh`, `name_quanhuyen`, `type`, `matp`) VALUES
('001', 'Quận Ba Đình', 'Quận', 1);

CREATE TABLE `tbl_xaphuongthitran` (
  `xaid` varchar(5) NOT NULL,
  `name_xaphuong` varchar(100) NOT NULL,
  `type` varchar(30) NOT NULL,
  `maqh` integer NOT NULL
);
INSERT INTO `tbl_xaphuongthitran` (`xaid`, `name_xaphuong`, `type`, `maqh`) VALUES
('00001', 'Phường Phúc Xá', 'Phường', 1);

CREATE TABLE `tbl_feeship` (
  `fee_id` integer NOT NULL,
  `fee_matp` integer NOT NULL,
  `fee_maqh` integer NOT NULL,
  `fee_xaid` integer NOT NULL,
  `fee_feeship` varchar(50) NOT NULL
);
INSERT INTO `tbl_feeship` (`fee_id`, `fee_matp`, `fee_maqh`, `fee_xaid`, `fee_feeship`) VALUES
(1, 1, 1, 1, '15000');
SQL;
    }
}
