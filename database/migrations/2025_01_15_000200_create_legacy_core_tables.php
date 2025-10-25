<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Create legacy customers table
        Schema::create('legacy_customers', function (Blueprint $table) {
            $table->unsignedInteger('customer_id')->primary();
            $table->string('customer_name');
            $table->string('customer_email')->unique();
            $table->string('customer_password');
            $table->string('customer_phone');
            $table->timestamps();
        });

        // Create legacy orders table
        Schema::create('legacy_orders', function (Blueprint $table) {
            $table->unsignedInteger('order_id')->primary();
            $table->string('order_code');
            $table->unsignedInteger('customer_id');
            $table->decimal('order_total', 10, 2);
            $table->string('order_status');
            $table->timestamps();

            $table->foreign('customer_id')->references('customer_id')->on('legacy_customers')->onDelete('cascade');
        });

        // Create legacy order details table
        Schema::create('legacy_order_details', function (Blueprint $table) {
            $table->unsignedInteger('order_details_id')->primary();
            $table->unsignedInteger('order_id');
            $table->unsignedInteger('product_id');
            $table->string('product_name');
            $table->decimal('product_price', 10, 2);
            $table->integer('product_sales_quantity');
            $table->timestamps();

            $table->foreign('order_id')->references('order_id')->on('legacy_orders')->onDelete('cascade');
            $table->foreign('product_id')->references('product_id')->on('legacy_products')->onDelete('cascade');
        });

        // Create legacy products table
        Schema::create('legacy_products', function (Blueprint $table) {
            $table->unsignedInteger('product_id')->primary();
            $table->string('product_name');
            $table->text('product_tags')->nullable();
            $table->string('product_quantity', 50);
            $table->integer('product_sold')->nullable();
            $table->string('product_slug');
            $table->unsignedInteger('category_id');
            $table->unsignedInteger('brand_id');
            $table->text('product_desc');
            $table->text('product_content');
            $table->integer('product_price');
            $table->string('price_cost', 100);
            $table->string('product_image');
            $table->string('product_file', 100)->nullable();
            $table->string('product_views', 100)->nullable();
            $table->integer('product_status');
            $table->timestamps();

            $table->foreign('category_id')->references('category_id')->on('legacy_category_products')->onDelete('cascade');
            $table->foreign('brand_id')->references('brand_id')->on('legacy_brands')->onDelete('cascade');
        });

        // Create legacy category products table
        Schema::create('legacy_category_products', function (Blueprint $table) {
            $table->unsignedInteger('category_id')->primary();
            $table->string('category_name');
            $table->string('category_slug');
            $table->text('category_desc');
            $table->text('category_keywords')->nullable();
            $table->integer('category_status');
            $table->timestamps();
        });

        // Create legacy brands table
        Schema::create('legacy_brands', function (Blueprint $table) {
            $table->unsignedInteger('brand_id')->primary();
            $table->string('brand_name');
            $table->string('brand_slug');
            $table->text('brand_desc');
            $table->text('brand_keywords')->nullable();
            $table->integer('brand_status');
            $table->timestamps();
        });

        // Create legacy product attributes table
        Schema::create('legacy_product_attributes', function (Blueprint $table) {
            $table->unsignedInteger('id')->primary();
            $table->unsignedInteger('product_id');
            $table->unsignedInteger('attribute_id');
            $table->string('attribute_value');
            $table->timestamps();

            $table->foreign('product_id')->references('product_id')->on('legacy_products')->onDelete('cascade');
            $table->foreign('attribute_id')->references('id')->on('legacy_attributes')->onDelete('cascade');
        });

        // Create legacy attributes table
        Schema::create('legacy_attributes', function (Blueprint $table) {
            $table->unsignedInteger('id')->primary();
            $table->string('name', 100);
            $table->string('description', 200);
            $table->integer('status')->default(1);
            $table->timestamps();
        });

        // Create legacy gallery table
        Schema::create('legacy_gallery', function (Blueprint $table) {
            $table->unsignedInteger('gallery_id')->primary();
            $table->string('gallery_name');
            $table->string('gallery_image');
            $table->unsignedInteger('product_id');
            $table->timestamps();

            $table->foreign('product_id')->references('product_id')->on('legacy_products')->onDelete('cascade');
        });

        // Create legacy comments table
        Schema::create('legacy_comments', function (Blueprint $table) {
            $table->unsignedInteger('comment_id')->primary();
            $table->string('comment_name');
            $table->string('comment_email');
            $table->text('comment_content');
            $table->unsignedInteger('product_id');
            $table->integer('comment_status');
            $table->timestamps();

            $table->foreign('product_id')->references('product_id')->on('legacy_products')->onDelete('cascade');
        });

        // Create legacy ratings table
        Schema::create('legacy_ratings', function (Blueprint $table) {
            $table->unsignedInteger('rating_id')->primary();
            $table->unsignedInteger('product_id');
            $table->unsignedInteger('customer_id');
            $table->integer('rating');
            $table->timestamps();

            $table->foreign('product_id')->references('product_id')->on('legacy_products')->onDelete('cascade');
            $table->foreign('customer_id')->references('customer_id')->on('legacy_customers')->onDelete('cascade');
        });

        // Create legacy coupons table
        Schema::create('legacy_coupons', function (Blueprint $table) {
            $table->unsignedInteger('coupon_id')->primary();
            $table->string('coupon_name');
            $table->string('coupon_code');
            $table->integer('coupon_number');
            $table->integer('coupon_condition');
            $table->integer('coupon_price');
            $table->timestamps();
        });

        // Create legacy posts table
        Schema::create('legacy_posts', function (Blueprint $table) {
            $table->unsignedInteger('post_id')->primary();
            $table->string('post_title');
            $table->string('post_slug');
            $table->text('post_desc');
            $table->longText('post_content');
            $table->string('post_meta_desc')->nullable();
            $table->text('post_meta_keywords')->nullable();
            $table->integer('post_status');
            $table->string('post_image')->nullable();
            $table->unsignedInteger('cate_post_id');
            $table->timestamps();

            $table->foreign('cate_post_id')->references('cate_post_id')->on('legacy_category_posts')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('legacy_posts');
        Schema::dropIfExists('legacy_coupons');
        Schema::dropIfExists('legacy_ratings');
        Schema::dropIfExists('legacy_comments');
        Schema::dropIfExists('legacy_gallery');
        Schema::dropIfExists('legacy_attributes');
        Schema::dropIfExists('legacy_product_attributes');
        Schema::dropIfExists('legacy_brands');
        Schema::dropIfExists('legacy_category_products');
        Schema::dropIfExists('legacy_products');
        Schema::dropIfExists('legacy_order_details');
        Schema::dropIfExists('legacy_orders');
        Schema::dropIfExists('legacy_customers');
    }
};
