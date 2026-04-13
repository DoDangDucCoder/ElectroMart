@extends('admin_layout')

@section('admin_content')
    @php
        $registeredUris = collect(app('router')->getRoutes()->getRoutes())->map(fn ($route) => $route->uri());
        $hasCategoryAddRoute = $registeredUris->contains('add-category-product');
        $hasCategoryEditRoute = $registeredUris->contains(fn ($uri) => str_starts_with($uri, 'edit-category-product/'));
        $hasCategoryDeleteRoute = $registeredUris->contains(fn ($uri) => str_starts_with($uri, 'delete-category-product/'));
        $hasCategoryToggleRoute = $registeredUris->contains(fn ($uri) => str_starts_with($uri, 'active-category-product/'))
            && $registeredUris->contains(fn ($uri) => str_starts_with($uri, 'unactive-category-product/'));
        $categoryLegacyActionsReady = $hasCategoryAddRoute && $hasCategoryEditRoute && $hasCategoryDeleteRoute && $hasCategoryToggleRoute;
    @endphp

    <div class="g-admin-page-shell g-admin-category-product-list">
        <section class="g-admin-page-hero">
            <div>
                <span class="g-admin-page-eyebrow">Danh mục sản phẩm</span>
                <h1>Liệt kê danh mục sản phẩm</h1>
                <p>Theo dõi cây danh mục, trạng thái hiển thị và thứ tự sắp xếp của các nhóm sản phẩm.</p>
            </div>
            <div class="g-admin-page-stats">
                <article>
                    <strong>{{ number_format($all_category_product->total()) }}</strong>
                    <span>Tổng danh mục</span>
                </article>
                <article>
                    <strong>{{ number_format($all_category_product->count()) }}</strong>
                    <span>Đang hiển thị trang này</span>
                </article>
            </div>
        </section>

        <section class="g-admin-panel-card">
            <header class="g-admin-panel-head">
                <div>
                    <strong>Danh sách danh mục</strong>
                    <p>Quản lý trạng thái hiển thị, vị trí sắp xếp và thao tác import hoặc export dữ liệu khi cần.</p>
                </div>
                <div class="g-admin-inline-actions">
                    <a href="{{ url('/add-category-product') }}" class="btn btn-info">Thêm danh mục</a>
                </div>
            </header>
            <div class="g-admin-panel-body">
                @if (session('message'))
                    <div class="alert alert-success">{{ session('message') }}</div>
                    {{ Session::put('message', null) }}
                @endif
                <div id="category_sort_notice" aria-live="polite"></div>

                @unless ($categoryLegacyActionsReady)
                    <div class="alert alert-warning">
                        Tác vụ quản trị danh mục sản phẩm legacy hiện chưa được nối route trong bản này. Màn hình đang ở chế độ chỉ xem để tránh dẫn tới lỗi 404, nên kéo thả sắp xếp cũng đang được tắt.
                    </div>
                @endunless

                <div class="g-admin-toolbar">
                    <div class="form-inline">
                        <div class="form-group">
                            <select class="input-sm form-control w-sm inline v-middle">
                                <option value="0">Chọn thao tác</option>
                                <option value="1">Xóa mục đã chọn</option>
                                <option value="2">Chỉnh sửa hàng loạt</option>
                                <option value="3">Xuất dữ liệu</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <button class="btn btn-sm btn-default">Áp dụng</button>
                        </div>
                    </div>
                    <div class="form-inline">
                        <div class="form-group">
                            <input type="text" class="input-sm form-control" placeholder="Tìm kiếm">
                        </div>
                        <div class="form-group">
                            <button class="btn btn-sm btn-default" type="button">Tìm</button>
                        </div>
                    </div>
                </div>

                <style type="text/css">
                    #category_order .ui-state-highlight {
                        padding: 24px;
                        background-color: #ffffcc;
                        border: 1px dotted #ccc;
                        cursor: move;
                        margin-top: 12px;
                    }
                </style>

                <div class="table-responsive">
                    <table class="table table-striped b-t b-light g-admin-data-table">
                        <thead>
                            <tr>
                                <th style="width:20px;">
                                    <label class="i-checks m-b-none">
                                        <input type="checkbox"><i></i>
                                    </label>
                                </th>
                                <th>Tên danh mục</th>
                                <th>Thuộc danh mục</th>
                                <th>Slug</th>
                                <th>Thứ tự danh mục</th>
                                <th>Hiển thị</th>
                                <th style="width:30px;"></th>
                            </tr>
                        </thead>
                        <tbody id="category_order" data-category-sort-enabled="{{ $categoryLegacyActionsReady ? 'true' : 'false' }}">
                            @foreach ($all_category_product as $cate_pro)
                                <tr id="{{ $cate_pro->category_id }}">
                                    <td>
                                        <label class="i-checks m-b-none">
                                            <input type="checkbox" name="post[]"><i></i>
                                        </label>
                                    </td>
                                    <td>{{ $cate_pro->category_name }}</td>
                                    <td>
                                        @if ($cate_pro->category_parent == 0)
                                            <span style="color:red;">Danh mục cha</span>
                                        @else
                                            @foreach ($category_product as $cate_sub_pro)
                                                @if ($cate_sub_pro->category_id == $cate_pro->category_parent)
                                                    <span style="color:green;">{{ $cate_sub_pro->category_name }}</span>
                                                @endif
                                            @endforeach
                                        @endif
                                    </td>
                                    <td>{{ $cate_pro->slug_category_product }}</td>
                                    <td>{{ $cate_pro->category_order }}</td>
                                    <td>
                                        <span class="text-ellipsis">
                                            @if ($categoryLegacyActionsReady)
                                                @if ($cate_pro->category_status == 0)
                                                    <a href="{{ URL::to('/unactive-category-product/' . $cate_pro->category_id) }}">
                                                        <span class="fa-thumb-styling fa fa-thumbs-up"></span>
                                                    </a>
                                                @else
                                                    <a href="{{ URL::to('/active-category-product/' . $cate_pro->category_id) }}">
                                                        <span class="fa-thumb-styling fa fa-thumbs-down"></span>
                                                    </a>
                                                @endif
                                            @else
                                                <span class="label label-default">Chỉ xem</span>
                                            @endif
                                        </span>
                                    </td>
                                    <td>
                                        @if ($categoryLegacyActionsReady)
                                            <a href="{{ URL::to('/edit-category-product/' . $cate_pro->category_id) }}"
                                                class="active styling-edit" ui-toggle-class="">
                                                <i class="fa fa-pencil-square-o text-success text-active"></i>
                                            </a>
                                            <a onclick="return confirm('Bạn có chắc muốn xóa danh mục này không?')"
                                                href="{{ URL::to('/delete-category-product/' . $cate_pro->category_id) }}"
                                                class="active styling-edit" ui-toggle-class="">
                                                <i class="fa fa-times text-danger text"></i>
                                            </a>
                                        @else
                                            <span class="text-muted">Legacy</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="g-admin-panel-grid two-col">
                    <div class="g-admin-surface">
                        <h3>Nhập dữ liệu</h3>
                        <form action="{{ $categoryLegacyActionsReady ? url('import-csv') : '#' }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="file" name="file" accept=".xlsx"><br>
                            <input type="submit" value="Nhập file Excel" name="import_csv" class="btn btn-warning" @disabled(! $categoryLegacyActionsReady)>
                        </form>
                    </div>

                    <div class="g-admin-surface">
                        <h3>Xuất dữ liệu</h3>
                        <form action="{{ $categoryLegacyActionsReady ? url('export-csv') : '#' }}" method="POST">
                            @csrf
                            <input type="submit" value="Xuất file Excel" name="export_csv" class="btn btn-success" @disabled(! $categoryLegacyActionsReady)>
                        </form>
                    </div>
                </div>
            </div>
            <footer class="panel-footer">
                <div class="row">
                    <div class="col-sm-5 text-center">
                        <small class="text-muted inline m-t-sm m-b-sm">Danh sách danh mục sản phẩm đang hiển thị bên dưới.</small>
                    </div>
                    <div class="col-sm-7 text-right text-center-xs">
                        <ul class="pagination pagination-sm m-t-none m-b-none">
                            {!! $all_category_product->links() !!}
                        </ul>
                    </div>
                </div>
            </footer>
        </section>
    </div>
@endsection
