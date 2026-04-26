<?php

namespace App\Http\Controllers;

use App\Infrastructure\Persistence\Eloquent\Customer\Customer;
use App\Infrastructure\Persistence\Eloquent\Catalog\Brand;
use App\Infrastructure\Persistence\Eloquent\Catalog\ProductCategory;
use App\Support\StorefrontCustomerAuth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use App\Http\Controllers\Concerns\LegacyStorefrontContext;

class CustomerAccountController extends Controller
{
    use LegacyStorefrontContext;

    public function index(Request $request)
    {
        $customer = $this->sessionCustomer();
        if (! $customer) {
            $this->clearCustomerSession();

            return $this->redirectToLogin('Vui lòng đăng nhập.');
        }

        $section = $request->get('section', 'profile');

        return view('pages.account.manager', array_merge(
            $this->seoContext($request, 'Quản lý tài khoản', 'account'),
            $this->catalogContext(),
            [
                'customer' => $customer,
                'active_section' => $section,
            ]
        ));
    }

    public function updateProfile(Request $request)
    {
        $customer = $this->sessionCustomer();
        if (! $customer) {
            $this->clearCustomerSession();

            return $this->redirectToLogin('Phiên đăng nhập không còn hợp lệ.');
        }

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
        ]);

        $customer->update($data);
        Session::put('customer_name', $customer->fresh()?->name ?? $data['name']);

        return Redirect::back()->with('message', 'Cập nhật thông tin thành công.');
    }

    public function postReview(Request $request)
    {
        if (! $this->sessionCustomer()) {
            $this->clearCustomerSession();

            return $this->redirectToLogin('Phiên đăng nhập không còn hợp lệ.');
        }

        // Simple feedback logic for now
        return Redirect::back()->with('message', 'Cảm ơn bạn đã gửi đánh giá!');
    }

    public function changePassword(Request $request)
    {
        $customer = $this->sessionCustomer();
        if (! $customer) {
            $this->clearCustomerSession();

            return $this->redirectToLogin('Phiên đăng nhập không còn hợp lệ.');
        }

        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:6|confirmed',
        ], [
            'new_password.min' => 'Mật khẩu mới phải có ít nhất 6 ký tự.',
            'new_password.confirmed' => 'Xác nhận mật khẩu mới không khớp.',
            'current_password.required' => 'Vui lòng nhập mật khẩu hiện tại.',
        ]);

        if (! \Illuminate\Support\Facades\Hash::check($request->current_password, $customer->password)) {
            return Redirect::back()->withErrors(['current_password' => 'Mật khẩu hiện tại không đúng.']);
        }

        $customer->update([
            'password' => \Illuminate\Support\Facades\Hash::make($request->new_password)
        ]);

        return Redirect::back()->with('message', 'Đổi mật khẩu thành công!');
    }

    protected function seoContext(Request $request, string $title, string $keyword): array
    {
        return [
            'meta_desc' => $title,
            'meta_keywords' => $keyword,
            'meta_title' => $title,
            'url_canonical' => $request->url(),
        ];
    }

    protected function catalogContext(): array
    {
        return [
            'category' => ProductCategory::where('is_active', true)
                ->orderBy('display_order')
                ->orderByDesc('id')
                ->get(),
            'brand' => Brand::where('is_active', true)
                ->orderBy('display_order')
                ->orderByDesc('id')
                ->get(),
            'category_post' => $this->legacyCategoryPosts(),
            'slider' => $this->legacyActiveSliders(),
        ];
    }

    protected function sessionCustomer(): ?Customer
    {
        $customerId = (int) Session::get('customer_id');

        if ($customerId <= 0) {
            return null;
        }

        return Customer::query()
            ->whereKey($customerId)
            ->where('is_active', true)
            ->first();
    }

    protected function redirectToLogin(string $message): RedirectResponse
    {
        return Redirect::to(StorefrontCustomerAuth::destinationUrl('login') . '?' . http_build_query([
            'redirect_to' => url('/account'),
        ]))->with('error', $message);
    }

    protected function clearCustomerSession(): void
    {
        Session::forget([
            'customer_id',
            'customer_name',
            'customer_picture',
        ]);

        StorefrontCustomerAuth::clearDisplaySession();
    }
}
