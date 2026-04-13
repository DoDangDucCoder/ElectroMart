@extends('admin_layout')

@section('admin_content')
    <div class="g-admin-page-shell g-admin-admin-settings">
        <section class="g-admin-page-hero">
            <div>
                <span class="g-admin-page-eyebrow">Cài đặt quản trị</span>
                <h1>Trung tâm điều hướng và cấu hình</h1>
                <p>Đi tới đúng các màn hình cấu hình đang hoạt động và được cấp quyền cho tài khoản hiện tại.</p>
            </div>
            <div class="g-admin-page-stats">
                <article>
                    <strong>{{ number_format($shortcutCount) }}</strong>
                    <span>Điểm đến đang truy cập được</span>
                </article>
                <article>
                    <strong>{{ number_format($permissionCount) }}</strong>
                    <span>Quyền hiện có</span>
                </article>
            </div>
        </section>

        <section class="g-admin-panel-card">
            <div class="g-admin-panel-body">
                <div class="alert alert-info" style="margin-bottom: 0;">
                    Tài khoản <strong>{{ $adminUser->name }}</strong> hiện đang dùng vai trò
                    <strong>{{ $roleNames->implode(', ') ?: 'chưa có vai trò' }}</strong>. Danh sách bên dưới đã tự lọc theo quyền thực tế.
                </div>
            </div>
        </section>

        @forelse ($groupedDestinations as $section => $items)
            <section class="g-admin-panel-card">
                <header class="g-admin-panel-head">
                    <div>
                        <strong>{{ $section }}</strong>
                        <p>Các màn hình thuộc nhóm {{ \Illuminate\Support\Str::lower($section) }} mà tài khoản này đang mở được.</p>
                    </div>
                </header>
                <div class="g-admin-panel-body">
                    <div class="g-admin-workspace-grid">
                        @foreach ($items as $destination)
                            <a href="{{ $destination['url'] }}" class="g-admin-workspace-card">
                                <span class="g-admin-workspace-icon">
                                    <i class="fa {{ $destination['icon'] }}" aria-hidden="true"></i>
                                </span>
                                <span class="g-admin-workspace-copy">
                                    <strong>{{ $destination['title'] }}</strong>
                                    <small>{{ $destination['description'] }}</small>
                                </span>
                            </a>
                        @endforeach
                    </div>
                </div>
            </section>
        @empty
            <section class="g-admin-panel-card">
                <div class="g-admin-panel-body">
                    <div class="g-admin-empty-state">Tài khoản này hiện chưa có lối đi quản trị nào ngoài đăng nhập cơ bản.</div>
                </div>
            </section>
        @endforelse
    </div>
@endsection
