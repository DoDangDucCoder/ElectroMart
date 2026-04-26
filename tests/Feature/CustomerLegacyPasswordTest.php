<?php

namespace Tests\Feature;

use App\Infrastructure\Persistence\Eloquent\Customer\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CustomerLegacyPasswordTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
    }

    public function test_customer_can_login_with_legacy_md5_password_and_hash_is_upgraded(): void
    {
        $customer = Customer::create([
            'name' => 'Legacy Md5 Customer',
            'email' => 'legacy-md5@example.com',
            'password' => md5('secret123'),
            'phone' => '0911111111',
            'is_active' => true,
        ]);

        $this->post('/login-customer', [
            'email_account' => 'legacy-md5@example.com',
            'password_account' => 'secret123',
            'redirect_target' => 'checkout',
        ])->assertRedirect('/checkout')
            ->assertSessionHasNoErrors();

        $customer->refresh();

        $this->assertTrue(Hash::check('secret123', $customer->password));
        $this->assertNotSame(md5('secret123'), $customer->password);
        $this->assertArrayHasKey('legacy_password_upgraded_at', $customer->meta);
    }

    public function test_customer_can_login_with_legacy_bcrypt_md5_password_and_hash_is_upgraded(): void
    {
        $legacyHash = bcrypt(md5('secret123'));

        $customer = Customer::create([
            'name' => 'Legacy Bcrypt Customer',
            'email' => 'legacy-bcrypt@example.com',
            'password' => $legacyHash,
            'phone' => '0912222222',
            'is_active' => true,
        ]);

        $this->post('/login-customer', [
            'email_account' => 'legacy-bcrypt@example.com',
            'password_account' => 'secret123',
            'redirect_target' => 'checkout',
        ])->assertRedirect('/checkout')
            ->assertSessionHasNoErrors();

        $customer->refresh();

        $this->assertTrue(Hash::check('secret123', $customer->password));
        $this->assertNotSame($legacyHash, $customer->password);
        $this->assertArrayHasKey('legacy_password_upgraded_at', $customer->meta);
    }
}
