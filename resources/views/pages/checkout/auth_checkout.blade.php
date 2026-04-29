@extends('layout')

@section('body_class', 'g-login-checkout')

@section('content')
<section id="form" class="shopw-auth-page">
    @if (session()->has('message'))
        <div class="alert alert-success">
            {!! session()->get('message') !!}
        </div>
    @elseif(session()->has('error'))
        <div class="alert alert-danger">
            {!! session()->get('error') !!}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="list-unstyled" style="margin: 0;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if ($currentCustomer)
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
    @else
        <div class="shopw-auth-layout shopw-auth-layout--single">
            <div class="shopw-auth-card">
                <div class="shopw-auth-card-head">
                    <h2>{{ $authPageHeading }}</h2>
                    <p>{{ $authPageDescription }}</p>
                </div>

                @if ($authPageMode === 'register')
                    <form action="{{ url('/add-customer') }}" method="POST" class="shopw-auth-form">
                        @csrf
                        <input type="hidden" name="redirect_target" value="{{ $authRedirectKey ?? 'checkout' }}">
                        <input type="hidden" name="redirect_to" value="{{ $authRedirectUrl ?? url('/checkout') }}">

                        <label class="shopw-auth-field">
                            <span>Họ và tên</span>
                            <input type="text" name="customer_name" value="{{ old('customer_name') }}" placeholder="Nguyen Van A" />
                        </label>

                        <label class="shopw-auth-field">
                            <span>Email</span>
                            <input type="email" name="customer_email" value="{{ old('customer_email') }}" placeholder="you@example.com" />
                        </label>

                        <label class="shopw-auth-field">
                            <span>Mật khẩu</span>
                            <input type="password" name="customer_password" placeholder="Tối thiểu 6 ký tự" />
                        </label>

                        <label class="shopw-auth-field">
                            <span>Số điện thoại</span>
                            <input type="text" name="customer_phone" value="{{ old('customer_phone') }}" placeholder="09xxxxxxxx" />
                        </label>

                        <button type="submit" class="btn btn-default shopw-auth-primary-link is-full">Đăng ký tài khoản</button>
                    </form>

                    <p class="shopw-auth-card-note">
                        Đã có tài khoản?
                        <a href="{{ $authLoginUrl }}" class="shopw-auth-inline-link">Đăng nhập</a>
                    </p>
                @else
                    <form action="{{ url('/login-customer') }}" method="POST" class="shopw-auth-form">
                        @csrf
                        <input type="hidden" name="redirect_target" value="{{ $authRedirectKey ?? 'checkout' }}">
                        <input type="hidden" name="redirect_to" value="{{ $authRedirectUrl ?? url('/checkout') }}">

                        <label class="shopw-auth-field">
                            <span>Email</span>
                            <input type="email" name="email_account" value="{{ old('email_account') }}" placeholder="you@example.com" />
                        </label>

                        <label class="shopw-auth-field">
                            <span>Mật khẩu</span>
                            <input type="password" name="password_account" placeholder="Nhập mật khẩu" />
                        </label>

                        <div class="shopw-auth-form-meta">
                            <a href="{{ url('/quen-mat-khau') }}" class="shopw-auth-inline-link">Quên mật khẩu</a>
                        </div>

                        <button type="submit" class="btn btn-default shopw-auth-primary-link is-full">Đăng nhập</button>
                    </form>

                    <p class="shopw-auth-card-note">
                        Chưa có tài khoản?
                        <a href="{{ $authRegisterUrl }}" class="shopw-auth-inline-link">Đăng ký ngay</a>
                    </p>
                @endif
            </div>
        </div>
    @endif
</section>
@endsection
