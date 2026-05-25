@extends('layouts.home-tech')

@section('body_class', 'g-history')

@section('content')
    <style>
        .simple-history-page {
            background-color: #f8f9fa;
            padding: 40px 0 60px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .simple-history-container {
            max-width: 1140px;
            margin: 0 auto;
            padding: 0 15px;
        }
        .history-card {
            background: #fff;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
        .history-header {
            margin-bottom: 25px;
            border-bottom: 1px solid #eee;
            padding-bottom: 15px;
        }
        .history-header h2 {
            font-size: 22px;
            color: #333;
            margin: 0;
        }
        .history-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }
        .history-table th {
            background-color: #f8f9fa;
            color: #333;
            font-weight: 600;
            text-transform: uppercase;
            padding: 12px 10px;
            border-bottom: 2px solid #e2e8f0;
            text-align: left;
        }
        .history-table td {
            padding: 15px 10px;
            border-bottom: 1px solid #e2e8f0;
            color: #555;
            vertical-align: middle;
        }
        .history-table tbody tr:hover {
            background-color: #fcfcfc;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 500;
        }
        .status-badge.bg-blue { background-color: #e0f2fe; color: #0369a1; }
        .status-badge.bg-green { background-color: #dcfce3; color: #166534; }
        .status-badge.bg-red { background-color: #fee2e2; color: #991b1b; }
        .status-badge.bg-gray { background-color: #f1f5f9; color: #475569; }
        
        .btn-action {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
            text-decoration: none;
            text-align: center;
            border: none;
            cursor: pointer;
            margin-bottom: 4px;
        }
        .btn-view { background-color: #f1f5f9; color: #475569; }
        .btn-view:hover { background-color: #e2e8f0; }
        .btn-cancel { background-color: #22c55e; color: #fff; }
        .btn-cancel:hover { background-color: #16a34a; }
        .order-code {
            color: #22c55e;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        .order-code i { font-size: 14px; }
        
        .empty-history {
            text-align: center;
            padding: 40px 0;
        }
        .empty-history i { font-size: 48px; color: #ccc; margin-bottom: 15px; }
    </style>

    <section class="simple-history-page">
        <div class="simple-history-container">
            @include('partials.storefront.page-header', [
                'title' => 'Lịch sử đơn hàng',
                'breadcrumbClass' => 'accp-crumb',
                'separator' => '›',
                'breadcrumbItems' => [
                    ['label' => 'Tài khoản', 'url' => url('/account')],
                    ['label' => 'Lịch sử đơn hàng', 'current' => true],
                ],
            ])

            <div class="history-card">
                <div class="history-header">
                    <div class="shopw-history-support-strip">
                        <a href="{{ url('/') }}">Quay lại trang chủ để tiếp tục chọn hàng</a>
                    </div>
                </div>

                @if (session('message'))
                    <div class="alert alert-success">{{ session('message') }}</div>
                @elseif(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                @if ($orders->count() === 0)
                    <div class="empty-history">
                        <i class="fa fa-inbox"></i>
                        <h3>Chưa có đơn hàng nào</h3>
                        <p>Hãy quay lại trang chủ để mua sắm nhé.</p>
                        <a href="{{ url('/') }}" class="btn btn-primary" style="margin-top: 15px;">Tiếp tục mua sắm</a>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="history-table">
                            <thead>
                                <tr>
                                    <th>MDH</th>
                                    <th>Phương thức thanh toán</th>
                                    <th>Trạng thái</th>
                                    <th>Thanh toán</th>
                                    <th>Số điện thoại</th>
                                    <th>Tổng tiền</th>
                                    <th>Ngày đặt</th>
                                    <th>Hoạt động</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($orders as $order)
                                    @php
                                        // Fake logic mapping for styling to match the react screenshot
                                        $statusClass = 'bg-blue';
                                        if (in_array(strtolower($order['status']), ['cancelled', 'hủy'])) $statusClass = 'bg-red';
                                        elseif (in_array(strtolower($order['status']), ['completed', 'hoàn thành'])) $statusClass = 'bg-green';
                                        
                                        $paymentStatusClass = 'bg-gray';
                                        if (in_array(strtolower($order['payment_status_label']), ['đã thanh toán', 'paid'])) $paymentStatusClass = 'bg-green';
                                        elseif (in_array(strtolower($order['payment_status_label']), ['chưa thanh toán', 'unpaid'])) $paymentStatusClass = 'bg-green'; // The screenshot strangely has "Chưa thanh toán" in green bg
                                    @endphp
                                    <tr>
                                        <td>
                                            <span class="order-code">
                                                <i class="fa fa-plus-circle"></i> MDH{{ $order['code'] }}
                                            </span>
                                        </td>
                                        <td>{{ $order['payment_label'] }}</td>
                                        <td><span class="status-badge {{ $statusClass }}">{{ $order['status_label'] }}</span></td>
                                        <td><span class="status-badge {{ $paymentStatusClass }}">{{ $order['payment_status_label'] }}</span></td>
                                        <td>{{ session('customer_phone') ?? '0123456789' }}</td>
                                        <td><strong>{{ number_format($order['total'], 0, ',', '.') }}đ</strong></td>
                                        <td>
                                            {{ $order['placed_at_label'] }}
                                        </td>
                                        <td>
                                            <a href="{{ url('/account/order-tracking/' . $order['code']) }}" class="btn-action btn-view" title="Xem chi tiết">Chi tiết</a>
                                            @if ($order['can_cancel'])
                                                <button type="button" class="btn-action btn-cancel" data-toggle="modal" data-target="#cancelOrderModal" data-order-code="{{ $order['code'] }}">HỦY</button>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div style="margin-top: 20px;">
                        {!! $orders->links('vendor.pagination.shop-pagination') !!}
                    </div>
                @endif
            </div>
        </div>
    </section>

    @include('pages.history.partials.cancel_order_modal')
@endsection
