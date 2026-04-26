@extends('admin.custom_auth.layout')

@section('title', 'Đăng nhập quản trị')
@section('hero_title', 'Đăng nhập khu vực quản trị')
@section('hero_copy', 'Truy cập bảng điều khiển để quản lý sản phẩm, nội dung, đơn hàng và vận hành hệ thống trên cùng một giao diện tập trung.')
@section('form_title', 'Xin chào')
@section('form_intro', 'Đăng nhập bằng email quản trị để tiếp tục vào trang điều hành.')

@section('hero_notes')
    <li><i class="fa fa-check-circle"></i><span>Chỉ tài khoản có vai trò quản trị mới có thể truy cập khu vực này.</span></li>
    <li><i class="fa fa-check-circle"></i><span>Nếu quên mật khẩu, bạn có thể yêu cầu email đặt lại ngay từ màn hình đăng nhập.</span></li>
    <li><i class="fa fa-check-circle"></i><span>Nếu hệ thống chưa có tài khoản quản trị nào, bạn có thể khởi tạo tài khoản đầu tiên.</span></li>
@endsection

@section('form_body')
    @if (! empty($adminAuthDatabaseUnavailable))
        <div class="admin-auth-meta">
            <strong>Đăng nhập tạm thời bị khóa.</strong><br>
            Trang này đã được tải ở chế độ an toàn để tránh lỗi 500 khi MySQL chưa chạy. Hãy bật dịch vụ DB rồi thử lại.
        </div>
    @else
        <form action="{{ route('admin.login.attempt') }}" method="post" novalidate class="admin-auth-grid">
            @csrf

            <div class="form-group">
                <label for="email">Email quản trị</label>
                <input type="email" class="form-control" id="email" name="email"
                    value="{{ old('email') }}" placeholder="quantri@congty.com" required>
            </div>

            <div class="form-group">
                <label for="password">Mật khẩu</label>
                <input type="password" class="form-control" id="password" name="password"
                    placeholder="Nhập mật khẩu quản trị" required>
            </div>

            <div class="admin-auth-link-row">
                <div class="checkbox">
                    <label>
                        <input type="checkbox" name="remember" value="1">
                        Ghi nhớ đăng nhập trên thiết bị này
                    </label>
                </div>

                <a href="{{ route('admin.password.request') }}" class="admin-auth-link">Quên mật khẩu?</a>
            </div>

            <button type="submit" class="btn btn-primary btn-block">Đăng nhập quản trị</button>
        </form>
    @endif

    @if (! empty($adminAuthShowSeedHint))
        <div class="admin-auth-meta">
            <strong>Dữ liệu seed local</strong><br>
            Khối này chỉ hiện ở môi trường local để tiện kiểm thử nhanh.<br>
            Quản trị cao nhất:
            <code>{{ $adminAuthSeedDefaults['primary_email'] ?? 'admin@shop.com' }}</code> /
            <code>{{ $adminAuthSeedDefaults['primary_password'] ?? 'admin123' }}</code><br>
            Biên tập viên:
            <code>{{ $adminAuthSeedDefaults['editor_email'] ?? 'editor@shop.com' }}</code> /
            <code>{{ $adminAuthSeedDefaults['editor_password'] ?? 'editor123' }}</code>
        </div>
    @endif

    <div class="admin-auth-link-row">
        <a href="{{ route('admin.register') }}" class="admin-auth-link">Đăng ký tài khoản quản trị</a>
        <a href="{{ url('/') }}" class="admin-auth-link">Quay lại trang chủ website</a>
    </div>
@endsection
