<?php

namespace App\Application\Shared\Services;

use App\Application\Shared\MailData\CouponCampaignMailData;
use App\Application\Shared\MailData\PasswordRecoveryMailData;
use App\Mail\LegacyCouponCampaignMail;
use App\Mail\LegacyPasswordRecoveryMail;
use App\Models\Coupon;
use App\Models\Customer;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class LegacyMailWorkflowService
{
    public function sendCouponCampaign(string $couponCode, bool $vipOnly): array
    {
        $couponTable = (new Coupon())->getTable();
        $customerTable = (new Customer())->getTable();

        if (
            ! Schema::hasTable($couponTable) ||
            ! Schema::hasTable($customerTable)
        ) {
            return $this->result(false, 'Dữ liệu email marketing hiện chưa sẵn sàng trên môi trường này.');
        }

        $coupon = Coupon::where('coupon_code', $couponCode)->first();
        if (! $coupon) {
            return $this->result(false, 'Không tìm thấy mã khuyến mãi cần gửi.');
        }

        if ((int) ($coupon->coupon_status ?? 0) !== 1) {
            return $this->result(false, 'Mã khuyến mãi đang bị khóa, không thể gửi chiến dịch email.');
        }

        $today = Carbon::now('Asia/Ho_Chi_Minh')->startOfDay();
        $startDate = $this->parseLegacyCouponDate($coupon->coupon_date_start ?? null);
        $endDate = $this->parseLegacyCouponDate($coupon->coupon_date_end ?? null);

        if ($startDate && $startDate->gt($today)) {
            return $this->result(false, 'Mã khuyến mãi chưa đến thời gian hiệu lực để gửi.');
        }

        if ($endDate && $endDate->lt($today)) {
            return $this->result(false, 'Mã khuyến mãi đã hết hạn, không thể gửi chiến dịch email.');
        }

        $hasVipColumn = Schema::hasColumn($customerTable, 'customer_vip');
        $customers = Customer::query()
            ->when($vipOnly && $hasVipColumn, fn ($query) => $query->where('customer_vip', 1))
            ->when(! $vipOnly && $hasVipColumn, function ($query) {
                $query->where(function ($innerQuery) {
                    $innerQuery->whereNull('customer_vip')
                        ->orWhere('customer_vip', 0);
                });
            })
            ->get();

        $emails = $customers->pluck('customer_email')
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
        $title = 'Mã khuyến mãi ngày ' . $now;
        $brandName = (string) config('services.storefront_brand.name', 'ShopW');
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
        if (! Schema::hasTable((new Customer())->getTable())) {
            return $this->result(false, 'Dữ liệu khách hàng hiện chưa sẵn sàng trên môi trường này.');
        }

        $customer = Customer::where('customer_email', $email)->first();
        if (! $customer) {
            return $this->result(false, 'Email chưa được đăng ký để khôi phục mật khẩu');
        }

        $token = Str::random(40);
        $customer->customer_token = $token;
        $customer->save();

        $now = Carbon::now('Asia/Ho_Chi_Minh')->format('d-m-Y');
        $brandName = (string) config('services.storefront_brand.name', 'ShopW');
        $title = 'Lấy lại mật khẩu ' . $brandName . ' ' . $now;
        $link = url('/update-new-pass?email=' . urlencode($email) . '&token=' . urlencode($token));
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

    public function resetPassword(string $email, string $token, string $password): array
    {
        if (! Schema::hasTable((new Customer())->getTable())) {
            return $this->result(false, 'Dữ liệu khách hàng hiện chưa sẵn sàng trên môi trường này.');
        }

        $customer = Customer::where('customer_email', $email)
            ->where('customer_token', $token)
            ->first();

        if (! $customer) {
            return $this->result(false, 'Vui lòng nhập lại email vì link đã quá hạn');
        }

        $customer->customer_password = md5($password);
        $customer->customer_token = Str::random(40);
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

    protected function parseLegacyCouponDate(mixed $value): ?Carbon
    {
        $value = trim((string) $value);

        if ($value === '') {
            return null;
        }

        foreach (['d/m/Y', 'Y-m-d', 'd-m-Y'] as $format) {
            try {
                return Carbon::createFromFormat($format, $value, 'Asia/Ho_Chi_Minh')->startOfDay();
            } catch (\Throwable) {
            }
        }

        try {
            return Carbon::parse($value, 'Asia/Ho_Chi_Minh')->startOfDay();
        } catch (\Throwable) {
            return null;
        }
    }
}
