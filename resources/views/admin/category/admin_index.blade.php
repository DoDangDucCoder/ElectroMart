@extends('admin_layout')

@section('admin_content')
<style>
    .adm-category-management{padding:20px 16px}
    .adm-category-management .adm-layout{grid-template-columns:1fr}
    .adm-category-management .adm-page-head{display:flex;align-items:flex-start;justify-content:space-between;gap:16px;margin-bottom:18px}
    .adm-category-management .adm-page-title{display:flex;align-items:flex-start;gap:14px;margin-bottom:0}
    .adm-category-management .adm-page-title-icon{width:44px;height:44px;display:inline-flex;align-items:center;justify-content:center;border-radius:10px;background:#fff;border:1px solid var(--adm-border);color:var(--adm-blue);box-shadow:var(--adm-shadow);font-size:20px;flex:0 0 auto}
    .adm-category-management .adm-page-title h1{margin-top:2px}
    .adm-category-management .adm-stats{grid-template-columns:repeat(5,minmax(0,1fr));gap:16px;margin-bottom:20px}
    .adm-category-management .adm-stat{min-height:88px;flex-direction:row;align-items:center;gap:14px;padding:16px 18px;overflow:hidden}
    .adm-category-management .adm-stat .icon{width:48px;height:48px;border-radius:14px;font-size:21px}
    .adm-category-management .adm-stat .num{font-size:24px}
    .adm-category-management .adm-stat .lbl{font-size:12px}
    .adm-category-management .adm-toolbar{display:grid;grid-template-columns:minmax(220px,1fr) 170px 170px 100px 150px;gap:12px;margin-bottom:14px;align-items:stretch;padding:16px;background:#fff;border:1px solid var(--adm-border);border-radius:var(--adm-radius);box-shadow:var(--adm-shadow)}
    .adm-category-management .adm-search,.adm-category-management .adm-select,.adm-category-management .adm-btn{min-height:40px;height:40px}
    .adm-category-management .adm-btn{justify-content:center;font-size:13px;padding-left:12px;padding-right:12px}
    .adm-category-management .adm-table-wrap{border-radius:var(--adm-radius);overflow-x:auto}
    .adm-category-management .adm-table-title{display:flex;align-items:center;justify-content:space-between;gap:12px;padding:16px 18px;border-bottom:1px solid var(--adm-border)}
    .adm-category-management .adm-table-title strong{font-size:16px;color:var(--adm-dark)}
    .adm-category-management .adm-table-title p{margin:4px 0 0;color:var(--adm-muted);font-size:12px}
    .adm-category-management .adm-table{width:100%;min-width:1040px;table-layout:fixed;font-size:12.5px}
    .adm-category-management .adm-table thead th{padding:12px 10px;text-align:center!important;vertical-align:middle}
    .adm-category-management .adm-table td{height:70px;padding:12px 10px;text-align:center;vertical-align:middle}
    .adm-category-management .adm-category-id-col{width:90px}
    .adm-category-management .adm-category-name-col{width:190px;text-align:center!important}
    .adm-category-management .adm-category-slug-col{width:170px;color:var(--adm-muted)}
    .adm-category-management .adm-category-parent-col{width:150px}
    .adm-category-management .adm-category-small-col{width:90px}
    .adm-category-management .adm-category-status-col{width:116px}
    .adm-category-management .adm-category-action-col{width:130px}
    .adm-category-management .adm-category-name{display:block;font-weight:800;color:var(--adm-dark)}
    .adm-category-management .adm-table td.id{color:var(--adm-blue);font-weight:800;white-space:nowrap}
    .adm-category-management .adm-actions{justify-content:center}
    .adm-category-management .adm-actions a,.adm-category-management .adm-actions button{width:34px;height:34px;border-radius:8px;padding:0}
    .adm-category-management .adm-actions form{margin:0}
    @media(max-width:1500px){.adm-category-management .adm-toolbar{grid-template-columns:minmax(200px,1fr) 160px 160px 96px 140px}}
    @media(max-width:1100px){.adm-category-management .adm-page-head{flex-direction:column}.adm-category-management .adm-stats,.adm-category-management .adm-toolbar{grid-template-columns:1fr}}
</style>

<div class="adm-page adm-category-management">
    <div class="adm-page-head">
        <div class="adm-page-title">
            <span class="adm-page-title-icon"><i class="fa fa-sitemap"></i></span>
            <div>
                <h1>Quản lý danh mục</h1>
                <div class="crumb"><a href="{{ url('/dashboard') }}">Trang chủ</a> › Sản phẩm › Quản lý danh mục</div>
            </div>
        </div>
    </div>

    <div class="adm-stats">
        <div class="adm-stat" data-filter-field="status" data-filter-value="all">
            <div class="icon blue"><i class="fa fa-sitemap"></i></div>
            <div><div class="num">{{ number_format($stats['total']) }}</div><div class="lbl">Tổng danh mục</div></div>
        </div>
        <div class="adm-stat" data-filter-field="status" data-filter-value="active">
            <div class="icon green"><i class="fa fa-check-circle"></i></div>
            <div><div class="num">{{ number_format($stats['active']) }}</div><div class="lbl">Đang hoạt động</div></div>
        </div>
        <div class="adm-stat" data-filter-field="status" data-filter-value="hidden">
            <div class="icon amber"><i class="fa fa-eye-slash"></i></div>
            <div><div class="num">{{ number_format($stats['hidden']) }}</div><div class="lbl">Tạm ẩn</div></div>
        </div>
        <div class="adm-stat" data-filter-field="level" data-filter-value="parent">
            <div class="icon teal"><i class="fa fa-folder"></i></div>
            <div><div class="num">{{ number_format($stats['parents']) }}</div><div class="lbl">Danh mục gốc</div></div>
        </div>
        <div class="adm-stat" data-filter-field="level" data-filter-value="child">
            <div class="icon blue"><i class="fa fa-folder-open"></i></div>
            <div><div class="num">{{ number_format($stats['children']) }}</div><div class="lbl">Danh mục con</div></div>
        </div>
    </div>

    <div class="adm-layout">
        <div>
            <form class="adm-toolbar" method="GET" action="{{ route('admin.categories.management') }}">
                <div class="adm-search">
                    <i class="fa fa-search"></i>
                    <input type="text" name="q" value="{{ $filters['q'] }}" placeholder="Tìm theo tên hoặc slug danh mục...">
                </div>
                <select class="adm-select" name="status">
                    <option value="all" @selected($filters['status'] === 'all')>Tất cả trạng thái</option>
                    <option value="active" @selected($filters['status'] === 'active')>Đang hoạt động</option>
                    <option value="hidden" @selected($filters['status'] === 'hidden')>Tạm ẩn</option>
                </select>
                <select class="adm-select" name="level">
                    <option value="all" @selected($filters['level'] === 'all')>Tất cả cấp</option>
                    <option value="parent" @selected($filters['level'] === 'parent')>Danh mục gốc</option>
                    <option value="child" @selected($filters['level'] === 'child')>Danh mục con</option>
                </select>
                <button class="adm-btn outline" type="submit"><i class="fa fa-filter"></i> Lọc</button>
                <a href="{{ url('/add-category-product') }}" class="adm-btn primary"><i class="fa fa-plus"></i> Thêm danh mục</a>
            </form>

            <div class="adm-table-wrap">
                <div class="adm-table-title">
                    <div>
                        <strong>Danh sách danh mục</strong>
                        <p>Quản lý và theo dõi tất cả danh mục sản phẩm trong hệ thống.</p>
                    </div>
                </div>
                <table class="adm-table">
                    <thead>
                        <tr>
                            <th class="adm-category-id-col">@include('admin.partials.sort_header', ['label' => 'Mã danh mục', 'asc' => 'oldest', 'desc' => 'newest', 'filters' => $filters])</th>
                            <th class="adm-category-name-col">@include('admin.partials.sort_header', ['label' => 'Tên danh mục', 'asc' => 'name_asc', 'desc' => 'name_desc', 'filters' => $filters])</th>
                            <th class="adm-category-slug-col">Slug</th>
                            <th class="adm-category-parent-col">Danh mục cha</th>
                            <th class="adm-category-small-col">@include('admin.partials.sort_header', ['label' => 'Sản phẩm', 'asc' => 'products_asc', 'desc' => 'products_desc', 'filters' => $filters])</th>
                            <th class="adm-category-small-col">Danh mục con</th>
                            <th class="adm-category-status-col">Trạng thái</th>
                            <th class="adm-category-action-col">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse ($categories as $category)
                        <tr>
                            <td class="id adm-category-id-col">DM{{ str_pad((string) $category->id, 4, '0', STR_PAD_LEFT) }}</td>
                            <td class="adm-category-name-col"><span class="adm-category-name">{{ $category->name }}</span></td>
                            <td class="adm-category-slug-col">{{ $category->slug }}</td>
                            <td class="adm-category-parent-col">{{ $category->parent?->name ?? 'Danh mục gốc' }}</td>
                            <td class="adm-category-small-col">{{ number_format((int) $category->products_count) }}</td>
                            <td class="adm-category-small-col">{{ number_format((int) $category->children_count) }}</td>
                            <td class="adm-category-status-col">
                                @if ($category->is_active)
                                    <span class="adm-badge green">Đang hoạt động</span>
                                @else
                                    <span class="adm-badge gray">Tạm ẩn</span>
                                @endif
                            </td>
                            <td class="adm-category-action-col">
                                <div class="adm-actions">
                                    <a href="{{ url('/edit-category-product/' . $category->id) }}" class="adm-btn outline" title="Sửa danh mục">
                                        <i class="fa fa-pencil"></i>
                                    </a>
                                    @if ($category->is_active)
                                        <form action="{{ url('/unactive-category-product/' . $category->id) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="adm-btn outline" title="Tắt trạng thái danh mục">
                                                <i class="fa fa-eye-slash"></i>
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ url('/active-category-product/' . $category->id) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="adm-btn outline" title="Bật trạng thái danh mục">
                                                <i class="fa fa-eye"></i>
                                            </button>
                                        </form>
                                    @endif
                                    <form action="{{ url('/delete-category-product/' . $category->id) }}"
                                        method="POST"
                                        onsubmit="return confirm('Bạn có chắc muốn xóa danh mục này? Danh mục đang có sản phẩm sẽ không được xóa.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="adm-btn danger" title="Xóa danh mục">
                                            <i class="fa fa-trash-o"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">
                                <div class="g-admin-empty-state">Chưa có danh mục nào phù hợp với bộ lọc hiện tại.</div>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
                @include('admin.partials.pagination', ['paginator' => $categories, 'label' => 'danh mục'])
            </div>
        </div>
    </div>
</div>
@endsection
