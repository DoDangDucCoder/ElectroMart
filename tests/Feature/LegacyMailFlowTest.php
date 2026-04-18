<?php

namespace Tests\Feature;

use App\Application\Shared\MailData\CouponCampaignMailData;
use App\Application\Shared\MailData\PasswordRecoveryMailData;
use App\Mail\LegacyCouponCampaignMail;
use App\Mail\LegacyPasswordRecoveryMail;
use App\Models\Coupon;
use App\Models\Customer;
use App\Models\User;
use App\Support\AdminAccess;
use App\Support\AdminPermissions;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class LegacyMailFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_password_recovery_pages_render_safely_without_legacy_storefront_tables(): void
    {
        $this->get('/quen-mat-khau')
            ->assertOk()
            ->assertSee('Điền email để lấy lại mật khẩu');

        $this->get('/update-new-pass?email=test@example.com&token=abcdefghij')
            ->assertOk()
            ->assertSee('Điền mật khẩu mới')
            ->assertSee('name="email" value="test@example.com"', false)
            ->assertSee('name="token" value="abcdefghij"', false);
    }

    public function test_recover_pass_handles_unknown_email_and_sends_mail_for_existing_customer(): void
    {
        $this->ensureLegacyCustomerTableExists();

        $this->from('/quen-mat-khau')
            ->post('/recover-pass', [
                'email_account' => 'missing@example.com',
            ])
            ->assertRedirect('/quen-mat-khau')
            ->assertSessionHas('error', 'Email chưa được đăng ký để khôi phục mật khẩu');

        $customer = $this->createLegacyCustomer([
            'customer_email' => 'recover@example.com',
            'customer_token' => 'old-token',
        ]);

        Mail::fake();

        $this->from('/quen-mat-khau')
            ->post('/recover-pass', [
                'email_account' => 'recover@example.com',
            ])
            ->assertRedirect('/quen-mat-khau')
            ->assertSessionHas('message', 'Gửi mail thành công,vui lòng vào email để reset password');

        Mail::assertSent(LegacyPasswordRecoveryMail::class, function (LegacyPasswordRecoveryMail $mail) {
            return $mail->hasTo('recover@example.com')
                && $mail->mailData instanceof PasswordRecoveryMailData
                && $mail->mailData->email === 'recover@example.com'
                && str_contains($mail->mailData->resetUrl, '/update-new-pass?email=');
        });

        $customer->refresh();
        $this->assertNotSame('old-token', $customer->customer_token);
        $this->assertNotEmpty($customer->customer_token);
    }

    public function test_mail_routes_fall_back_to_canonical_pages_without_referer(): void
    {
        $this->ensureLegacyCustomerTableExists();
        $this->ensureLegacyCouponTableExists();

        $admin = $this->createAdminUser();

        $this->actingAs($admin, 'web')
            ->post('/send-coupon/10/1/15/MISSING')
            ->assertRedirect('/list-coupon')
            ->assertSessionHas('error', 'Không tìm thấy mã khuyến mãi cần gửi.');

        $this->post('/recover-pass', [
            'email_account' => 'missing@example.com',
        ])->assertRedirect('/quen-mat-khau')
            ->assertSessionHas('error', 'Email chưa được đăng ký để khôi phục mật khẩu');
    }

    public function test_reset_new_pass_validates_and_updates_legacy_customer_password(): void
    {
        $this->ensureLegacyCustomerTableExists();

        $customer = $this->createLegacyCustomer([
            'customer_email' => 'reset@example.com',
            'customer_token' => 'valid-reset-token',
            'customer_password' => md5('oldsecret'),
        ]);

        $this->from('/update-new-pass?email=reset@example.com&token=valid-reset-token')
            ->post('/reset-new-pass', [
                'email' => 'reset@example.com',
                'token' => 'valid-reset-token',
                'password_account' => '123',
            ])
            ->assertRedirect('/update-new-pass?email=reset@example.com&token=valid-reset-token')
            ->assertSessionHasErrors('password_account');

        $this->post('/reset-new-pass', [
            'email' => 'reset@example.com',
            'token' => 'expired-token',
            'password_account' => 'secret123',
        ])
            ->assertRedirect('quen-mat-khau')
            ->assertSessionHas('error', 'Vui lòng nhập lại email vì link đã quá hạn');

        $this->post('/reset-new-pass', [
            'email' => 'reset@example.com',
            'token' => 'valid-reset-token',
            'password_account' => 'secret123',
        ])
            ->assertRedirect('dang-nhap')
            ->assertSessionHas('message', 'Mật khẩu đã cập nhật mới,vui lòng đăng nhập');

        $customer->refresh();
        $this->assertSame(md5('secret123'), $customer->customer_password);
        $this->assertNotSame('valid-reset-token', $customer->customer_token);
    }

    public function test_admin_coupon_campaign_routes_handle_missing_coupon_and_send_to_expected_audience(): void
    {
        $admin = $this->createAdminUser();
        $this->ensureLegacyCustomerTableExists();
        $this->ensureLegacyCouponTableExists();

        $this->actingAs($admin, 'web')
            ->from('/list-coupon')
            ->post('/send-coupon/10/1/15/MISSING')
            ->assertRedirect('/list-coupon')
            ->assertSessionHas('error', 'Không tìm thấy mã khuyến mãi cần gửi.');

        Coupon::create([
            'coupon_name' => 'VIP Campaign',
            'coupon_code' => 'VIP15',
            'coupon_time' => 25,
            'coupon_number' => 15,
            'coupon_condition' => 1,
            'coupon_date_start' => '01/03/2026',
            'coupon_date_end' => '31/12/2026',
            'coupon_status' => 1,
            'coupon_used' => '',
        ]);

        $this->createLegacyCustomer([
            'customer_email' => 'normal@example.com',
            'customer_vip' => null,
        ]);
        $this->createLegacyCustomer([
            'customer_email' => 'normal-zero@example.com',
            'customer_vip' => 0,
        ]);
        $this->createLegacyCustomer([
            'customer_email' => 'vip@example.com',
            'customer_vip' => 1,
        ]);

        Mail::fake();

        $this->actingAs($admin, 'web')
            ->from('/list-coupon')
            ->post('/send-coupon/25/1/15/VIP15')
            ->assertRedirect('/list-coupon')
            ->assertSessionHas('message', 'Gửi mã khuyến mãi khách thường thành công');

        Mail::assertSent(LegacyCouponCampaignMail::class, function (LegacyCouponCampaignMail $mail) {
            return $mail->hasTo('normal@example.com')
                && $mail->hasTo('normal-zero@example.com')
                && ! $mail->hasTo('vip@example.com')
                && $mail->mailData instanceof CouponCampaignMailData
                && $mail->mailData->couponCode === 'VIP15'
                && $mail->mailData->audienceLabel === 'khách hàng thường';
        });

        $this->actingAs($admin, 'web')
            ->from('/list-coupon')
            ->post('/send-coupon-vip/25/1/15/VIP15')
            ->assertRedirect('/list-coupon')
            ->assertSessionHas('message', 'Gửi mã khuyến mãi khách vip thành công');

        Mail::assertSent(LegacyCouponCampaignMail::class, function (LegacyCouponCampaignMail $mail) {
            return $mail->hasTo('vip@example.com')
                && ! $mail->hasTo('normal@example.com')
                && ! $mail->hasTo('normal-zero@example.com')
                && $mail->mailData instanceof CouponCampaignMailData
                && $mail->mailData->couponCode === 'VIP15'
                && $mail->mailData->audienceLabel === 'khách hàng thân thiết';
        });
    }

    public function test_admin_coupon_campaign_rejects_locked_and_expired_coupons(): void
    {
        $admin = $this->createAdminUser();
        $this->ensureLegacyCustomerTableExists();
        $this->ensureLegacyCouponTableExists();

        Coupon::create([
            'coupon_name' => 'Locked Campaign',
            'coupon_code' => 'LOCKED15',
            'coupon_time' => 10,
            'coupon_number' => 15,
            'coupon_condition' => 1,
            'coupon_date_start' => '01/03/2026',
            'coupon_date_end' => '31/12/2026',
            'coupon_status' => 0,
            'coupon_used' => '',
        ]);

        Coupon::create([
            'coupon_name' => 'Expired Campaign',
            'coupon_code' => 'EXPIRED15',
            'coupon_time' => 10,
            'coupon_number' => 15,
            'coupon_condition' => 1,
            'coupon_date_start' => '01/01/2025',
            'coupon_date_end' => '02/01/2025',
            'coupon_status' => 1,
            'coupon_used' => '',
        ]);

        $this->createLegacyCustomer([
            'customer_email' => 'normal@example.com',
            'customer_vip' => 0,
        ]);

        Mail::fake();

        $this->actingAs($admin, 'web')
            ->from('/list-coupon')
            ->post('/send-coupon/10/1/15/LOCKED15')
            ->assertRedirect('/list-coupon')
            ->assertSessionHas('error', 'Mã khuyến mãi đang bị khóa, không thể gửi chiến dịch email.');

        $this->actingAs($admin, 'web')
            ->from('/list-coupon')
            ->post('/send-coupon/10/1/15/EXPIRED15')
            ->assertRedirect('/list-coupon')
            ->assertSessionHas('error', 'Mã khuyến mãi đã hết hạn, không thể gửi chiến dịch email.');

        Mail::assertNothingSent();
    }

    protected function createAdminUser(array $attributes = []): User
    {
        foreach (AdminPermissions::all() as $permissionName) {
            Permission::firstOrCreate([
                'name' => $permissionName,
                'guard_name' => AdminAccess::guardName(),
            ]);
        }

        $adminRole = Role::firstOrCreate([
            'name' => 'Admin',
            'guard_name' => AdminAccess::guardName(),
        ]);
        $adminRole->syncPermissions(AdminPermissions::defaultsForRole('Admin'));

        $user = User::factory()->create($attributes);
        $user->assignRole($adminRole);

        return $user;
    }

    protected function ensureLegacyCustomerTableExists(): void
    {
        if (Schema::hasTable('tbl_customers')) {
            if (! Schema::hasColumn('tbl_customers', 'customer_token')) {
                Schema::table('tbl_customers', function (Blueprint $table) {
                    $table->string('customer_token')->nullable();
                });
            }

            return;
        }

        Schema::create('tbl_customers', function (Blueprint $table) {
            $table->increments('customer_id');
            $table->string('customer_name')->nullable();
            $table->string('customer_email')->nullable();
            $table->string('customer_password')->nullable();
            $table->string('customer_phone')->nullable();
            $table->integer('customer_vip')->nullable();
            $table->string('customer_picture')->nullable();
            $table->string('customer_token')->nullable();
        });
    }

    protected function ensureLegacyCouponTableExists(): void
    {
        if (Schema::hasTable('tbl_coupon')) {
            return;
        }

        Schema::create('tbl_coupon', function (Blueprint $table) {
            $table->increments('coupon_id');
            $table->string('coupon_name')->nullable();
            $table->string('coupon_code')->nullable();
            $table->integer('coupon_time')->default(0);
            $table->bigInteger('coupon_number')->default(0);
            $table->integer('coupon_condition')->default(1);
            $table->string('coupon_date_start')->nullable();
            $table->string('coupon_date_end')->nullable();
            $table->integer('coupon_status')->default(1);
            $table->text('coupon_used')->nullable();
        });
    }

    protected function createLegacyCustomer(array $overrides = []): Customer
    {
        $customer = new Customer();
        $customer->forceFill(array_merge([
            'customer_name' => 'Legacy Mail Customer ' . Str::random(4),
            'customer_email' => 'legacy-mail-' . Str::lower(Str::random(6)) . '@example.com',
            'customer_password' => md5('secret123'),
            'customer_phone' => '0900000000',
            'customer_vip' => null,
            'customer_picture' => null,
            'customer_token' => null,
        ], $overrides));
        $customer->save();

        return $customer;
    }
}
