@extends('admin_layout')

@section('admin_content')
    @php
        $currentOrder = $getorder->first();
        $firstDetail = $order_details->first();
        $shippingFee = $firstDetail->product_feeship ?? 0;
        $total = 0;
        $statusOptions = [
            1 => 'Chờ xử lý',
            2 => 'Đang xử lý',
            3 => 'Đang giao',
            4 => 'Đã giao hàng',
            5 => 'Đã hủy',
        ];
        $currentStatus = (int) $order_status;
        $isPendingOrder = $currentStatus === 1;
        $isFinalOrder = in_array($currentStatus, [4, 5], true);
        $isCancelledOrder = $currentStatus === 5;
        $orderStatusLabel = $statusOptions[$currentStatus] ?? 'Chưa xác định';
    @endphp

    <div class="g-admin-page-shell g-admin-order-view">
        <style>
            @media (max-width: 767px) {
                .g-admin-order-view .g-admin-data-table th,
                .g-admin-order-view .g-admin-data-table td {
                    white-space: nowrap;
                    font-size: 12px;
                }

                .g-admin-order-view .g-admin-order-qty-editor {
                    min-width: 170px;
                }
            }
        </style>

        <input type="hidden" name="_token" value="{{ csrf_token() }}">

        <section class="g-admin-page-hero">
            <div>
                <span class="g-admin-page-eyebrow">Đơn hàng</span>
                <h1>Chi tiết đơn hàng {{ $currentOrder?->order_code }}</h1>
                <p>
                    Kiểm tra thông tin khách hàng, địa chỉ giao hàng, tồn kho và cập nhật trạng thái xử lý
                    cho đơn hàng này.
                </p>
            </div>

            <div class="g-admin-page-stats">
                <article>
                    <strong>{{ number_format($order_details->count()) }}</strong>
                    <span>Dòng sản phẩm</span>
                </article>
                <article>
                    <strong>{{ $orderStatusLabel }}</strong>
                    <span>Trạng thái đơn</span>
                </article>
            </div>
        </section>

        <div class="g-admin-panel-grid two-col">
            <section class="g-admin-panel-card">
                <div class="g-admin-panel-head">
                    <div>
                        <div>Thông tin đăng nhập</div>
                        <p>Thông tin khách hàng đi kèm với đơn hàng.</p>
                    </div>
                </div>
                <div class="g-admin-panel-body">
                    <div class="g-admin-detail-list">
                        <div>
                            <span>Khách hàng</span>
                            <strong>{{ data_get($customer, 'customer_name', 'Chưa có dữ liệu') }}</strong>
                        </div>
                        <div>
                            <span>Số điện thoại</span>
                            <strong>{{ data_get($customer, 'customer_phone', 'Chưa có dữ liệu') }}</strong>
                        </div>
                        <div>
                            <span>Email</span>
                            <strong>{{ data_get($customer, 'customer_email', 'Chưa có dữ liệu') }}</strong>
                        </div>
                    </div>
                </div>
            </section>

            <section class="g-admin-panel-card">
                <div class="g-admin-panel-head">
                    <div>
                        <div>Thông tin vận chuyển</div>
                        <p>Thông tin người nhận, ghi chú giao hàng và phương thức thanh toán.</p>
                    </div>
                </div>
                <div class="g-admin-panel-body">
                    <div class="g-admin-detail-list">
                        <div>
                            <span>Người nhận</span>
                            <strong>{{ data_get($shipping, 'shipping_name', 'Chưa có dữ liệu') }}</strong>
                        </div>
                        <div>
                            <span>Địa chỉ</span>
                            <strong>{{ data_get($shipping, 'shipping_address', 'Chưa có dữ liệu') }}</strong>
                        </div>
                        <div>
                            <span>Điện thoại</span>
                            <strong>{{ data_get($shipping, 'shipping_phone', 'Chưa có dữ liệu') }}</strong>
                        </div>
                        <div>
                            <span>Email</span>
                            <strong>{{ data_get($shipping, 'shipping_email', 'Chưa có dữ liệu') }}</strong>
                        </div>
                        <div>
                            <span>Ghi chú</span>
                            <strong>{{ data_get($shipping, 'shipping_notes') ?: 'Không có ghi chú' }}</strong>
                        </div>
                        <div>
                            <span>Phương thức TT</span>
                            <strong>
                                @if (data_get($shipping, 'shipping_method') == 0)
                                    Chuyển khoản
                                @elseif(data_get($shipping, 'shipping_method') == 1)
                                    COD
                                @else
                                    Không rõ
                                @endif
                            </strong>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <section class="g-admin-panel-card">
            <div class="g-admin-panel-head">
                <div>
                    <div>Chi tiết sản phẩm trong đơn</div>
                    <p>Kiểm tra số lượng, giá bán và cập nhật trạng thái xử lý cho từng dòng sản phẩm.</p>
                </div>
                @if ($currentOrder)
                    <a target="_blank" href="{{ url('/print-order/' . $currentOrder->order_code) }}" class="btn btn-primary btn-sm">
                        In hoá đơn
                    </a>
                @endif
            </div>
            <div class="g-admin-panel-body">
                @if (session('message'))
                    <div class="alert alert-success">{{ session('message') }}</div>
                @endif

                <div id="order_management_notice" class="alert alert-info" hidden></div>

                @if ($isCancelledOrder)
                    <div class="alert alert-warning">
                        <strong>Đơn hàng đã bị hủy.</strong>
                        @if (!empty($currentOrder?->order_destroy))
                            Lý do hủy: {{ $currentOrder->order_destroy }}
                        @endif
                    </div>
                @endif

                @if ($order_details->isEmpty())
                    <div class="g-admin-empty-state">Không có chi tiết sản phẩm nào cho đơn hàng này.</div>
                @else
                    <div class="table-responsive">
                        <table class="table table-striped b-t b-light g-admin-data-table">
                            <thead>
                                <tr>
                                    <th>STT</th>
                                    <th>Sản phẩm</th>
                                    <th>Thuộc tính</th>
                                    <th>Tồn kho</th>
                                    <th>Mã giảm giá</th>
                                    <th>Phí vận chuyển</th>
                                    <th>Số lượng</th>
                                    <th>Giá bán</th>
                                    <th>Giá gốc</th>
                                    <th>Tổng tiền</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($order_details as $index => $details)
                                    @php
                                        $subtotal = $details->product_price * $details->product_sales_quantity;
                                        $total += $subtotal;
                                    @endphp
                                    <tr class="color_qty_{{ $details->product_id }}">
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <div class="g-admin-order-line-name">{{ $details->product_name }}</div>
                                        </td>
                                        <td>{{ $details->product_attribute ?: 'Mặc định' }}</td>
                                        <td>{{ optional($details->product)->product_quantity ?? 0 }}</td>
                                        <td>{{ $details->product_coupon != 'no' ? $details->product_coupon : 'Không mã' }}</td>
                                        <td>{{ number_format($details->product_feeship, 0, ',', '.') }}đ</td>
                                        <td>
                                            <div class="g-admin-order-qty-editor">
                                                <input type="number" min="0" {{ $isPendingOrder ? '' : 'disabled' }}
                                                    class="form-control order_qty_{{ $details->product_id }}"
                                                    value="{{ $details->product_sales_quantity }}" name="product_sales_quantity">

                                                <input type="hidden" name="order_qty_storage"
                                                    class="order_qty_storage_{{ $details->product_id }}"
                                                    value="{{ optional($details->product)->product_quantity ?? 0 }}">

                                                <input type="hidden" name="order_code" class="order_code"
                                                    value="{{ $details->order_code }}">

                                                <input type="hidden" name="order_product_id" class="order_product_id"
                                                    value="{{ $details->product_id }}">

                                                @if ($isPendingOrder)
                                                    <button class="btn btn-default update_quantity_order"
                                                        data-product_id="{{ $details->product_id }}" name="update_quantity_order">
                                                        Cập nhật
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                        <td>{{ number_format($details->product_price, 0, ',', '.') }}đ</td>
                                        <td>{{ number_format((float) ($details->product_cost_price ?? optional($details->product)->price_cost ?? 0), 0, ',', '.') }}đ</td>
                                        <td>{{ number_format($subtotal, 0, ',', '.') }}đ</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @php
                        $total_coupon = $total + $shippingFee;
                    @endphp

                    @if ($coupon_condition == 1)
                        @php
                            $total_after_coupon = ($total * $coupon_number) / 100;
                            $total_coupon = $total + $shippingFee - $total_after_coupon;
                            $discountLabel = number_format($total_after_coupon, 0, ',', '.') . 'đ';
                        @endphp
                    @else
                        @php
                            $total_coupon = $total + $shippingFee - $coupon_number;
                            $discountLabel = number_format($coupon_number, 0, ',', '.') . 'đ';
                        @endphp
                    @endif

                    <div class="g-admin-panel-grid two-col">
                        <div class="g-admin-surface">
                            <h3>Tổng kết thanh toán</h3>
                            <ul class="g-admin-note-list">
                                <li>Tạm tính: {{ number_format($total, 0, ',', '.') }}đ</li>
                                <li>Tổng giảm: {{ $discountLabel }}</li>
                                <li>Phí vận chuyển: {{ number_format($shippingFee, 0, ',', '.') }}đ</li>
                                <li>Thanh toán: {{ number_format(max(0, $total_coupon), 0, ',', '.') }}đ</li>
                            </ul>
                        </div>

                        <div class="g-admin-surface">
                            <h3>Cập nhật trạng thái đơn</h3>
                            <form>
                                @csrf
                                <select class="form-control order_details" {{ $isFinalOrder ? 'disabled' : '' }}>
                                    @foreach ($statusOptions as $value => $label)
                                        @continue($value < $currentStatus && $value !== 5)
                                        <option id="{{ $currentOrder?->order_id }}" value="{{ $value }}" @selected($currentStatus === $value)>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @if ($isFinalOrder)
                                    <p class="text-muted" style="margin-top:8px;">Đơn hàng đã ở trạng thái cuối nên không thể cập nhật tiếp.</p>
                                @else
                                    <p class="text-muted" style="margin-top:8px;">Luồng xử lý: Chờ xử lý -> Đang xử lý -> Đang giao -> Đã giao hàng. Có thể hủy trước khi giao thành công.</p>
                                @endif
                            </form>
                        </div>
                    </div>
                @endif
            </div>
        </section>
    </div>
@endsection
