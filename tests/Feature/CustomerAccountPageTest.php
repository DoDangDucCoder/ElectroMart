<?php

namespace Tests\Feature;

use App\Http\Middleware\VerifyCsrfToken;
use App\Infrastructure\Persistence\Eloquent\Customer\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerAccountPageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(VerifyCsrfToken::class);
    }

    public function test_customer_can_view_account_page(): void
    {
        $customer = $this->createCustomer();

        $this->withSession(['customer_id' => $customer->id])
            ->get('/account')
            ->assertOk()
            ->assertSee('Quản lý tài khoản')
            ->assertSee('Account Customer')
            ->assertSee('account@example.com');
    }

    public function test_guest_is_redirected_from_account_page_to_current_storefront_login(): void
    {
        $this->get('/account')
            ->assertRedirect($this->accountLoginRedirect())
            ->assertSessionHas('error', 'Vui lòng đăng nhập.');
    }

    public function test_stale_customer_session_is_cleared_from_account_page(): void
    {
        $this->withSession([
            'customer_id' => 999999,
            'customer_name' => 'Ghost Customer',
        ])->get('/account')
            ->assertRedirect($this->accountLoginRedirect())
            ->assertSessionHas('error', 'Vui lòng đăng nhập.')
            ->assertSessionMissing('customer_id')
            ->assertSessionMissing('customer_name');
    }

    public function test_stale_customer_session_cannot_update_account_profile(): void
    {
        $this->withSession(['customer_id' => 999999])
            ->post('/account/update', [
                'name' => 'Ghost Customer',
                'phone' => '0900000000',
            ])
            ->assertRedirect($this->accountLoginRedirect())
            ->assertSessionHas('error', 'Phiên đăng nhập không còn hợp lệ.')
            ->assertSessionMissing('customer_id');
    }

    public function test_customer_can_update_account_profile(): void
    {
        $customer = $this->createCustomer();

        $this->withSession(['customer_id' => $customer->id])
            ->post('/account/update', [
                'name' => 'Updated Customer',
                'phone' => '0912345678',
            ])
            ->assertRedirect()
            ->assertSessionHas('message', 'Cập nhật thông tin thành công.')
            ->assertSessionHas('customer_name', 'Updated Customer');

        $this->assertDatabaseHas('customers', [
            'id' => $customer->id,
            'name' => 'Updated Customer',
            'phone' => '0912345678',
        ]);
    }

    protected function createCustomer(array $attributes = []): Customer
    {
        return Customer::create(array_merge([
            'name' => 'Account Customer',
            'email' => 'account@example.com',
            'password' => bcrypt('secret123'),
            'phone' => '0900000000',
            'is_active' => true,
        ], $attributes));
    }

    protected function accountLoginRedirect(): string
    {
        return url('/dang-nhap') . '?' . http_build_query([
            'redirect_to' => url('/account'),
        ]);
    }
}
