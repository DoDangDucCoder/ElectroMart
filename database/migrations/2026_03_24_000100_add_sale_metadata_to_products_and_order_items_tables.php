<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (! Schema::hasColumn('products', 'sale_type')) {
                $table->enum('sale_type', ['percent', 'fixed'])->nullable()->after('price');
            }

            if (! Schema::hasColumn('products', 'sale_value')) {
                $table->decimal('sale_value', 15, 2)->nullable()->after('sale_type');
            }
        });

        Schema::table('order_items', function (Blueprint $table) {
            if (! Schema::hasColumn('order_items', 'original_price')) {
                $table->decimal('original_price', 15, 2)->nullable()->after('attributes');
            }

            if (! Schema::hasColumn('order_items', 'cost_price')) {
                $table->decimal('cost_price', 15, 2)->default(0)->after('sale_price');
            }

            if (! Schema::hasColumn('order_items', 'sale_type')) {
                $table->enum('sale_type', ['percent', 'fixed'])->nullable()->after('cost_price');
            }

            if (! Schema::hasColumn('order_items', 'sale_value')) {
                $table->decimal('sale_value', 15, 2)->nullable()->after('sale_type');
            }

            if (! Schema::hasColumn('order_items', 'sale_amount')) {
                $table->decimal('sale_amount', 15, 2)->default(0)->after('sale_value');
            }
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $drops = collect(['original_price', 'cost_price', 'sale_type', 'sale_value', 'sale_amount'])
                ->filter(fn (string $column) => Schema::hasColumn('order_items', $column))
                ->all();

            if ($drops !== []) {
                $table->dropColumn($drops);
            }
        });

        Schema::table('products', function (Blueprint $table) {
            $drops = collect(['sale_type', 'sale_value'])
                ->filter(fn (string $column) => Schema::hasColumn('products', $column))
                ->all();

            if ($drops !== []) {
                $table->dropColumn($drops);
            }
        });
    }
};
