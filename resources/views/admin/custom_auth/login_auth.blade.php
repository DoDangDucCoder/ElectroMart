@extends('admin.custom_auth.layout')

@section('title', 'Đăng nhập quản trị')
@section('hero_title', 'Hệ thống quản trị DangDuc')
@section('hero_copy', 'Quản lý toàn diện sản phẩm, đơn hàng, khách hàng và báo cáo hiệu quả – Nền tảng vận hành mạnh mẽ cho tăng trưởng bền vững.')
@section('form_title', 'Đăng nhập quản trị')
@section('form_intro', 'Chào mừng bạn quay trở lại hệ thống quản trị DangDuc')
@section('badge', 'Quản trị DangDuc')

@section('hero_notes')
    {{-- Dashboard preview mockup --}}
    <li style="display:block;margin-top:20px">
        <div style="background:rgba(255,255,255,.08);border-radius:14px;padding:18px 16px;backdrop-filter:blur(6px);border:1px solid rgba(255,255,255,.08)">
            <div style="display:flex;align-items:center;gap:8px;margin-bottom:14px">
                <img src="{{ asset('uploads/brand/dangduc-logo-icon-256.png') }}" alt="" style="width:22px;height:22px;border-radius:4px">
                <span style="font-size:12px;font-weight:700;opacity:.7">dangduc</span>
                <span style="font-size:10px;color:#6b7f99;margin-left:auto">Tổng quan</span>
            </div>
            <div style="display:flex;gap:10px;margin-bottom:14px;font-size:11px">
                <span style="flex:1;display:flex;align-items:center;gap:6px"><i class="fa fa-dashboard" style="color:#3b82f6;font-size:10px"></i>Tổng quan</span>
                <span style="flex:1;display:flex;align-items:center;gap:6px"><i class="fa fa-shopping-bag" style="color:#ef4444;font-size:10px"></i>Đơn hàng</span>
                <span style="flex:1;display:flex;align-items:center;gap:6px"><i class="fa fa-cube" style="color:#8b5cf6;font-size:10px"></i>Sản phẩm</span>
            </div>
            <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:8px;margin-bottom:12px">
                <div style="background:rgba(255,255,255,.06);border-radius:8px;padding:8px 6px;text-align:center">
                    <div style="font-size:13px;font-weight:800">2.489.000.000đ</div>
                    <div style="font-size:8px;opacity:.5">Tổng doanh thu</div>
                </div>
                <div style="background:rgba(255,255,255,.06);border-radius:8px;padding:8px 6px;text-align:center">
                    <div style="font-size:13px;font-weight:800">1.259</div>
                    <div style="font-size:8px;opacity:.5">Đơn hàng</div>
                </div>
                <div style="background:rgba(255,255,255,.06);border-radius:8px;padding:8px 6px;text-align:center">
                    <div style="font-size:13px;font-weight:800">5.243</div>
                    <div style="font-size:8px;opacity:.5">Khách hàng</div>
                </div>
                <div style="background:rgba(255,255,255,.06);border-radius:8px;padding:8px 6px;text-align:center">
                    <div style="font-size:13px;font-weight:800">1.248</div>
                    <div style="font-size:8px;opacity:.5">Sản phẩm</div>
                </div>
            </div>
            <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:6px;font-size:10px;opacity:.6">
                <span>#DD250524001 Nguyễn Văn An 23.990.000đ</span>
                <span>#DD250524002 Trần Thị Bích Ngọc 28.990.000đ</span>
                <span>#DD250524003 Lê Minh Đức 18.490.000đ</span>
                <span>#DD250524004 Phạm Hoàng Nam 25.490.000đ</span>
            </div>
        </div>
    </li>
    {{-- Feature cards --}}
    <li class="admin-auth-feature-grid">
        <div class="admin-auth-feature-card">
            <i class="fa fa-cube"></i>
            <div class="admin-auth-feature-title">Quản lý sản phẩm</div>
            <div class="admin-auth-feature-copy">Thêm, sửa, phân loại và kiểm soát tồn kho dễ dàng.</div>
        </div>
        <div class="admin-auth-feature-card">
            <i class="fa fa-shopping-cart"></i>
            <div class="admin-auth-feature-title">Theo dõi đơn hàng</div>
            <div class="admin-auth-feature-copy">Xử lý đơn hàng nhanh chóng, chính xác theo từng trạng thái.</div>
        </div>
        <div class="admin-auth-feature-card">
            <i class="fa fa-bar-chart"></i>
            <div class="admin-auth-feature-title">Báo cáo & phân quyền</div>
            <div class="admin-auth-feature-copy">Báo cáo trực quan, phân quyền linh hoạt, bảo mật.</div>
        </div>
    </li>
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
                <label for="email"><i class="fa fa-user" style="margin-right:4px;color:#1f6feb"></i> Tên đăng nhập hoặc email</label>
                <input type="email" class="form-control" id="email" name="email"
                    value="{{ old('email') }}" placeholder="Nhập tên đăng nhập hoặc email" required>
            </div>

            <div class="form-group">
                <label for="password"><i class="fa fa-lock" style="margin-right:4px;color:#1f6feb"></i> Mật khẩu</label>
                <div style="position:relative">
                    <input type="password" class="form-control" id="password" name="password"
                        placeholder="Nhập mật khẩu" required style="padding-right:44px">
                    <button type="button" onclick="var p=document.getElementById('password');p.type=p.type==='password'?'text':'password'" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);border:none;background:none;color:#9ca3af;cursor:pointer;font-size:16px"><i class="fa fa-eye"></i></button>
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

            <button type="submit" class="btn btn-primary btn-block" style="display:flex;align-items:center;justify-content:center;gap:8px"><i class="fa fa-sign-in"></i> Đăng nhập</button>
            <a href="{{ url('/') }}" class="btn btn-default btn-block" style="display:flex;align-items:center;justify-content:center;gap:8px;text-decoration:none"><i class="fa fa-globe"></i> Quay về website</a>
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

    <div style="text-align:center;margin-top:8px">
        <span style="font-size:12px;color:#9ca3af"><i class="fa fa-lock" style="margin-right:4px"></i>Chỉ dành cho quản trị viên được cấp quyền</span>
    </div>
    <div style="text-align:center;background:#f0fdf4;border-radius:10px;padding:12px;border:1px solid #dcfce7">
        <div style="font-size:12px;color:#15803d;font-weight:600"><i class="fa fa-shield" style="margin-right:4px;color:#16a34a"></i>Bảo mật 2 lớp / Truy cập an toàn</div>
        <div style="font-size:11px;color:#6b7280;margin-top:4px">Hệ thống được bảo vệ bởi nhiều lớp bảo mật hiện đại để đảm bảo an toàn dữ liệu.</div>
    </div>
    <div style="text-align:center;font-size:11px;color:#9ca3af;margin-top:8px">© {{ date('Y') }} Quản trị DangDuc. Đã đăng ký bản quyền.</div>
@endsection
