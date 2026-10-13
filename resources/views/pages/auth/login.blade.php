@extends('layouts.home-tech')
@section('body_class','auth-page')
@php
$meta_title=$meta_title??'Đăng nhập - DangDuc';
$meta_desc=$meta_desc??'Đăng nhập tài khoản DangDuc để mua sắm và quản lý đơn hàng';
$url_canonical=$url_canonical??url('/dang-nhap');
@endphp
@section('content')
<div class="auth-wrap">
    @include('partials.storefront.breadcrumb', ['class' => 'accp-crumb', 'separator' => '›', 'items' => [['label' => 'Đăng nhập', 'current' => true]]])
    <div class="auth-container">
        <div class="auth-left">
            <div class="auth-brand">
                <div style="width:48px;height:48px;background:#0d9488;border-radius:12px;display:flex;align-items:center;justify-content:center;color:#fff;font-size:24px;font-weight:800">D</div>
                <h2>Chào mừng trở lại!</h2>
                <p>Đăng nhập để tiếp tục mua sắm và quản lý đơn hàng tại DangDuc</p>
            </div>
            <div class="auth-features">
                <div class="auth-feat"><i class="fa fa-truck"></i><div><strong>Theo dõi đơn hàng</strong><span>Cập nhật realtime</span></div></div>
                <div class="auth-feat"><i class="fa fa-star"></i><div><strong>Tích điểm đổi quà</strong><span>Ưu đãi hấp dẫn</span></div></div>
                <div class="auth-feat"><i class="fa fa-shield"></i><div><strong>Bảo hành nhanh</strong><span>Hỗ trợ tận tâm</span></div></div>
                <div class="auth-feat"><i class="fa fa-gift"></i><div><strong>Ưu đãi thành viên</strong><span>Giảm giá riêng</span></div></div>
            </div>
        </div>
        <div class="auth-right">
            <div class="auth-card">
                <h1>Đăng nhập</h1>
                <p>Nhập thông tin tài khoản của bạn</p>
                @if(session('error'))
                    <div class="auth-notice auth-notice--error">{{ session('error') }}</div>
                @endif
                @if(session('message'))
                    <div class="auth-notice auth-notice--success">{{ session('message') }}</div>
                @endif
                <form action="{{ url('/login-customer') }}" method="POST">
                    @csrf
                    <div class="accp-field"><label>Email hoặc Số điện thoại</label><input type="text" name="email_account" value="{{ old('email_account') }}" placeholder="Nhập email hoặc số điện thoại" required></div>
                    <div class="accp-field"><label>Mật khẩu</label>
                        <div class="input-wrap"><input type="password" name="password_account" placeholder="Nhập mật khẩu" required data-auth-password><button type="button" class="toggle-pw" aria-label="Hiện mật khẩu"><i class="fa fa-eye"></i></button></div>
                    </div>
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px">
                        <label style="display:flex;align-items:center;gap:6px;font-size:13px;color:#374151;cursor:pointer"><input type="checkbox" name="remember"> Ghi nhớ đăng nhập</label>
                        <a href="{{ url('/quen-mat-khau') }}" style="font-size:13px;color:#0d9488;text-decoration:none;font-weight:500">Quên mật khẩu?</a>
                    </div>
                    <button type="submit" class="accp-btn accp-btn--primary accp-btn--block" style="height:46px;font-size:15px"><i class="fa fa-sign-in"></i> Đăng nhập</button>
                </form>
                {{-- Fix #13: Chỉ hiển thị Social Login khi đã cấu hình credentials --}}
                @if(config('services.google.client_id') || config('services.facebook.client_id'))
                <div class="auth-divider"><span>Hoặc đăng nhập bằng</span></div>
                <div class="auth-social">
                    @if(config('services.google.client_id'))
                    <a href="{{ url('/login-customer-google') }}" class="auth-social-btn google"><i class="fa fa-google"></i> Google</a>
                    @endif
                    @if(config('services.facebook.client_id'))
                    <a href="{{ url('/login-facebook-customer') }}" class="auth-social-btn facebook"><i class="fa fa-facebook"></i> Facebook</a>
                    @endif
                </div>
                @endif
                <div class="auth-footer">Chưa có tài khoản? <a href="{{ url('/dang-ky') }}">Đăng ký ngay</a></div>
            </div>
        </div>
    </div>
</div>
<style>
body.auth-page{background:#f3f5f7;font-family:'Inter',-apple-system,sans-serif}
.auth-wrap{min-height:60vh}
.auth-container{max-width:1000px;margin:0 auto;padding:0 16px 40px;display:grid;grid-template-columns:1fr 1fr;gap:32px;align-items:center}
@media(max-width:768px){.auth-container{grid-template-columns:1fr}}
.auth-left{padding:24px}
.auth-brand h2{font-size:28px;font-weight:800;color:#0f172a;margin:16px 0 8px}
.auth-brand p{font-size:14px;color:#6b7280;line-height:1.5}
.auth-features{display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-top:24px}
.auth-feat{display:flex;align-items:center;gap:10px;padding:12px;background:#f0fdfa;border-radius:10px}
.auth-feat i{width:36px;height:36px;border-radius:8px;background:#0d9488;color:#fff;display:flex;align-items:center;justify-content:center;font-size:14px;flex-shrink:0}
.auth-feat strong{font-size:12.5px;color:#111827;display:block}.auth-feat span{font-size:11px;color:#6b7280}
.auth-card{background:#fff;border-radius:16px;padding:32px;box-shadow:0 4px 24px rgba(0,0,0,.06)}
.auth-card h1{font-size:24px;font-weight:800;color:#0f172a;margin:0 0 4px}
.auth-card p{font-size:14px;color:#6b7280;margin:0 0 24px}
.auth-notice{border-radius:8px;margin:0 0 16px;padding:10px 12px;font-size:13px;font-weight:600;line-height:1.45}
.auth-notice--error{background:#fef2f2;border:1px solid #fecaca;color:#b91c1c}
.auth-notice--success{background:#ecfdf5;border:1px solid #a7f3d0;color:#047857}
.auth-divider{display:flex;align-items:center;gap:12px;margin:20px 0}
.auth-divider::before,.auth-divider::after{content:'';flex:1;height:1px;background:#e5e7eb}
.auth-divider span{font-size:12px;color:#9ca3af;white-space:nowrap}
.auth-social{display:flex;gap:12px}
.auth-social-btn{flex:1;height:42px;border:1px solid #e5e7eb;border-radius:8px;display:flex;align-items:center;justify-content:center;gap:8px;font-size:13px;font-weight:600;color:#374151;text-decoration:none;transition:all .15s}
.auth-social-btn:hover{border-color:#0d9488;color:#0d9488;text-decoration:none}
.auth-social-btn.google:hover{border-color:#ea4335;color:#ea4335}
.auth-social-btn.facebook:hover{border-color:#1877f2;color:#1877f2}
.auth-footer{text-align:center;margin-top:20px;font-size:14px;color:#6b7280}
.auth-footer a{color:#0d9488;font-weight:600;text-decoration:none}
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
