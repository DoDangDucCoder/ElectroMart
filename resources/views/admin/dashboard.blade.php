@extends('admin_layout')

@section('admin_content')
    <div class="g-admin-page-shell g-admin-dashboard">
        <section class="g-admin-page-hero">
            <div>
                <span class="g-admin-page-eyebrow">Tổng quan</span>
                <h1>Tổng quan vận hành</h1>
                <p>
                    Theo dõi nhanh doanh số, lượt truy cập và những nội dung đang có mức quan tâm cao nhất
                    trong hệ thống.
                </p>
            </div>

            <div class="g-admin-page-stats">
                <article>
                    <strong>{{ number_format($dashboard_totals['orders'] ?? 0) }}</strong>
                    <span>Đơn hàng đang quản lý</span>
                </article>
                <article>
                    <strong>{{ number_format($visitors_total) }}</strong>
                    <span>Tổng lượt truy cập ghi nhận</span>
                </article>
            </div>
        </section>

        <section class="g-admin-kpi-grid">
            <article class="g-admin-kpi-card">
                <span>Đang online</span>
                <strong>{{ number_format($visitor_count) }}</strong>
                <p>Khách đang được ghi nhận từ IP hiện tại.</p>
            </article>
            <article class="g-admin-kpi-card">
                <span>Tháng trước</span>
                <strong>{{ number_format($visitor_last_month_count) }}</strong>
                <p>Lượt truy cập ở tháng trước.</p>
            </article>
            <article class="g-admin-kpi-card">
                <span>Tháng này</span>
                <strong>{{ number_format($visitor_this_month_count) }}</strong>
                <p>Lượt truy cập từ đầu tháng đến nay.</p>
            </article>
            <article class="g-admin-kpi-card">
                <span>Một năm</span>
                <strong>{{ number_format($visitor_year_count) }}</strong>
                <p>Lượt truy cập trong 365 ngày gần nhất.</p>
            </article>
            @if (!empty($chat_inbox_stats))
                <article class="g-admin-kpi-card g-admin-kpi-card-chat" data-admin-chat-dashboard-card>
                    <span>Hộp thư chat</span>
                    <strong>{{ number_format($chat_inbox_stats['unread_total']) }}</strong>
                    <p>
                        {{ number_format($chat_inbox_stats['my_unread']) }} hội thoại chưa đọc đang thuộc phụ trách của bạn.
                    </p>
                    <a href="{{ url('/chat-inbox?scope=mine') }}" class="g-admin-inline-link">
                        Xem hội thoại của tôi
                    </a>
                </article>
            @endif
        </section>

        <section class="g-admin-panel-card">
            <div class="g-admin-panel-head">
                <div>
                    <div>Thống kê đơn hàng và doanh số</div>
                    <p>Chọn khoảng thời gian hoặc bộ lọc nhanh để xem doanh số, lợi nhuận và số lượng đơn hàng.</p>
                </div>
            </div>
            <div class="g-admin-panel-body">
                <form autocomplete="off" class="g-admin-toolbar">
                    @csrf

                    <div class="form-inline">
                        <div class="form-group">
                            <label for="datepicker">Từ ngày</label>
                            <input type="text" id="datepicker" class="form-control" placeholder="YYYY-MM-DD">
                        </div>

                        <div class="form-group">
                            <label for="datepicker2">Đến ngày</label>
                            <input type="text" id="datepicker2" class="form-control" placeholder="YYYY-MM-DD">
                        </div>

                        <div class="form-group">
                            <label for="dashboard-filter">Lọc theo</label>
                            <select id="dashboard-filter" class="dashboard-filter form-control">
                                <option value="">--Chọn--</option>
                                <option value="7ngay">7 ngày qua</option>
                                <option value="thangtruoc">Tháng trước</option>
                                <option value="thangnay">Tháng này</option>
                                <option value="365ngayqua">365 ngày qua</option>
                            </select>
                        </div>
                    </div>

                    <div class="g-admin-inline-actions">
                        <input type="button" id="btn-dashboard-filter" class="btn btn-primary" value="Lọc kết quả">
                    </div>
                </form>

                <div id="chart" class="g-admin-chart-stage" style="height: 280px;"></div>
            </div>
        </section>

        <div class="g-admin-panel-grid two-col">
            <section class="g-admin-panel-card">
                <div class="g-admin-panel-head">
                    <div>
                        <div>Cơ cấu dữ liệu</div>
                        <p>Tỷ trọng dữ liệu hiện có theo từng nhóm nội dung và bán hàng.</p>
                    </div>
                </div>
                <div class="g-admin-panel-body">
                    <div id="donut" class="g-admin-chart-stage" style="height: 280px;"></div>
                </div>
            </section>

            <section class="g-admin-panel-card">
                <div class="g-admin-panel-head">
                    <div>
                        <div>Tổng quan nhanh</div>
                        <p>Số lượng dữ liệu hiện có theo từng nhóm vận hành.</p>
                    </div>
                </div>
                <div class="g-admin-panel-body">
                    <div class="g-admin-summary-grid">
                        <article class="g-admin-summary-item">
                            <span>Sản phẩm</span>
                            <strong>{{ number_format($product) }}</strong>
                        </article>
                        <article class="g-admin-summary-item">
                            <span>Bài viết</span>
                            <strong>{{ number_format($post) }}</strong>
                        </article>
                        <article class="g-admin-summary-item">
                            <span>Đơn hàng</span>
                            <strong>{{ number_format($order) }}</strong>
                        </article>
                        <article class="g-admin-summary-item">
                            <span>Video</span>
                            <strong>{{ number_format($video) }}</strong>
                        </article>
                        <article class="g-admin-summary-item">
                            <span>Khách hàng</span>
                            <strong>{{ number_format($customer) }}</strong>
                        </article>
                    </div>
                </div>
            </section>
        </div>

        <div class="g-admin-panel-grid two-col">
            <section class="g-admin-panel-card">
                <div class="g-admin-panel-head">
                    <div>
                        <div>Bài viết được xem nhiều</div>
                        <p>Danh sách lấy trực tiếp từ thứ tự lượt xem hiện có.</p>
                    </div>
                </div>
                <div class="g-admin-panel-body">
                    @if ($post_views->isEmpty())
                        <div class="g-admin-empty-state">Chưa có dữ liệu bài viết để hiển thị.</div>
                    @else
                        <ol class="g-admin-top-list">
                            @foreach ($post_views as $postItem)
                                <li>
                                    <a target="_blank" href="{{ url('/bai-viet/' . $postItem->post_slug) }}">
                                        <span>{{ $postItem->post_title }}</span>
                                        <strong>{{ number_format($postItem->post_views) }} lượt xem</strong>
                                    </a>
                                </li>
                            @endforeach
                        </ol>
                    @endif
                </div>
            </section>

            <section class="g-admin-panel-card">
                <div class="g-admin-panel-head">
                    <div>
                        <div>Sản phẩm được xem nhiều</div>
                        <p>Danh sách nổi bật để theo dõi nhu cầu hiện tại từ storefront.</p>
                    </div>
                </div>
                <div class="g-admin-panel-body">
                    @if ($product_views->isEmpty())
                        <div class="g-admin-empty-state">Chưa có dữ liệu sản phẩm để hiển thị.</div>
                    @else
                        <ol class="g-admin-top-list">
                            @foreach ($product_views as $productItem)
                                <li>
                                    <a target="_blank" href="{{ url('/chi-tiet/' . $productItem->product_slug) }}">
                                        <span>{{ $productItem->product_name }}</span>
                                        <strong>{{ number_format($productItem->product_views) }} lượt xem</strong>
                                    </a>
                                </li>
                            @endforeach
                        </ol>
                    @endif
                </div>
            </section>
        </div>
    </div>
@endsection
