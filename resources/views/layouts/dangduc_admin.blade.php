<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>@yield('page_title', 'DANGDUC ADMIN')</title>
    <link rel="stylesheet" href="{{ asset('backend/css/font-awesome.css') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('backend/css/admin-dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('backend/css/dangduc-admin.css') }}">
</head>
<body>
@php
    $dd_active = $dd_active ?? request()->path();
@endphp
<div class="dd-shell">
    {{-- =========== Sidebar =========== --}}
    <aside class="dd-side">
        <div class="dd-side-brand">
            <div class="dd-logo">D</div>
            <div class="dd-brand-text">
                <div class="name">DANGDUC</div>
                <div class="role">ADMIN</div>
            </div>
        </div>
        <ul class="dd-nav">
            <li class="dd-nav-item">
                <a href="{{ url('/dashboard') }}"><i class="fa fa-th-large"></i><span>Tổng quan</span></a>
            </li>
            <li class="dd-nav-item {{ $dd_active === 'admin/orders-management' ? 'active' : '' }}">
                <a href="javascript:void(0)"><i class="fa fa-shopping-bag"></i><span>Đơn hàng</span><span class="dd-nav-badge">12</span></a>
                <ul class="dd-nav-sub">
                    <li class="{{ $dd_active==='admin/orders-management' ? 'active':'' }}"><a href="{{ url('/admin/orders-management') }}">Danh sách đơn hàng</a></li>
                </ul>
            </li>
            <li class="dd-nav-item">
                <a href="{{ url('/admin/products-management') }}"><i class="fa fa-cube"></i><span>Sản phẩm</span></a>
            </li>
            <li class="dd-nav-item {{ $dd_active === 'admin/customers' ? 'active' : '' }}">
                <a href="{{ route('admin.customers') }}"><i class="fa fa-users"></i><span>Khách hàng</span></a>
            </li>
            <li class="dd-nav-item">
                <a href="{{ url('/all-post') }}"><i class="fa fa-file-text"></i><span>Bài viết</span></a>
            </li>
            <li class="dd-nav-item">
                <a href="javascript:void(0)"><i class="fa fa-bar-chart"></i><span>Báo cáo</span></a>
            </li>
            <li class="dd-nav-item {{ $dd_active==='admin/inventory' ? 'active' : '' }}">
                <a href="{{ route('admin.inventory') }}"><i class="fa fa-archive"></i><span>Kho hàng</span></a>
            </li>
            <li class="dd-nav-item">
                <a href="javascript:void(0)"><i class="fa fa-cog"></i><span>Cấu hình</span></a>
            </li>
            <li class="dd-nav-item">
                <a href="{{ url('/users') }}"><i class="fa fa-user-circle-o"></i><span>Quản trị viên</span></a>
            </li>
            <li class="dd-nav-item">
                <a href="javascript:void(0)"><i class="fa fa-history"></i><span>Nhật ký hoạt động</span></a>
            </li>
        </ul>
        <div class="dd-side-foot">
            <button type="button" class="dd-collapse"><i class="fa fa-angle-double-left"></i><span>Thu gọn</span></button>
        </div>
    </aside>

    {{-- =========== Main =========== --}}
    <div class="dd-main">
        <header class="dd-header">
            <button class="dd-burger" type="button" aria-label="menu"><i class="fa fa-bars"></i></button>
            <h1 class="dd-page-title-h">@yield('header_title', 'Trang quản trị')</h1>
            <div class="dd-h-search">
                <i class="fa fa-search"></i>
                <input type="text" placeholder="@yield('header_search', 'Tìm kiếm...')">
            </div>
            <button class="dd-h-icon" type="button" title="Thông báo"><i class="fa fa-bell-o"></i><span class="dot">5</span></button>
            <button class="dd-h-icon" type="button" title="Tin nhắn"><i class="fa fa-comment-o"></i><span class="dot">3</span></button>
            <div class="dd-h-user">
                <div class="av">A</div>
                <div class="nm"><strong>Admin</strong><small>Quản trị viên</small></div>
                <i class="fa fa-angle-down ca"></i>
            </div>
        </header>

        <main class="dd-content">
            @yield('content')
        </main>
    </div>
</div>
</body>
</html>
