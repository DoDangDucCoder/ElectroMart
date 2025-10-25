<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $this->info('Creating performance indexes...');

        // Products table indexes
        if (Schema::hasTable('products')) {
            $this->createProductIndexes();
        }

        // Orders table indexes
        if (Schema::hasTable('orders')) {
            $this->createOrderIndexes();
        }

        // Customers table indexes
        if (Schema::hasTable('customers')) {
            $this->createCustomerIndexes();
        }

        // Categories table indexes
        if (Schema::hasTable('product_categories')) {
            $this->createCategoryIndexes();
        }

        // Legacy tables indexes
        $this->createLegacyTableIndexes();
    }

    private function createProductIndexes(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Composite indexes for common queries
            $table->index(['is_active', 'is_featured'], 'idx_products_active_featured');
            $table->index(['primary_category_id', 'is_active'], 'idx_products_category_active');
            $table->index(['brand_id', 'is_active'], 'idx_products_brand_active');
            $table->index(['price', 'is_active'], 'idx_products_price_active');
            $table->index(['stock_quantity', 'is_active'], 'idx_products_stock_active');
            $table->index(['created_at', 'is_active'], 'idx_products_created_active');
            $table->index(['views_count', 'is_active'], 'idx_products_views_active');
            
            // Search indexes
            $table->index(['name'], 'idx_products_name');
            $table->index(['slug'], 'idx_products_slug');
            $table->index(['sku'], 'idx_products_sku');
            
            // Full-text search index
            $table->fullText(['name', 'description'], 'idx_products_search');
        });
    }

    private function createOrderIndexes(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Status and customer indexes
            $table->index(['customer_id', 'status'], 'idx_orders_customer_status');
            $table->index(['status', 'created_at'], 'idx_orders_status_created');
            $table->index(['payment_status', 'created_at'], 'idx_orders_payment_created');
            $table->index(['fulfillment_status', 'created_at'], 'idx_orders_fulfillment_created');
            
            // Date range queries
            $table->index(['created_at'], 'idx_orders_created');
            $table->index(['placed_at'], 'idx_orders_placed');
            $table->index(['completed_at'], 'idx_orders_completed');
            
            // Financial queries
            $table->index(['total', 'status'], 'idx_orders_total_status');
            $table->index(['currency', 'created_at'], 'idx_orders_currency_created');
            
            // Code lookup
            $table->index(['code'], 'idx_orders_code');
        });
    }

    private function createCustomerIndexes(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            // Email and phone indexes
            $table->index(['email'], 'idx_customers_email');
            $table->index(['phone'], 'idx_customers_phone');
            
            // Status and verification
            $table->index(['is_active', 'email_verified_at'], 'idx_customers_active_verified');
            $table->index(['created_at', 'is_active'], 'idx_customers_created_active');
            
            // Loyalty system
            $table->index(['loyalty_tier_id'], 'idx_customers_loyalty_tier');
            $table->index(['loyalty_points_balance'], 'idx_customers_loyalty_points');
        });
    }

    private function createCategoryIndexes(): void
    {
        Schema::table('product_categories', function (Blueprint $table) {
            // Hierarchy indexes
            $table->index(['parent_id', 'is_active'], 'idx_categories_parent_active');
            $table->index(['is_active', 'display_order'], 'idx_categories_active_order');
            
            // Search indexes
            $table->index(['name'], 'idx_categories_name');
            $table->index(['slug'], 'idx_categories_slug');
            $table->index(['code'], 'idx_categories_code');
            
            // Full-text search
            $table->fullText(['name', 'description'], 'idx_categories_search');
        });
    }

    private function createLegacyTableIndexes(): void
    {
        // Legacy product table indexes
        if (Schema::hasTable('tbl_product')) {
            DB::statement('CREATE INDEX IF NOT EXISTS idx_tbl_product_category ON tbl_product(category_id)');
            DB::statement('CREATE INDEX IF NOT EXISTS idx_tbl_product_brand ON tbl_product(brand_id)');
            DB::statement('CREATE INDEX IF NOT EXISTS idx_tbl_product_status ON tbl_product(product_status)');
            DB::statement('CREATE INDEX IF NOT EXISTS idx_tbl_product_price ON tbl_product(product_price)');
            DB::statement('CREATE INDEX IF NOT EXISTS idx_tbl_product_created ON tbl_product(created_at)');
        }

        // Legacy order table indexes
        if (Schema::hasTable('tbl_order')) {
            DB::statement('CREATE INDEX IF NOT EXISTS idx_tbl_order_customer ON tbl_order(customer_id)');
            DB::statement('CREATE INDEX IF NOT EXISTS idx_tbl_order_status ON tbl_order(order_status)');
            DB::statement('CREATE INDEX IF NOT EXISTS idx_tbl_order_created ON tbl_order(created_at)');
        }

        // Legacy customer table indexes
        if (Schema::hasTable('tbl_customer')) {
            DB::statement('CREATE INDEX IF NOT EXISTS idx_tbl_customer_email ON tbl_customer(customer_email)');
            DB::statement('CREATE INDEX IF NOT EXISTS idx_tbl_customer_phone ON tbl_customer(customer_phone)');
            DB::statement('CREATE INDEX IF NOT EXISTS idx_tbl_customer_created ON tbl_customer(created_at)');
        }
    }

    public function down(): void
    {
        $this->info('Dropping performance indexes...');

        // Drop product indexes
        if (Schema::hasTable('products')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropIndex('idx_products_active_featured');
                $table->dropIndex('idx_products_category_active');
                $table->dropIndex('idx_products_brand_active');
                $table->dropIndex('idx_products_price_active');
                $table->dropIndex('idx_products_stock_active');
                $table->dropIndex('idx_products_created_active');
                $table->dropIndex('idx_products_views_active');
                $table->dropIndex('idx_products_name');
                $table->dropIndex('idx_products_slug');
                $table->dropIndex('idx_products_sku');
                $table->dropIndex('idx_products_search');
            });
        }

        // Drop order indexes
        if (Schema::hasTable('orders')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropIndex('idx_orders_customer_status');
                $table->dropIndex('idx_orders_status_created');
                $table->dropIndex('idx_orders_payment_created');
                $table->dropIndex('idx_orders_fulfillment_created');
                $table->dropIndex('idx_orders_created');
                $table->dropIndex('idx_orders_placed');
                $table->dropIndex('idx_orders_completed');
                $table->dropIndex('idx_orders_total_status');
                $table->dropIndex('idx_orders_currency_created');
                $table->dropIndex('idx_orders_code');
            });
        }

        // Drop customer indexes
        if (Schema::hasTable('customers')) {
            Schema::table('customers', function (Blueprint $table) {
                $table->dropIndex('idx_customers_email');
                $table->dropIndex('idx_customers_phone');
                $table->dropIndex('idx_customers_active_verified');
                $table->dropIndex('idx_customers_created_active');
                $table->dropIndex('idx_customers_loyalty_tier');
                $table->dropIndex('idx_customers_loyalty_points');
            });
        }

        // Drop category indexes
        if (Schema::hasTable('product_categories')) {
            Schema::table('product_categories', function (Blueprint $table) {
                $table->dropIndex('idx_categories_parent_active');
                $table->dropIndex('idx_categories_active_order');
                $table->dropIndex('idx_categories_name');
                $table->dropIndex('idx_categories_slug');
                $table->dropIndex('idx_categories_code');
                $table->dropIndex('idx_categories_search');
            });
        }
    }

    private function info(string $message): void
    {
        echo "INFO: {$message}\n";
    }
};
