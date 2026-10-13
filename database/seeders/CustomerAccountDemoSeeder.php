<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CustomerAccountDemoSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $product = DB::table('products')
            ->where('sku', 'IP15PM256')
            ->first();

        if (! $product) {
            return;
        }

        $customer = DB::table('customers')
            ->where('email', 'customer1@example.com')
            ->first();

        if (! $customer) {
            DB::table('customers')->insert([
                'name' => 'Nguyen Van An',
                'email' => 'customer1@example.com',
                'phone' => '0901000001',
                'password' => bcrypt('password'),
                'is_active' => true,
                'email_verified_at' => $now,
                'meta' => json_encode(['seed' => 'customer-account-demo']),
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            $customer = DB::table('customers')
                ->where('email', 'customer1@example.com')
                ->first();
        }

        $customerId = (int) $customer->id;
        $productId = (int) $product->id;
        $unitPrice = (float) ($product->sale_price ?: $product->price);
        $subtotal = $unitPrice;

        $this->seedPaymentMethods($customerId, $now);

        $orderId = $this->seedOrder($customerId, $subtotal, $now);
        $orderItemId = $this->seedOrderItem($orderId, $productId, (string) $product->name, (string) $product->sku, $unitPrice, $now);

        $this->seedOrderShipping($orderId, $customer, $now);
        $this->seedPayment($orderId, $subtotal, $now);
        $this->seedVatInvoice($customerId, $orderId, $subtotal, $now);
        $this->seedOrderReturn($customerId, $orderId, $orderItemId, $unitPrice, $now);
    }

    private function seedPaymentMethods(int $customerId, mixed $now): void
    {
        if (! Schema::hasTable('customer_payment_methods')) {
            return;
        }

        foreach ([
            [
                'type' => 'card',
                'provider' => 'visa',
                'holder_name' => 'Nguyen Van An',
                'account_identifier' => '4111111111111111',
                'expiry_month' => 12,
                'expiry_year' => 2028,
                'is_default' => true,
                'is_verified' => true,
                'meta' => ['seed' => 'customer-account-demo'],
            ],
            [
                'type' => 'wallet',
                'provider' => 'momo',
                'holder_name' => 'Nguyen Van An',
                'account_identifier' => '0123456789',
                'expiry_month' => null,
                'expiry_year' => null,
                'is_default' => false,
                'is_verified' => true,
                'meta' => ['seed' => 'customer-account-demo'],
            ],
        ] as $method) {
            $exists = DB::table('customer_payment_methods')
                ->where('customer_id', $customerId)
                ->where('provider', $method['provider'])
                ->where('account_identifier', $method['account_identifier'])
                ->exists();

            $method['customer_id'] = $customerId;
            $method['meta'] = json_encode($method['meta']);
            $method['updated_at'] = $now;
            $method['deleted_at'] = null;

            if (! $exists) {
                $method['created_at'] = $now;
            }

            DB::table('customer_payment_methods')->updateOrInsert(
                [
                    'customer_id' => $customerId,
                    'provider' => $method['provider'],
                    'account_identifier' => $method['account_identifier'],
                ],
                $method
            );
        }
    }

    private function seedOrder(int $customerId, float $subtotal, mixed $now): int
    {
        DB::table('orders')->updateOrInsert(
            ['code' => 'DD-DEMO-ACCOUNT-001'],
            [
                'customer_id' => $customerId,
                'status' => 'completed',
                'payment_status' => 'paid',
                'fulfillment_status' => 'fulfilled',
                'currency' => 'VND',
                'subtotal' => $subtotal,
                'discount_total' => 0,
                'shipping_total' => 0,
                'tax_total' => 0,
                'total' => $subtotal,
                'source' => 'web',
                'note' => 'Demo account order for seed data',
                'placed_at' => $now->copy()->subDays(10),
                'confirmed_at' => $now->copy()->subDays(10)->addHour(),
                'shipped_at' => $now->copy()->subDays(8),
                'completed_at' => $now->copy()->subDays(6),
                'meta' => json_encode(['seed' => 'customer-account-demo']),
                'updated_at' => $now,
                'deleted_at' => null,
            ]
        );

        return (int) DB::table('orders')->where('code', 'DD-DEMO-ACCOUNT-001')->value('id');
    }

    private function seedOrderItem(int $orderId, int $productId, string $productName, string $sku, float $unitPrice, mixed $now): int
    {
        DB::table('order_items')->updateOrInsert(
            [
                'order_id' => $orderId,
                'sku' => $sku,
            ],
            [
                'product_id' => $productId,
                'product_name' => $productName,
                'attributes' => json_encode(['color' => 'Natural Titanium', 'storage' => '256GB']),
                'original_price' => $unitPrice,
                'price' => $unitPrice,
                'sale_price' => $unitPrice,
                'cost_price' => 32000000,
                'sale_amount' => 0,
                'discount_amount' => 0,
                'quantity' => 1,
                'subtotal' => $unitPrice,
                'tax_amount' => 0,
                'meta' => json_encode(['seed' => 'customer-account-demo']),
                'updated_at' => $now,
            ]
        );

        return (int) DB::table('order_items')
            ->where('order_id', $orderId)
            ->where('sku', $sku)
            ->value('id');
    }

    private function seedOrderShipping(int $orderId, object $customer, mixed $now): void
    {
        if (! Schema::hasTable('order_shipping')) {
            return;
        }

        DB::table('order_shipping')->updateOrInsert(
            ['order_id' => $orderId],
            [
                'recipient_name' => (string) $customer->name,
                'recipient_phone' => (string) $customer->phone,
                'email' => (string) $customer->email,
                'country' => 'Vietnam',
                'address_line1' => '123 Duong ABC',
                'address_line2' => 'Phuong XYZ',
                'shipping_method' => 'standard',
                'shipping_fee' => 0,
                'insurance_fee' => 0,
                'meta' => json_encode(['seed' => 'customer-account-demo']),
                'updated_at' => $now,
            ]
        );
    }

    private function seedPayment(int $orderId, float $amount, mixed $now): void
    {
        if (! Schema::hasTable('payments')) {
            return;
        }

        DB::table('payments')->updateOrInsert(
            [
                'order_id' => $orderId,
                'provider' => 'vnpay',
                'reference' => 'DEMO-VNPAY-ACCOUNT-001',
            ],
            [
                'status' => 'succeeded',
                'currency' => 'VND',
                'amount' => $amount,
                'fee' => 0,
                'paid_at' => $now->copy()->subDays(10)->addMinutes(5),
                'meta' => json_encode(['seed' => 'customer-account-demo']),
                'updated_at' => $now,
            ]
        );
    }

    private function seedVatInvoice(int $customerId, int $orderId, float $value, mixed $now): void
    {
        if (! Schema::hasTable('vat_invoices')) {
            return;
        }

        DB::table('vat_invoices')->updateOrInsert(
            ['code' => 'VAT-DEMO-ACCOUNT-001'],
            [
                'customer_id' => $customerId,
                'order_id' => $orderId,
                'type' => 'company',
                'tax_code' => '0101234567',
                'company_name' => 'Dang Duc Demo Company',
                'company_address' => '123 Duong ABC, Ha Noi',
                'email' => 'accounting@example.com',
                'value' => $value,
                'status' => 'issued',
                'issued_at' => $now->copy()->subDays(5),
                'meta' => json_encode(['seed' => 'customer-account-demo']),
                'updated_at' => $now,
                'deleted_at' => null,
            ]
        );
    }

    private function seedOrderReturn(int $customerId, int $orderId, int $orderItemId, float $refundAmount, mixed $now): void
    {
        if (! Schema::hasTable('order_returns')) {
            return;
        }

        DB::table('order_returns')->updateOrInsert(
            ['code' => 'RET-DEMO-ACCOUNT-001'],
            [
                'customer_id' => $customerId,
                'order_id' => $orderId,
                'order_item_id' => $orderItemId,
                'quantity' => 1,
                'reason' => 'Demo return request for account page',
                'refund_amount' => $refundAmount,
                'refund_method' => 'vnpay',
                'status' => 'refunded',
                'refunded_at' => $now->copy()->subDays(3),
                'meta' => json_encode(['seed' => 'customer-account-demo']),
                'updated_at' => $now,
                'deleted_at' => null,
            ]
        );
    }
}
