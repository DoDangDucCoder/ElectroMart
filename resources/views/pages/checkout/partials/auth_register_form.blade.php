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
