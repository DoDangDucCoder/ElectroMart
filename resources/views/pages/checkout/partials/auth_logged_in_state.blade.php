@php
    $authAvatarUrl = $currentCustomer?->avatar_path
        ? asset($currentCustomer->avatar_path)
        : ($storefrontCustomerPicture ?: asset('frontend/images/storefront-account-avatar.svg'));
@endphp

<div class="shopw-auth-layout shopw-auth-layout--single">
    <div class="shopw-auth-logged-in">
        <div class="shopw-auth-identity">
            <span class="shopw-auth-identity-avatar">
                <img src="{{ $authAvatarUrl }}" alt="{{ $authCustomerDisplayName }}">
            </span>
            <div class="shopw-auth-identity-copy">
                <span class="shopw-auth-identity-kicker">Tài khoản đã xác thực</span>
                <strong>{{ $authCustomerDisplayName }}</strong>
                <p>{{ $authLoggedInDescription }}</p>
                @if ($authCustomerEmail)
                    <small>{{ $authCustomerEmail }}</small>
                @endif
                @if ($authCustomerPhone)
                    <small>{{ $authCustomerPhone }}</small>
                @endif
            </div>
        </div>
        <div class="shopw-auth-logged-actions">
            <a href="{{ $authRedirectUrl }}" class="shopw-auth-primary-link">{{ $authContinueLabel }}</a>
            <a href="{{ url('/history') }}" class="shopw-auth-secondary-link">Đơn hàng của tôi</a>
            <a href="{{ url('/dang-xuat-khach-hang?redirect=' . ($authRedirectKey ?? 'checkout')) }}"
                class="shopw-auth-secondary-link">Đăng xuất</a>
        </div>
    </div>
</div>
