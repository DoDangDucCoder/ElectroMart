<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attributes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attribute_group_id')
                ->nullable()
                ->constrained('attribute_groups')
                ->nullOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('code')->nullable()->unique();
            $table->string('type')->default('text'); // text, number, select, multiselect
            $table->boolean('is_filterable')->default(false);
            $table->boolean('is_required')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('display_order')->default(0);
            $table->json('config')->nullable(); // thêm config cho attribute (unit, options...)
            $table->timestamps();
            $table->softDeletes();

            $table->index(['attribute_group_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attributes');
    }
};
