@php
    use Illuminate\Support\Str;

    $adminUser = auth('web')->user();
    $adminName = trim((string) ($adminUser?->name ?: 'Admin'));
    $adminInitials = (string) Str::of($adminName)
        ->explode(' ')
        ->filter()
        ->take(2)
        ->map(fn (string $part) => Str::upper(Str::substr($part, 0, 1)))
        ->implode('');
    if ($adminInitials === '') { $adminInitials = 'AD'; }

    $brandName = config('app.brand_admin_name', 'dangduc');

    $f = $filters ?? ['q' => '', 'status' => 'all', 'level' => 'all', 'menu' => 'all', 'per_page' => 10];

    $renderTree = function (array $nodes, int $depth = 0) use (&$renderTree) {
        foreach ($nodes as $node) {
            $hasChildren = ! empty($node['children']);
            $openClass = $depth < 1 ? ' is-open' : '';
            echo '<div class="acp-tree-node' . $openClass . '">';
            echo '<div class="acp-tree-row" data-cat-id="' . $node['id'] . '" data-cat-name="' . e($node['name']) . '" data-cat-slug="' . e($node['slug']) . '" data-cat-active="' . ($node['is_active'] ? 1 : 0) . '" data-cat-count="' . (int) $node['product_count'] . '" data-cat-thumb="' . e($node['thumbnail'] ?? '') . '" data-cat-depth="' . ($depth + 1) . '">';
            if ($hasChildren) {
                echo '<span class="acp-tree-toggle"><i class="fa fa-chevron-down"></i></span>';
            } else {
                echo '<span class="acp-tree-toggle is-empty"><i class="fa fa-chevron-down"></i></span>';
            }
            echo '<i class="acp-tree-icon fa ' . ($hasChildren ? 'fa-folder-open-o' : 'fa-folder-o') . '"></i>';
            echo '<span class="acp-tree-name">' . e($node['name']) . '</span>';
            echo '<span class="acp-tree-count">(' . number_format($node['product_count']) . ')</span>';
            echo '</div>';
            if ($hasChildren) {
                echo '<div class="acp-tree-children">';
                $renderTree($node['children'], $depth + 1);
                echo '</div>';
            }
            echo '</div>';
        }
    };
@endphp
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Quản lý danh mục sản phẩm</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="{{ asset('backend/css/font-awesome.css') }}" rel="stylesheet">
    <link href="{{ asset('backend/css/admin-category-page.css') }}?v=20260501" rel="stylesheet">
