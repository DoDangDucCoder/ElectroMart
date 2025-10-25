<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('analytics_daily', function (Blueprint $table) {
            $table->id();
            $table->date('date')->unique();
            $table->unsignedInteger('orders_count')->default(0);
            $table->decimal('sales', 15, 2)->default(0);
            $table->decimal('profit', 15, 2)->default(0);
            $table->unsignedInteger('items_sold')->default(0);
            $table->unsignedInteger('new_customers')->default(0);
            $table->unsignedInteger('visits')->default(0);
            $table->unsignedInteger('marketing_spend')->default(0);
            $table->json('meta')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('analytics_daily');
    }
};
