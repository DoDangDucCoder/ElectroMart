<?php

namespace Tests\Feature;

use App\Infrastructure\Persistence\Eloquent\Customer\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerRegistrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
    }

    public function test_customer_can_register_from_checkout_login_page(): void
    {
        $this->post('/add-customer', [
            'customer_name' => 'Checkout User',
            'customer_email' => 'checkout-user@example.com',
            'customer_password' => 'secret123',
            'customer_phone' => '0912345678',
        ])->assertRedirect('/checkout')
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('customers', [
            'name' => 'Checkout User',
            'email' => 'checkout-user@example.com',
            'phone' => '0912345678',
        ]);

        $this->assertNotNull(session('customer_id'));
    }

    public function test_customer_registration_validation_errors_are_shown_on_login_checkout_page(): void
    {
        $response = $this->from('/dang-nhap')->post('/add-customer', [
            'customer_name' => '',
            'customer_email' => 'not-an-email',
            'customer_password' => '123',
            'customer_phone' => str_repeat('9', 40),
        ]);

        $response->assertRedirect('/dang-nhap')
            ->assertSessionHasErrors([
                'customer_name',
                'customer_email',
                'customer_password',
                'customer_phone',
            ]);

        $this->followingRedirects()
            ->from('/dang-nhap')
            ->post('/add-customer', [
                'customer_name' => '',
                'customer_email' => 'not-an-email',
                'customer_password' => '123',
                'customer_phone' => str_repeat('9', 40),
            ])->assertSee('Vui lòng nhập họ và tên.')
            ->assertSee('Email không hợp lệ.')
            ->assertSee('Mật khẩu phải có ít nhất 6 ký tự.');
    }

    public function test_customer_registration_rejects_duplicate_email(): void
    {
        Customer::create([
            'name' => 'Existing Customer',
            'email' => 'existing@example.com',
            'password' => bcrypt('secret123'),
            'phone' => '0911111111',
            'is_active' => true,
        ]);

        $this->from('/dang-nhap')->post('/add-customer', [
            'customer_name' => 'Another Customer',
            'customer_email' => 'existing@example.com',
            'customer_password' => 'secret123',
            'customer_phone' => '0922222222',
        ])->assertRedirect('/dang-nhap')
            ->assertSessionHasErrors(['customer_email']);
    }

    public function test_customer_login_validates_required_fields_and_rejects_invalid_credentials(): void
    {
        Customer::create([
            'name' => 'Login Customer',
            'email' => 'login@example.com',
            'password' => bcrypt('secret123'),
            'phone' => '0933333333',
            'is_active' => true,
        ]);

        $this->from('/dang-nhap')->post('/login-customer', [
            'email_account' => '',
            'password_account' => '',
        ])->assertRedirect('/dang-nhap')
            ->assertSessionHasErrors(['email_account', 'password_account']);

        $this->from('/dang-nhap')->post('/login-customer', [
            'email_account' => 'login@example.com',
            'password_account' => 'wrong-password',
        ])->assertRedirect('/dang-nhap')
            ->assertSessionHas('error', 'Invalid login credentials.');

        $this->post('/login-customer', [
            'email_account' => 'login@example.com',
            'password_account' => 'secret123',
        ])->assertRedirect('/checkout')
            ->assertSessionHasNoErrors();

        $this->assertNotNull(session('customer_id'));
    }
}
