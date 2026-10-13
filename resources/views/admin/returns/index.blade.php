@extends('admin_layout')

@section('admin_content')

@php
    $statusLabels = [
        'pending' => ['label' => 'Chờ xử lý', 'class' => 'amber'],
        'approved' => ['label' => 'Đã duyệt trả hàng', 'class' => 'green'],
        'rejected' => ['label' => 'Từ chối', 'class' => 'red'],
        'refunded' => ['label' => 'Đã hoàn tiền', 'class' => 'blue'],
    ];
    $statusHints = [
        'pending' => 'Yêu cầu mới, admin cần kiểm tra lý do và sản phẩm.',
        'approved' => 'Yêu cầu hợp lệ, chờ nhận hàng hoặc xử lý hoàn tiền.',
        'rejected' => 'Không đủ điều kiện trả hàng/hoàn tiền.',
        'refunded' => 'Đã hoàn tiền, đơn hàng được ghi nhận trạng thái hoàn tiền.',
    ];
    $refundMethodLabels = [
        'cash' => 'Tiền mặt',
        'cod' => 'COD',
        'bank_transfer' => 'Chuyển khoản',
        'transfer' => 'Chuyển khoản',
        'card' => 'Thẻ',
        'momo' => 'MoMo',
        'vnpay' => 'VNPay',
        'paypal' => 'PayPal',
    ];
@endphp

