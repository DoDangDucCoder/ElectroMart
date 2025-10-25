@extends('admin_layout')
@section('admin_content')
    <div class="table-agile-info">
        <div class="panel panel-default">
            <div class="panel-heading">
                Liệt kê sản phẩm
            </div>

            <div class="table-responsive p-3">
                @if (session('message'))
                    <span class="text-alert">{{ session('message') }}</span>
                    {{ Session::put('message', null) }}
                @endif

                <table class="table table-striped b-t b-light" id="myTable">
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
                            <th>Slug</th>
                            <th>Giá bán</th>
                            <th>Giá gốc</th>
                            <th>Hình sản phẩm</th>
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
                                <td><a href="{{ url('/add-gallery/' . $pro->product_id) }}">Thêm thư viện ảnh</a></td>
                                <td><a href="{{ url('product-attribute', [$pro->product_id]) }}">Thêm thuộc tính</a></td>

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
                                                href="https://view.officeapps.live.com/op/view.aspx?src={{ urlencode($documentPath) }}">Xem
                                                file</a>
                                        @else
                                            <a target="_blank" href="{{ $documentPath }}">Tải file</a>
                                        @endif
                                    </td>
                                @else
                                    <td>Không có</td>
                                @endif

                                <td>{{ $pro->product_quantity }}</td>
                                <td>{{ $pro->product_slug }}</td>
                                <td>{{ number_format($pro->product_price, 0, ',', '.') }}đ</td>
                                <td>{{ number_format((float) $pro->price_cost, 0, ',', '.') }}đ</td>
                                <td>
                                    @if ($pro->product_image)
                                        <img src="{{ asset($pro->product_image) }}" alt="{{ $pro->product_name }}"
                                            height="100" width="100">
                                    @else
                                        <span class="text-muted">No image</span>
                                    @endif
                                </td>
                                <td>{{ $pro->category_name }}</td>
                                <td>{{ $pro->brand_name }}</td>
                                <td>
                                    <span class="text-ellipsis">
                                        @if ($pro->product_status == 0)
                                            <a href="{{ URL::to('/unactive-product/' . $pro->product_id) }}"><span
                                                    class="fa-thumb-styling fa fa-thumbs-up"></span></a>
                                        @else
                                            <a href="{{ URL::to('/active-product/' . $pro->product_id) }}"><span
                                                    class="fa-thumb-styling fa fa-thumbs-down"></span></a>
                                        @endif
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ URL::to('/edit-product/' . $pro->product_id) }}" class="active styling-edit"
                                        ui-toggle-class="">
                                        <i class="fa fa-pencil-square-o text-success text-active"></i>
                                    </a>
                                    <a onclick="return confirm('Bạn có chắc là muốn xóa sản phẩm này không?')"
                                        href="{{ URL::to('/delete-product/' . $pro->product_id) }}"
                                        class="active styling-edit" ui-toggle-class="">
                                        <i class="fa fa-times text-danger text"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="14" class="text-center text-muted">Chưa có sản phẩm nào.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                @if ($all_product instanceof \Illuminate\Pagination\AbstractPaginator)
                    <div class="d-flex justify-content-end">
                        {{ $all_product->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
