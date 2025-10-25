<?php

namespace App\Application\Shared\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class DatabaseOptimizationService
{
    public function optimizeTables(): array
    {
        $results = [];
        
        try {
            // Get all tables
            $tables = $this->getAllTables();
            
            foreach ($tables as $table) {
                $results[$table] = $this->optimizeTable($table);
            }
            
            Log::info('Database optimization completed', ['results' => $results]);
            
        } catch (\Exception $e) {
            Log::error('Database optimization failed', ['error' => $e->getMessage()]);
            $results['error'] = $e->getMessage();
        }
        
        return $results;
    }

    public function analyzeTables(): array
    {
        $results = [];
        
        try {
            $tables = $this->getAllTables();
            
            foreach ($tables as $table) {
                $results[$table] = $this->analyzeTable($table);
            }
            
        } catch (\Exception $e) {
            Log::error('Database analysis failed', ['error' => $e->getMessage()]);
            $results['error'] = $e->getMessage();
        }
        
        return $results;
    }

    public function addMissingIndexes(): array
    {
        $indexes = [
            // Products table indexes
            'products' => [
                'idx_products_slug' => 'slug',
                'idx_products_sku' => 'sku',
                'idx_products_brand_category' => ['brand_id', 'primary_category_id'],
                'idx_products_active_featured' => ['is_active', 'is_featured'],
                'idx_products_price_range' => ['price', 'sale_price'],
                'idx_products_stock' => 'stock_quantity',
                'idx_products_views' => 'views_count',
                'idx_products_created' => 'created_at',
            ],
            
            // Categories table indexes
            'product_categories' => [
                'idx_categories_slug' => 'slug',
                'idx_categories_parent' => 'parent_id',
                'idx_categories_active_order' => ['is_active', 'display_order'],
            ],
            
            // Brands table indexes
            'brands' => [
                'idx_brands_slug' => 'slug',
                'idx_brands_active_order' => ['is_active', 'display_order'],
            ],
            
            // Orders table indexes
            'orders' => [
                'idx_orders_customer_status' => ['customer_id', 'status'],
                'idx_orders_status' => 'status',
                'idx_orders_placed_at' => 'placed_at',
                'idx_orders_total' => 'total_amount',
            ],
            
            // Order items table indexes
            'order_items' => [
                'idx_order_items_order' => 'order_id',
                'idx_order_items_product' => 'product_id',
            ],
            
            // Customers table indexes
            'customers' => [
                'idx_customers_email' => 'email',
                'idx_customers_phone' => 'phone',
                'idx_customers_active' => 'is_active',
                'idx_customers_loyalty' => 'loyalty_points_balance',
            ],
            
            // Product media table indexes
            'product_media' => [
                'idx_product_media_product' => 'product_id',
                'idx_product_media_collection' => 'collection',
            ],
            
            // Product reviews table indexes
            'product_reviews' => [
                'idx_reviews_product' => 'product_id',
                'idx_reviews_customer' => 'customer_id',
                'idx_reviews_approved' => 'is_approved',
                'idx_reviews_rating' => 'rating',
            ],
            
            // Blog posts table indexes
            'blog_posts' => [
                'idx_posts_slug' => 'slug',
                'idx_posts_category' => 'blog_category_id',
                'idx_posts_status_published' => ['status', 'published_at'],
                'idx_posts_featured' => 'is_featured',
            ],
            
            // Coupons table indexes
            'coupons' => [
                'idx_coupons_code' => 'code',
                'idx_coupons_active' => 'is_active',
                'idx_coupons_expires' => 'expires_at',
            ],
        ];

        $results = [];
        
        foreach ($indexes as $table => $tableIndexes) {
            if (!Schema::hasTable($table)) {
                continue;
            }
            
            foreach ($tableIndexes as $indexName => $columns) {
                try {
                    if (!$this->indexExists($table, $indexName)) {
                        $this->addIndex($table, $indexName, $columns);
                        $results[$table][] = "Added index: {$indexName}";
                    }
                } catch (\Exception $e) {
                    $results[$table][] = "Failed to add index {$indexName}: " . $e->getMessage();
                }
            }
        }
        
        return $results;
    }

