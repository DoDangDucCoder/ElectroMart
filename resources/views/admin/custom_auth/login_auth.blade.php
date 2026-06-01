@extends('admin.custom_auth.layout')

@section('title', 'Đăng nhập quản trị')
@section('hero_title')
    Hệ thống quản trị <strong>DangDuc</strong>
@endsection
@section('hero_copy', 'Quản lý toàn diện sản phẩm, đơn hàng, khách hàng và báo cáo hiệu quả – Nền tảng vận hành mạnh mẽ cho tăng trưởng bền vững.')
@section('form_title', 'Đăng nhập quản trị')
@section('form_intro', 'Chào mừng bạn quay trở lại hệ thống quản trị DangDuc')
@section('badge', 'Quản trị DangDuc')

@section('hero_notes')
    <li class="admin-login-illustration" aria-hidden="true">
        <div class="admin-login-orbit"></div>
        <div class="admin-login-float clipboard"><i class="fa fa-clipboard"></i></div>
        <div class="admin-login-float bag"><i class="fa fa-shopping-bag"></i></div>
        <div class="admin-login-float users"><i class="fa fa-users"></i></div>
        <div class="admin-login-float chart"><i class="fa fa-bar-chart"></i></div>
        <div class="admin-login-plant"></div>
        <div class="admin-login-laptop"></div>
        <div class="admin-login-cup"></div>
        <div class="admin-login-platform"></div>
    </li>
    <li class="admin-auth-feature-grid">
        <div class="admin-auth-feature-card">
            <i class="fa fa-shield"></i>
            <div class="admin-auth-feature-title">Bảo mật dữ liệu</div>
            <div class="admin-auth-feature-copy">An toàn tuyệt đối</div>
        </div>
        <div class="admin-auth-feature-card">
            <i class="fa fa-bolt"></i>
            <div class="admin-auth-feature-title">Hiệu suất vượt trội</div>
            <div class="admin-auth-feature-copy">Vận hành nhanh chóng</div>
        </div>
        <div class="admin-auth-feature-card">
            <i class="fa fa-users"></i>
            <div class="admin-auth-feature-title">Quản lý toàn diện</div>
            <div class="admin-auth-feature-copy">Tối ưu quy trình</div>
        </div>
    </li>
@endsection

@section('form_body')
    <div class="admin-auth-avatar" aria-hidden="true">
        <i class="fa fa-user"></i>
    </div>

    @if (! empty($adminAuthDatabaseUnavailable))
        <div class="admin-auth-meta">
            <strong>Đăng nhập tạm thời bị khóa.</strong><br>
            Trang này đã được tải ở chế độ an toàn để tránh lỗi 500 khi MySQL chưa chạy. Hãy bật dịch vụ DB rồi thử lại.
        </div>
    @else
        <form action="{{ route('admin.login.attempt') }}" method="post" novalidate class="admin-auth-grid" style="gap:12px">
            @csrf

            <div class="form-group">
                <label for="email">Tên đăng nhập hoặc email</label>
                <div class="admin-auth-field">
                    <i class="fa fa-user admin-auth-field-icon"></i>
                    <input type="email" class="form-control" id="email" name="email"
                        value="{{ old('email') }}" placeholder="Nhập tên đăng nhập hoặc email" required>
                </div>
            </div>

            <div class="form-group">
                <label for="password">Mật khẩu</label>
                <div class="admin-auth-field">
                    <i class="fa fa-lock admin-auth-field-icon"></i>
                    <input type="password" class="form-control" id="password" name="password"
                        placeholder="Nhập mật khẩu" required>
                    <button type="button" class="admin-auth-password-toggle" onclick="var p=document.getElementById('password');p.type=p.type==='password'?'text':'password'">
                        <i class="fa fa-eye-slash"></i>
                    </button>
                </div>
            </div>

            <div class="admin-auth-link-row">
                <div class="checkbox">
                    <label>
                        <input type="checkbox" name="remember" value="1">
                        Ghi nhớ đăng nhập
                    </label>
                </div>

                <a href="{{ route('admin.password.request') }}" class="admin-auth-link">Quên mật khẩu?</a>
            </div>

            <button type="submit" class="btn btn-primary btn-block" style="display:flex;align-items:center;justify-content:center;gap:10px"><i class="fa fa-lock"></i> Đăng nhập</button>
            <div class="admin-auth-separator">HOẶC</div>
            <a href="{{ url('/') }}" class="btn btn-default btn-block" style="display:flex;align-items:center;justify-content:center;gap:10px;text-decoration:none"><i class="fa fa-globe"></i> Quay về website</a>
        </form>
    @endif

    @if (! empty($adminAuthShowSeedHint))
        <div class="admin-auth-meta">
            <strong>Dữ liệu seed local</strong><br>
            Khối này chỉ hiện ở môi trường local để tiện kiểm thử nhanh.<br>
            Quản trị cao nhất:
            <code>{{ $adminAuthSeedDefaults['primary_email'] ?? 'admin@shop.com' }}</code> /
            <code>{{ $adminAuthSeedDefaults['primary_password'] ?? 'CHANGE_ME_STRONG_PASSWORD' }}</code><br>
            Biên tập viên:
            <code>{{ $adminAuthSeedDefaults['editor_email'] ?? 'editor@shop.com' }}</code> /
            <code>{{ $adminAuthSeedDefaults['editor_password'] ?? 'CHANGE_ME_STRONG_PASSWORD' }}</code>
        </div>
    @endif

    <div class="admin-auth-safe-box">
        <i class="fa fa-shield"></i>
        <div>
            <strong>Bảo mật 2 lớp / Truy cập an toàn</strong>
            <span>Hệ thống được bảo vệ bởi nhiều lớp bảo mật để đảm bảo an toàn dữ liệu.</span>
        </div>
    </div>
@endsection
