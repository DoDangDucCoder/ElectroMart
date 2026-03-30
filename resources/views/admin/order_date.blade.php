@extends('admin_layout')

@section('admin_content')
    <div class="g-admin-page-shell g-admin-order-date-list">
        <section class="g-admin-page-hero">
            <div>
                <span class="g-admin-page-eyebrow">Theo ngày</span>
                <h1>Đơn hàng theo ngày</h1>
                <p>Danh sách đơn hàng được lọc theo khoảng thời gian đã chọn từ trang tổng quan.</p>
            </div>

            <div class="g-admin-page-stats">
                <article>
                    <strong>{{ number_format($order->count()) }}</strong>
                    <span>Đơn hàng tìm thấy</span>
                </article>
            </div>
        </section>

        <section class="g-admin-panel-card">
            <div class="g-admin-panel-head">
                <div>
                    <div>Danh sách đơn hàng</div>
                    <p>Theo dõi nhanh các đơn hàng khớp với mốc thời gian đang xem.</p>
                </div>
            </div>
            <div class="g-admin-panel-body">
                @if (session('message'))
                    <div class="alert alert-success">{{ session('message') }}</div>
                @endif

                @if ($order->isEmpty())
                    <div class="g-admin-empty-state">Không có đơn hàng nào khớp với ngày đã chọn.</div>
                @else
                    <div class="table-responsive">
                        <table class="table table-striped b-t b-light g-admin-data-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Mã đơn</th>
                                    <th>Ngày đặt</th>
                                    <th>Trạng thái</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($order as $index => $ord)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $ord->order_code }}</td>
                                        <td>{{ $ord->created_at }}</td>
                                        <td>
                                            @if ($ord->order_status == 1)
                                                <span class="g-admin-status-chip is-warning">Đơn hàng mới</span>
                                            @elseif($ord->order_status == 2)
                                                <span class="g-admin-status-chip is-success">Đã xử lý</span>
                                            @else
                                                <span class="g-admin-status-chip is-danger">Đã hủy</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="g-admin-order-table-actions">
                                                <a href="{{ URL::to('/view-order/' . $ord->order_code) }}" class="btn btn-xs btn-success">
                                                    <i class="fa fa-eye"></i> Xem
                                                </a>
                                                <form action="{{ url('/delete-order/' . $ord->order_code) }}" method="POST"
                                                    onsubmit="return confirm('Bạn có chắc muốn xóa đơn hàng này không?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-xs btn-danger">
                                                        <i class="fa fa-times"></i> Xóa
                                                    </button>
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
        </section>
    </div>
@endsection
