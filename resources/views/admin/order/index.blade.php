@extends('admin_layout')

@section('admin_content')

<div class="adm-page">
    <div class="adm-page-title">
        <h1><i class="fa fa-shopping-cart" style="color:var(--adm-teal);margin-right:8px"></i>Quản lý đơn hàng</h1>
        <div class="crumb"><a href="{{ url('/dashboard') }}">Trang chủ</a> › Quản lý đơn hàng</div>
    </div>

    <div class="adm-stats">
        <div class="adm-stat">
            <div class="icon blue"><i class="fa fa-list"></i></div>
            <div><div class="num">{{ number_format($stats['total']) }}</div><div class="lbl">Tổng đơn hàng</div></div>
        </div>
        <div class="adm-stat">
            <div class="icon amber"><i class="fa fa-clock-o"></i></div>
            <div><div class="num" style="color:var(--adm-amber)">{{ number_format($stats['pending']) }}</div><div class="lbl">Chờ xác nhận</div></div>
        </div>
        <div class="adm-stat">
            <div class="icon teal"><i class="fa fa-cog"></i></div>
            <div><div class="num">{{ number_format($stats['processing']) }}</div><div class="lbl">Đang xử lý</div></div>
        </div>
        <div class="adm-stat">
            <div class="icon blue"><i class="fa fa-truck"></i></div>
            <div><div class="num">{{ number_format($stats['shipped']) }}</div><div class="lbl">Đang giao</div></div>
        </div>
        <div class="adm-stat">
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
                    <option value="shipping" @selected($filters['status'] === 'shipping')>Đang giao</option>
                    <option value="shipped" @selected($filters['status'] === 'shipped')>Đã giao</option>
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
                <button class="adm-btn outline" type="submit"><i class="fa fa-filter"></i> Lọc</button>
            </form>

            <div class="adm-table-wrap">
                <table class="adm-table">
                    <thead>
                        <tr>
                            <th>Mã đơn</th>
                            <th>Ngày tạo</th>
                            <th>Khách hàng</th>
                            <th>Sản phẩm</th>
                            <th>Tổng tiền</th>
                            <th>Thanh toán</th>
                            <th>Trạng thái</th>
                            <th>Thao tác</th>
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
                                'shipped' => ['Đã giao', 'blue'],
                                'completed' => ['Hoàn thành', 'green'],
                                'cancelled' => ['Đã hủy', 'red'],
                                'refunded' => ['Đã hoàn tiền', 'red'],
                            ];
                            [$statusLabel, $statusClass] = $statusMap[$order->status] ?? [ucfirst((string) $order->status), 'gray'];
                            $firstItem = $order->items->first();
                            $payment = $order->payments->first();
                        @endphp
                        <tr>
                            <td class="id" style="font-size:11px;font-weight:600;color:var(--adm-teal)">{{ $order->code }}</td>
                            <td style="font-size:11px;white-space:nowrap;color:var(--adm-muted)">{{ optional($order->placed_at ?? $order->created_at)->format('d/m/Y H:i') }}</td>
                            <td>
                                <div class="adm-user-cell">
                                    <div class="u-avatar">{{ mb_substr($order->customer?->name ?? 'K', 0, 1) }}</div>
                                    <div>
                                        <span class="u-name">{{ $order->customer?->name ?? 'Khách vãng lai' }}</span>
                                        <div style="font-size:10px;color:var(--adm-muted)">{{ $order->customer?->phone ?? $order->customer?->email ?? 'Không có thông tin' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td style="font-size:12px;max-width:180px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
                                {{ $firstItem?->product_name ?? 'Không có sản phẩm' }}
                                @if ($order->items_count > 1)
                                    +{{ $order->items_count - 1 }}
                                @endif
                            </td>
                            <td style="font-size:12px;font-weight:700;color:var(--adm-dark)">{{ number_format((float) $order->total, 0, ',', '.') }}đ</td>
                            <td><span style="font-size:11px;color:var(--adm-muted)">{{ strtoupper($payment?->provider ?? 'COD') }} / {{ $order->payment_status }}</span></td>
                            <td><span class="adm-badge {{ $statusClass }}">{{ $statusLabel }}</span></td>
                            <td>
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
                <div class="adm-pagination">
                    <span class="info">Hiển thị {{ $orders->count() }} / {{ $orders->total() }} đơn hàng</span>
                    <div class="pages">{{ $orders->links() }}</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
