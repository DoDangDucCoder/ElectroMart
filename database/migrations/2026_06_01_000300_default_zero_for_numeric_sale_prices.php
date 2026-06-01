<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('UPDATE products SET sale_price = 0 WHERE sale_price IS NULL');
        DB::statement('UPDATE products SET sale_value = 0 WHERE sale_value IS NULL');
        DB::statement('UPDATE order_items SET sale_price = 0 WHERE sale_price IS NULL');
        DB::statement('UPDATE order_items SET sale_value = 0 WHERE sale_value IS NULL');
        DB::statement('UPDATE product_variants SET sale_price = 0 WHERE sale_price IS NULL');
        DB::statement('UPDATE product_variants SET cost_price = 0 WHERE cost_price IS NULL');

        DB::statement('ALTER TABLE products MODIFY sale_price DECIMAL(15,2) NOT NULL DEFAULT 0');
        DB::statement('ALTER TABLE products MODIFY sale_value DECIMAL(15,2) NOT NULL DEFAULT 0');
        DB::statement('ALTER TABLE order_items MODIFY original_price DECIMAL(15,2) NOT NULL DEFAULT 0');
        DB::statement('ALTER TABLE order_items MODIFY sale_price DECIMAL(15,2) NOT NULL DEFAULT 0');
        DB::statement('ALTER TABLE order_items MODIFY sale_value DECIMAL(15,2) NOT NULL DEFAULT 0');
        DB::statement('ALTER TABLE order_items MODIFY sale_amount DECIMAL(15,2) NOT NULL DEFAULT 0');
        DB::statement('ALTER TABLE order_items MODIFY discount_amount DECIMAL(15,2) NOT NULL DEFAULT 0');
        DB::statement('ALTER TABLE product_variants MODIFY sale_price DECIMAL(15,2) NOT NULL DEFAULT 0');
        DB::statement('ALTER TABLE product_variants MODIFY cost_price DECIMAL(15,2) NOT NULL DEFAULT 0');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE products MODIFY sale_price DECIMAL(15,2) NULL DEFAULT NULL');
        DB::statement('ALTER TABLE products MODIFY sale_value DECIMAL(15,2) NULL DEFAULT NULL');
        DB::statement('ALTER TABLE order_items MODIFY original_price DECIMAL(15,2) NULL DEFAULT NULL');
        DB::statement('ALTER TABLE order_items MODIFY sale_price DECIMAL(15,2) NULL DEFAULT NULL');
        DB::statement('ALTER TABLE order_items MODIFY sale_value DECIMAL(15,2) NULL DEFAULT NULL');
        DB::statement('ALTER TABLE product_variants MODIFY sale_price DECIMAL(15,2) NULL DEFAULT NULL');
        DB::statement('ALTER TABLE product_variants MODIFY cost_price DECIMAL(15,2) NULL DEFAULT NULL');
    }
};