<style>
.adm-return-table{table-layout:fixed;width:100%}
.adm-return-table th,.adm-return-table td{vertical-align:middle}
.adm-return-table .col-code{width:180px}
.adm-return-table .col-order{width:220px}
.adm-return-table .col-product{width:auto;min-width:260px}
.adm-return-table .col-qty{width:86px;text-align:center}
.adm-return-table .col-refund{width:150px}
.adm-return-table .col-status{width:128px;text-align:center}
.adm-return-table .col-action{width:190px}
.adm-return-table .cell-muted{display:block;color:var(--adm-muted);font-size:12px;line-height:1.35;margin-top:4px}
.adm-return-table .cell-strong{font-weight:800;color:var(--adm-text,#0f172a)}
.adm-return-table .reason{margin-top:8px;color:var(--adm-muted);line-height:1.45;word-break:break-word}
.adm-return-table .return-action-form{display:flex;gap:6px;align-items:center;justify-content:flex-end}
.adm-return-table .return-action-form .adm-select{min-width:128px}
@media(max-width:1280px){.adm-table-wrap{overflow-x:auto}.adm-return-table{min-width:1120px}}
</style>

<div class="adm-page">
    <div class="adm-page-title">
        <h1><i class="fa fa-undo" style="color:var(--adm-blue);margin-right:6px"></i>Quản lý trả hàng / hoàn tiền</h1>
        <div class="crumb"><a href="{{ url('/dashboard') }}">Trang chủ</a> / Đơn hàng / Trả hàng</div>
    </div>

    @if (session('message'))
        <div class="alert alert-success">{{ session('message') }}</div>
    @endif

    <div class="adm-stats">
        <div class="adm-stat" data-filter-field="status" data-filter-value="all"><div class="icon blue"><i class="fa fa-clipboard"></i></div><div><div class="num">{{ number_format($stats['total']) }}</div><div class="lbl">Tổng yêu cầu</div></div></div>
        <div class="adm-stat" data-filter-field="status" data-filter-value="pending"><div class="icon amber"><i class="fa fa-clock-o"></i></div><div><div class="num">{{ number_format($stats['pending']) }}</div><div class="lbl">Chờ xử lý</div></div></div>
        <div class="adm-stat" data-filter-field="status" data-filter-value="approved"><div class="icon green"><i class="fa fa-check-circle"></i></div><div><div class="num">{{ number_format($stats['approved']) }}</div><div class="lbl">Đã duyệt trả hàng</div></div></div>
        <div class="adm-stat" data-filter-field="status" data-filter-value="rejected"><div class="icon red"><i class="fa fa-ban"></i></div><div><div class="num">{{ number_format($stats['rejected']) }}</div><div class="lbl">Từ chối</div></div></div>
        <div class="adm-stat" data-filter-field="status" data-filter-value="refunded"><div class="icon teal"><i class="fa fa-money"></i></div><div><div class="num">{{ number_format($stats['refunded']) }}</div><div class="lbl">Đã hoàn tiền</div></div></div>
    </div>

    <form method="GET" action="{{ route('admin.returns') }}" class="adm-toolbar" style="align-items:flex-end;flex-wrap:wrap">
        <div class="adm-search">
            <i class="fa fa-search"></i>
            <input type="text" name="q" value="{{ $filters['q'] }}" placeholder="Tìm mã trả hàng, đơn hàng, khách, lý do...">
        </div>
        <div>
            <label class="adm-filter-label" for="return-status">Trạng thái</label>
            <select id="return-status" name="status" class="adm-select">
                <option value="all" @selected($filters['status'] === 'all')>Tất cả</option>
                @foreach ($statuses as $status)
                    <option value="{{ $status }}" @selected($filters['status'] === $status)>{{ $statusLabels[$status]['label'] ?? 'Không rõ' }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="adm-filter-label" for="return-date-from">Từ ngày</label>
            <input id="return-date-from" type="date" name="date_from" value="{{ $filters['date_from'] }}" class="adm-select">
        </div>
        <div>
            <label class="adm-filter-label" for="return-date-to">Đến ngày</label>
            <input id="return-date-to" type="date" name="date_to" value="{{ $filters['date_to'] }}" class="adm-select">
        </div>

        <input type="hidden" name="sort" value="{{ $filters['sort'] ?? 'newest' }}">

        <button class="adm-btn primary" type="submit"><i class="fa fa-filter"></i> Lọc</button>
        <a class="adm-btn outline" href="{{ route('admin.returns') }}"><i class="fa fa-refresh"></i> Xóa lọc</a>
    </form>

    <div class="adm-table-wrap">
        <table class="adm-table adm-return-table">
            <thead>
                <tr>
                    <th class="col-code">@include('admin.partials.sort_header', ['label' => 'Mã trả hàng', 'asc' => 'oldest', 'desc' => 'newest', 'filters' => $filters])</th>
                    <th class="col-order">Đơn hàng / Khách hàng</th>
                    <th class="col-product">Sản phẩm / Lý do</th>
                    <th class="col-qty">Số lượng</th>
                    <th class="col-refund">@include('admin.partials.sort_header', ['label' => 'Hoàn tiền', 'asc' => 'refund_asc', 'desc' => 'refund_desc', 'filters' => $filters])</th>
                    <th class="col-status">Trạng thái</th>
                    <th class="col-action">Xử lý</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($returns as $return)
                    @php
                        $status = $statusLabels[$return->status] ?? ['label' => 'Không rõ', 'class' => 'gray'];
                        $customerName = $return->customer?->name ?? 'Khách đã xóa';
                        $refundMethod = $return->refund_method ? ($refundMethodLabels[$return->refund_method] ?? 'Không rõ') : null;
                    @endphp
                    <tr>
                        <td>
                            <div class="id">{{ $return->code }}</div>
                            <span class="cell-muted">{{ $return->created_at?->format('d/m/Y H:i') }}</span>
                        </td>
                        <td>
                            @if ($return->order)
                                <a href="{{ url('/view-order/' . $return->order->code) }}" class="dd-link">{{ $return->order->code }}</a>
                                <span class="cell-muted">Tổng đơn: {{ number_format((float) $return->order->total, 0, ',', '.') }}đ</span>
                            @else
                                <span style="color:var(--adm-muted)">Đơn đã xóa</span>
                            @endif
                            <div class="cell-strong" style="margin-top:8px">{{ $customerName }}</div>
                            @if ($return->customer?->email)
                                <span class="cell-muted">{{ $return->customer->email }}</span>
                            @endif
                        </td>
                        <td>
                            <strong>{{ $return->orderItem?->product_name ?? 'Toàn bộ đơn / chưa chỉ định' }}</strong>
                            @if ($return->orderItem?->sku)
                                <span class="cell-muted">Mã tồn kho: {{ $return->orderItem->sku }}</span>
                            @endif
                            <div class="reason">{{ \Illuminate\Support\Str::limit((string) ($return->reason ?? ''), 140) }}</div>
                        </td>
                        <td style="text-align:center">{{ number_format($return->quantity) }}</td>
                        <td style="white-space:nowrap">
                            <strong>{{ number_format((float) $return->refund_amount, 0, ',', '.') }}đ</strong>
                            @if ($refundMethod)
                                <span class="cell-muted">{{ $refundMethod }}</span>
                            @endif
                            @if ($return->refunded_at)
                                <span class="cell-muted">Hoàn: {{ $return->refunded_at->format('d/m/Y H:i') }}</span>
                            @endif
                        </td>
                        <td>
                            <span class="adm-badge {{ $status['class'] }}">{{ $status['label'] }}</span>
                            <span class="cell-muted">{{ $statusHints[$return->status] ?? '' }}</span>
                        </td>
                        <td>
                            <form method="POST" action="{{ route('admin.returns.status', array_merge(['orderReturn' => $return], request()->query())) }}" class="return-action-form">
                                @csrf
                                @method('PATCH')
                                <select name="status" class="adm-select" style="height:32px">
                                    @foreach ($statuses as $nextStatus)
                                        <option value="{{ $nextStatus }}" @selected($return->status === $nextStatus)>{{ $statusLabels[$nextStatus]['label'] ?? 'Không rõ' }}</option>
                                    @endforeach
                                </select>
                                <button type="submit" class="adm-btn outline" style="height:32px;padding:0 10px"><i class="fa fa-check"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align:center;padding:36px;color:var(--adm-muted)">
                            <i class="fa fa-undo" style="font-size:28px;display:block;margin-bottom:8px"></i>
                            Chưa có yêu cầu trả hàng phù hợp với bộ lọc hiện tại.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @include('admin.partials.pagination', ['paginator' => $returns, 'label' => 'yêu cầu'])
    </div>
</div>
@endsection
