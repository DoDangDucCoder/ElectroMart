@extends('admin_layout')

@section('admin_content')

@php
    $statusLabels = [
        'pending' => ['label' => 'Chờ thanh toán', 'class' => 'amber'],
        'processing' => ['label' => 'Đang xử lý', 'class' => 'blue'],
        'succeeded' => ['label' => 'Thành công', 'class' => 'green'],
        'failed' => ['label' => 'Thất bại', 'class' => 'red'],
        'refunded' => ['label' => 'Đã hoàn tiền', 'class' => 'purple'],
    ];

    $methodLabels = [
        'cod' => 'COD',
        'cash' => 'Tiền mặt',
        'bank_transfer' => 'Chuyển khoản',
        'card' => 'Thẻ',
        'momo' => 'MoMo',
        'vnpay' => 'VNPay',
        'paypal' => 'PayPal',
    ];

    $formatMoney = static fn ($amount, $currency = 'VND') => number_format((float) $amount, 0, ',', '.') . ($currency === 'VND' ? 'đ' : ' ' . $currency);
@endphp

<div class="adm-page">
    <div class="adm-page-title">
        <h1><i class="fa fa-credit-card" style="color:var(--adm-blue);margin-right:6px"></i>Thanh toán / giao dịch</h1>
        <div class="crumb"><a href="{{ url('/dashboard') }}">Trang chủ</a> / Đơn hàng / Thanh toán</div>
    </div>

    <div class="adm-stats">
        <div class="adm-stat"><div class="icon blue"><i class="fa fa-credit-card"></i></div><div><div class="num">{{ number_format($stats['total']) }}</div><div class="lbl">Tổng thanh toán</div></div></div>
        <div class="adm-stat"><div class="icon amber"><i class="fa fa-clock-o"></i></div><div><div class="num">{{ number_format($stats['pending']) }}</div><div class="lbl">Chờ thanh toán</div></div></div>
        <div class="adm-stat"><div class="icon teal"><i class="fa fa-spinner"></i></div><div><div class="num">{{ number_format($stats['processing']) }}</div><div class="lbl">Đang xử lý</div></div></div>
        <div class="adm-stat"><div class="icon green"><i class="fa fa-check-circle"></i></div><div><div class="num">{{ number_format($stats['succeeded']) }}</div><div class="lbl">Thành công</div></div></div>
        <div class="adm-stat"><div class="icon red"><i class="fa fa-exclamation-circle"></i></div><div><div class="num">{{ number_format($stats['failed_or_refunded']) }}</div><div class="lbl">Lỗi / hoàn tiền</div></div></div>
        <div class="adm-stat"><div class="icon purple"><i class="fa fa-money"></i></div><div><div class="num" style="font-size:18px">{{ $formatMoney($stats['total_amount']) }}</div><div class="lbl">Tổng giá trị</div></div></div>
    </div>

    <form method="GET" action="{{ route('admin.transactions') }}" class="adm-toolbar" style="align-items:flex-end;flex-wrap:wrap">
        <div class="adm-search" style="min-width:320px">
            <i class="fa fa-search"></i>
            <input type="text" name="q" value="{{ $filters['q'] }}" placeholder="Tìm mã thanh toán, đơn hàng, khách hàng...">
        </div>
        <div>
            <label class="adm-filter-label" for="payment-method">Phương thức</label>
            <select id="payment-method" name="method" class="adm-select">
                <option value="all" @selected($filters['method'] === 'all')>Tất cả</option>
                @foreach ($methods as $method)
                    <option value="{{ $method }}" @selected($filters['method'] === $method)>{{ $methodLabels[$method] ?? strtoupper($method) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="adm-filter-label" for="payment-status">Trạng thái</label>
            <select id="payment-status" name="status" class="adm-select">
                <option value="all" @selected($filters['status'] === 'all')>Tất cả</option>
                @foreach ($statuses as $status)
                    <option value="{{ $status }}" @selected($filters['status'] === $status)>{{ $statusLabels[$status]['label'] ?? $status }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="adm-filter-label" for="payment-date-from">Từ ngày</label>
            <input id="payment-date-from" type="date" name="date_from" value="{{ $filters['date_from'] }}" class="adm-select">
        </div>
        <div>
            <label class="adm-filter-label" for="payment-date-to">Đến ngày</label>
            <input id="payment-date-to" type="date" name="date_to" value="{{ $filters['date_to'] }}" class="adm-select">
        </div>
        <div>
            <label class="adm-filter-label" for="payment-per-page">Mỗi trang</label>
            <select id="payment-per-page" name="per_page" class="adm-select">
                @foreach ([15, 25, 50] as $size)
                    <option value="{{ $size }}" @selected((int) $filters['per_page'] === $size)>{{ $size }}</option>
                @endforeach
            </select>
        </div>
        <button class="adm-btn primary" type="submit"><i class="fa fa-filter"></i> Lọc</button>
        <a class="adm-btn outline" href="{{ route('admin.transactions') }}"><i class="fa fa-refresh"></i> Xóa lọc</a>
    </form>

    <div class="adm-table-wrap">
        <table class="adm-table">
            <thead>
                <tr>
                    <th>Thanh toán</th>
                    <th>Đơn hàng</th>
                    <th>Khách hàng</th>
                    <th>Phương thức</th>
                    <th>Trạng thái</th>
                    <th>Số tiền</th>
                    <th>Phí</th>
                    <th>Ngày tạo</th>
                    <th>Đã thanh toán</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($payments as $payment)
                    @php
                        $status = $statusLabels[$payment->status] ?? ['label' => $payment->status, 'class' => 'gray'];
                        $method = $methodLabels[$payment->provider] ?? strtoupper((string) $payment->provider);
                        $customer = $payment->order?->customer;
                        $customerName = $customer?->name ?? 'Khách đã xóa';
                    @endphp
                    <tr>
                        <td>
                            <strong class="id">#{{ $payment->id }}</strong>
                            @if ($payment->reference)
                                <small style="display:block;color:var(--adm-muted);margin-top:4px">Ref: {{ $payment->reference }}</small>
                            @endif
                        </td>
                        <td>
                            @if ($payment->order)
                                <a href="{{ url('/view-order/' . $payment->order->code) }}" class="dd-link">{{ $payment->order->code }}</a>
                                <small style="display:block;color:var(--adm-muted)">Đơn hàng: {{ $payment->order->status }} / {{ $payment->order->payment_status }}</small>
                            @else
                                <span style="color:var(--adm-muted)">Đơn đã xóa</span>
                            @endif
                        </td>
                        <td>
                            <div class="adm-user-cell">
                                <div class="u-avatar">{{ mb_substr($customerName, 0, 1) }}</div>
                                <span class="u-name">{{ $customerName }}</span>
                            </div>
                            @if ($customer?->email)
                                <small style="display:block;color:var(--adm-muted);margin-top:4px">{{ $customer->email }}</small>
                            @endif
                        </td>
                        <td>
                            <strong>{{ $method }}</strong>
                            <small style="display:block;color:var(--adm-muted)">{{ strtoupper((string) $payment->currency) }}</small>
                        </td>
                        <td><span class="adm-badge {{ $status['class'] }}">{{ $status['label'] }}</span></td>
                        <td><strong>{{ $formatMoney($payment->amount, $payment->currency) }}</strong></td>
                        <td>{{ $formatMoney($payment->fee, $payment->currency) }}</td>
                        <td style="white-space:nowrap">{{ $payment->created_at?->format('d/m/Y H:i') }}</td>
                        <td style="white-space:nowrap">
                            @if ($payment->paid_at)
                                {{ $payment->paid_at->format('d/m/Y H:i') }}
                            @else
                                <span style="color:var(--adm-muted)">Chưa ghi nhận</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" style="text-align:center;padding:36px;color:var(--adm-muted)">
                            <i class="fa fa-credit-card" style="font-size:28px;display:block;margin-bottom:8px"></i>
                            Chưa có thanh toán phù hợp với bộ lọc hiện tại.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @include('admin.partials.pagination', ['paginator' => $payments, 'label' => 'thanh toán'])
    </div>
</div>
@endsection
