<?php

namespace Tests\Feature;

use App\Application\Shared\Services\BackupRecoveryService;
use App\Application\Shared\Services\DatabaseTestingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class DatabaseTestingServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_database_testing_service_runs_against_current_schema_without_runtime_errors(): void
    {
        $this->seedCommerceFixture();

        config([
            'backup.enabled' => true,
            'backup.compression' => false,
            'backup.encryption' => false,
            'backup.storage.local' => true,
            'backup.storage.s3' => false,
            'backup.storage.ftp' => false,
            'backup.components.database' => true,
            'backup.components.files' => false,
            'backup.components.config' => false,
            'backup.components.logs' => false,
            'backup.notifications.email' => false,
            'backup.notifications.slack' => false,
        ]);

        $this->app->forgetInstance(BackupRecoveryService::class);
        $this->app->forgetInstance(DatabaseTestingService::class);

        $service = $this->app->make(DatabaseTestingService::class);
        $results = $service->runDatabaseTests();
        $report = $service->generateTestReport();

        $this->assertTrue($results['connection_tests']['basic_connection']['success']);
        $this->assertTrue($results['connection_tests']['transaction_support']['success']);
        $this->assertTrue($results['schema_tests']['table_structure']['success']);
        $this->assertTrue($results['schema_tests']['foreign_keys']['success']);
        $this->assertTrue($results['data_tests']['relationships']['success']);
        $this->assertTrue($results['backup_tests']['backup_creation']['success']);
        $this->assertTrue($results['backup_tests']['backup_restoration']['success']);
        $this->assertGreaterThan(0, $report['summary']['total_tests']);
    }

    private function seedCommerceFixture(): void
    {
        $brandId = DB::table('brands')->insertGetId([
            'name' => 'Fixture Brand',
            'slug' => 'fixture-brand',
            'code' => 'FBR',
            'description' => 'Fixture brand',
            'is_active' => true,
            'display_order' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $categoryId = DB::table('product_categories')->insertGetId([
            'name' => 'Fixture Category',
            'slug' => 'fixture-category',
            'code' => 'FCAT',
            'is_active' => true,
            'display_order' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $customerId = DB::table('customers')->insertGetId([
            'name' => 'Fixture Customer',
            'email' => 'fixture@example.com',
            'phone' => '0900000000',
            'password' => bcrypt('password'),
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $productId = DB::table('products')->insertGetId([
            'brand_id' => $brandId,
            'primary_category_id' => $categoryId,
            'name' => 'Fixture Product',
            'slug' => 'fixture-product',
            'sku' => 'FIXTURE-001',
            'price' => 100000,
            'stock_quantity' => 10,
            'stock_alert_threshold' => 1,
            'is_active' => true,
            'is_featured' => false,
            'display_order' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $orderId = DB::table('orders')->insertGetId([
            'code' => 'ORDER-FIXTURE-001',
            'customer_id' => $customerId,
            'status' => 'completed',
            'payment_status' => 'paid',
            'fulfillment_status' => 'fulfilled',
            'currency' => 'VND',
            'subtotal' => 100000,
            'discount_total' => 0,
            'shipping_total' => 0,
            'tax_total' => 0,
            'total' => 100000,
            'placed_at' => now(),
            'completed_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('order_items')->insert([
            'order_id' => $orderId,
            'product_id' => $productId,
            'product_name' => 'Fixture Product',
            'sku' => 'FIXTURE-001',
            'price' => 100000,
            'discount_amount' => 0,
            'quantity' => 1,
            'subtotal' => 100000,
            'tax_amount' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
