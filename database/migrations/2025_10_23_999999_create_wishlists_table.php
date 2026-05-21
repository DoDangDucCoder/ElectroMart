<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Fix #26: Tạo bảng wishlists để lưu SP yêu thích theo customer_id
     * Thay thế wishlist dùng session (mất khi logout).
     */
    public function up(): void
    {
        Schema::create('wishlists', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('product_id');
            $table->timestamps();

            $table->unique(['customer_id', 'product_id']); // Không cho trùng
            $table->index('customer_id');
            $table->index('product_id');

            $table->foreign('customer_id')
                  ->references('id')->on('customers')
                  ->onDelete('cascade');
            $table->foreign('product_id')
                  ->references('id')->on('products')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wishlists');
    }
};
