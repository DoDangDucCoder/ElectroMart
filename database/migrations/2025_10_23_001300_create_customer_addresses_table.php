<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customer_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')
                ->constrained('customers')
                ->cascadeOnDelete();
            $table->string('label')->nullable(); // Nhà riêng, Văn phòng...
            $table->string('recipient_name');
            $table->string('recipient_phone')->nullable();
            $table->string('email')->nullable();
            $table->string('country')->default('Vietnam');
            $table->foreignId('location_city_id')->nullable()->constrained('locations')->nullOnDelete();
            $table->foreignId('location_province_id')->nullable()->constrained('locations')->nullOnDelete();
            $table->foreignId('location_ward_id')->nullable()->constrained('locations')->nullOnDelete();
            $table->string('postal_code')->nullable();
            $table->string('address_line1');
            $table->string('address_line2')->nullable();
            $table->boolean('is_default')->default(false);
            $table->json('meta')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['customer_id', 'is_default']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_addresses');
    }
};