    public function getSlowQueries(): array
    {
        try {
            // Enable slow query log
            DB::statement("SET GLOBAL slow_query_log = 'ON'");
            DB::statement("SET GLOBAL long_query_time = 1");
            
            // Get slow queries from information_schema
            $slowQueries = DB::select("
                SELECT 
                    sql_text,
                    exec_count,
                    avg_timer_wait/1000000000 as avg_time_seconds,
                    sum_timer_wait/1000000000 as total_time_seconds
                FROM performance_schema.events_statements_summary_by_digest 
                WHERE avg_timer_wait > 1000000000 
                ORDER BY avg_timer_wait DESC 
                LIMIT 10
            ");
            
            return $slowQueries;
            
        } catch (\Exception $e) {
            Log::error('Failed to get slow queries', ['error' => $e->getMessage()]);
            return ['error' => $e->getMessage()];
        }
    }

    private function getAllTables(): array
    {
        return DB::select("SHOW TABLES");
    }

    private function optimizeTable(string $table): string
    {
        try {
            DB::statement("OPTIMIZE TABLE {$table}");
            return "Optimized successfully";
        } catch (\Exception $e) {
            return "Failed: " . $e->getMessage();
        }
    }

    private function analyzeTable(string $table): array
    {
        try {
            $result = DB::select("ANALYZE TABLE {$table}");
            return [
                'status' => $result[0]->Msg_text ?? 'Unknown',
                'operation' => $result[0]->Op ?? 'Unknown',
            ];
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    private function indexExists(string $table, string $indexName): bool
    {
        $indexes = DB::select("SHOW INDEX FROM {$table}");
        
        foreach ($indexes as $index) {
            if ($index->Key_name === $indexName) {
                return true;
            }
        }
        
        return false;
    }

    public function createMissingIndexes(): array
    {
        $results = [];
        
        try {
            // Check for missing indexes on frequently queried columns
            $missingIndexes = [
                'products' => [
                    'is_active' => 'idx_products_is_active',
                    'primary_category_id' => 'idx_products_category',
                    'brand_id' => 'idx_products_brand',
                    'price' => 'idx_products_price',
                ],
                'orders' => [
                    'customer_id' => 'idx_orders_customer',
                    'status' => 'idx_orders_status',
                    'created_at' => 'idx_orders_created',
                ],
                'customers' => [
                    'email' => 'idx_customers_email',
                    'phone' => 'idx_customers_phone',
                    'is_active' => 'idx_customers_active',
                ],
            ];

            foreach ($missingIndexes as $table => $columns) {
                if (!Schema::hasTable($table)) {
                    continue;
                }

                foreach ($columns as $column => $indexName) {
                    if (!Schema::hasColumn($table, $column)) {
                        continue;
                    }

                    if (!$this->indexExists($table, $indexName)) {
                        try {
                            $this->addIndex($table, $indexName, $column);
                            $results[$table][$column] = 'Index created';
                        } catch (\Exception $e) {
                            $results[$table][$column] = 'Failed: ' . $e->getMessage();
                        }
                    } else {
                        $results[$table][$column] = 'Index already exists';
                    }
                }
            }
            
            Log::info('Missing indexes check completed');
        } catch (\Exception $e) {
            Log::error('Failed to create missing indexes', ['error' => $e->getMessage()]);
            $results['error'] = $e->getMessage();
        }

        return $results;
    }

    private function addIndex(string $table, string $indexName, $columns): void
    {
        $columnString = is_array($columns) ? implode(', ', $columns) : $columns;
        DB::statement("ALTER TABLE {$table} ADD INDEX {$indexName} ({$columnString})");
    }
}
