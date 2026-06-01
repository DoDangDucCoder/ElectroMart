<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('UPDATE products SET sale_price = price WHERE sale_price <= 0');
        DB::statement('UPDATE order_items SET sale_price = price WHERE sale_price <= 0');
        DB::statement('UPDATE product_variants SET sale_price = price WHERE sale_price <= 0');
    }

    public function down(): void
    {
        DB::statement('UPDATE products SET sale_price = 0 WHERE sale_type IS NULL AND sale_value = 0');
        DB::statement('UPDATE order_items SET sale_price = 0 WHERE sale_type IS NULL AND sale_value = 0 AND sale_amount = 0');
    }
};
