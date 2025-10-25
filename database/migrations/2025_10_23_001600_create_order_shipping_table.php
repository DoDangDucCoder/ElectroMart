<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_shipping', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->string('recipient_name');
            $table->string('recipient_phone')->nullable();
            $table->string('email')->nullable();
            $table->string('country')->default('Vietnam');
            $table->foreignId('city_id')->nullable()->constrained('locations')->nullOnDelete();
            $table->foreignId('province_id')->nullable()->constrained('locations')->nullOnDelete();
            $table->foreignId('ward_id')->nullable()->constrained('locations')->nullOnDelete();
            $table->string('postal_code')->nullable();
            $table->string('address_line1');
            $table->string('address_line2')->nullable();
            $table->string('shipping_method')->nullable();
            $table->string('tracking_number')->nullable();
            $table->decimal('shipping_fee', 15, 2)->default(0);
            $table->decimal('insurance_fee', 15, 2)->default(0);
            $table->json('meta')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_shipping');
    }
};
