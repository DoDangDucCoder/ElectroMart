<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_search_queries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->foreignId('category_id')->nullable()->constrained('product_categories')->nullOnDelete();
            $table->string('keyword')->nullable();
            $table->string('product_code')->nullable();
            $table->decimal('price_min', 15, 2)->nullable();
            $table->decimal('price_max', 15, 2)->nullable();
            $table->string('sort')->nullable();
            $table->unsignedInteger('results_count')->default(0);
            $table->string('source', 32)->default('web');
            $table->string('ip_address')->nullable();
            $table->timestamp('searched_at');
            $table->timestamps();

            $table->index(['searched_at'], 'idx_product_search_queries_searched_at');
            $table->index(['keyword', 'searched_at'], 'idx_product_search_queries_keyword_searched_at');
            $table->index(['customer_id', 'searched_at'], 'idx_product_search_queries_customer_searched_at');
            $table->index(['source', 'searched_at'], 'idx_product_search_queries_source_searched_at');
        });

        Schema::create('product_recommendation_impressions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->string('context', 64)->default('storefront');
            $table->string('source_label')->nullable();
            $table->string('ip_address')->nullable();
            $table->timestamp('impressed_at');
            $table->timestamps();

            $table->index(['product_id', 'impressed_at'], 'idx_product_reco_impressions_product_impressed_at');
            $table->index(['customer_id', 'impressed_at'], 'idx_product_reco_impressions_customer_impressed_at');
            $table->index(['context', 'impressed_at'], 'idx_product_reco_impressions_context_impressed_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_recommendation_impressions');
        Schema::dropIfExists('product_search_queries');
    }
};
