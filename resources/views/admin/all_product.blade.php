@extends('admin_layout')

@section('admin_content')
    @php
        $productItems = method_exists($all_product, 'getCollection') ? $all_product->getCollection() : collect($all_product);
    @endphp

    <div class="g-admin-page-shell g-admin-product-list">
        <section class="g-admin-page-hero">
            <div>
                <span class="g-admin-page-eyebrow">Sản phẩm</span>
                <h1>Liệt kê sản phẩm</h1>
                <p>Theo dõi danh sách sản phẩm, tài liệu đính kèm, thư viện ảnh và trạng thái hiển thị trong một màn hình.</p>
            </div>
            <div class="g-admin-page-stats">
                <article>
                    <strong>{{ number_format(method_exists($all_product, 'total') ? $all_product->total() : $productItems->count()) }}</strong>
                    <span>Tổng sản phẩm</span>
                </article>
                <article>
                    <strong>{{ number_format($productItems->count()) }}</strong>
                    <span>Đang hiển thị trang này</span>
                </article>
            </div>
        </section>

        <section class="g-admin-panel-card">
            <header class="g-admin-panel-head">
                <div>
                    <strong>Danh sách sản phẩm</strong>
                    <p>Quản lý nhanh thư viện ảnh, biến thể, tệp tài liệu và trạng thái hiển thị của từng sản phẩm.</p>
                </div>
                <div class="g-admin-inline-actions">
                    <a href="{{ url('/add-product') }}" class="btn btn-info">Thêm sản phẩm</a>
                </div>
            </header>
            <div class="g-admin-panel-body">
                @if (session('message'))
                    <div class="alert alert-success">{{ session('message') }}</div>
                    {{ Session::put('message', null) }}
                @endif

                @if (session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                    {{ Session::put('error', null) }}
                @endif

                <div class="table-responsive">
                    <table class="table table-striped b-t b-light g-admin-data-table" id="myTable">
                        <thead>
                            <tr>
                                <th style="width:20px;">
                                    <label class="i-checks m-b-none">
                                        <input type="checkbox"><i></i>
                                    </label>
                                </th>
                                <th>Tên sản phẩm</th>
                                <th>Thư viện ảnh</th>
                                <th>Thuộc tính</th>
                                <th>Tài liệu</th>
                                <th>Số lượng</th>
                                <th>Đường dẫn tĩnh (slug)</th>
                                <th>Giá nhập</th>
                                <th>Giá gốc</th>
                                <th>Khuyến mãi</th>
                                <th>Giá sau sale</th>
                                <th>Lợi nhuận đơn vị</th>
                                <th>Hình ảnh</th>
                                <th>Danh mục</th>
                                <th>Thương hiệu</th>
                                <th>Hiển thị</th>
                                <th style="width:30px;"></th>
                            </tr>
                        </thead>
                        <tbody class="align-middle">
                            @forelse ($all_product as $pro)
                                <tr>
                                    <td>
                                        <label class="i-checks m-b-none">
                                            <input type="checkbox" name="post[]"><i></i>
                                        </label>
                                    </td>

                                    <td>{{ $pro->product_name }}</td>
                                    <td><a href="{{ url('/add-gallery/' . $pro->product_id) }}">Quản lý thư viện ảnh</a></td>
                                    <td><a href="{{ url('product-attribute', [$pro->product_id]) }}">Quản lý thuộc tính</a></td>

                                    @if ($pro->product_file)
                                        @php
                                            $filename = $pro->product_file;
                                            $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                                            $documentPath = asset('uploads/document/' . $filename);
                                        @endphp
                                        <td>
                                            @if ($extension === 'pdf')
                                                <a target="_blank" href="{{ $documentPath }}">Xem file</a>
                                            @elseif ($extension === 'docx')
                                                <a target="_blank"
                                                    href="https://view.officeapps.live.com/op/view.aspx?src={{ urlencode($documentPath) }}">Xem file</a>
                                            @else
                                                <a target="_blank" href="{{ $documentPath }}">Tải file</a>
                                            @endif
                                        </td>
                                    @else
                                        <td>Không có</td>
                                    @endif

                                    <td>{{ $pro->product_quantity }}</td>
                                    <td>{{ $pro->product_slug }}</td>
                                    <td>{{ number_format((float) $pro->price_cost, 0, ',', '.') }}đ</td>
                                    <td>{{ number_format((float) $pro->price, 0, ',', '.') }}đ</td>
                                    <td>
                                        @if ((float) $pro->sale_amount > 0)
                                            @if ($pro->sale_type === 'percent')
                                                -{{ rtrim(rtrim(number_format((float) $pro->sale_value, 2, '.', ''), '0'), '.') }}%
                                            @else
                                                -{{ number_format((float) $pro->sale_value, 0, ',', '.') }}đ
                                            @endif
                                        @else
                                            Không sale
                                        @endif
                                    </td>
                                    <td>
                                        {{ number_format((float) $pro->final_price, 0, ',', '.') }}đ
                                        @if ($pro->sale_explanation)
                                            <small class="g-admin-cell-note">{{ $pro->sale_explanation }}</small>
                                        @endif
                                    </td>
                                    <td>{{ number_format((float) $pro->profit_per_unit, 0, ',', '.') }}đ</td>
                                    <td>
                                        @if ($pro->product_image)
                                            <img src="{{ \App\Support\AssetUrl::product($pro->product_image) }}" alt="{{ $pro->product_name }}"
                                                height="100" width="100">
                                        @else
                                            <span class="text-muted">Chưa có ảnh</span>
                                        @endif
                                    </td>
                                    <td>{{ $pro->category_name }}</td>
                                    <td>{{ $pro->brand_name }}</td>
                                    <td>
                                        <span class="text-ellipsis">
                                            @if ($pro->product_status == 0)
                                                <form action="{{ url('/unactive-product/' . $pro->product_id) }}"
                                                    method="POST" style="display:inline-block;">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit"
                                                        style="border:0;background:none;padding:0;"
                                                        title="Ẩn sản phẩm">
                                                        <span class="fa-thumb-styling fa fa-thumbs-up"></span>
                                                    </button>
                                                </form>
                                            @else
                                                <form action="{{ url('/active-product/' . $pro->product_id) }}"
                                                    method="POST" style="display:inline-block;">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit"
                                                        style="border:0;background:none;padding:0;"
                                                        title="Hiển thị sản phẩm">
                                                        <span class="fa-thumb-styling fa fa-thumbs-down"></span>
                                                    </button>
                                                </form>
                                            @endif
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ URL::to('/edit-product/' . $pro->product_id) }}" class="active styling-edit"
                                            ui-toggle-class="">
                                            <i class="fa fa-pencil-square-o text-success text-active"></i>
                                        </a>
                                        <form action="{{ url('/delete-product/' . $pro->product_id) }}" method="POST"
                                            style="display:inline-block;"
                                            onsubmit="return confirm('Bạn có chắc muốn xóa sản phẩm này không?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="active styling-edit"
                                                style="border:0;background:none;padding:0;" ui-toggle-class="">
                                                <i class="fa fa-times text-danger text"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="17">
                                        <div class="g-admin-empty-state">Chưa có sản phẩm nào.</div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    @if ($all_product instanceof \Illuminate\Pagination\AbstractPaginator)
                        @include('admin.partials.pagination', ['paginator' => $all_product, 'label' => 'sản phẩm'])
                    @endif
                </div>
            </div>
        </section>
    </div>
@endsection
