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
