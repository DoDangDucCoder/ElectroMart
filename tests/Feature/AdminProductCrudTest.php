<?php

namespace Tests\Feature;

use App\Infrastructure\Persistence\Eloquent\Catalog\Brand;
use App\Infrastructure\Persistence\Eloquent\Catalog\Product;
use App\Infrastructure\Persistence\Eloquent\Catalog\ProductCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Tests\TestCase;

class AdminProductCrudTest extends TestCase
{
    use RefreshDatabase;

    protected array $filesToCleanup = [];

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
    }

    protected function tearDown(): void
    {
        foreach ($this->filesToCleanup as $path) {
            if (! $path) {
                continue;
            }

            $fullPath = public_path($path);
            if (File::exists($fullPath)) {
                File::delete($fullPath);
            }

            $galleryCopy = public_path('uploads/gallery/' . basename($path));
            if (File::exists($galleryCopy)) {
                File::delete($galleryCopy);
            }
        }

        parent::tearDown();
    }

    public function test_admin_can_create_product_with_media_and_document(): void
    {
        [$product, $category, $brand] = $this->createProductThroughForm();

        $this->assertNotNull($product);
        $this->assertEquals('Test Product', $product->product_name);
        $this->assertEquals($category->id, $product->primary_category_id);
        $this->assertEquals($brand->id, $product->brand_id);
        $this->assertTrue((bool) $product->is_active);
        $this->assertTrue(Str::startsWith($product->product_file, 'manual'));
        $this->assertTrue(Str::endsWith($product->product_file, '.pdf'));

        $this->assertFileExists(public_path($product->product_image));
        $this->assertFileExists(public_path('uploads/document/' . $product->product_file));

        $this->registerPathForCleanup($product->product_image);
        $this->registerPathForCleanup('uploads/document/' . $product->product_file);
    }

    public function test_admin_can_update_product_and_replace_media_and_document(): void
    {
        [$product] = $this->createProductThroughForm();
        $oldImage = $product->product_image;
        $oldDocument = 'uploads/document/' . $product->product_file;

        $newCategory = $this->createCategory(['name' => 'Updated Category', 'slug' => 'updated-category']);

        $response = $this->post("/update-product/{$product->product_id}", [
            'product_name' => 'Updated Product',
            'product_slug' => 'updated-product',
            'product_price' => '2500000',
            'price_cost' => '2000000',
            'product_quantity' => 3,
            'product_desc' => 'Updated description',
            'product_content' => 'Updated content',
            'product_cate' => $newCategory->id,
            'product_brand' => $product->brand_id,
            'product_status' => 1,
            'product_tags' => 'updated',
            'product_image' => UploadedFile::fake()->create('updated.jpg', 200, 'image/jpeg'),
            'document' => UploadedFile::fake()->create('specs.pdf', 30, 'application/pdf'),
        ]);

        $response->assertRedirect('all-product');

        $product->refresh();

        $this->assertEquals('Updated Product', $product->product_name);
        $this->assertEquals($newCategory->id, $product->primary_category_id);
        $this->assertFalse((bool) $product->is_active);
        $this->assertTrue(Str::startsWith($product->product_file, 'specs'));
        $this->assertTrue(Str::endsWith($product->product_file, '.pdf'));

        $this->assertFalse(File::exists(public_path($oldImage)));
        $this->assertFalse(File::exists(public_path($oldDocument)));

        $this->assertFileExists(public_path($product->product_image));
        $this->assertFileExists(public_path('uploads/document/' . $product->product_file));

        $this->registerPathForCleanup($product->product_image);
        $this->registerPathForCleanup('uploads/document/' . $product->product_file);
    }

    public function test_admin_can_delete_product_and_remove_media(): void
    {
        [$product] = $this->createProductThroughForm();
        $imagePath = $product->product_image;
        $documentPath = 'uploads/document/' . $product->product_file;

        $response = $this->get("/delete-product/{$product->product_id}");
        $response->assertRedirect('all-product');

        $this->assertDatabaseCount('products', 0);
        $this->assertFalse(File::exists(public_path($imagePath)));
        $this->assertFalse(File::exists(public_path($documentPath)));
        $this->assertFalse(File::exists(public_path('uploads/gallery/' . basename($imagePath))));
    }

    /**
     * @return array{0: Product, 1: ProductCategory, 2: Brand}
     */
    protected function createProductThroughForm(array $overrides = []): array
    {
        $category = $overrides['category'] ?? $this->createCategory();
        $brand = $overrides['brand'] ?? $this->createBrand();

        $payload = [
            'product_name' => 'Test Product',
            'product_slug' => 'test-product-' . uniqid(),
            'product_price' => '1500000',
            'price_cost' => '1200000',
            'product_quantity' => 5,
            'product_desc' => 'Sample description',
            'product_content' => 'Sample content',
            'product_cate' => $category->id,
            'product_brand' => $brand->id,
            'product_status' => 0,
            'product_tags' => 'tag1,tag2',
            'product_image' => UploadedFile::fake()->create('product.jpg', 200, 'image/jpeg'),
            'document' => UploadedFile::fake()->create('manual.pdf', 20, 'application/pdf'),
        ];

        $payload = array_merge($payload, Arr::except($overrides, ['category', 'brand']));

        $response = $this->post('/save-product', $payload);
        $response->assertRedirect('add-product');
        $response->assertSessionHas('message');

        $product = Product::latest('id')->firstOrFail();

        return [$product, $category, $brand];
    }

    protected function createCategory(array $attributes = []): ProductCategory
    {
        $defaults = [
            'name' => 'Danh mục ' . uniqid(),
            'slug' => 'danh-muc-' . uniqid(),
            'description' => 'Mô tả danh mục',
            'is_active' => true,
            'display_order' => 0,
            'meta' => null,
        ];

        return ProductCategory::create(array_merge($defaults, $attributes));
    }

    protected function createBrand(array $attributes = []): Brand
    {
        $defaults = [
            'name' => 'Thương hiệu ' . uniqid(),
            'slug' => 'thuong-hieu-' . uniqid(),
            'description' => 'Mô tả thương hiệu',
            'is_active' => true,
            'display_order' => 0,
            'meta' => null,
        ];

        return Brand::create(array_merge($defaults, $attributes));
    }

    protected function registerPathForCleanup(?string $path): void
    {
        if ($path) {
            $this->filesToCleanup[] = $path;
        }
    }
}
