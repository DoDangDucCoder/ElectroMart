<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            color: #111827;
            font-size: 12px;
        }

        .print-shell {
            width: 100%;
        }

        .print-header {
            text-align: center;
            margin-bottom: 24px;
        }

        .print-header h1 {
            margin: 0 0 8px;
            font-size: 20px;
        }

        .print-header p {
            margin: 0;
            font-size: 12px;
        }

        .print-section {
            margin-bottom: 18px;
        }

        .print-section h2 {
            margin: 0 0 10px;
            font-size: 14px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #111827;
            padding: 8px;
            vertical-align: top;
        }

        th {
            background: #f3f4f6;
            text-align: left;
        }

        .print-summary {
            width: 45%;
            margin-left: auto;
        }

        .print-summary td:first-child {
            width: 50%;
        }

        .print-signature {
            margin-top: 30px;
        }

        .print-signature td {
            border: 0;
            padding: 24px 8px 0;
            text-align: center;
        }
    </style>
</head>
<body>
    @php
        $order = $printOrder['order'];
        $customer = $printOrder['customer'];
        $shipping = $printOrder['shipping'];
        $details = $printOrder['details'];
        $totals = $printOrder['totals'];
    @endphp

    <div class="print-shell">
        <div class="print-header">
            <h1>Công ty TNHH một thành viên ABCD</h1>
            <p>Độc lập - Tự do - Hạnh phúc</p>
            <p>Phiếu đơn hàng #{{ $order->order_code }}</p>
        </div>

        <div class="print-section">
            <h2>Thông tin khách đặt hàng</h2>
            <table>
                <thead>
                    <tr>
                        <th>Tên khách đặt</th>
                        <th>Số điện thoại</th>
                        <th>Email</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ $customer->customer_name ?? 'Chưa có dữ liệu' }}</td>
                        <td>{{ $customer->customer_phone ?? 'Chưa có dữ liệu' }}</td>
                        <td>{{ $customer->customer_email ?? 'Chưa có dữ liệu' }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="print-section">
            <h2>Thông tin giao hàng</h2>
            <table>
                <thead>
                    <tr>
                        <th>Tên người nhận</th>
                        <th>Địa chỉ</th>
                        <th>Số điện thoại</th>
                        <th>Email</th>
                        <th>Ghi chú</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ $shipping->shipping_name ?? 'Chưa có dữ liệu' }}</td>
                        <td>{{ $shipping->shipping_address ?? 'Chưa có dữ liệu' }}</td>
                        <td>{{ $shipping->shipping_phone ?? 'Chưa có dữ liệu' }}</td>
                        <td>{{ $shipping->shipping_email ?? 'Chưa có dữ liệu' }}</td>
                        <td>{{ $shipping->shipping_notes ?? 'Không có ghi chú' }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="print-section">
            <h2>Chi tiết đơn hàng</h2>
            <table>
                <thead>
                    <tr>
                        <th>Tên sản phẩm</th>
                        <th>Thuộc tính</th>
                        <th>Mã giảm giá</th>
                        <th>Phí ship</th>
                        <th>Số lượng</th>
                        <th>Giá sản phẩm</th>
                        <th>Thành tiền</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($details as $detail)
                        <tr>
                            <td>{{ $detail->product_name }}</td>
                            <td>{{ $detail->product_attribute ?: 'Mặc định' }}</td>
                            <td>{{ $detail->product_coupon !== 'no' ? $detail->product_coupon : 'Không mã' }}</td>
                            <td>{{ number_format($detail->product_feeship, 0, ',', '.') }}đ</td>
                            <td>{{ $detail->product_sales_quantity }}</td>
                            <td>{{ number_format($detail->product_price, 0, ',', '.') }}đ</td>
                            <td>{{ number_format($detail->product_price * $detail->product_sales_quantity, 0, ',', '.') }}đ</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <table class="print-summary">
            <tbody>
                <tr>
                    <td>Tạm tính</td>
                    <td>{{ number_format($totals['subtotal'], 0, ',', '.') }}đ</td>
                </tr>
                <tr>
                    <td>Tổng giảm</td>
                    <td>{{ $totals['coupon_label'] }}</td>
                </tr>
                <tr>
                    <td>Phí ship</td>
                    <td>{{ number_format($totals['shipping'], 0, ',', '.') }}đ</td>
                </tr>
                <tr>
                    <td>Thanh toán</td>
                    <td>{{ number_format($totals['total'], 0, ',', '.') }}đ</td>
                </tr>
            </tbody>
        </table>

        <table class="print-signature">
            <tbody>
                <tr>
                    <td width="35%">Người lập phiếu</td>
                    <td width="30%"></td>
                    <td width="35%">Người nhận</td>
                </tr>
            </tbody>
        </table>
    </div>
</body>
</html>
