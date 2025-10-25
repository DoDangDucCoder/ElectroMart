<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('segment_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('segment_id')->constrained('customer_segments')->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->timestamp('joined_at')->nullable();
            $table->timestamps();

            $table->unique(['segment_id', 'customer_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('segment_members');
    }
};
