@extends('admin_layout')

@php
    use Illuminate\Support\Str;
@endphp

@section('admin_content')
    <div class="g-admin-page-shell g-admin-brand-list">
        <section class="g-admin-page-hero">
            <div>
                <span class="g-admin-page-eyebrow">Thương hiệu</span>
                <h1>Liệt kê thương hiệu</h1>
                <p>Theo dõi thương hiệu hiện có, lọc nhanh theo từ khóa và cập nhật trạng thái hiển thị.</p>
            </div>
            <div class="g-admin-page-stats">
                <article>
                    <strong>{{ number_format($brands->total()) }}</strong>
                    <span>Tổng thương hiệu</span>
                </article>
                <article>
                    <strong>{{ number_format($brands->count()) }}</strong>
                    <span>Đang hiển thị trang này</span>
                </article>
            </div>
        </section>

        <section class="g-admin-panel-card">
            <header class="g-admin-panel-head">
                <div>
                    <strong>Danh sách thương hiệu</strong>
                    <p>Quản lý tên, mô tả, thứ tự hiển thị và trạng thái của các thương hiệu sản phẩm.</p>
                </div>
                <div class="g-admin-inline-actions">
                    <a href="{{ url('/add-brand-product') }}" class="btn btn-sm btn-primary">Thêm thương hiệu</a>
                </div>
            </header>
            <div class="g-admin-panel-body">
                @if (session('message'))
                    <div class="alert alert-success">{{ session('message') }}</div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                <form class="form-inline mb-3" method="get" action="{{ url('/all-brand-product') }}">
                    <div class="form-group mr-2">
                        <input type="text" name="search" class="form-control" placeholder="Tìm kiếm"
                            value="{{ $search }}">
                    </div>
                    <button type="submit" class="btn btn-sm btn-primary mr-2">Lọc</button>
                    <a href="{{ url('/all-brand-product') }}" class="btn btn-sm btn-default">Xóa lọc</a>
                </form>

                <div class="table-responsive">
                    <table class="table table-striped b-t b-light g-admin-data-table">
                        <thead>
                            <tr>
                                <th style="width:20px;">
                                    <label class="i-checks m-b-none">
                                        <input type="checkbox"><i></i>
                                    </label>
                                </th>
                                <th>Tên thương hiệu</th>
                                <th>Slug</th>
                                <th>Mô tả</th>
                                <th>Thứ tự</th>
                                <th>Trạng thái</th>
                                <th class="text-right" style="width:120px;">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($brands as $brand)
                                <tr>
                                    <td>
                                        <label class="i-checks m-b-none">
                                            <input type="checkbox" name="brand[]"><i></i>
                                        </label>
                                    </td>
                                    <td>{{ $brand->brand_name }}</td>
                                    <td>{{ $brand->brand_slug }}</td>
                                    <td>{{ Str::limit($brand->brand_desc, 120) }}</td>
                                    <td>{{ $brand->display_order }}</td>
                                    <td>
                                        @if ($brand->brand_status === 0)
                                            <span class="label label-success">Hiển thị</span>
                                        @else
                                            <span class="label label-default">Ẩn</span>
                                        @endif
                                    </td>
                                    <td class="text-right">
                                        @if ($brand->brand_status === 0)
                                            <form action="{{ url('/unactive-brand-product/' . $brand->brand_id) }}"
                                                method="POST" style="display:inline-block;">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-xs btn-warning"
                                                    title="Ẩn thương hiệu"><i class="fa fa-eye-slash"></i></button>
                                            </form>
                                        @else
                                            <form action="{{ url('/active-brand-product/' . $brand->brand_id) }}"
                                                method="POST" style="display:inline-block;">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-xs btn-success"
                                                    title="Hiển thị thương hiệu"><i class="fa fa-eye"></i></button>
                                            </form>
                                        @endif
                                        <a href="{{ url('/edit-brand-product/' . $brand->brand_id) }}"
                                            class="btn btn-xs btn-info" title="Chỉnh sửa"><i
                                                class="fa fa-pencil-square-o"></i></a>
                                        <form action="{{ url('/delete-brand-product/' . $brand->brand_id) }}"
                                            method="POST" style="display:inline-block;"
                                            onsubmit="return confirm('Xác nhận xóa thương hiệu này?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-xs btn-danger" title="Xóa">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7">
                                        <div class="g-admin-empty-state">Chưa có thương hiệu nào.</div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="text-right">
                    {{ $brands->appends(request()->query())->links() }}
                </div>
            </div>
        </section>
    </div>
@endsection
