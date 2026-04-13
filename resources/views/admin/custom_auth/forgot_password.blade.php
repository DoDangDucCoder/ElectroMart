@extends('admin.custom_auth.layout')

@section('title', 'Quên mật khẩu quản trị')
@section('hero_title', 'Khôi phục mật khẩu quản trị')
@section('hero_copy', 'Nhập email quản trị đã đăng ký để nhận liên kết đặt lại mật khẩu và quay lại bảng điều khiển an toàn.')
@section('form_title', 'Quên mật khẩu')
@section('form_intro', 'Nếu email tồn tại và có quyền quản trị, hệ thống sẽ gửi cho bạn một liên kết đặt lại mật khẩu.')

@section('hero_notes')
    <li><i class="fa fa-check-circle"></i><span>Liên kết đặt lại mật khẩu chỉ có hiệu lực trong thời gian ngắn.</span></li>
    <li><i class="fa fa-check-circle"></i><span>Nếu bạn không nhận được email, hãy kiểm tra hộp thư spam hoặc thử lại sau ít phút.</span></li>
    <li><i class="fa fa-check-circle"></i><span>Chỉ email của tài khoản quản trị hợp lệ mới có thể tiếp tục bước tiếp theo.</span></li>
@endsection

@section('form_body')
    @if (! empty($adminAuthDatabaseUnavailable))
        <div class="admin-auth-meta">
            <strong>Chưa thể gửi email đặt lại mật khẩu.</strong>
            <div>Luồng khôi phục mật khẩu cần truy cập cơ sở dữ liệu để xác định tài khoản quản trị hợp lệ.</div>
        </div>
    @else
        <form action="{{ route('admin.password.email') }}" method="post" novalidate class="admin-auth-grid">
            @csrf

            <div class="form-group">
                <label for="email">Email quản trị</label>
                <input type="email" class="form-control" id="email" name="email"
                    value="{{ old('email') }}" placeholder="quantri@congty.com" required>
            </div>

            <button type="submit" class="btn btn-primary btn-block">Gửi liên kết đặt lại mật khẩu</button>
        </form>
    @endif

    <div class="admin-auth-link-row">
        <a href="{{ route('admin.login') }}" class="admin-auth-link">Quay lại đăng nhập</a>
        <a href="{{ route('admin.register') }}" class="admin-auth-link">Tạo tài khoản quản trị mới</a>
    </div>
@endsection