</head>
<body class="admin-cat-body">
<div class="acp-app" data-app>

    {{-- ============================ SIDEBAR ============================ --}}
    <aside class="acp-sidebar">
        <div class="acp-brand">
            <span class="acp-brand-logo"><i class="fa fa-shopping-bag"></i></span>
            <div class="acp-brand-text">
                <div class="acp-brand-name">{{ $brandName }}</div>
                <div class="acp-brand-role">ADMIN</div>
            </div>
        </div>

        <nav class="acp-nav">
            <ul>
                <li class="acp-nav-item">
                    <a class="acp-nav-link" href="{{ url('/dashboard') }}">
                        <i class="fa fa-th-large"></i><span>Tổng quan</span>
                    </a>
                </li>
                <li class="acp-nav-item">
                    <a class="acp-nav-link" href="{{ url('/admin/orders-management') }}">
                        <i class="fa fa-shopping-bag"></i><span>Đơn hàng</span>
                        <span class="acp-badge">12</span>
                    </a>
                </li>
                <li class="acp-nav-item is-open">
                    <button type="button" class="acp-nav-link" data-toggle-sub>
                        <i class="fa fa-cube"></i><span>Sản phẩm</span>
                        <i class="fa fa-chevron-down acp-caret"></i>
                    </button>
                    <ul class="acp-subnav">
                        <li class="acp-nav-item">
                            <a class="acp-nav-link is-active" href="{{ url('/admin/categories-management') }}" aria-current="page">
                                <i class="fa fa-folder"></i><span>Danh mục</span>
                            </a>
                        </li>
                        <li class="acp-nav-item">
                            <a class="acp-nav-link" href="{{ url('/admin/products-management') }}">
                                <i class="fa fa-cubes"></i><span>Tất cả sản phẩm</span>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="acp-nav-item">
                    <a class="acp-nav-link" href="{{ url('/users') }}">
                        <i class="fa fa-user-o"></i><span>Khách hàng</span>
                    </a>
                </li>
                <li class="acp-nav-item">
                    <a class="acp-nav-link" href="{{ url('/all-post') }}">
                        <i class="fa fa-file-text-o"></i><span>Bài viết</span>
                    </a>
                </li>
                <li class="acp-nav-item">
                    <a class="acp-nav-link" href="{{ url('/manage-slider') }}">
                        <i class="fa fa-image"></i><span>Banner</span>
                    </a>
                </li>
                <li class="acp-nav-item">
                    <a class="acp-nav-link" href="{{ url('/list-coupon') }}">
                        <i class="fa fa-percent"></i><span>Khuyến mãi</span>
                    </a>
                </li>
                <li class="acp-nav-item">
                    <a class="acp-nav-link" href="{{ url('/comment') }}">
                        <i class="fa fa-star-o"></i><span>Đánh giá</span>
                    </a>
                </li>
                <li class="acp-nav-item">
                    <a class="acp-nav-link" href="#">
                        <i class="fa fa-archive"></i><span>Kho hàng</span>
                    </a>
                </li>
                <li class="acp-nav-item">
                    <a class="acp-nav-link" href="{{ url('/delivery') }}">
                        <i class="fa fa-truck"></i><span>Giao hàng</span>
                    </a>
                </li>
                <li class="acp-nav-item">
                    <a class="acp-nav-link" href="#">
                        <i class="fa fa-credit-card"></i><span>Thanh toán</span>
                    </a>
                </li>
                <li class="acp-nav-item">
                    <a class="acp-nav-link" href="{{ url('/product-profit') }}">
                        <i class="fa fa-bar-chart"></i><span>Báo cáo</span>
                    </a>
                </li>
                <li class="acp-nav-item">
                    <a class="acp-nav-link" href="{{ url('/information') }}">
                        <i class="fa fa-cog"></i><span>Cấu hình</span>
                    </a>
                </li>
                <li class="acp-nav-item">
                    <a class="acp-nav-link" href="{{ url('/index-spatie') }}">
                        <i class="fa fa-user-secret"></i><span>Quản trị viên</span>
                    </a>
                </li>
                <li class="acp-nav-item">
                    <a class="acp-nav-link" href="#">
                        <i class="fa fa-history"></i><span>Nhật ký hoạt động</span>
                    </a>
                </li>
            </ul>
        </nav>

        <div class="acp-sidebar-foot">
            <button type="button" class="acp-collapse-btn">
                <i class="fa fa-angle-double-left"></i><span>Thu gọn</span>
            </button>
        </div>
    </aside>

    {{-- ============================ MAIN ============================ --}}
    <div class="acp-main">

        {{-- Topbar --}}
        <header class="acp-topbar">
            <button type="button" class="acp-topbar-toggle" aria-label="Mở menu" data-sidebar-toggle>
                <i class="fa fa-bars"></i>
            </button>
            <h1 class="acp-topbar-title">Quản lý danh mục sản phẩm</h1>

            <div class="acp-topbar-spacer"></div>

            <form class="acp-topbar-search" method="GET" action="{{ url('/admin/categories-management') }}" role="search">
                <i class="fa fa-search"></i>
                <input type="search" name="q" value="{{ $f['q'] }}" placeholder="Tìm kiếm danh mục, slug...">
            </form>

            <div class="acp-topbar-actions">
                <button type="button" class="acp-bell" aria-label="Thông báo">
                    <i class="fa fa-bell-o"></i>
                    <span class="acp-badge-count">5</span>
                </button>
                <div class="acp-userchip">
                    <span class="acp-userchip-avatar">{{ $adminInitials }}</span>
                    <div class="acp-userchip-text">
                        <div class="acp-userchip-name">{{ $adminName ?: 'Admin' }}</div>
                        <div class="acp-userchip-role">Quản trị viên</div>
                    </div>
                </div>
            </div>
        </header>

        {{-- Page body --}}
        <main class="acp-page">

            {{-- Breadcrumb + actions --}}
            <div class="acp-breadcrumb-row">
                <nav class="acp-breadcrumb" aria-label="Breadcrumb">
                    <a href="{{ url('/dashboard') }}">Trang chủ</a>
                    <i class="fa fa-angle-right acp-bc-sep"></i>
                    <a href="#">Danh mục</a>
                    <i class="fa fa-angle-right acp-bc-sep"></i>
                    <span class="acp-bc-current">Tất cả danh mục</span>
                </nav>
                <div class="acp-page-actions">
                    <button type="button" class="acp-btn acp-btn-default">
                        <i class="fa fa-download"></i><span>Xuất file</span>
                    </button>
                    <a href="{{ url('/add-category-product') }}" class="acp-btn acp-btn-primary">
                        <i class="fa fa-plus"></i><span>Thêm danh mục</span>
                    </a>
                </div>
            </div>

            {{-- Stats --}}
            <section class="acp-stats">
                <article class="acp-stat-card">
                    <div>
                        <div class="acp-stat-label">Tổng danh mục</div>
                        <div class="acp-stat-value">{{ number_format($stats['total']) }}</div>
                        <div class="acp-stat-meta">100% tổng số</div>
                    </div>
                    <span class="acp-stat-icon is-blue"><i class="fa fa-folder"></i></span>
                </article>
                <article class="acp-stat-card">
                    <div>
                        <div class="acp-stat-label">Đang hiển thị</div>
                        <div class="acp-stat-value">{{ number_format($stats['active']) }}</div>
                        <div class="acp-stat-meta">{{ rtrim(rtrim(number_format($stats['active_pct'], 1), '0'), '.') }}% tổng số</div>
                    </div>
                    <span class="acp-stat-icon is-green"><i class="fa fa-eye"></i></span>
                </article>
                <article class="acp-stat-card">
                    <div>
                        <div class="acp-stat-label">Danh mục cha</div>
                        <div class="acp-stat-value">{{ number_format($stats['parents']) }}</div>
                        <div class="acp-stat-meta">{{ rtrim(rtrim(number_format($stats['parent_pct'], 1), '0'), '.') }}% tổng số</div>
                    </div>
                    <span class="acp-stat-icon is-purple"><i class="fa fa-sitemap"></i></span>
                </article>
                <article class="acp-stat-card">
                    <div>
                        <div class="acp-stat-label">Danh mục con</div>
                        <div class="acp-stat-value">{{ number_format($stats['children']) }}</div>
                        <div class="acp-stat-meta">{{ rtrim(rtrim(number_format($stats['children_pct'], 1), '0'), '.') }}% tổng số</div>
                    </div>
                    <span class="acp-stat-icon is-orange"><i class="fa fa-sitemap"></i></span>
                </article>
                <article class="acp-stat-card">
                    <div>
                        <div class="acp-stat-label">Đã ẩn</div>
                        <div class="acp-stat-value">{{ number_format($stats['hidden']) }}</div>
                        <div class="acp-stat-meta">{{ rtrim(rtrim(number_format($stats['hidden_pct'], 1), '0'), '.') }}% tổng số</div>
                    </div>
                    <span class="acp-stat-icon is-red"><i class="fa fa-eye-slash"></i></span>
                </article>
            </section>

            {{-- Filter card --}}
            <form method="GET" action="{{ url('/admin/categories-management') }}" class="acp-filter-card">
                <div class="acp-filter-grid">
                    <div class="acp-field">
                        <div class="acp-input-with-icon">
                            <i class="fa fa-search"></i>
                            <input type="text" name="q" value="{{ $f['q'] }}" class="acp-input" placeholder="Tìm kiếm tên danh mục, slug...">
                        </div>
                    </div>
                    <div class="acp-field">
                        <label class="acp-field-label">Trạng thái</label>
                        <select name="status" class="acp-select">
                            <option value="all"    @selected($f['status']==='all')>Tất cả</option>
                            <option value="active" @selected($f['status']==='active')>Đang hiển thị</option>
                            <option value="hidden" @selected($f['status']==='hidden')>Đã ẩn</option>
                        </select>
                    </div>
                    <div class="acp-field">
                        <label class="acp-field-label">Cấp danh mục</label>
                        <select name="level" class="acp-select">
                            <option value="all"    @selected($f['level']==='all')>Tất cả</option>
                            <option value="parent" @selected($f['level']==='parent')>Danh mục cha</option>
                            <option value="child"  @selected($f['level']==='child')>Danh mục con</option>
                        </select>
                    </div>
                    <div class="acp-field">
                        <label class="acp-field-label">Hiển thị trong menu</label>
                        <select name="menu" class="acp-select">
                            <option value="all"  @selected($f['menu']==='all')>Tất cả</option>
                            <option value="yes"  @selected($f['menu']==='yes')>Đang hiển thị</option>
                            <option value="no"   @selected($f['menu']==='no')>Đang ẩn</option>
                        </select>
                    </div>
                    <button type="submit" class="acp-btn acp-btn-primary">
                        <i class="fa fa-filter"></i><span>Bộ lọc</span>
                    </button>
                    <a href="{{ url('/admin/categories-management') }}" class="acp-btn acp-btn-default">Đặt lại</a>
                </div>
            </form>

            {{-- Main grid --}}
            <div class="acp-main-grid">

                {{-- Table card --}}
                <section class="acp-card">
                    <header class="acp-card-head">
                        <h2 class="acp-card-title">Danh sách danh mục</h2>
                        <div class="acp-card-head-meta">
                            <span data-selected-counter>Đã chọn 0 mục</span>
                            <button type="button" class="acp-link-danger" data-bulk-delete>
                                <i class="fa fa-trash-o"></i> Xóa
                            </button>
                        </div>
                    </header>

                    <div class="acp-table-wrap">
                        <table class="acp-table">
                            <thead>
                                <tr>
                                    <th class="acp-th-check">
                                        <input type="checkbox" class="acp-checkbox" data-check-all>
                                    </th>
                                    <th>Danh mục</th>
                                    <th>Slug</th>
                                    <th class="acp-num">Cấp</th>
                                    <th class="acp-num">Số sản phẩm</th>
                                    <th>Hiển thị menu</th>
                                    <th>Trạng thái</th>
                                    <th>Cập nhật</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($categories as $c)
                                    <tr>
                                        <td class="acp-td-check">
                                            <input type="checkbox" class="acp-checkbox" data-row-check value="{{ $c->id }}">
                                        </td>
                                        <td>
                                            <div class="acp-cat-cell {{ $c->level >= 3 ? 'is-l3' : ($c->level === 2 ? 'is-l2' : '') }}">
                                                <span class="acp-cat-thumb">
                                                    @if ($c->thumbnail)
                                                        <img src="{{ asset('uploads/' . $c->thumbnail) }}" alt="{{ $c->name }}">
                                                    @else
                                                        <i class="fa fa-{{ $c->level > 1 ? 'folder-o' : 'cube' }}"></i>
                                                    @endif
                                                </span>
                                                <span class="acp-cat-name">{{ $c->name }}</span>
                                            </div>
                                        </td>
                                        <td><span class="acp-slug">{{ $c->slug }}</span></td>
                                        <td class="acp-num">{{ $c->level }}</td>
                                        <td class="acp-num">{{ number_format($c->product_count) }}</td>
                                        <td>
                                            <label class="acp-switch">
                                                <input type="checkbox" {{ $c->is_menu ? 'checked' : '' }} data-menu-toggle data-id="{{ $c->id }}">
                                                <span class="acp-switch-slider"></span>
                                            </label>
                                        </td>
                                        <td>
                                            @if (! $c->is_active)
                                                <span class="acp-pill acp-pill-hidden">Đã ẩn</span>
                                            @elseif ($c->is_featured)
                                                <span class="acp-pill acp-pill-featured">Nổi bật</span>
                                            @else
                                                <span class="acp-pill acp-pill-success">Đang hiển thị</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="acp-date-line">
                                                <div class="acp-date-day">{{ optional($c->updated_at)->format('d/m/Y') ?? '—' }}</div>
                                                <div class="acp-date-time">{{ optional($c->updated_at)->format('H:i') }}</div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="acp-actions">
                                                <a class="acp-action-btn" href="#" title="Xem"><i class="fa fa-eye"></i></a>
                                                <a class="acp-action-btn is-edit" href="{{ url('/edit-category-product/' . $c->id) }}" title="Sửa"><i class="fa fa-pencil"></i></a>
                                                <a class="acp-action-btn is-del" href="{{ url('/delete-category-product/' . $c->id) }}" title="Xóa" onclick="return confirm('Bạn có chắc muốn xóa danh mục này?');"><i class="fa fa-trash-o"></i></a>
                                                <button class="acp-action-btn" type="button" title="Thêm thao tác"><i class="fa fa-ellipsis-v"></i></button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" style="text-align:center; padding:48px 16px; color:#94a3b8;">
                                            Không có danh mục nào khớp với bộ lọc.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Footer / Pagination --}}
                    @php
                        $from = $categories->firstItem() ?? 0;
                        $to = $categories->lastItem() ?? 0;
                        $total = $categories->total();
                        $current = $categories->currentPage();
                        $last = $categories->lastPage();

                        $pages = [];
                        if ($last <= 7) {
                            for ($i = 1; $i <= $last; $i++) { $pages[] = $i; }
                        } else {
                            $pages[] = 1;
                            if ($current > 3) { $pages[] = '...'; }
                            for ($i = max(2, $current - 1); $i <= min($last - 1, $current + 1); $i++) { $pages[] = $i; }
                            if ($current < $last - 2) { $pages[] = '...'; }
                            $pages[] = $last;
                        }
                    @endphp
                    <footer class="acp-card-foot">
                        <div class="acp-foot-info">
                            Hiển thị {{ $from }} đến {{ $to }} của {{ number_format($total) }} danh mục
                        </div>
                        <div class="acp-pagination">
                            <a class="acp-page-btn {{ $categories->onFirstPage() ? 'is-disabled' : '' }}" href="{{ $categories->previousPageUrl() ?? '#' }}" aria-label="Trước">
                                <i class="fa fa-angle-left"></i>
                            </a>
                            @foreach ($pages as $p)
                                @if ($p === '...')
                                    <span class="acp-page-ellipsis">…</span>
                                @else
                                    <a class="acp-page-btn {{ $p == $current ? 'is-active' : '' }}" href="{{ $categories->url($p) }}">{{ $p }}</a>
                                @endif
                            @endforeach
                            <a class="acp-page-btn {{ $categories->hasMorePages() ? '' : 'is-disabled' }}" href="{{ $categories->nextPageUrl() ?? '#' }}" aria-label="Sau">
                                <i class="fa fa-angle-right"></i>
                            </a>
                        </div>
                        <div class="acp-perpage">
                            <select onchange="window.location.href=this.value">
                                @foreach ([10, 20, 50] as $pp)
                                    <option value="{{ $categories->url(1) . (str_contains($categories->url(1),'?')?'&':'?') . 'per_page=' . $pp }}" {{ $f['per_page']==$pp ? 'selected':'' }}>{{ $pp }} / trang</option>
                                @endforeach
                            </select>
                        </div>
                    </footer>
                </section>

                {{-- Right column --}}
                <aside class="acp-aside">

                    {{-- Tree --}}
                    <section class="acp-card acp-tree-card">
                        <header class="acp-card-head">
                            <h2 class="acp-card-title">Cấu trúc danh mục</h2>
                            <button type="button" class="acp-btn acp-btn-ghost" data-tree-collapse>Thu gọn</button>
                        </header>
                        <div class="acp-tree" data-tree>
                            @php $renderTree($tree, 0); @endphp
                        </div>
                        <div class="acp-tree-foot">Tổng số: {{ number_format($stats['total']) }} danh mục</div>
                    </section>

                    {{-- Quick actions --}}
                    <section class="acp-card acp-quick-card">
                        <header class="acp-card-head">
                            <h2 class="acp-card-title">Thao tác nhanh</h2>
                        </header>
                        <div class="acp-quick-list">
                            <a class="acp-quick-btn" href="{{ url('/add-category-product') }}">
                                <i class="fa fa-plus"></i><span>Thêm danh mục cha</span>
                            </a>
                            <a class="acp-quick-btn" href="{{ url('/add-category-product') }}?type=child">
                                <i class="fa fa-plus-square-o"></i><span>Thêm danh mục con</span>
                            </a>
                            <button type="button" class="acp-quick-btn">
                                <i class="fa fa-sort-amount-asc"></i><span>Sắp xếp lại danh mục</span>
                            </button>
                            <button type="button" class="acp-quick-btn">
                                <i class="fa fa-upload"></i><span>Nhập dữ liệu</span>
                            </button>
                        </div>
                    </section>

                    {{-- Selected detail --}}
                    <section class="acp-card" id="acpDetailCard" hidden>
                        <header class="acp-card-head">
                            <h2 class="acp-card-title">Chi tiết danh mục đã chọn</h2>
                        </header>
                        <div class="acp-detail-body">
                            <div class="acp-detail-row">
                                <span class="acp-detail-thumb" data-detail-thumb><i class="fa fa-cube"></i></span>
                                <div style="flex:1; min-width:0;">
                                    <div class="acp-detail-title-row">
                                        <h3 class="acp-detail-title" data-detail-name>—</h3>
                                        <span class="acp-pill acp-pill-success" data-detail-pill>Đang hiển thị</span>
                                    </div>
                                </div>
                            </div>
                            <dl class="acp-detail-meta">
                                <dt>Slug:</dt><dd data-detail-slug>—</dd>
                                <dt>Đường dẫn:</dt><dd data-detail-path>—</dd>
                                <dt>Mô tả ngắn:</dt><dd data-detail-desc>—</dd>
                            </dl>
                        </div>
                        <div class="acp-detail-foot">
                            <span>Cấp: <strong data-detail-depth>—</strong></span>
                            <span>Số sản phẩm: <strong data-detail-count>—</strong></span>
                            <span class="acp-pill acp-pill-featured" data-detail-featured hidden>Nổi bật</span>
                        </div>
                    </section>

                </aside>
            </div>

        </main>
    </div>
