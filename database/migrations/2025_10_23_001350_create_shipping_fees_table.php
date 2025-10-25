<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipping_fees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('city_id')->nullable()->constrained('locations')->nullOnDelete();
            $table->foreignId('province_id')->nullable()->constrained('locations')->nullOnDelete();
            $table->foreignId('ward_id')->nullable()->constrained('locations')->nullOnDelete();
            $table->decimal('amount', 15, 2)->default(0);
            $table->string('currency', 3)->default('VND');
            $table->timestamps();

            $table->index(['city_id', 'province_id', 'ward_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipping_fees');
    }
};
