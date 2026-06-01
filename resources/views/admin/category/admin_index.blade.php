@extends('admin_layout')

@section('admin_content')

<div class="adm-page">
    <div class="adm-page-title">
        <h1><i class="fa fa-sitemap" style="color:var(--adm-teal);margin-right:8px"></i>Quản lý danh mục</h1>
        <div class="crumb"><a href="{{ url('/dashboard') }}">Trang chủ</a> › Quản lý danh mục</div>
    </div>

    <div class="adm-stats" style="grid-template-columns:repeat(5,1fr)">
        <div class="adm-stat">
            <div class="icon blue"><i class="fa fa-sitemap"></i></div>
            <div><div class="num">{{ number_format($stats['total']) }}</div><div class="lbl">Tổng danh mục</div></div>
        </div>
        <div class="adm-stat">
            <div class="icon green"><i class="fa fa-check-circle"></i></div>
            <div><div class="num">{{ number_format($stats['active']) }}</div><div class="lbl">Đang hoạt động</div></div>
        </div>
        <div class="adm-stat">
            <div class="icon amber"><i class="fa fa-eye-slash"></i></div>
            <div><div class="num">{{ number_format($stats['hidden']) }}</div><div class="lbl">Tạm ẩn</div></div>
        </div>
        <div class="adm-stat">
            <div class="icon teal"><i class="fa fa-folder"></i></div>
            <div><div class="num">{{ number_format($stats['parents']) }}</div><div class="lbl">Danh mục gốc</div></div>
        </div>
        <div class="adm-stat">
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
                <table class="adm-table">
                    <thead>
                        <tr>
                            <th>Mã</th>
                            <th>Danh mục</th>
                            <th>Slug</th>
                            <th>Danh mục cha</th>
                            <th>Sản phẩm</th>
                            <th>Danh mục con</th>
                            <th>Thứ tự</th>
                            <th>Trạng thái</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse ($categories as $category)
                        <tr>
                            <td class="id">DM{{ str_pad((string) $category->id, 4, '0', STR_PAD_LEFT) }}</td>
                            <td><strong>{{ $category->name }}</strong></td>
                            <td style="font-size:11px;color:var(--adm-muted)">{{ $category->slug }}</td>
                            <td>{{ $category->parent?->name ?? 'Danh mục gốc' }}</td>
                            <td>{{ number_format((int) $category->products_count) }}</td>
                            <td>{{ number_format((int) $category->children_count) }}</td>
                            <td>{{ number_format((int) ($category->display_order ?? 0)) }}</td>
                            <td>
                                @if ($category->is_active)
                                    <span class="adm-badge green">Hoạt động</span>
                                @else
                                    <span class="adm-badge gray">Tạm ẩn</span>
                                @endif
                            </td>
                            <td>
                                <div class="adm-actions" style="display:flex;gap:6px;flex-wrap:wrap">
                                    <a href="{{ url('/edit-category-product/' . $category->id) }}" class="adm-btn outline" title="Sửa danh mục">
                                        <i class="fa fa-pencil"></i> Sửa
                                    </a>
                                    @if ($category->is_active)
                                        <form action="{{ url('/unactive-category-product/' . $category->id) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="adm-btn outline" title="Tắt trạng thái danh mục">
                                                <i class="fa fa-eye-slash"></i> Tắt
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ url('/active-category-product/' . $category->id) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="adm-btn outline" title="Bật trạng thái danh mục">
                                                <i class="fa fa-eye"></i> Bật
                                            </button>
                                        </form>
                                    @endif
                                    <form action="{{ url('/delete-category-product/' . $category->id) }}"
                                        method="POST"
                                        onsubmit="return confirm('Bạn có chắc muốn xóa danh mục này? Danh mục đang có sản phẩm sẽ không được xóa.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="adm-btn danger" title="Xóa danh mục">
                                            <i class="fa fa-trash-o"></i> Xóa
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9">
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
