<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_media', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')
                ->constrained('products')
                ->cascadeOnDelete();
            $table->string('file_path');
            $table->string('disk')->default('public');
            $table->string('collection')->default('default'); // gallery, thumbnail, manual, etc
            $table->unsignedInteger('display_order')->default(0);
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['product_id', 'collection']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_media');
    }
};
