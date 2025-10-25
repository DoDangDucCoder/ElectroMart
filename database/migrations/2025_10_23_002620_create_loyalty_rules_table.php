<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loyalty_rules', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->enum('event', ['order_completed', 'signup', 'birthday', 'review', 'custom']);
            $table->decimal('points', 15, 2)->default(0);
            $table->decimal('multiplier', 8, 4)->default(0);
            $table->boolean('is_active')->default(true);
            $table->json('conditions')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loyalty_rules');
    }
};
