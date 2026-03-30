<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_views', function (Blueprint $table) {
            $table->index(['customer_id', 'product_id', 'viewed_at'], 'idx_product_views_customer_product_viewed');
            $table->index(['ip_address', 'product_id', 'viewed_at'], 'idx_product_views_ip_product_viewed');
        });
    }

    public function down(): void
    {
        Schema::table('product_views', function (Blueprint $table) {
            $table->dropIndex('idx_product_views_customer_product_viewed');
            $table->dropIndex('idx_product_views_ip_product_viewed');
        });
    }
};
