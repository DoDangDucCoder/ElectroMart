<?php

namespace App\Application\Shared\Services;

use App\Application\Shared\MailData\CouponCampaignMailData;
use App\Application\Shared\MailData\PasswordRecoveryMailData;
use App\Mail\LegacyCouponCampaignMail;
use App\Mail\LegacyPasswordRecoveryMail;
use App\Infrastructure\Persistence\Eloquent\Customer\Customer;
use App\Infrastructure\Persistence\Eloquent\Marketing\Coupon;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class LegacyMailWorkflowService
{
    private const PASSWORD_RESET_EXPIRY_MINUTES = 60;

    public function sendCouponCampaign(string $couponCode, bool $vipOnly): array
    {
        $couponTable = (new Coupon)->getTable();
        $customerTable = (new Customer)->getTable();

        if (
            ! Schema::hasTable($couponTable) ||
            ! Schema::hasTable($customerTable)
        ) {
            return $this->result(false, 'Dữ liệu email marketing hiện chưa sẵn sàng trên môi trường này.');
        }

        $coupon = Coupon::where('code', $couponCode)->first();
        if (! $coupon) {
            return $this->result(false, 'Không tìm thấy mã khuyến mãi cần gửi.');
        }

        if (! $coupon->is_active) {
            return $this->result(false, 'Mã khuyến mãi đang bị khóa, không thể gửi chiến dịch email.');
        }

        $today = Carbon::now('Asia/Ho_Chi_Minh')->startOfDay();
        $startDate = $coupon->starts_at?->copy()->timezone('Asia/Ho_Chi_Minh')->startOfDay();
        $endDate = $coupon->expires_at?->copy()->timezone('Asia/Ho_Chi_Minh')->startOfDay();

        if ($startDate && $startDate->gt($today)) {
            return $this->result(false, 'Mã khuyến mãi chưa đến thời gian hiệu lực để gửi.');
        }

        if ($endDate && $endDate->lt($today)) {
            return $this->result(false, 'Mã khuyến mãi đã hết hạn, không thể gửi chiến dịch email.');
        }

        $customers = Customer::query()
            ->where('is_active', true)
            ->get();

        $customers = $vipOnly
            ? $customers->filter(fn (Customer $customer): bool => $this->isVipCustomer($customer))
            : $customers->reject(fn (Customer $customer): bool => $this->isVipCustomer($customer));

        $emails = $customers->pluck('email')
            ->filter(fn ($email) => filter_var($email, FILTER_VALIDATE_EMAIL))
            ->unique()
            ->values()
            ->all();

        if ($emails === []) {
            return $this->result(false, $vipOnly
                ? 'Không có khách hàng VIP hợp lệ để gửi email.'
                : 'Không có khách hàng thường hợp lệ để gửi email.');
        }

        $now = Carbon::now('Asia/Ho_Chi_Minh')->format('d-m-Y H:i:s');
        $title = 'Mã khuyến mãi ngày '.$now;
        $brandName = (string) config('services.storefront_brand.name', 'DangDuc');
        $mailData = CouponCampaignMailData::fromCoupon(
            $coupon,
            $vipOnly ? 'khách hàng thân thiết' : 'khách hàng thường',
            shopName: $brandName
        );

        $mail = new LegacyCouponCampaignMail(
            mailData: $mailData,
            subjectLine: $title
        );

        Mail::to($emails)->send($mail);

        return $this->result(true, $vipOnly
            ? 'Gửi mã khuyến mãi khách vip thành công'
            : 'Gửi mã khuyến mãi khách thường thành công');
    }

    public function sendPasswordRecovery(string $email): array
    {
        if (! Schema::hasTable((new Customer)->getTable())) {
            return $this->result(false, 'Dữ liệu khách hàng hiện chưa sẵn sàng trên môi trường này.');
        }

        $customer = Customer::where('email', $email)->first();
        if (! $customer) {
            return $this->result(false, 'Email chưa được đăng ký để khôi phục mật khẩu');
        }

        $token = Str::random(64);
        $meta = is_array($customer->meta) ? $customer->meta : [];
        unset($meta['password_reset_token']);
        $meta['password_reset_token_hash'] = $this->hashPasswordResetToken($token);
        $meta['password_reset_requested_at'] = Carbon::now('Asia/Ho_Chi_Minh')->toIso8601String();
        $customer->meta = $meta;
        $customer->save();

        $now = Carbon::now('Asia/Ho_Chi_Minh')->format('d-m-Y');
        $brandName = (string) config('services.storefront_brand.name', 'DangDuc');
        $title = 'Lấy lại mật khẩu '.$brandName.' '.$now;
        $link = url('/update-new-pass?email='.urlencode($email).'&token='.urlencode($token));
        $mailData = new PasswordRecoveryMailData(
            title: $title,
            email: $email,
            resetUrl: $link,
            shopName: $brandName
        );

        Mail::to($email)->send(new LegacyPasswordRecoveryMail(
            mailData: $mailData,
            subjectLine: $title
        ));

        return $this->result(true, 'Gửi mail thành công,vui lòng vào email để reset password');
    }

    public function validatePasswordResetToken(string $email, string $token): array
    {
        if (! Schema::hasTable((new Customer)->getTable())) {
            return $this->result(false, 'Dữ liệu khách hàng hiện chưa sẵn sàng trên môi trường này.');
        }

        $customer = Customer::where('email', $email)->first();
        $meta = $customer && is_array($customer->meta) ? $customer->meta : [];

        if (! $customer || ! $this->passwordResetTokenMatches($meta, $token)) {
            return $this->result(false, 'Liên kết đặt lại mật khẩu không hợp lệ hoặc đã hết hạn.');
        }

        if ($this->passwordResetTokenExpired($meta)) {
            $this->clearPasswordResetMeta($customer, $meta);

            return $this->result(false, 'Liên kết đặt lại mật khẩu đã hết hạn. Vui lòng yêu cầu lại email khôi phục.');
        }

        return $this->result(true, 'Liên kết đặt lại mật khẩu hợp lệ.');
    }

    public function resetPassword(string $email, string $token, string $password): array
    {
        if (! Schema::hasTable((new Customer)->getTable())) {
            return $this->result(false, 'Dữ liệu khách hàng hiện chưa sẵn sàng trên môi trường này.');
        }

        $customer = Customer::where('email', $email)->first();
        $meta = $customer && is_array($customer->meta) ? $customer->meta : [];

        $validation = $this->validatePasswordResetToken($email, $token);
        if (! $validation['ok']) {
            return $validation;
        }

        $customer->password = Hash::make($password);
        $customer->remember_token = null;
        $customer->meta = $this->passwordResetMetaCleared($meta);
        $customer->save();

        return $this->result(true, 'Mật khẩu đã cập nhật mới,vui lòng đăng nhập');
    }

    protected function result(bool $ok, string $message): array
    {
        return [
            'ok' => $ok,
            'message' => $message,
        ];
    }

    protected function hashPasswordResetToken(string $token): string
    {
        return hash_hmac('sha256', $token, (string) config('app.key'));
    }

    protected function passwordResetTokenMatches(array $meta, string $token): bool
    {
        $hash = (string) ($meta['password_reset_token_hash'] ?? '');
        if ($hash !== '' && hash_equals($hash, $this->hashPasswordResetToken($token))) {
            return true;
        }

        $legacyPlainToken = (string) ($meta['password_reset_token'] ?? '');

        return $legacyPlainToken !== '' && hash_equals($legacyPlainToken, $token);
    }

    protected function passwordResetTokenExpired(array $meta): bool
    {
        $requestedAt = $meta['password_reset_requested_at'] ?? null;

        if (! is_string($requestedAt) || trim($requestedAt) === '') {
            return true;
        }

        try {
            return Carbon::parse($requestedAt, 'Asia/Ho_Chi_Minh')
                ->addMinutes(self::PASSWORD_RESET_EXPIRY_MINUTES)
                ->isPast();
        } catch (\Throwable) {
            return true;
        }
    }

    protected function clearPasswordResetMeta(Customer $customer, array $meta): void
    {
        $customer->meta = $this->passwordResetMetaCleared($meta);
        $customer->save();
    }

    protected function passwordResetMetaCleared(array $meta): array
    {
        unset(
            $meta['password_reset_token'],
            $meta['password_reset_token_hash'],
            $meta['password_reset_requested_at']
        );

        return $meta;
    }

    protected function isVipCustomer(Customer $customer): bool
    {
        $meta = is_array($customer->meta) ? $customer->meta : [];
        $legacyVip = $meta['legacy_vip'] ?? null;

        if ($legacyVip !== null) {
            return (int) $legacyVip === 1;
        }

        return (float) ($customer->loyalty_points_balance ?? 0) >= 1000;
    }
}
