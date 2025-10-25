<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campaign_metrics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained('marketing_campaigns')->cascadeOnDelete();
            $table->date('date');
            $table->unsignedInteger('recipients')->default(0);
            $table->unsignedInteger('sent')->default(0);
            $table->unsignedInteger('delivered')->default(0);
            $table->unsignedInteger('opened')->default(0);
            $table->unsignedInteger('clicked')->default(0);
            $table->unsignedInteger('conversions')->default(0);
            $table->decimal('revenue', 15, 2)->default(0);
            $table->decimal('cost', 15, 2)->default(0);
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->unique(['campaign_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campaign_metrics');
    }
};
