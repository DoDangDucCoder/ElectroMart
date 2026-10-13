@extends('admin_layout')

@php
    use Illuminate\Support\Str;
    $filters = $filters ?? [
        'search' => $search ?? request('search', ''),
        'status' => request('status', 'all'),
        'sort' => request('sort', 'name'),
        'per_page' => (int) request('per_page', 10),
    ];
    $stats = $stats ?? [
        'total' => $brands->total(),
        'page' => $brands->count(),
        'visible' => 0,
        'hidden' => 0,
    ];
@endphp

@section('admin_content')
    <style>
        .adm-brand-management{padding:20px 16px}
        .adm-brand-management .adm-page-head{display:flex;align-items:flex-start;justify-content:space-between;gap:16px;margin-bottom:18px}
        .adm-brand-management .adm-page-title{display:flex;align-items:flex-start;gap:14px;margin-bottom:0}
        .adm-brand-management .adm-page-title-icon{width:44px;height:44px;display:inline-flex;align-items:center;justify-content:center;border-radius:10px;background:#fff;border:1px solid var(--adm-border);color:var(--adm-blue);box-shadow:var(--adm-shadow);font-size:20px;flex:0 0 auto}
        .adm-brand-management .adm-page-title h1{margin-top:2px}
        .adm-brand-management .adm-stats{grid-template-columns:repeat(4,minmax(0,1fr));gap:16px;margin-bottom:20px}
        .adm-brand-management .adm-stat{min-height:88px;flex-direction:row;align-items:center;gap:14px;padding:16px 18px;overflow:hidden}
        .adm-brand-management .adm-stat .icon{width:48px;height:48px;border-radius:14px;font-size:21px}
        .adm-brand-management .adm-stat .num{font-size:24px}
        .adm-brand-management .adm-stat .lbl{font-size:12px}
        .adm-brand-management .adm-toolbar{display:grid;grid-template-columns:minmax(220px,1fr) 180px 100px 100px 160px;gap:12px;margin-bottom:14px;align-items:stretch;padding:16px;background:#fff;border:1px solid var(--adm-border);border-radius:var(--adm-radius);box-shadow:var(--adm-shadow)}
        .adm-brand-management .adm-search,.adm-brand-management .adm-select,.adm-brand-management .adm-btn{min-height:40px;height:40px}
        .adm-brand-management .adm-btn{justify-content:center;font-size:13px;padding-left:12px;padding-right:12px}
        .adm-brand-management .adm-table-wrap{border-radius:var(--adm-radius);overflow-x:auto}
        .adm-brand-management .adm-table-title{padding:16px 18px;border-bottom:1px solid var(--adm-border)}
        .adm-brand-management .adm-table-title strong{font-size:16px;color:var(--adm-dark)}
        .adm-brand-management .adm-table-title p{margin:4px 0 0;color:var(--adm-muted);font-size:12px}
        .adm-brand-management .adm-table{width:100%;min-width:980px;table-layout:fixed;font-size:12.5px}
        .adm-brand-management .adm-table thead th{padding:12px 10px;text-align:center!important;vertical-align:middle}
        .adm-brand-management .adm-table td{height:58px;padding:10px;text-align:center;vertical-align:middle}
        .adm-brand-management .adm-brand-name-col{width:190px}
        .adm-brand-management .adm-brand-slug-col{width:190px}
        .adm-brand-management .adm-brand-desc-col{width:260px}
        .adm-brand-management .adm-brand-status-col{width:120px}
        .adm-brand-management .adm-brand-action-col{width:130px}
        .adm-brand-management .adm-brand-name{font-weight:800;color:var(--adm-dark)}
        .adm-brand-management .adm-brand-desc-empty{color:var(--adm-muted);font-style:italic}
        .adm-brand-management .adm-actions{justify-content:center}
        .adm-brand-management .adm-actions a,.adm-brand-management .adm-actions button{width:34px;height:34px;border-radius:8px;padding:0}
        .adm-brand-management .adm-actions form{margin:0}
        @media(max-width:1500px){.adm-brand-management .adm-toolbar{grid-template-columns:minmax(200px,1fr) 160px 96px 96px 150px}}
        @media(max-width:1100px){.adm-brand-management .adm-page-head{flex-direction:column}.adm-brand-management .adm-stats,.adm-brand-management .adm-toolbar{grid-template-columns:1fr}}
    </style>

    <div class="adm-page adm-brand-management">
        <div class="adm-page-head">
            <div class="adm-page-title">
                <span class="adm-page-title-icon"><i class="fa fa-tags"></i></span>
                <div>
                    <h1>Quản lý thương hiệu</h1>
                    <div class="crumb"><a href="{{ url('/dashboard') }}">Trang chủ</a> › Sản phẩm › Thương hiệu</div>
                </div>
            </div>
        </div>

        <div class="adm-stats">
            <div class="adm-stat" data-filter-field="status" data-filter-value="all"><div class="icon blue"><i class="fa fa-tag"></i></div><div><div class="num">{{ number_format($stats['total']) }}</div><div class="lbl">Tổng thương hiệu</div></div></div>
            <div class="adm-stat"><div class="icon green"><i class="fa fa-eye"></i></div><div><div class="num">{{ number_format($stats['page']) }}</div><div class="lbl">Hiển thị trang này</div></div></div>
            <div class="adm-stat" data-filter-field="status" data-filter-value="visible"><div class="icon purple"><i class="fa fa-check-circle-o"></i></div><div><div class="num">{{ number_format($stats['visible']) }}</div><div class="lbl">Đang hiển thị</div></div></div>
            <div class="adm-stat" data-filter-field="status" data-filter-value="hidden"><div class="icon amber"><i class="fa fa-eye-slash"></i></div><div><div class="num">{{ number_format($stats['hidden']) }}</div><div class="lbl">Tạm ẩn</div></div></div>
        </div>

        <form class="adm-toolbar" method="get" action="{{ route('admin.brands.management') }}">
            <div class="adm-search">
                <i class="fa fa-search"></i>
                <input type="text" name="search" value="{{ $filters['search'] }}" placeholder="Tìm kiếm thương hiệu...">
            </div>
            <select class="adm-select" name="status">
                <option value="all" @selected($filters['status'] === 'all')>Tất cả trạng thái</option>
                <option value="visible" @selected($filters['status'] === 'visible')>Đang hoạt động</option>
                <option value="hidden" @selected($filters['status'] === 'hidden')>Tạm ẩn</option>
            </select>
            <button type="submit" class="adm-btn outline"><i class="fa fa-filter"></i> Lọc</button>
            <a href="{{ route('admin.brands.management') }}" class="adm-btn outline"><i class="fa fa-refresh"></i> Xóa lọc</a>
            <a href="{{ url('/add-brand-product') }}" class="adm-btn primary"><i class="fa fa-plus"></i> Thêm thương hiệu</a>
        </form>

        <div class="adm-table-wrap">
            <div class="adm-table-title">
                <strong>Danh sách thương hiệu</strong>
                <p>Quản lý tên, mô tả và trạng thái của các thương hiệu sản phẩm.</p>
            </div>

            @if (session('message'))
                <div class="alert alert-success" style="margin:16px">{{ session('message') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger" style="margin:16px">{{ session('error') }}</div>
            @endif

            <table class="adm-table">
                <thead>
                    <tr>
                        <th class="adm-brand-name-col">@include('admin.partials.sort_header', ['label' => 'Tên thương hiệu', 'asc' => 'name', 'desc' => 'newest', 'filters' => $filters])</th>
                        <th class="adm-brand-slug-col">Đường dẫn tĩnh (slug)</th>
                        <th class="adm-brand-desc-col">Mô tả</th>
                        <th class="adm-brand-status-col">Trạng thái</th>
                        <th class="adm-brand-action-col">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($brands as $brand)
                        <tr>
                            <td class="adm-brand-name-col"><span class="adm-brand-name">{{ $brand->brand_name }}</span></td>
                            <td class="adm-brand-slug-col">{{ $brand->brand_slug }}</td>
                            <td class="adm-brand-desc-col">
                                @if (filled($brand->brand_desc ?? null))
                                    {{ Str::limit((string) $brand->brand_desc, 120) }}
                                @else
                                    <span class="adm-brand-desc-empty">Chưa cập nhật</span>
                                @endif
                            </td>
                            <td class="adm-brand-status-col">
                                @if ($brand->brand_status === 0)
                                    <span class="adm-badge green">Đang hoạt động</span>
                                @else
                                    <span class="adm-badge gray">Tạm ẩn</span>
                                @endif
                            </td>
                            <td class="adm-brand-action-col">
                                <div class="adm-actions">
                                    @if ($brand->brand_status === 0)
                                        <form action="{{ url('/unactive-brand-product/' . $brand->brand_id) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="adm-btn outline" title="Ẩn thương hiệu"><i class="fa fa-eye-slash"></i></button>
                                        </form>
                                    @else
                                        <form action="{{ url('/active-brand-product/' . $brand->brand_id) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="adm-btn outline" title="Hiển thị thương hiệu"><i class="fa fa-eye"></i></button>
                                        </form>
                                    @endif
                                    <a href="{{ url('/edit-brand-product/' . $brand->brand_id) }}" class="adm-btn outline" title="Chỉnh sửa"><i class="fa fa-pencil"></i></a>
                                    <form action="{{ url('/delete-brand-product/' . $brand->brand_id) }}" method="POST" onsubmit="return confirm('Xác nhận xóa thương hiệu này?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="adm-btn danger" title="Xóa"><i class="fa fa-trash-o"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">
                                <div class="g-admin-empty-state">Chưa có thương hiệu nào.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            @include('admin.partials.pagination', ['paginator' => $brands, 'label' => 'thương hiệu'])
        </div>
    </div>
@endsection
