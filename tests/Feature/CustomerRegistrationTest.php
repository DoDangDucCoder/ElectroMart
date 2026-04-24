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
            'redirect_target' => 'checkout',
        ])->assertRedirect('/checkout')
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('customers', [
            'name' => 'Checkout User',
            'email' => 'checkout-user@example.com',
            'phone' => '0912345678',
        ]);

        $this->assertNotNull(session('customer_id'));
    }

    public function test_customer_registration_validation_errors_are_shown_on_register_page(): void
    {
        $response = $this->from('/dang-ky')->post('/add-customer', [
            'customer_name' => '',
            'customer_email' => 'not-an-email',
            'customer_password' => '123',
            'customer_phone' => str_repeat('9', 40),
        ]);

        $response->assertRedirect('/dang-ky')
            ->assertSessionHasErrors([
                'customer_name',
                'customer_email',
                'customer_password',
                'customer_phone',
            ]);

        $this->followingRedirects()
            ->from('/dang-ky')
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

        $this->from('/dang-ky')->post('/add-customer', [
            'customer_name' => 'Another Customer',
            'customer_email' => 'existing@example.com',
            'customer_password' => 'secret123',
            'customer_phone' => '0922222222',
        ])->assertRedirect('/dang-ky')
            ->assertSessionHasErrors(['customer_email']);
    }

    public function test_customer_registration_rejects_duplicate_phone(): void
    {
        Customer::create([
            'name' => 'Existing Customer',
            'email' => 'existing-phone@example.com',
            'password' => bcrypt('secret123'),
            'phone' => '0964065314',
            'is_active' => true,
        ]);

        $this->from('/dang-ky')->post('/add-customer', [
            'customer_name' => 'Another Customer',
            'customer_email' => 'another-phone@example.com',
            'customer_password' => 'secret123',
            'customer_phone' => '0964065314',
        ])->assertRedirect('/dang-ky')
            ->assertSessionHasErrors(['customer_phone']);

        $this->assertDatabaseMissing('customers', [
            'email' => 'another-phone@example.com',
        ]);
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
            ->assertSessionHas('error', 'Thông tin đăng nhập không chính xác.');

        $this->post('/login-customer', [
            'email_account' => 'login@example.com',
            'password_account' => 'secret123',
            'redirect_target' => 'checkout',
        ])->assertRedirect('/checkout')
            ->assertSessionHasNoErrors();

        $this->assertNotNull(session('customer_id'));
    }

    public function test_customer_login_and_registration_follow_requested_destination(): void
    {
        Customer::create([
            'name' => 'History Customer',
            'email' => 'history@example.com',
            'password' => bcrypt('secret123'),
            'phone' => '0909999999',
            'is_active' => true,
        ]);

        $this->post('/login-customer', [
            'email_account' => 'history@example.com',
            'password_account' => 'secret123',
            'redirect_target' => 'history',
        ])->assertRedirect('/history');

        $this->post('/add-customer', [
            'customer_name' => 'Payment Customer',
            'customer_email' => 'payment-user@example.com',
            'customer_password' => 'secret123',
            'customer_phone' => '0910000000',
            'redirect_target' => 'payment',
        ])->assertRedirect('/payment');
    }

    public function test_customer_login_returns_to_previous_storefront_page_after_visiting_login(): void
    {
        Customer::create([
            'name' => 'Return Customer',
            'email' => 'return@example.com',
            'password' => bcrypt('secret123'),
            'phone' => '0912222222',
            'is_active' => true,
        ]);

        $this->from('/gio-hang')
            ->get('/dang-nhap')
            ->assertOk();

        $this->post('/login-customer', [
            'email_account' => 'return@example.com',
            'password_account' => 'secret123',
            'redirect_target' => 'history',
        ])->assertRedirect('/gio-hang');
    }

    public function test_customer_auth_flow_honors_explicit_redirect_url_when_present(): void
    {
        Customer::create([
            'name' => 'Explicit Redirect Customer',
            'email' => 'explicit@example.com',
            'password' => bcrypt('secret123'),
            'phone' => '0913333333',
            'is_active' => true,
        ]);

        $this->post('/login-customer', [
            'email_account' => 'explicit@example.com',
            'password_account' => 'secret123',
            'redirect_target' => 'history',
            'redirect_to' => url('/checkout'),
        ])->assertRedirect('/checkout');

        $this->post('/add-customer', [
            'customer_name' => 'Explicit Register Customer',
            'customer_email' => 'explicit-register@example.com',
            'customer_password' => 'secret123',
            'customer_phone' => '0914444444',
            'redirect_target' => 'history',
            'redirect_to' => url('/gio-hang'),
        ])->assertRedirect('/gio-hang');
    }

    public function test_customer_auth_flow_rejects_system_redirect_targets_even_on_same_host(): void
    {
        Customer::create([
            'name' => 'Safe Redirect Customer',
            'email' => 'safe-redirect@example.com',
            'password' => bcrypt('secret123'),
            'phone' => '0915555555',
            'is_active' => true,
        ]);

        $this->post('/login-customer', [
            'email_account' => 'safe-redirect@example.com',
            'password_account' => 'secret123',
            'redirect_target' => 'history',
            'redirect_to' => url('/admin'),
        ])->assertRedirect('/history');
    }

    public function test_customer_login_ignores_admin_referer_and_falls_back_to_storefront_destination(): void
    {
        Customer::create([
            'name' => 'Admin Referer Customer',
            'email' => 'admin-referer@example.com',
            'password' => bcrypt('secret123'),
            'phone' => '0916666666',
            'is_active' => true,
        ]);

        $this->from('/admin')
            ->get('/dang-nhap?redirect=checkout')
            ->assertOk();

        $this->post('/login-customer', [
            'email_account' => 'admin-referer@example.com',
            'password_account' => 'secret123',
            'redirect_target' => 'history',
        ])->assertRedirect('/checkout');
    }

    public function test_logged_in_customer_auth_pages_render_account_identity_and_logout(): void
    {
        $customer = Customer::create([
            'name' => 'Logged Customer',
            'email' => 'logged-state@example.com',
            'password' => bcrypt('secret123'),
            'phone' => '0930000000',
            'is_active' => true,
        ]);

        $this->withSession([
            'customer_id' => $customer->id,
        ])->get('/dang-nhap?redirect=checkout')
            ->assertOk()
            ->assertSee('Tài khoản đã xác thực')
            ->assertSee($customer->email)
            ->assertSee('/dang-xuat-khach-hang?redirect=checkout', false);

        $this->withSession([
            'customer_id' => $customer->id,
        ])->get('/dang-ky?redirect=checkout')
            ->assertOk()
            ->assertSee('Tài khoản đã xác thực')
            ->assertSee($customer->email)
            ->assertSee('/dang-xuat-khach-hang?redirect=checkout', false);
    }

    public function test_customer_logout_clears_session_and_returns_to_login_state(): void
    {
        $customer = Customer::create([
            'name' => 'Logout Customer',
            'email' => 'logout@example.com',
            'password' => bcrypt('secret123'),
            'phone' => '0940000000',
            'is_active' => true,
        ]);

        $response = $this->withSession([
            'customer_id' => $customer->id,
        ])->get('/dang-xuat-khach-hang?redirect=login');

        $response->assertRedirect('/dang-nhap')
            ->assertSessionHas('message', 'Đăng xuất thành công.');

        $this->assertNull(session('customer_id'));
    }
}
