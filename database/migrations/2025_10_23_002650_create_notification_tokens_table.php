<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('notification_tokens');

        Schema::create('notification_tokens', function (Blueprint $table) {
            $table->id();
            $table->morphs('notifiable'); // customer/admin etc.
            $table->string('token')->unique();
            $table->string('device_type')->nullable();
            $table->string('platform')->nullable(); // ios/android/web
            $table->string('app_version')->nullable();
            $table->boolean('is_active')->default(true);
            $table->json('meta')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_tokens');
    }
};
