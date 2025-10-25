<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('marketing_campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->enum('status', ['draft', 'pending_approval', 'approved', 'running', 'paused', 'completed', 'cancelled'])->default('draft');
            $table->text('objective')->nullable();
            $table->text('description')->nullable();
            $table->json('segments')->nullable(); // list segment ids targetted
            $table->dateTime('scheduled_at')->nullable();
            $table->dateTime('ends_at')->nullable();
            $table->unsignedInteger('expected_recipients')->nullable();
            $table->decimal('budget', 15, 2)->nullable();
            $table->string('currency', 3)->default('VND');
            $table->json('meta')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'scheduled_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('marketing_campaigns');
    }
};
