@extends('admin_layout')

@section('admin_content')
    @php
        $couponItems = method_exists($coupon, 'getCollection') ? $coupon->getCollection() : collect($coupon);
        $activeCoupons = $couponItems->where('coupon_status', 1)->count();
        $expiredCoupons = $couponItems->filter(fn ($item) => $item->coupon_date_end < $today)->count();
    @endphp

    <div class="g-admin-page-shell g-admin-coupon-list">
        <section class="g-admin-page-hero">
            <div>
                <span class="g-admin-page-eyebrow">Coupon</span>
                <h1>Liệt kê mã giảm giá</h1>
                <p>
                    Theo dõi thời gian hiệu lực, trạng thái hoạt động và gửi coupon trực tiếp cho nhóm khách thường hoặc VIP.
                </p>
            </div>

            <div class="g-admin-page-stats">
                <article>
                    <strong>{{ number_format($coupon->total()) }}</strong>
                    <span>Tổng coupon</span>
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
                <p>Coupon có trạng thái active trong trang hiện tại.</p>
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
                    <div>Danh sách coupon</div>
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

                @if ($couponItems->isEmpty())
                    <div class="g-admin-empty-state">Chưa có mã giảm giá nào trên môi trường này.</div>
                @else
                    <div class="table-responsive">
                        <table class="table table-striped b-t b-light g-admin-data-table">
                            <thead>
                                <tr>
                                    <th>Tên mã</th>
                                    <th>Hiệu lực</th>
                                    <th>Mã coupon</th>
                                    <th>Quota</th>
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
                                            @if ($cou->coupon_date_end >= $today)
                                                <span class="g-admin-status-chip is-success">Còn hạn</span>
                                            @else
                                                <span class="g-admin-status-chip is-danger">Đã hết hạn</span>
                                            @endif
                                        </td>
                                        <td>
                                            <form action="{{ url('/delete-coupon/' . $cou->coupon_id) }}" method="POST"
                                                onsubmit="return confirm('Bạn có chắc muốn xóa mã này không?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-xs btn-danger">
                                                    <i class="fa fa-times"></i> Xóa
                                                </button>
                                            </form>
                                        </td>
                                        <td>
                                            <div class="g-admin-action-stack">
                                                <form action="{{ url('/send-coupon-vip', [
                                                    'coupon_time' => $cou->coupon_time,
                                                    'coupon_condition' => $cou->coupon_condition,
                                                    'coupon_number' => $cou->coupon_number,
                                                    'coupon_code' => $cou->coupon_code,
                                                ]) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="btn btn-primary btn-sm">Gửi khách VIP</button>
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
                            Hiển thị {{ $coupon->count() }} / {{ $coupon->total() }} coupon
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
