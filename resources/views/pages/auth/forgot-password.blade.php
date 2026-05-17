@extends('layouts.home-tech')
@section('body_class','auth-page')
@php
$meta_title=$meta_title??'Quên mật khẩu - DangDuc';
$meta_desc=$meta_desc??'Khôi phục mật khẩu tài khoản DangDuc';
$url_canonical=$url_canonical??url('/quen-mat-khau');
@endphp
@section('content')
<div class="auth-wrap">
    @include('partials.storefront.breadcrumb', ['class' => 'accp-crumb', 'separator' => '›', 'items' => [['label' => 'Quên mật khẩu', 'current' => true]]])
    <div style="max-width:480px;margin:0 auto;padding:0 16px 40px">
        <div class="auth-card" style="text-align:center">
            <div style="width:64px;height:64px;border-radius:50%;background:#f0fdfa;color:#0d9488;display:flex;align-items:center;justify-content:center;font-size:28px;margin:0 auto 16px"><i class="fa fa-lock"></i></div>
            <h1 style="font-size:24px;font-weight:800;color:#0f172a;margin:0 0 8px">Quên mật khẩu?</h1>
            <p style="font-size:14px;color:#6b7280;margin:0 0 24px;line-height:1.5">Nhập email hoặc số điện thoại đã đăng ký, chúng tôi sẽ gửi mã xác nhận để đặt lại mật khẩu.</p>
            <form action="{{ url('/recover-pass') }}" method="POST" style="text-align:left">
                @csrf
                <div class="accp-field"><label>Email hoặc Số điện thoại</label><input type="text" name="email_account" placeholder="Nhập email hoặc số điện thoại" required></div>
                <button type="submit" class="accp-btn accp-btn--primary accp-btn--block" style="height:46px;font-size:15px;margin-top:8px"><i class="fa fa-paper-plane"></i> Gửi mã xác nhận</button>
            </form>
            <div style="margin-top:20px"><a href="{{ url('/dang-nhap') }}" style="font-size:13px;color:#0d9488;text-decoration:none;font-weight:500"><i class="fa fa-arrow-left"></i> Quay lại Đăng nhập</a></div>
        </div>
        <div style="text-align:center;margin-top:16px;font-size:13px;color:#6b7280"><i class="fa fa-lock" style="color:#0d9488"></i> Thông tin của bạn được bảo mật tuyệt đối</div>
        <div class="auth-card" style="margin-top:20px;padding:20px">
            <h4 style="font-size:14px;font-weight:700;color:#111827;margin:0 0 12px;text-align:center">Cần hỗ trợ thêm?</h4>
            <div style="display:flex;flex-direction:column;gap:10px">
                <div style="display:flex;align-items:center;gap:10px;font-size:13px"><i class="fa fa-phone" style="color:#0d9488;width:18px;text-align:center"></i><span style="color:#6b7280">Hotline:</span><strong>1900 1234</strong></div>
                <div style="display:flex;align-items:center;gap:10px;font-size:13px"><i class="fa fa-envelope" style="color:#0d9488;width:18px;text-align:center"></i><span style="color:#6b7280">Email:</span><strong>support@dangduc.vn</strong></div>
                <div style="display:flex;align-items:center;gap:10px;font-size:13px"><i class="fa fa-comments" style="color:#0d9488;width:18px;text-align:center"></i><span style="color:#6b7280">Chat trực tuyến 24/7</span></div>
            </div>
        </div>
    </div>
</div>
<style>
body.auth-page{background:#f3f5f7;font-family:'Inter',-apple-system,sans-serif}
.auth-wrap{min-height:60vh}
.auth-card{background:#fff;border-radius:16px;padding:32px;box-shadow:0 4px 24px rgba(0,0,0,.06)}
</style>
@endsection
