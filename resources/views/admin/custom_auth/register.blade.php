@extends('admin.custom_auth.layout')

@section('title', 'Đăng ký quản trị')
@section('hero_title', 'Tạo tài khoản cho khu vực quản trị')
@section('hero_copy', 'Khởi tạo tài khoản quản trị đầu tiên hoặc đăng ký thêm tài khoản vận hành mới theo mã cấp quyền của hệ thống.')
@section('form_title', 'Đăng ký tài khoản')
@section('form_intro', $adminAuthBootstrapMode
    ? 'Hệ thống hiện chưa có tài khoản quản trị nào. Tài khoản đầu tiên sẽ được dùng để khởi tạo quyền điều hành.'
    : 'Điền thông tin tài khoản mới. Nếu hệ thống yêu cầu, hãy nhập mã cấp quyền quản trị được cấp cho bạn.')

@section('hero_notes')
    <li><i class="fa fa-check-circle"></i><span>Tài khoản đầu tiên sẽ được gán vai trò <strong>Quản trị cao nhất</strong> để khởi tạo hệ thống.</span></li>
    <li><i class="fa fa-check-circle"></i><span>Các tài khoản đăng ký sau đó mặc định nhận vai trò <strong>{{ $adminAuthRegistrationRole }}</strong>.</span></li>
    <li><i class="fa fa-check-circle"></i><span>Quản trị viên hiện tại vẫn có thể đổi vai trò hoặc phân quyền chi tiết sau khi bạn đăng ký.</span></li>
@endsection

@section('form_body')
    @if (! empty($adminAuthDatabaseUnavailable))
        <div class="admin-auth-meta">
            <strong>Chưa thể tạo tài khoản quản trị.</strong>
            <div>Hệ thống đang không kết nối được cơ sở dữ liệu nên không thể kiểm tra email, vai trò hoặc lưu tài khoản mới.</div>
        </div>
    @elseif (! $adminAuthRegistrationEnabled)
        <div class="admin-auth-meta">
            <strong>Đăng ký quản trị hiện chưa sẵn sàng.</strong>
            <div>
                @if ($adminAuthBootstrapMode)
                    Hệ thống đang tạm tắt đăng ký quản trị đầu tiên. Hãy kiểm tra lại cấu hình trước khi tiếp tục.
                @else
                    Hệ thống cần được cấu hình <code>ADMIN_REGISTRATION_CODE</code> để cấp thêm tài khoản quản trị mới.
                @endif
            </div>
        </div>
    @else
        <form action="{{ route('admin.register.store') }}" method="post" novalidate class="admin-auth-grid">
            @csrf

            <div class="admin-auth-grid admin-auth-grid--double">
                <div class="form-group">
                    <label for="name">Tên quản trị viên</label>
                    <input type="text" class="form-control" id="name" name="name"
                        value="{{ old('name') }}" placeholder="Nguyen Van A" required>
                </div>

                <div class="form-group">
                    <label for="phone">Số điện thoại</label>
                    <input type="text" class="form-control" id="phone" name="phone"
                        value="{{ old('phone') }}" placeholder="0909xxxxxx">
                </div>
            </div>

            <div class="form-group">
                <label for="email">Email quản trị</label>
                <input type="email" class="form-control" id="email" name="email"
                    value="{{ old('email') }}" placeholder="quantri@congty.com" required>
            </div>

            @if ($adminAuthRequiresRegistrationCode)
                <div class="form-group">
                    <label for="registration_code">Mã cấp quyền đăng ký</label>
                    <input type="password" class="form-control" id="registration_code" name="registration_code"
                        placeholder="Nhập mã cấp quyền do quản trị viên cung cấp" required>
                </div>
            @endif

            <div class="admin-auth-grid admin-auth-grid--double">
                <div class="form-group">
                    <label for="password">Mật khẩu</label>
                    <input type="password" class="form-control" id="password" name="password"
                        placeholder="Ít nhất 8 ký tự" required>
                </div>

                <div class="form-group">
                    <label for="password_confirmation">Xác nhận mật khẩu</label>
                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation"
                        placeholder="Nhập lại mật khẩu" required>
                </div>
            </div>

            <div class="admin-auth-meta">
                <strong>Vai trò dự kiến:</strong> {{ $adminAuthRegistrationRole }}<br>
                @if ($adminAuthBootstrapMode)
                    Tài khoản này sẽ được dùng để hoàn tất bước khởi tạo quyền quản trị đầu tiên.
                @else
                    Sau khi tạo tài khoản, bạn có thể đăng nhập ngay và quản trị viên cấp cao hơn vẫn có thể điều chỉnh quyền sau đó.
                @endif
            </div>

            <div class="admin-auth-link-row">
                <button type="submit" class="btn btn-primary">Tạo tài khoản quản trị</button>
                <a href="{{ route('admin.login') }}" class="admin-auth-link">Quay lại đăng nhập</a>
            </div>
        </form>
    @endif
@endsection
