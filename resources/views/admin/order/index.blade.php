@extends('admin_layout')

@section('admin_content')
<style>
    .adm-order-management {
        padding: 20px 16px;
    }

    .adm-order-management .adm-layout {
        grid-template-columns: 1fr;
    }

    .adm-order-management .adm-page-title {
        display: flex;
        align-items: flex-start;
        gap: 14px;
        margin-bottom: 18px;
    }

    .adm-order-management .adm-page-title-icon {
        width: 44px;
        height: 44px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        background: #fff;
        border: 1px solid var(--adm-border);
        color: var(--adm-blue);
        box-shadow: var(--adm-shadow);
        font-size: 20px;
        flex: 0 0 auto;
    }

    .adm-order-management .adm-page-title h1 {
        margin-top: 2px;
    }

    .adm-order-management .adm-stats {
        grid-template-columns: repeat(5, minmax(0, 1fr));
        gap: 16px;
        margin-bottom: 20px;
    }

    .adm-order-management .adm-stat {
        min-height: 88px;
        flex-direction: row;
        align-items: center;
        gap: 14px;
        padding: 16px 18px;
        overflow: hidden;
    }

    .adm-order-management .adm-stat .icon {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        font-size: 21px;
    }

    .adm-order-management .adm-stat .num {
        font-size: 24px;
    }

    .adm-order-management .adm-stat .lbl {
        font-size: 12px;
    }

    .adm-order-management .adm-toolbar {
        display: grid;
        grid-template-columns: minmax(220px, 1fr) 170px 180px 110px 118px;
        gap: 12px;
        margin-bottom: 14px;
        align-items: stretch;
        padding: 16px;
        background: #fff;
        border: 1px solid var(--adm-border);
        border-radius: var(--adm-radius);
        box-shadow: var(--adm-shadow);
    }

    .adm-order-management .adm-search,
    .adm-order-management .adm-select,
    .adm-order-management .adm-btn {
        min-height: 40px;
        height: 40px;
    }

    .adm-order-management .adm-btn {
        justify-content: center;
        font-size: 13px;
    }

    .adm-order-management .adm-table-wrap {
        border-radius: var(--adm-radius);
        overflow-x: auto;
    }

    .adm-order-management .adm-table {
        width: 100%;
        min-width: 1040px;
        table-layout: fixed;
        font-size: 12.5px;
    }

    .adm-order-management .adm-table thead th {
        padding: 12px 10px;
        text-align: center !important;
        vertical-align: middle;
    }

    .adm-order-management .adm-table td {
        height: 96px;
        padding: 12px 10px;
        text-align: center;
        vertical-align: middle;
    }

    .adm-order-management .adm-order-code-col {
        width: 118px;
    }

    .adm-order-management .adm-order-date-col {
        width: 100px;
    }

    .adm-order-management .adm-order-customer-col {
        width: 210px;
        text-align: center !important;
    }

    .adm-order-management .adm-order-product-col {
        width: 220px;
        text-align: center !important;
    }

    .adm-order-management .adm-order-money-col {
        width: 120px;
        white-space: nowrap;
    }

    .adm-order-management .adm-order-payment-col {
        width: 116px;
    }

    .adm-order-management .adm-order-status-col {
        width: 106px;
    }

    .adm-order-management .adm-order-action-col {
        width: 86px;
    }

    .adm-order-management .adm-order-code {
        display: block;
        color: var(--adm-blue);
        font-weight: 800;
        font-size: 12px;
        line-height: 1.3;
    }

    .adm-order-management .adm-order-date {
        color: var(--adm-text);
        font-size: 12px;
        line-height: 1.5;
    }

    .adm-order-management .adm-order-time {
        color: var(--adm-muted);
        display: block;
    }

    .adm-order-management .adm-user-cell {
        justify-content: center;
    }

    .adm-order-management .adm-order-product {
        display: block;
        margin-left: auto;
        margin-right: auto;
        max-width: 190px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        color: var(--adm-dark);
        font-weight: 600;
    }

    .adm-order-management .adm-order-payment {
        display: inline-flex;
        flex-direction: column;
        gap: 3px;
        align-items: center;
        color: var(--adm-text);
        font-size: 12px;
    }

    .adm-order-management .adm-actions {
        justify-content: center;
    }

    .adm-order-management .adm-actions a {
        width: 34px;
        height: 34px;
        border-radius: 8px;
    }

    @media (max-width: 1500px) {
        .adm-order-management .adm-toolbar {
            grid-template-columns: minmax(200px, 1fr) 160px 170px 96px 108px;
        }
    }

    @media (max-width: 1100px) {
        .adm-order-management .adm-stats,
        .adm-order-management .adm-toolbar {
            grid-template-columns: 1fr;
        }

        .adm-order-management .adm-table-wrap {
            overflow-x: auto;
        }
    }
