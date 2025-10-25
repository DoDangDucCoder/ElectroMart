<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_views', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->foreignId('customer_id')->nullable()->constrained('customers')->onDelete('set null');
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamp('viewed_at');
            $table->timestamps();
            
            $table->index(['product_id', 'viewed_at']);
            $table->index(['customer_id', 'viewed_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_views');
    }
};