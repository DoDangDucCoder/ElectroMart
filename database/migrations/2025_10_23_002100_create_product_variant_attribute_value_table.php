<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('product_variant_attribute_value');

        Schema::create('product_variant_attribute_value', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_variant_id')->constrained('product_variants')->cascadeOnDelete();
            $table->foreignId('attribute_id')->constrained('attributes')->cascadeOnDelete();
            $table->foreignId('attribute_value_id')->nullable()->constrained('attribute_values')->nullOnDelete();
            $table->string('custom_value')->nullable(); // trường hợp attribute không dùng preset
            $table->timestamps();

            $table->unique(['product_variant_id', 'attribute_id'], 'product_variant_attribute_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_variant_attribute_value');
    }
};
