@extends('admin_layout')

@section('admin_content')
    @php
        $couponItems = method_exists($coupon, 'getCollection') ? $coupon->getCollection() : collect($coupon);
        $filters = $filters ?? [
            'q' => request('q', ''),
            'status' => request('status', 'all'),
            'type' => request('type', 'all'),
            'validity' => request('validity', 'all'),
            'per_page' => (int) request('per_page', 10),
        ];
        $stats = $stats ?? ['total' => $coupon->total(), 'active' => 0, 'valid' => 0, 'expired' => 0];
        $todayDate = $today instanceof \Carbon\Carbon
            ? $today->copy()->timezone('Asia/Ho_Chi_Minh')->startOfDay()
            : \Illuminate\Support\Carbon::now('Asia/Ho_Chi_Minh')->startOfDay();
        $parseCouponDate = static function ($value) {
            $value = trim((string) $value);

            if ($value === '') {
                return null;
            }

            foreach (['d/m/Y', 'Y-m-d'] as $format) {
                try {
                    return \Illuminate\Support\Carbon::createFromFormat($format, $value)->startOfDay();
                } catch (\Throwable) {
                }
            }

            try {
                return \Illuminate\Support\Carbon::parse($value)->startOfDay();
            } catch (\Throwable) {
                return null;
            }
        };
    @endphp

    <style>
        .adm-coupon-management {
            padding: 20px 16px;
        }

        .adm-coupon-management .adm-page-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 18px;
        }

        .adm-coupon-management .adm-page-title {
            display: flex;
            align-items: flex-start;
            gap: 14px;
            margin-bottom: 0;
        }

        .adm-coupon-management .adm-page-title-icon {
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

        .adm-coupon-management .adm-page-title h1 {
            margin-top: 2px;
        }

        .adm-coupon-management .adm-page-actions {
            display: flex;
            gap: 12px;
            align-items: center;
            flex-wrap: wrap;
        }

        .adm-coupon-management .adm-stats {
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 16px;
            margin-bottom: 20px;
        }

        .adm-coupon-management .adm-stat {
            min-height: 88px;
            flex-direction: row;
            align-items: center;
            gap: 14px;
            padding: 16px 18px;
            overflow: hidden;
        }

        .adm-coupon-management .adm-stat .icon {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            font-size: 21px;
        }

        .adm-coupon-management .adm-stat .num {
            font-size: 24px;
        }

        .adm-coupon-management .adm-stat .lbl {
            font-size: 12px;
        }

        .adm-coupon-management .adm-toolbar {
            display: grid;
            grid-template-columns: minmax(220px, 1fr) 160px 160px 160px 100px 112px;
            gap: 12px;
            margin-bottom: 14px;
            align-items: end;
            padding: 16px;
            background: #fff;
            border: 1px solid var(--adm-border);
            border-radius: var(--adm-radius);
            box-shadow: var(--adm-shadow);
        }

        .adm-coupon-management .adm-search,
        .adm-coupon-management .adm-select,
        .adm-coupon-management .adm-btn {
            min-height: 40px;
            height: 40px;
        }

        .adm-coupon-management .adm-btn {
            justify-content: center;
            font-size: 13px;
            padding-left: 12px;
            padding-right: 12px;
        }

        .adm-coupon-management .adm-filter-label {
            display: block;
            margin-bottom: 6px;
            font-size: 11px;
            color: var(--adm-muted);
            font-weight: 700;
        }

        .adm-coupon-management .adm-table-wrap {
            border-radius: var(--adm-radius);
            overflow-x: auto;
        }

        .adm-coupon-management .adm-table-title {
            padding: 16px 18px;
            border-bottom: 1px solid var(--adm-border);
            font-size: 16px;
            font-weight: 800;
            color: var(--adm-dark);
        }

        .adm-coupon-management .adm-table {
            width: 100%;
            min-width: 1120px;
            table-layout: fixed;
            font-size: 12.5px;
        }

        .adm-coupon-management .adm-table thead th {
            padding: 12px 10px;
            text-align: center !important;
            vertical-align: middle;
        }

        .adm-coupon-management .adm-table td {
            height: 58px;
            padding: 10px;
            text-align: center;
            vertical-align: middle;
        }

        .adm-coupon-management .adm-coupon-name-col {
            width: 150px;
            text-align: center !important;
        }

        .adm-coupon-management .adm-coupon-code-col {
            width: 128px;
        }

        .adm-coupon-management .adm-coupon-type-col {
            width: 118px;
        }

        .adm-coupon-management .adm-coupon-value-col,
        .adm-coupon-management .adm-coupon-limit-col {
            width: 90px;
            white-space: nowrap;
        }

        .adm-coupon-management .adm-coupon-date-col {
            width: 160px;
            white-space: nowrap;
        }

        .adm-coupon-management .adm-coupon-status-col,
        .adm-coupon-management .adm-coupon-validity-col {
            width: 118px;
        }

        .adm-coupon-management .adm-coupon-send-col {
            width: 190px;
        }

        .adm-coupon-management .adm-coupon-action-col {
            width: 94px;
        }

        .adm-coupon-management .adm-coupon-name {
            display: block;
            color: var(--adm-dark);
            font-weight: 800;
            line-height: 1.35;
        }

        .adm-coupon-management .adm-actions,
        .adm-coupon-management .adm-inline-form-actions {
            justify-content: center;
        }

        .adm-coupon-management .adm-actions a,
        .adm-coupon-management .adm-actions button {
            width: 34px;
            height: 34px;
            border-radius: 8px;
        }

        .adm-coupon-management .adm-actions form {
            margin: 0;
        }

        .adm-coupon-management .adm-send-group {
            display: flex;
            justify-content: center;
            gap: 6px;
            flex-wrap: wrap;
        }

        .adm-coupon-management .adm-send-group form {
            margin: 0;
        }

        .adm-coupon-management .adm-send-button {
            height: 34px;
            border-radius: 8px;
            padding: 0 10px;
            font-size: 12px;
            font-weight: 700;
            border: 1px solid var(--adm-border);
            background: #fff;
            color: var(--adm-text);
        }

        .adm-coupon-management .adm-send-button:disabled {
            opacity: .55;
            cursor: not-allowed;
        }

        @media (max-width: 1500px) {
            .adm-coupon-management .adm-toolbar {
                grid-template-columns: minmax(200px, 1fr) 150px 150px 150px 96px 108px;
            }
        }

        @media (max-width: 1100px) {
            .adm-coupon-management .adm-page-head {
                flex-direction: column;
            }

            .adm-coupon-management .adm-stats,
            .adm-coupon-management .adm-toolbar {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="adm-page adm-coupon-management">
        <div class="adm-page-head">
            <div class="adm-page-title">
                <span class="adm-page-title-icon"><i class="fa fa-ticket"></i></span>
                <div>
                    <h1>Quản lý mã giảm giá</h1>
                    <div class="crumb"><a href="{{ url('/dashboard') }}">Trang chủ</a> › Bán hàng › Mã giảm giá</div>
                </div>
            </div>
            <div class="adm-page-actions">
                <a href="{{ url('/mail-preview') }}" class="adm-btn outline"><i class="fa fa-envelope-o"></i> Xem trước email</a>
                <a href="{{ route('admin.coupons.export', request()->except('page')) }}" class="adm-btn outline"><i class="fa fa-download"></i> Xuất CSV</a>
                <a href="{{ url('/insert-coupon') }}" class="adm-btn primary"><i class="fa fa-plus"></i> Tạo mã mới</a>
            </div>
        </div>

        <div class="adm-stats">
            <div class="adm-stat" data-filter-field="status" data-filter-value="all">
                <div class="icon blue"><i class="fa fa-ticket"></i></div>
                <div><div class="num">{{ number_format($stats['total']) }}</div><div class="lbl">Tổng mã giảm giá</div></div>
            </div>
            <div class="adm-stat" data-filter-field="status" data-filter-value="active">
                <div class="icon green"><i class="fa fa-check-circle"></i></div>
                <div><div class="num">{{ number_format($stats['active']) }}</div><div class="lbl">Đang hoạt động</div></div>
            </div>
            <div class="adm-stat" data-filter-field="validity" data-filter-value="valid">
                <div class="icon amber"><i class="fa fa-clock-o"></i></div>
                <div><div class="num">{{ number_format($stats['valid']) }}</div><div class="lbl">Còn hạn</div></div>
            </div>
            <div class="adm-stat" data-filter-field="validity" data-filter-value="expired">
                <div class="icon red"><i class="fa fa-exclamation-circle"></i></div>
                <div><div class="num">{{ number_format($stats['expired']) }}</div><div class="lbl">Hết hạn</div></div>
            </div>
        </div>

        <form class="adm-toolbar" method="GET" action="{{ url('/list-coupon') }}">
            <div class="adm-search">
                <i class="fa fa-search"></i>
                <input type="text" name="q" value="{{ $filters['q'] }}" placeholder="Tìm theo tên chương trình hoặc mã giảm giá...">
            </div>
            <div>
                <label class="adm-filter-label" for="coupon-status">Trạng thái</label>
                <select id="coupon-status" class="adm-select" name="status">
                    <option value="all" @selected($filters['status'] === 'all')>Tất cả</option>
                    <option value="active" @selected($filters['status'] === 'active')>Đang hoạt động</option>
                    <option value="inactive" @selected($filters['status'] === 'inactive')>Đã khóa</option>
                </select>
            </div>
            <div>
                <label class="adm-filter-label" for="coupon-type">Loại giảm</label>
                <select id="coupon-type" class="adm-select" name="type">
                    <option value="all" @selected($filters['type'] === 'all')>Tất cả</option>
                    <option value="percent" @selected($filters['type'] === 'percent')>Giảm theo %</option>
                    <option value="fixed" @selected($filters['type'] === 'fixed')>Giảm theo tiền</option>
                </select>
            </div>
            <div>
                <label class="adm-filter-label" for="coupon-validity">Hiệu lực</label>
                <select id="coupon-validity" class="adm-select" name="validity">
                    <option value="all" @selected($filters['validity'] === 'all')>Tất cả</option>
                    <option value="valid" @selected($filters['validity'] === 'valid')>Còn hạn</option>
                    <option value="expired" @selected($filters['validity'] === 'expired')>Đã hết hạn</option>
                </select>
            </div>

            <button class="adm-btn primary" type="submit"><i class="fa fa-filter"></i> Lọc</button>
            <a class="adm-btn outline" href="{{ url('/list-coupon') }}"><i class="fa fa-refresh"></i> Xóa lọc</a>
        </form>

        <div class="adm-table-wrap">
            <div class="adm-table-title">Danh sách mã giảm giá</div>

            @if (session('message'))
                <div class="alert alert-success" style="margin:16px">{{ session('message') }}</div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger" style="margin:16px">{{ session('error') }}</div>
            @endif

            @if ($couponItems->isEmpty())
                <div class="g-admin-empty-state">Chưa có mã giảm giá nào phù hợp với bộ lọc hiện tại.</div>
            @else
                <table class="adm-table">
                    <thead>
                        <tr>
                            <th class="adm-coupon-name-col">@include('admin.partials.sort_header', ['label' => 'Tên chương trình', 'asc' => 'oldest', 'desc' => 'newest', 'filters' => $filters])</th>
                            <th class="adm-coupon-code-col">Mã giảm giá</th>
                            <th class="adm-coupon-type-col">Loại giảm</th>
                            <th class="adm-coupon-value-col">@include('admin.partials.sort_header', ['label' => 'Giá trị', 'asc' => 'value_asc', 'desc' => 'value_desc', 'filters' => $filters])</th>
                            <th class="adm-coupon-limit-col">@include('admin.partials.sort_header', ['label' => 'Lượt dùng', 'asc' => 'usage_asc', 'desc' => 'usage_desc', 'filters' => $filters])</th>
                            <th class="adm-coupon-date-col">@include('admin.partials.sort_header', ['label' => 'Thời gian hiệu lực', 'asc' => 'expiry_asc', 'desc' => 'expiry_desc', 'filters' => $filters])</th>
                            <th class="adm-coupon-status-col">Trạng thái</th>
                            <th class="adm-coupon-validity-col">Hạn dùng</th>
                            <th class="adm-coupon-send-col">Gửi email</th>
                            <th class="adm-coupon-action-col">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($coupon as $cou)
                            @php
                                $couponEndDate = $parseCouponDate($cou->coupon_date_end ?? null);
                                $couponLocked = (int) ($cou->coupon_status ?? 0) !== 1;
                                $couponExpired = ! ($couponEndDate && $couponEndDate->gte($todayDate));
                                $campaignBlockedReason = $couponLocked
                                    ? 'Mã giảm giá đang bị khóa'
                                    : ($couponExpired ? 'Mã giảm giá đã hết hạn' : null);
                            @endphp
                            <tr>
                                <td class="adm-coupon-name-col"><span class="adm-coupon-name">{{ $cou->coupon_name }}</span></td>
                                <td class="adm-coupon-code-col"><strong>{{ $cou->coupon_code }}</strong></td>
                                <td class="adm-coupon-type-col">
                                    @if ($cou->coupon_condition == 1)
                                        <span class="adm-badge blue">Giảm theo %</span>
                                    @else
                                        <span class="adm-badge blue">Giảm theo tiền</span>
                                    @endif
                                </td>
                                <td class="adm-coupon-value-col">
                                    @if ($cou->coupon_condition == 1)
                                        {{ $cou->coupon_number }}%
                                    @else
                                        {{ number_format($cou->coupon_number, 0, ',', '.') }}đ
                                    @endif
                                </td>
                                <td class="adm-coupon-limit-col">{{ number_format((int) $cou->coupon_time) }}</td>
                                <td class="adm-coupon-date-col">{{ $cou->coupon_date_start }} - {{ $cou->coupon_date_end }}</td>
                                <td class="adm-coupon-status-col">
                                    @if ($cou->coupon_status == 1)
                                        <span class="adm-badge green">Đang hoạt động</span>
                                    @else
                                        <span class="adm-badge red">Đã khóa</span>
                                    @endif
                                </td>
                                <td class="adm-coupon-validity-col">
                                    @if ($couponEndDate && $couponEndDate->gte($todayDate))
                                        <span class="adm-badge green">Còn hạn</span>
                                    @else
                                        <span class="adm-badge red">Đã hết hạn</span>
                                    @endif
                                </td>
                                <td class="adm-coupon-send-col">
                                    @if ($campaignBlockedReason)
                                        <button type="button" class="adm-send-button" disabled title="{{ $campaignBlockedReason }}">
                                            <i class="fa fa-paper-plane-o"></i> Không thể gửi
                                        </button>
                                    @else
                                        <div class="adm-send-group">
                                            <form action="{{ url('/send-coupon', [
                                                'coupon_time' => $cou->coupon_time,
                                                'coupon_condition' => $cou->coupon_condition,
                                                'coupon_number' => $cou->coupon_number,
                                                'coupon_code' => $cou->coupon_code,
                                            ]) }}" method="POST" onsubmit="return confirm('Gửi mã {{ $cou->coupon_code }} cho tất cả khách hàng đã đăng ký có email hợp lệ?')">
                                                @csrf
                                                <button type="submit" class="adm-send-button">
                                                    <i class="fa fa-paper-plane-o"></i> Tất cả
                                                </button>
                                            </form>
                                            <form action="{{ url('/send-coupon-vip', [
                                                'coupon_time' => $cou->coupon_time,
                                                'coupon_condition' => $cou->coupon_condition,
                                                'coupon_number' => $cou->coupon_number,
                                                'coupon_code' => $cou->coupon_code,
                                            ]) }}" method="POST" onsubmit="return confirm('Gửi mã {{ $cou->coupon_code }} cho khách hàng thân thiết?')">
                                                @csrf
                                                <button type="submit" class="adm-send-button">
                                                    <i class="fa fa-star-o"></i> VIP
                                                </button>
                                            </form>
                                        </div>
                                    @endif
                                </td>
                                <td class="adm-coupon-action-col">
                                    <div class="adm-actions">
                                        <a href="{{ url('/edit-coupon/' . $cou->coupon_id) }}" title="Sửa"><i class="fa fa-pencil"></i></a>
                                        <form action="{{ url('/delete-coupon/' . $cou->coupon_id) }}" method="POST" onsubmit="return confirm('Bạn có chắc muốn xóa mã này không?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" title="Xóa"><i class="fa fa-trash-o"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif

            @include('admin.partials.pagination', ['paginator' => $coupon, 'label' => 'mã giảm giá'])
        </div>
    </div>
@endsection
