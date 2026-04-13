@extends('admin_layout')

@section('admin_content')
    <div class="g-admin-page-shell g-admin-admin-profile">
        <section class="g-admin-page-hero">
            <div>
                <span class="g-admin-page-eyebrow">Tài khoản quản trị</span>
                <h1>Hồ sơ của bạn</h1>
                <p>Cập nhật thông tin đăng nhập, mật khẩu và xem nhanh vai trò hiện tại trong khu quản trị.</p>
            </div>
            <div class="g-admin-page-stats">
                <article>
                    <strong>{{ number_format($roleNames->count()) }}</strong>
                    <span>Vai trò đang gán</span>
                </article>
                <article>
                    <strong>{{ number_format($permissionNames->count()) }}</strong>
                    <span>Quyền truy cập hiện có</span>
                </article>
            </div>
        </section>

        @if (session('message'))
            <section class="g-admin-panel-card">
                <div class="g-admin-panel-body">
                    <div class="alert alert-success" style="margin-bottom: 0;">{{ session('message') }}</div>
                </div>
            </section>
        @endif

        @if ($errors->any())
            <section class="g-admin-panel-card">
                <div class="g-admin-panel-body">
                    <div class="alert alert-danger" style="margin-bottom: 0;">
                        <ul style="margin-bottom: 0; padding-left: 18px;">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </section>
        @endif

        <div class="g-admin-panel-grid two-col">
            <section class="g-admin-panel-card">
                <header class="g-admin-panel-head">
                    <div>
                        <strong>Thông tin hiện tại</strong>
                        <p>Tổng hợp thông tin đăng nhập và quyền hiện đang áp dụng cho tài khoản này.</p>
                    </div>
                </header>
                <div class="g-admin-panel-body">
                    <div class="g-admin-profile-meta">
                        <article>
                            <span>Tên hiển thị</span>
                            <strong>{{ $adminUser->name }}</strong>
                        </article>
                        <article>
                            <span>Email đăng nhập</span>
                            <strong>{{ $adminUser->email }}</strong>
                        </article>
                        @if ($profileSupportsPhone)
                            <article>
                                <span>Số điện thoại</span>
                                <strong>{{ $adminUser->phone ?: 'Chưa cập nhật' }}</strong>
                            </article>
                        @endif
                    </div>

                    <div class="g-admin-profile-section">
                        <h3>Vai trò</h3>
                        <div class="g-admin-tag-list">
                            @forelse ($roleNames as $roleName)
                                <span class="g-admin-tag">{{ $roleName }}</span>
                            @empty
                                <span class="text-muted">Chưa có vai trò.</span>
                            @endforelse
                        </div>
                    </div>

                    <div class="g-admin-profile-section">
                        <h3>Quyền đang có</h3>
                        <div class="g-admin-tag-list">
                            @forelse ($permissionNames as $permissionName)
                                <span class="g-admin-tag g-admin-tag--subtle">{{ $permissionName }}</span>
                            @empty
                                <span class="text-muted">Tài khoản này chưa có quyền tác vụ nào.</span>
                            @endforelse
                        </div>
                    </div>
                </div>
            </section>

            <section class="g-admin-panel-card">
                <header class="g-admin-panel-head">
                    <div>
                        <strong>Cập nhật hồ sơ</strong>
                        <p>Đổi tên, email và mật khẩu nếu cần. Chỉ yêu cầu mật khẩu hiện tại khi bạn muốn đổi mật khẩu mới.</p>
                    </div>
                </header>
                <div class="g-admin-panel-body">
                    <form action="{{ route('admin.profile.update') }}" method="POST" class="g-admin-site-info-form">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label for="admin_profile_name">Tên hiển thị</label>
                            <input id="admin_profile_name" type="text" name="name" class="form-control"
                                value="{{ old('name', $adminUser->name) }}" required>
                        </div>
                        <div class="form-group">
                            <label for="admin_profile_email">Email đăng nhập</label>
                            <input id="admin_profile_email" type="email" name="email" class="form-control"
                                value="{{ old('email', $adminUser->email) }}" required>
                        </div>
                        @if ($profileSupportsPhone)
                            <div class="form-group">
                                <label for="admin_profile_phone">Số điện thoại</label>
                                <input id="admin_profile_phone" type="text" name="phone" class="form-control"
                                    value="{{ old('phone', $adminUser->phone) }}" placeholder="Số điện thoại liên hệ">
                            </div>
                        @endif
                        <div class="form-group">
                            <label for="admin_profile_current_password">Mật khẩu hiện tại</label>
                            <input id="admin_profile_current_password" type="password" name="current_password" class="form-control"
                                placeholder="Chỉ nhập khi muốn đổi mật khẩu">
                        </div>
                        <div class="form-group">
                            <label for="admin_profile_password">Mật khẩu mới</label>
                            <input id="admin_profile_password" type="password" name="password" class="form-control"
                                placeholder="Tối thiểu 8 ký tự">
                        </div>
                        <div class="form-group">
                            <label for="admin_profile_password_confirmation">Xác nhận mật khẩu mới</label>
                            <input id="admin_profile_password_confirmation" type="password" name="password_confirmation" class="form-control"
                                placeholder="Nhập lại mật khẩu mới">
                        </div>
                        <div class="g-admin-inline-actions">
                            <button type="submit" class="btn btn-info">Lưu hồ sơ</button>
                            <a href="{{ route('admin.settings') }}" class="btn btn-default">Đi tới cài đặt</a>
                        </div>
                    </form>
                </div>
            </section>
        </div>

        @if ($recommendedDestinations->isNotEmpty())
            <section class="g-admin-panel-card">
                <header class="g-admin-panel-head">
                    <div>
                        <strong>Đi nhanh tới các màn hình thường dùng</strong>
                        <p>Các lối tắt phù hợp với quyền hiện tại của tài khoản này.</p>
                    </div>
                </header>
                <div class="g-admin-panel-body">
                    <div class="g-admin-workspace-grid">
                        @foreach ($recommendedDestinations as $destination)
                            <a href="{{ $destination['url'] }}" class="g-admin-workspace-card">
                                <span class="g-admin-workspace-icon">
                                    <i class="fa {{ $destination['icon'] }}" aria-hidden="true"></i>
                                </span>
                                <span class="g-admin-workspace-copy">
                                    <strong>{{ $destination['title'] }}</strong>
                                    <small>{{ $destination['description'] }}</small>
                                    <span class="g-admin-workspace-section">{{ $destination['section'] }}</span>
                                </span>
                            </a>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif
    </div>
@endsection
