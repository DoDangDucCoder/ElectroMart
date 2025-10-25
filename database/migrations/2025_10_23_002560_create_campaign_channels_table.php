<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campaign_channels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained('marketing_campaigns')->cascadeOnDelete();
            $table->enum('channel', ['email', 'sms', 'push', 'inapp']);
            $table->string('template_name')->nullable();
            $table->text('subject')->nullable();
            $table->longText('content')->nullable();
            $table->json('settings')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['campaign_id', 'channel']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campaign_channels');
    }
};
