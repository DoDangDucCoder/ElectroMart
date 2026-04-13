@extends('admin.custom_auth.layout')

@section('title', 'Đặt lại mật khẩu quản trị')
@section('hero_title', 'Đặt lại mật khẩu cho tài khoản quản trị')
@section('hero_copy', 'Tạo mật khẩu mới để quay lại khu vực quản trị với phiên đăng nhập an toàn và nhất quán.')
@section('form_title', 'Mật khẩu mới')
@section('form_intro', 'Nhập mật khẩu mới cho tài khoản quản trị của bạn. Sau khi hoàn tất, hãy đăng nhập lại từ màn hình admin.')

@section('hero_notes')
    <li><i class="fa fa-check-circle"></i><span>Hãy chọn mật khẩu mới đủ mạnh và không trùng với mật khẩu cũ nếu có thể.</span></li>
    <li><i class="fa fa-check-circle"></i><span>Liên kết này chỉ áp dụng cho đúng email quản trị đã yêu cầu đặt lại.</span></li>
    <li><i class="fa fa-check-circle"></i><span>Sau khi cập nhật mật khẩu, phiên ghi nhớ cũ sẽ được làm mới lại.</span></li>
@endsection

@section('form_body')
    @if (! empty($adminAuthDatabaseUnavailable))
        <div class="admin-auth-meta">
            <strong>Chưa thể đặt lại mật khẩu quản trị.</strong>
            <div>Hệ thống cần kết nối được cơ sở dữ liệu để xác minh token và cập nhật mật khẩu mới.</div>
        </div>
    @else
        <form action="{{ route('admin.password.update') }}" method="post" novalidate class="admin-auth-grid">
            @csrf

            <input type="hidden" name="token" value="{{ $token }}">

            <div class="form-group">
                <label for="email">Email quản trị</label>
                <input type="email" class="form-control" id="email" name="email"
                    value="{{ old('email', $resetEmail) }}" placeholder="quantri@congty.com" required>
            </div>

            <div class="admin-auth-grid admin-auth-grid--double">
                <div class="form-group">
                    <label for="password">Mật khẩu mới</label>
                    <input type="password" class="form-control" id="password" name="password"
                        placeholder="Ít nhất 8 ký tự" required>
                </div>

                <div class="form-group">
                    <label for="password_confirmation">Xác nhận mật khẩu mới</label>
                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation"
                        placeholder="Nhập lại mật khẩu mới" required>
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-block">Cập nhật mật khẩu quản trị</button>
        </form>
    @endif

    <div class="admin-auth-link-row">
        <a href="{{ route('admin.password.request') }}" class="admin-auth-link">Gửi lại liên kết đặt lại mật khẩu</a>
        <a href="{{ route('admin.login') }}" class="admin-auth-link">Quay lại đăng nhập</a>
    </div>
@endsection
