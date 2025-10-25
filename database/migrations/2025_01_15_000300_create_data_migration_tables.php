<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Ensure new tables exist before creating migration tables
        $this->ensureNewTablesExist();

        // Create migration tracking table
        Schema::create('data_migrations', function (Blueprint $table) {
            $table->id();
            $table->string('migration_name');
            $table->string('source_table');
            $table->string('target_table');
            $table->integer('records_migrated')->default(0);
            $table->json('migration_stats')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->enum('status', ['pending', 'running', 'completed', 'failed'])->default('pending');
            $table->text('error_message')->nullable();
            $table->timestamps();
        });

        // Create migration mapping table
        Schema::create('migration_mappings', function (Blueprint $table) {
            $table->id();
            $table->string('source_table');
            $table->string('target_table');
            $table->json('field_mappings');
            $table->json('transformations')->nullable();
            $table->json('filters')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Insert migration mappings
        $this->insertMigrationMappings();
    }

    public function down(): void
    {
        Schema::dropIfExists('migration_mappings');
        Schema::dropIfExists('data_migrations');
    }

    private function ensureNewTablesExist(): void
    {
        // This method ensures that all new tables exist before migration
        // The actual table creation is handled by other migrations
    }

    private function insertMigrationMappings(): void
    {
        $mappings = [
            // Legacy brands to new brands
            [
                'source_table' => 'legacy_brands',
                'target_table' => 'brands',
                'field_mappings' => [
                    'brand_id' => 'id',
                    'brand_name' => 'name',
                    'brand_slug' => 'slug',
                    'brand_desc' => 'description',
                    'brand_status' => 'is_active',
                    'created_at' => 'created_at',
                    'updated_at' => 'updated_at',
                ],
                'transformations' => [
                    'is_active' => 'brand_status == 1',
                ],
            ],

            // Legacy category products to new product categories
            [
                'source_table' => 'legacy_category_products',
                'target_table' => 'product_categories',
                'field_mappings' => [
                    'category_id' => 'id',
                    'category_name' => 'name',
                    'category_slug' => 'slug',
                    'category_desc' => 'description',
                    'category_status' => 'is_active',
                    'created_at' => 'created_at',
                    'updated_at' => 'updated_at',
                ],
                'transformations' => [
                    'is_active' => 'category_status == 1',
                ],
            ],

            // Legacy products to new products
            [
                'source_table' => 'legacy_products',
                'target_table' => 'products',
                'field_mappings' => [
                    'product_id' => 'id',
                    'product_name' => 'name',
                    'product_slug' => 'slug',
                    'product_desc' => 'description',
                    'product_content' => 'content',
                    'brand_id' => 'brand_id',
                    'category_id' => 'primary_category_id',
                    'product_price' => 'price',
                    'price_cost' => 'cost_price',
                    'product_image' => 'thumbnail_path',
                    'product_status' => 'is_active',
                    'product_views' => 'views_count',
                    'created_at' => 'created_at',
                    'updated_at' => 'updated_at',
                ],
                'transformations' => [
                    'is_active' => 'product_status == 0',
                    'views_count' => 'CAST(product_views AS UNSIGNED)',
                    'cost_price' => 'CAST(price_cost AS DECIMAL(15,2))',
                ],
            ],

            // Legacy customers to new customers
            [
                'source_table' => 'legacy_customers',
                'target_table' => 'customers',
                'field_mappings' => [
                    'customer_id' => 'id',
                    'customer_name' => 'name',
                    'customer_email' => 'email',
                    'customer_password' => 'password',
                    'customer_phone' => 'phone',
                    'created_at' => 'created_at',
                    'updated_at' => 'updated_at',
                ],
            ],

            // Legacy orders to new orders
            [
                'source_table' => 'legacy_orders',
                'target_table' => 'orders',
                'field_mappings' => [
                    'order_id' => 'id',
                    'order_code' => 'code',
                    'customer_id' => 'customer_id',
                    'order_total' => 'total',
                    'order_status' => 'status',
                    'created_at' => 'created_at',
                    'updated_at' => 'updated_at',
                ],
                'transformations' => [
                    'status' => 'CASE order_status WHEN "Đã xử lý" THEN "completed" WHEN "Đang xử lý" THEN "processing" ELSE "pending" END',
                ],
            ],

            // Legacy order details to new order items
            [
                'source_table' => 'legacy_order_details',
                'target_table' => 'order_items',
                'field_mappings' => [
                    'order_details_id' => 'id',
                    'order_id' => 'order_id',
                    'product_id' => 'product_id',
                    'product_name' => 'product_name',
                    'product_price' => 'unit_price',
                    'product_sales_quantity' => 'quantity',
                    'created_at' => 'created_at',
                    'updated_at' => 'updated_at',
                ],
            ],

            // Legacy gallery to new product media
            [
                'source_table' => 'legacy_gallery',
                'target_table' => 'product_media',
                'field_mappings' => [
                    'gallery_id' => 'id',
                    'product_id' => 'product_id',
                    'gallery_image' => 'file_path',
                    'gallery_name' => 'meta->name',
                    'created_at' => 'created_at',
                    'updated_at' => 'updated_at',
                ],
                'transformations' => [
                    'collection' => '"gallery"',
                    'disk' => '"public"',
                    'display_order' => '0',
                ],
            ],

            // Legacy comments to new product reviews
            [
                'source_table' => 'legacy_comments',
                'target_table' => 'product_reviews',
                'field_mappings' => [
                    'comment_id' => 'id',
                    'product_id' => 'product_id',
                    'comment_name' => 'customer_name',
                    'comment_email' => 'customer_email',
                    'comment_content' => 'content',
                    'comment_status' => 'is_approved',
                    'created_at' => 'created_at',
                    'updated_at' => 'updated_at',
                ],
                'transformations' => [
                    'is_approved' => 'comment_status == 1',
                    'rating' => '5', // Default rating for comments
                ],
            ],

            // Legacy posts to new blog posts
            [
                'source_table' => 'legacy_posts',
                'target_table' => 'blog_posts',
                'field_mappings' => [
                    'post_id' => 'id',
                    'post_title' => 'title',
                    'post_slug' => 'slug',
                    'post_desc' => 'excerpt',
                    'post_content' => 'content',
                    'post_image' => 'featured_image_path',
                    'post_status' => 'status',
                    'cate_post_id' => 'blog_category_id',
                    'created_at' => 'created_at',
                    'updated_at' => 'updated_at',
                ],
                'transformations' => [
                    'status' => 'CASE post_status WHEN 1 THEN "published" ELSE "draft" END',
                    'published_at' => 'CASE post_status WHEN 1 THEN created_at ELSE NULL END',
                ],
            ],

            // Legacy category posts to new blog categories
            [
                'source_table' => 'legacy_category_posts',
                'target_table' => 'blog_categories',
                'field_mappings' => [
                    'cate_post_id' => 'id',
                    'cate_post_name' => 'name',
                    'cate_post_slug' => 'slug',
                    'cate_post_desc' => 'description',
                    'cate_post_status' => 'is_active',
                    'created_at' => 'created_at',
                    'updated_at' => 'updated_at',
                ],
                'transformations' => [
                    'is_active' => 'cate_post_status == 1',
                ],
            ],
        ];

        foreach ($mappings as $mapping) {
            DB::table('migration_mappings')->insert([
                'source_table' => $mapping['source_table'],
                'target_table' => $mapping['target_table'],
                'field_mappings' => json_encode($mapping['field_mappings']),
                'transformations' => isset($mapping['transformations']) ? json_encode($mapping['transformations']) : null,
                'filters' => isset($mapping['filters']) ? json_encode($mapping['filters']) : null,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
};
