<?php

namespace Tests\Unit;

use App\Application\DataMigration\Services\LegacyDataMigrator;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class LegacyDataMigratorTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        if (! Schema::hasTable('tbl_order_details')) {
            Schema::create('tbl_order_details', function (Blueprint $table) {
                $table->string('order_code');
                $table->unsignedBigInteger('product_id')->nullable();
                $table->string('product_name')->nullable();
                $table->decimal('product_price', 15, 2)->nullable();
                $table->unsignedInteger('product_sales_quantity')->nullable();
                $table->decimal('product_feeship', 15, 2)->nullable();
                $table->string('product_coupon')->nullable();
            });
        }
    }

    public function test_it_migrates_legacy_orders_without_detail_rows(): void
    {
        $migrator = new class extends LegacyDataMigrator
        {
            public function migrateOrderForTest(object $legacy)
            {
                return $this->upsertOrder($legacy);
            }
        };

        $order = $migrator->migrateOrderForTest((object) [
            'order_id' => 1001,
            'order_code' => 'LEGACY-1001',
            'order_status' => 0,
            'order_total' => 99000,
            'order_date' => '2026-04-14 08:00:00',
            'shipping_id' => null,
        ])->load('items', 'payments');

        $this->assertSame('LEGACY-1001', $order->code);
        $this->assertSame(0.0, (float) $order->subtotal);
        $this->assertSame(0.0, (float) $order->shipping_total);
        $this->assertSame(99000.0, (float) $order->total);
        $this->assertTrue($order->items->isEmpty());
        $this->assertCount(1, $order->payments);
        $this->assertSame('manual', $order->payments->first()->provider);
    }
}
