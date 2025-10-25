<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loyalty_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->foreignId('order_id')->nullable()->constrained('orders')->nullOnDelete();
            $table->foreignId('rule_id')->nullable()->constrained('loyalty_rules')->nullOnDelete();
            $table->foreignId('tier_id')->nullable()->constrained('loyalty_tiers')->nullOnDelete();
            $table->decimal('points', 15, 2);
            $table->enum('type', ['earn', 'redeem', 'adjust'])->default('earn');
            $table->string('description')->nullable();
            $table->decimal('balance_after', 15, 2)->default(0);
            $table->timestamps();

            $table->index(['customer_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loyalty_transactions');
    }
};
