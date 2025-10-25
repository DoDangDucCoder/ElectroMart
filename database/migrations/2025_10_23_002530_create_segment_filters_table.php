<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('segment_filters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('segment_id')->constrained('customer_segments')->cascadeOnDelete();
            $table->string('field');
            $table->string('operator');
            $table->string('value')->nullable();
            $table->json('value_json')->nullable();
            $table->unsignedTinyInteger('group')->default(0);
            $table->boolean('is_or_group')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('segment_filters');
    }
};