</style>

<div class="adm-page adm-order-management">
    <div class="adm-page-title">
        <span class="adm-page-title-icon"><i class="fa fa-shopping-cart"></i></span>
        <div>
            <h1>Quản lý đơn hàng</h1>
            <div class="crumb"><a href="{{ url('/dashboard') }}">Trang chủ</a> › Bán hàng › Quản lý đơn hàng</div>
        </div>
    </div>

    <div class="adm-stats">
        <div class="adm-stat" data-filter-field="status" data-filter-value="all">
            <div class="icon blue"><i class="fa fa-list"></i></div>
            <div><div class="num">{{ number_format($stats['total']) }}</div><div class="lbl">Tổng đơn hàng</div></div>
        </div>
        <div class="adm-stat" data-filter-field="status" data-filter-value="pending">
            <div class="icon amber"><i class="fa fa-clock-o"></i></div>
            <div><div class="num" style="color:var(--adm-amber)">{{ number_format($stats['pending']) }}</div><div class="lbl">Chờ xác nhận</div></div>
        </div>
        <div class="adm-stat" data-filter-field="status" data-filter-value="processing">
            <div class="icon teal"><i class="fa fa-cog"></i></div>
            <div><div class="num">{{ number_format($stats['processing']) }}</div><div class="lbl">Đang xử lý</div></div>
        </div>
        <div class="adm-stat" data-filter-field="status" data-filter-value="shipped">
            <div class="icon blue"><i class="fa fa-truck"></i></div>
            <div><div class="num">{{ number_format($stats['shipped']) }}</div><div class="lbl">Đang giao</div></div>
        </div>
        <div class="adm-stat" data-filter-field="status" data-filter-value="completed">
            <div class="icon green"><i class="fa fa-check-circle"></i></div>
            <div><div class="num">{{ number_format($stats['completed']) }}</div><div class="lbl">Hoàn thành</div></div>
        </div>
    </div>

    <div class="adm-layout">
        <div>
            <form class="adm-toolbar" method="GET" action="{{ route('admin.orders.management') }}">
                <div class="adm-search">
                    <i class="fa fa-search"></i>
                    <input type="text" name="q" value="{{ $filters['q'] }}" placeholder="Tìm mã đơn, khách hàng, sản phẩm...">
                </div>
                <select class="adm-select" name="status">
                    <option value="all" @selected($filters['status'] === 'all')>Tất cả đơn</option>
                    <option value="pending" @selected($filters['status'] === 'pending')>Chờ xác nhận</option>
                    <option value="confirmed" @selected($filters['status'] === 'confirmed')>Đã xác nhận</option>
                    <option value="processing" @selected($filters['status'] === 'processing')>Đang xử lý</option>
                    <option value="shipped" @selected($filters['status'] === 'shipped')>Đang giao</option>
                    <option value="completed" @selected($filters['status'] === 'completed')>Hoàn thành</option>
                    <option value="cancelled" @selected($filters['status'] === 'cancelled')>Đã hủy</option>
                </select>
                <select class="adm-select" name="payment_status">
                    <option value="all" @selected($filters['payment_status'] === 'all')>Tất cả thanh toán</option>
                    <option value="pending" @selected($filters['payment_status'] === 'pending')>Chờ thanh toán</option>
                    <option value="paid" @selected($filters['payment_status'] === 'paid')>Đã thanh toán</option>
                    <option value="failed" @selected($filters['payment_status'] === 'failed')>Thất bại</option>
                    <option value="refunded" @selected($filters['payment_status'] === 'refunded')>Hoàn tiền</option>
                </select>
                <input type="hidden" name="sort" value="{{ $filters['sort'] ?? 'newest' }}">
                <button class="adm-btn outline" type="submit"><i class="fa fa-filter"></i> Lọc</button>
                <a class="adm-btn outline" href="{{ route('admin.orders.export', request()->except('page')) }}"><i class="fa fa-download"></i> Xuất CSV</a>
            </form>

            <div class="adm-table-wrap">
                <table class="adm-table">
                    <thead>
                        <tr>
                            <th class="adm-order-code-col">@include('admin.partials.sort_header', ['label' => 'Mã đơn', 'asc' => 'oldest', 'desc' => 'newest', 'filters' => $filters])</th>
                            <th class="adm-order-date-col">@include('admin.partials.sort_header', ['label' => 'Ngày đặt', 'asc' => 'date_asc', 'desc' => 'date_desc', 'filters' => $filters])</th>
                            <th class="adm-order-customer-col">Khách hàng</th>
                            <th class="adm-order-product-col">Sản phẩm</th>
                            <th class="adm-order-money-col">@include('admin.partials.sort_header', ['label' => 'Tổng tiền', 'asc' => 'total_asc', 'desc' => 'total_desc', 'filters' => $filters])</th>
                            <th class="adm-order-payment-col">Phương thức TT</th>
                            <th class="adm-order-status-col">Trạng thái</th>
                            <th class="adm-order-action-col">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse ($orders as $order)
                        @php
                            $statusMap = [
                                'pending' => ['Chờ xác nhận', 'amber'],
                                'confirmed' => ['Đã xác nhận', 'teal'],
                                'processing' => ['Đang xử lý', 'teal'],
                                'shipping' => ['Đang giao', 'blue'],
                                'shipped' => ['Đang giao', 'blue'],
                                'completed' => ['Hoàn thành', 'green'],
                                'cancelled' => ['Đã hủy', 'red'],
                                'refunded' => ['Đã hoàn tiền', 'red'],
                            ];
                            $paymentMethodLabels = [
                                'cod' => 'COD',
                                'cash' => 'Tiền mặt',
                                'bank_transfer' => 'Chuyển khoản',
                                'transfer' => 'Chuyển khoản',
                                'card' => 'Thẻ',
                                'momo' => 'MoMo',
                                'vnpay' => 'VNPay',
                                'paypal' => 'PayPal',
                            ];
                            [$statusLabel, $statusClass] = $statusMap[$order->status] ?? ['Không rõ', 'gray'];
                            $firstItem = $order->items->first();
                            $payment = $order->payments->first();
                            $provider = strtolower((string) ($payment?->provider ?? 'cod'));
                            $paymentMethod = $paymentMethodLabels[$provider] ?? strtoupper((string) ($payment?->provider ?? 'COD'));
                        @endphp
                        <tr>
                            <td class="id adm-order-code-col">
                                <span class="adm-order-code">{{ $order->code }}</span>
                            </td>
                            <td class="adm-order-date-col">
                                <span class="adm-order-date">
                                    {{ optional($order->placed_at ?? $order->created_at)->format('d/m/Y') }}
                                    <span class="adm-order-time">{{ optional($order->placed_at ?? $order->created_at)->format('H:i') }}</span>
                                </span>
                            </td>
                            <td class="adm-order-customer-col" style="text-align:center;vertical-align:middle">
                                @php($customerContact = $order->customer?->phone ?? $order->customer?->email)
                                <strong>{{ $order->customer?->name ?? 'Khách vãng lai' }}</strong>
                                @if ($customerContact)
                                    <div style="font-size:10px;color:var(--adm-muted)">{{ $customerContact }}</div>
                                @endif
                            </td>
                            <td class="adm-order-product-col" style="text-align:center;vertical-align:middle">
                                <span class="adm-order-product">{{ $firstItem?->product_name ?? 'Không có sản phẩm' }}</span>
                                @if ($order->items_count > 1)
                                    +{{ $order->items_count - 1 }}
                                @endif
                            </td>
                            <td class="adm-order-money-col" style="font-weight:800;color:var(--adm-dark)">{{ number_format((float) $order->total, 0, ',', '.') }}đ</td>
                            <td class="adm-order-payment-col">
                                <span class="adm-order-payment">
                                    <strong>{{ $paymentMethod }}</strong>
                                </span>
                            </td>
                            <td class="adm-order-status-col"><span class="adm-badge {{ $statusClass }}">{{ $statusLabel }}</span></td>
                            <td class="adm-order-action-col">
                                <div class="adm-actions">
                                    <a href="{{ url('/view-order/' . $order->code) }}" title="Xem"><i class="fa fa-eye"></i></a>
                                    <a href="{{ url('/track-order/' . $order->code) }}" title="Theo dõi"><i class="fa fa-map-marker"></i></a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">
                                <div class="g-admin-empty-state">Chưa có đơn hàng nào phù hợp với bộ lọc hiện tại.</div>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
                @include('admin.partials.pagination', ['paginator' => $orders, 'label' => 'đơn hàng'])
            </div>
        </div>
    </div>
</div>
@endsection
