@extends('layouts.home-tech')
@section('body_class','auth-page')
@php
$meta_title=$meta_title??'Đăng ký - DangDuc';
$meta_desc=$meta_desc??'Tạo tài khoản DangDuc để mua sắm công nghệ hàng đầu';
$url_canonical=$url_canonical??url('/dang-ky');
@endphp
@section('content')
<div class="auth-wrap">
    @include('partials.storefront.breadcrumb', ['class' => 'accp-crumb', 'separator' => '›', 'items' => [['label' => 'Đăng ký', 'current' => true]]])
    <div class="auth-container">
        <div class="auth-left">
            <div class="auth-brand">
                <div style="width:48px;height:48px;background:#0d9488;border-radius:12px;display:flex;align-items:center;justify-content:center;color:#fff;font-size:24px;font-weight:800">D</div>
                <h2>Tạo tài khoản DangDuc</h2>
                <p>Tham gia cộng đồng công nghệ hàng đầu Việt Nam</p>
            </div>
            <div class="auth-features">
                <div class="auth-feat"><i class="fa fa-truck"></i><div><strong>Theo dõi đơn hàng</strong><span>Mọi lúc mọi nơi</span></div></div>
                <div class="auth-feat"><i class="fa fa-star"></i><div><strong>Tích điểm đổi quà</strong><span>Ưu đãi hấp dẫn</span></div></div>
                <div class="auth-feat"><i class="fa fa-shield"></i><div><strong>Bảo hành nhanh</strong><span>Hỗ trợ tận tâm</span></div></div>
                <div class="auth-feat"><i class="fa fa-gift"></i><div><strong>Ưu đãi thành viên</strong><span>Giảm giá riêng</span></div></div>
            </div>
        </div>
        <div class="auth-right">
            <div class="auth-card">
                <h1>Đăng ký tài khoản</h1>
                <p>Chỉ mất 1 phút để bắt đầu!</p>
                <form action="{{ url('/add-customer') }}" method="POST">
                    @csrf
                    <div class="accp-field"><label>Họ và tên</label><input type="text" name="customer_name" value="{{ old('customer_name') }}" placeholder="Nhập họ và tên đầy đủ" required></div>
                    <div class="accp-field"><label>Email</label><input type="email" name="customer_email" value="{{ old('customer_email') }}" placeholder="Nhập địa chỉ email" required></div>
                    <div class="accp-field"><label>Số điện thoại</label><input type="text" name="customer_phone" value="{{ old('customer_phone') }}" placeholder="Nhập số điện thoại" required></div>
                    <div class="accp-field"><label>Mật khẩu</label>
                        <div class="input-wrap"><input type="password" name="customer_password" placeholder="Tạo mật khẩu (ít nhất 8 ký tự)" required data-auth-password><button type="button" class="toggle-pw" aria-label="Hiện mật khẩu"><i class="fa fa-eye"></i></button></div>
                    </div>
                    <div class="accp-field"><label>Xác nhận mật khẩu</label>
                        <div class="input-wrap"><input type="password" name="customer_password_confirmation" placeholder="Nhập lại mật khẩu" required data-auth-password><button type="button" class="toggle-pw" aria-label="Hiện mật khẩu"><i class="fa fa-eye"></i></button></div>
                    </div>
                    <div style="margin-bottom:16px">
                        <label style="display:flex;align-items:flex-start;gap:8px;font-size:13px;color:#374151;cursor:pointer"><input type="checkbox" name="agree_terms" value="1" required style="margin-top:3px" @checked(old('agree_terms'))><span>Tôi đồng ý với <a href="#" style="color:#0d9488">Điều khoản sử dụng</a> và <a href="#" style="color:#0d9488">Chính sách bảo mật</a></span></label>
                    </div>
                    <div style="margin-bottom:16px">
                        <label style="display:flex;align-items:center;gap:8px;font-size:13px;color:#374151;cursor:pointer"><input type="checkbox" name="newsletter" value="1" @checked(old('newsletter'))><span>Nhận thông báo ưu đãi qua email</span></label>
                    </div>
                    <button type="submit" class="accp-btn accp-btn--primary accp-btn--block" style="height:46px;font-size:15px"><i class="fa fa-user-plus"></i> Đăng ký</button>
                </form>
                {{-- Fix #13: Chỉ hiển thị Social Login khi đã cấu hình credentials --}}
                @if(config('services.google.client_id') || config('services.facebook.client_id'))
                <div class="auth-divider"><span>Hoặc đăng ký bằng</span></div>
                <div class="auth-social">
                    @if(config('services.google.client_id'))
                    <a href="{{ url('/login-customer-google') }}" class="auth-social-btn google"><i class="fa fa-google"></i> Google</a>
                    @endif
                    @if(config('services.facebook.client_id'))
                    <a href="{{ url('/login-facebook-customer') }}" class="auth-social-btn facebook"><i class="fa fa-facebook"></i> Facebook</a>
                    @endif
                </div>
                @endif
                <div class="auth-footer">Đã có tài khoản? <a href="{{ url('/dang-nhap') }}">Đăng nhập</a></div>
            </div>
        </div>
    </div>
</div>
<style>
body.auth-page{background:#f3f5f7;font-family:'Inter',-apple-system,sans-serif;overflow-x:hidden}
.auth-wrap{min-height:60vh}
.auth-container{box-sizing:border-box;width:100%;max-width:1000px;margin:0 auto;padding:0 16px 40px;display:grid;grid-template-columns:1fr 1fr;gap:32px;align-items:center}
@media(max-width:768px){.auth-container{grid-template-columns:1fr}}
.auth-left,.auth-right,.auth-card{min-width:0}
.auth-left{padding:24px}
.auth-brand h2{font-size:28px;font-weight:800;color:#0f172a;margin:16px 0 8px}
.auth-brand p{font-size:14px;color:#6b7280;line-height:1.5}
.auth-features{display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-top:24px}
.auth-feat{display:flex;align-items:center;gap:10px;padding:12px;background:#f0fdfa;border-radius:10px}
.auth-feat i{width:36px;height:36px;border-radius:8px;background:#0d9488;color:#fff;display:flex;align-items:center;justify-content:center;font-size:14px;flex-shrink:0}
.auth-feat strong{font-size:12.5px;color:#111827;display:block}.auth-feat span{font-size:11px;color:#6b7280}
.auth-card{background:#fff;border-radius:16px;padding:32px;box-shadow:0 4px 24px rgba(0,0,0,.06)}
.auth-card *{box-sizing:border-box}
.auth-card h1{font-size:24px;font-weight:800;color:#0f172a;margin:0 0 4px}
.auth-card p{font-size:14px;color:#6b7280;margin:0 0 24px}
.auth-card .accp-field .input-wrap{
    display:flex;
    align-items:center;
    border:1px solid #e5e7eb;
    border-radius:8px;
    background:#fff;
    overflow:hidden;
}
.auth-card .accp-field .input-wrap input{
    flex:1 1 auto;
    width:auto;
    min-width:0;
    border:0;
    border-radius:0;
    box-shadow:none;
    padding-right:8px;
}
.auth-card .accp-field .input-wrap .toggle-pw{
    position:static;
    flex:0 0 42px;
    width:42px;
    height:42px;
    display:inline-flex;
    align-items:center;
    justify-content:center;
    padding:0;
    border:0;
    background:transparent;
    transform:none;
    z-index:2;
    color:#64748b;
    cursor:pointer;
    font-size:15px;
}
.auth-card .accp-field .input-wrap:focus-within{
    border-color:#0d9488;
    box-shadow:0 0 0 3px rgba(13,148,136,.1);
}
.auth-card .accp-field .input-wrap input:focus{box-shadow:none}
.auth-divider{display:flex;align-items:center;gap:12px;margin:20px 0}
.auth-divider::before,.auth-divider::after{content:'';flex:1;height:1px;background:#e5e7eb}
.auth-divider span{font-size:12px;color:#9ca3af;white-space:nowrap}
.auth-social{display:flex;gap:12px}
.auth-social-btn{flex:1;height:42px;border:1px solid #e5e7eb;border-radius:8px;display:flex;align-items:center;justify-content:center;gap:8px;font-size:13px;font-weight:600;color:#374151;text-decoration:none;transition:all .15s}
.auth-social-btn:hover{border-color:#0d9488;color:#0d9488;text-decoration:none}
.auth-footer{text-align:center;margin-top:20px;font-size:14px;color:#6b7280}
.auth-footer a{color:#0d9488;font-weight:600;text-decoration:none}
@media(max-width:768px){
    .auth-container{padding-left:16px;padding-right:16px;overflow:hidden}
    .auth-left{padding:24px 0}
    .auth-card{padding:24px 18px}
    .auth-card label span{min-width:0;overflow-wrap:anywhere}
    .auth-social{flex-direction:column}
}
</style>
<script>
document.querySelectorAll('.auth-card .toggle-pw').forEach(function (button) {
    button.addEventListener('click', function () {
        var input = button.parentElement.querySelector('[data-auth-password]');
        if (!input) return;

        var icon = button.querySelector('i');
        var isPassword = input.getAttribute('type') === 'password';
        input.setAttribute('type', isPassword ? 'text' : 'password');
        button.setAttribute('aria-label', isPassword ? 'Ẩn mật khẩu' : 'Hiện mật khẩu');

        if (icon) {
            icon.classList.toggle('fa-eye-slash', isPassword);
            icon.classList.toggle('fa-eye', !isPassword);
        }
    });
});
</script>
@endsection
