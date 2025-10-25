<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('automation_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('automation_rule_id')->constrained('automation_rules')->cascadeOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->enum('status', ['pending', 'executed', 'skipped', 'failed'])->default('pending');
            $table->text('message')->nullable();
            $table->json('payload')->nullable();
            $table->timestamps();

            $table->index(['automation_rule_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('automation_logs');
    }
};
