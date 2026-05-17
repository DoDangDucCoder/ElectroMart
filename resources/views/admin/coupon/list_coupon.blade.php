@extends('admin_layout')

@section('admin_content')
    @php
        $couponItems = method_exists($coupon, 'getCollection') ? $coupon->getCollection() : collect($coupon);
        $activeCoupons = $couponItems->where('coupon_status', 1)->count();
        $todayDate = \Illuminate\Support\Carbon::now('Asia/Ho_Chi_Minh')->startOfDay();
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
        $expiredCoupons = $couponItems->filter(function ($item) use ($parseCouponDate, $todayDate) {
            $couponEndDate = $parseCouponDate($item->coupon_date_end ?? null);

            return $couponEndDate ? $couponEndDate->lt($todayDate) : false;
        })->count();
    @endphp

    <div class="g-admin-page-shell g-admin-coupon-list">
        <section class="g-admin-page-hero">
            <div>
                <span class="g-admin-page-eyebrow">Mã giảm giá</span>
                <h1>Liệt kê mã giảm giá</h1>
                <p>
                    Theo dõi thời gian hiệu lực, trạng thái hoạt động và gửi mã giảm giá trực tiếp cho nhóm khách thường hoặc khách thân thiết.
                </p>
            </div>

            <div class="g-admin-page-stats">
                <article>
                    <strong>{{ number_format($coupon->total()) }}</strong>
                    <span>Tổng mã giảm giá</span>
                </article>
                <article>
                    <strong>{{ number_format($activeCoupons) }}</strong>
                    <span>Đang kích hoạt trên trang hiện tại</span>
                </article>
            </div>
        </section>

        <section class="g-admin-kpi-grid">
            <article class="g-admin-kpi-card">
                <span>Đang hoạt động</span>
                <strong>{{ number_format($activeCoupons) }}</strong>
                <p>Mã giảm giá có trạng thái đang hoạt động trong trang hiện tại.</p>
            </article>
            <article class="g-admin-kpi-card">
                <span>Hết hạn</span>
                <strong>{{ number_format($expiredCoupons) }}</strong>
                <p>Cần rà soát hoặc dọn khỏi chiến dịch đang chạy.</p>
            </article>
        </section>

        <section class="g-admin-panel-card">
            <div class="g-admin-panel-head">
                <div>
                    <div>Danh sách mã giảm giá</div>
                    <p>Theo dõi hiệu lực, trạng thái và gửi mã ưu đãi tới các nhóm khách hàng phù hợp.</p>
                </div>
                <div class="g-admin-inline-actions">
                    <a href="{{ url('/mail-preview') }}" class="btn btn-default btn-sm">Xem trước email</a>
                    <a href="{{ url('/insert-coupon') }}" class="btn btn-primary btn-sm">Tạo mã mới</a>
                </div>
            </div>
            <div class="g-admin-panel-body">
                @if (session('message'))
                    <div class="alert alert-success">{{ session('message') }}</div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                @if ($couponItems->isEmpty())
                    <div class="g-admin-empty-state">Chưa có mã giảm giá nào trên môi trường này.</div>
                @else
                    <div class="table-responsive">
                        <table class="table table-striped b-t b-light g-admin-data-table">
                            <thead>
                                <tr>
                                    <th>Tên mã</th>
                                    <th>Hiệu lực</th>
                                    <th>Mã giảm giá</th>
                                    <th>Số lượt</th>
                                    <th>Loại giảm</th>
                                    <th>Giá trị</th>
                                    <th>Trạng thái</th>
                                    <th>Hạn dùng</th>
                                    <th>Quản lý</th>
                                    <th>Gửi mã</th>
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
                                        <td>
                                            <div class="g-admin-order-line-name">{{ $cou->coupon_name }}</div>
                                        </td>
                                        <td>
                                            <div>{{ $cou->coupon_date_start }}</div>
                                            <small class="text-muted">{{ $cou->coupon_date_end }}</small>
                                        </td>
                                        <td><code>{{ $cou->coupon_code }}</code></td>
                                        <td>{{ $cou->coupon_time }}</td>
                                        <td>
                                            @if ($cou->coupon_condition == 1)
                                                <span class="g-admin-status-chip is-info">Giảm theo %</span>
                                            @else
                                                <span class="g-admin-status-chip">Giảm theo tiền</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($cou->coupon_condition == 1)
                                                Giảm {{ $cou->coupon_number }}%
                                            @else
                                                Giảm {{ number_format($cou->coupon_number, 0, ',', '.') }}đ
                                            @endif
                                        </td>
                                        <td>
                                            @if ($cou->coupon_status == 1)
                                                <span class="g-admin-status-chip is-success">Đang kích hoạt</span>
                                            @else
                                                <span class="g-admin-status-chip is-danger">Đã khóa</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($couponEndDate && $couponEndDate->gte($todayDate))
                                                <span class="g-admin-status-chip is-success">Còn hạn</span>
                                            @else
                                                <span class="g-admin-status-chip is-danger">Đã hết hạn</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="g-admin-action-stack">
                                                <a href="{{ url('/edit-coupon/' . $cou->coupon_id) }}" class="btn btn-xs btn-info">
                                                    <i class="fa fa-pencil"></i> Sửa
                                                </a>
                                                <form action="{{ url('/delete-coupon/' . $cou->coupon_id) }}" method="POST"
                                                    onsubmit="return confirm('Bạn có chắc muốn xóa mã này không?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-xs btn-danger">
                                                        <i class="fa fa-times"></i> Xóa
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="g-admin-action-stack">
                                                @if ($campaignBlockedReason)
                                                    <button type="button" class="btn btn-primary btn-sm" disabled>Gửi khách thân thiết</button>
                                                    <button type="button" class="btn btn-default btn-sm" disabled>Gửi khách thường</button>
                                                    <small class="text-muted">{{ $campaignBlockedReason }}</small>
                                                @else
                                                    <form action="{{ url('/send-coupon-vip', [
                                                        'coupon_time' => $cou->coupon_time,
                                                        'coupon_condition' => $cou->coupon_condition,
                                                        'coupon_number' => $cou->coupon_number,
                                                        'coupon_code' => $cou->coupon_code,
                                                    ]) }}" method="POST">
                                                        @csrf
                                                        <button type="submit" class="btn btn-primary btn-sm">Gửi khách thân thiết</button>
                                                    </form>

                                                    <form action="{{ url('/send-coupon', [
                                                        'coupon_time' => $cou->coupon_time,
                                                        'coupon_condition' => $cou->coupon_condition,
                                                        'coupon_number' => $cou->coupon_number,
                                                        'coupon_code' => $cou->coupon_code,
                                                    ]) }}" method="POST">
                                                        @csrf
                                                        <button type="submit" class="btn btn-default btn-sm">Gửi khách thường</button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
            <div class="panel-footer">
                <div class="row">
                    <div class="col-sm-5 text-center">
                        <small class="text-muted inline m-t-sm m-b-sm">
                            Hiển thị {{ $coupon->count() }} / {{ $coupon->total() }} mã giảm giá
                        </small>
                    </div>
                    <div class="col-sm-7 text-right text-center-xs">
                        {{ $coupon->links() }}
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
