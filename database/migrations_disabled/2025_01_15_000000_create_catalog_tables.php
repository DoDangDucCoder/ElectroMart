<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Create product_categories table if not exists
        if (!Schema::hasTable('product_categories')) {
            Schema::create('product_categories', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->string('code')->nullable()->unique();
                $table->foreignId('parent_id')
                    ->nullable()
                    ->constrained('product_categories')
                    ->nullOnDelete();
                $table->text('description')->nullable();
                $table->string('thumbnail_path')->nullable();
                $table->boolean('is_active')->default(true);
                $table->unsignedInteger('display_order')->default(0);
                $table->string('meta_title')->nullable();
                $table->text('meta_description')->nullable();
                $table->json('meta')->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->index(['is_active', 'display_order']);
                $table->index(['parent_id', 'is_active']);
            });
        }

        // Create brands table if not exists
        if (!Schema::hasTable('brands')) {
            Schema::create('brands', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->string('code')->nullable()->unique();
                $table->text('description')->nullable();
                $table->string('logo_path')->nullable();
                $table->string('website')->nullable();
                $table->boolean('is_active')->default(true);
                $table->unsignedInteger('display_order')->default(0);
                $table->string('meta_title')->nullable();
                $table->text('meta_description')->nullable();
                $table->json('meta')->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->index(['is_active', 'display_order']);
            });
        }

        // Create product_category_pivot table if not exists
        if (!Schema::hasTable('product_category_pivot')) {
            Schema::create('product_category_pivot', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
                $table->foreignId('category_id')->constrained('product_categories')->cascadeOnDelete();
                $table->timestamps();

                $table->unique(['product_id', 'category_id']);
                $table->index(['category_id', 'product_id']);
            });
        }

        // Create product_media table if not exists
        if (!Schema::hasTable('product_media')) {
            Schema::create('product_media', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
                $table->string('file_path');
                $table->string('disk')->default('public');
                $table->string('collection')->default('gallery');
                $table->unsignedInteger('display_order')->default(0);
                $table->json('meta')->nullable();
                $table->timestamps();

                $table->index(['product_id', 'collection', 'display_order']);
            });
        }

        // Create product_reviews table if not exists
        if (!Schema::hasTable('product_reviews')) {
            Schema::create('product_reviews', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
                $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
                $table->string('customer_name')->nullable();
                $table->string('customer_email')->nullable();
                $table->unsignedTinyInteger('rating')->default(5);
                $table->string('title')->nullable();
                $table->text('content')->nullable();
                $table->boolean('is_approved')->default(false);
                $table->boolean('is_featured')->default(false);
                $table->json('meta')->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->index(['product_id', 'is_approved']);
                $table->index(['customer_id', 'product_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('product_reviews');
        Schema::dropIfExists('product_media');
        Schema::dropIfExists('product_category_pivot');
        Schema::dropIfExists('brands');
        Schema::dropIfExists('product_categories');
    }
};
