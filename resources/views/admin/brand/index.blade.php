@extends('admin_layout')

@php
    use Illuminate\Support\Str;
@endphp

@section('admin_content')
    <div class="table-agile-info">
        <div class="panel panel-default">
            <div class="panel-heading d-flex justify-content-between align-items-center">
                <span>Liệt kê thương hiệu</span>
                <a href="{{ url('/add-brand-product') }}" class="btn btn-sm btn-primary">Thêm thương hiệu</a>
            </div>
            <div class="panel-body">
                @if (session('message'))
                    <div class="alert alert-success">{{ session('message') }}</div>
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
                    <table class="table table-striped b-t b-light">
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
                                            <a href="{{ url('/unactive-brand-product/' . $brand->brand_id) }}"
                                                class="btn btn-xs btn-warning"
                                                title="Ẩn thương hiệu"><i class="fa fa-eye-slash"></i></a>
                                        @else
                                            <a href="{{ url('/active-brand-product/' . $brand->brand_id) }}"
                                                class="btn btn-xs btn-success"
                                                title="Hiển thị thương hiệu"><i class="fa fa-eye"></i></a>
                                        @endif
                                        <a href="{{ url('/edit-brand-product/' . $brand->brand_id) }}"
                                            class="btn btn-xs btn-info" title="Chỉnh sửa"><i
                                                class="fa fa-pencil-square-o"></i></a>
                                        <a href="{{ url('/delete-brand-product/' . $brand->brand_id) }}"
                                            class="btn btn-xs btn-danger"
                                            onclick="return confirm('Xác nhận xóa thương hiệu này?')" title="Xóa">
                                            <i class="fa fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted">Chưa có thương hiệu nào.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="text-right">
                    {{ $brands->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