</div>

<script>
(function () {
    'use strict';

    // Sub-menu toggle in sidebar
    document.querySelectorAll('[data-toggle-sub]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var li = btn.closest('.acp-nav-item');
            if (li) li.classList.toggle('is-open');
        });
    });

    // Sidebar mobile toggle
    var app = document.querySelector('[data-app]');
    document.querySelectorAll('[data-sidebar-toggle]').forEach(function (b) {
        b.addEventListener('click', function () {
            if (app) app.classList.toggle('is-sidebar-open');
        });
    });

    // Tree expand/collapse
    document.querySelectorAll('.acp-tree-row').forEach(function (row) {
        row.addEventListener('click', function (ev) {
            var node = row.parentElement;
            var toggle = row.querySelector('.acp-tree-toggle');
            var hasChildren = toggle && !toggle.classList.contains('is-empty');
            if (hasChildren && (ev.target.closest('.acp-tree-toggle') || ev.target === row)) {
                node.classList.toggle('is-open');
            }
            updateDetail(row.dataset);
        });
    });

    var collapseBtn = document.querySelector('[data-tree-collapse]');
    if (collapseBtn) {
        collapseBtn.addEventListener('click', function () {
            document.querySelectorAll('.acp-tree-node.is-open').forEach(function (n) {
                n.classList.remove('is-open');
            });
        });
    }

    // Detail panel update
    var detailCard = document.getElementById('acpDetailCard');

    function updateDetail(d) {
        if (!detailCard || !d || !d.catId) return;
        detailCard.hidden = false;

        var name  = detailCard.querySelector('[data-detail-name]');
        var slug  = detailCard.querySelector('[data-detail-slug]');
        var path  = detailCard.querySelector('[data-detail-path]');
        var pill  = detailCard.querySelector('[data-detail-pill]');
        var depth = detailCard.querySelector('[data-detail-depth]');
        var count = detailCard.querySelector('[data-detail-count]');
        var thumb = detailCard.querySelector('[data-detail-thumb]');

        if (name)  name.textContent  = d.catName || '—';
        if (slug)  slug.textContent  = d.catSlug || '—';
        if (path)  path.textContent  = '/' + (d.catSlug || '');
        if (depth) depth.textContent = d.catDepth || '—';
        if (count) count.textContent = (d.catCount || '0');
        if (pill) {
            var active = d.catActive === '1';
            pill.className = 'acp-pill ' + (active ? 'acp-pill-success' : 'acp-pill-hidden');
            pill.textContent = active ? 'Đang hiển thị' : 'Đã ẩn';
        }
        if (thumb) {
            thumb.innerHTML = d.catThumb
                ? '<img src="' + d.catThumb + '" alt="">'
                : '<i class="fa fa-cube"></i>';
        }
    }

    // Bulk select
    var checkAll = document.querySelector('[data-check-all]');
    var rowChecks = document.querySelectorAll('[data-row-check]');
    var counter = document.querySelector('[data-selected-counter]');

    function refreshCounter() {
        if (!counter) return;
        var n = document.querySelectorAll('[data-row-check]:checked').length;
        counter.textContent = 'Đã chọn ' + n + ' mục';
    }
    if (checkAll) {
        checkAll.addEventListener('change', function () {
            rowChecks.forEach(function (c) { c.checked = checkAll.checked; });
            refreshCounter();
        });
    }
    rowChecks.forEach(function (c) {
        c.addEventListener('change', refreshCounter);
    });

    // Auto-open first tree node and trigger detail
    var firstRow = document.querySelector('.acp-tree-node.is-open .acp-tree-children .acp-tree-row');
    if (firstRow) updateDetail(firstRow.dataset);
})();
</script>
</body>
</html>
