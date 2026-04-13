@extends('admin_layout')

@section('admin_content')
    <div class="g-admin-page-shell g-admin-product-profit">
        <section class="g-admin-page-hero">
            <div>
                <span class="g-admin-page-eyebrow">Khám phá storefront</span>
                <h1>Tìm kiếm và gợi ý sản phẩm</h1>
                <p>Theo dõi từ khóa tìm kiếm phổ biến và các sản phẩm xuất hiện nhiều nhất trong block gợi ý trên storefront.</p>
            </div>
            <div class="g-admin-page-stats">
                <article>
                    <strong>{{ number_format($summary['total_searches']) }}</strong>
                    <span>Lượt tìm kiếm đã ghi nhận</span>
                </article>
                <article>
                    <strong>{{ number_format($summary['recommendation_impressions']) }}</strong>
                    <span>Lượt hiển thị gợi ý sản phẩm</span>
                </article>
            </div>
        </section>

        <section class="g-admin-panel-card">
            <header class="g-admin-panel-head">
                <div>
                    <strong>Bộ lọc báo cáo</strong>
                    <p>Lọc theo thời gian để so sánh mức độ quan tâm của khách trên storefront.</p>
                </div>
            </header>
            <div class="g-admin-panel-body">
                @if (($discovery_tables_ready['search_queries'] ?? true) === false || ($discovery_tables_ready['recommendation_impressions'] ?? true) === false)
                    <div class="alert alert-warning">
                        Một phần dữ liệu analytics chưa sẵn sàng trên cơ sở dữ liệu hiện tại.
                        Báo cáo đang hiển thị ở chế độ an toàn với các khối chưa có bảng dữ liệu sẽ để trống.
                    </div>
                @endif

                <form method="GET" action="{{ url('/product-discovery-report') }}" class="g-admin-report-toolbar">
                    <div class="form-group">
                        <label for="date_from">Từ ngày</label>
                        <input type="date" id="date_from" name="date_from" value="{{ $filters['date_from'] ?? '' }}"
                            class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="date_to">Đến ngày</label>
                        <input type="date" id="date_to" name="date_to" value="{{ $filters['date_to'] ?? '' }}"
                            class="form-control">
                    </div>
                    <div class="form-group">
                        <label>&nbsp;</label>
                        <div class="g-admin-inline-actions">
                            <button type="submit" class="btn btn-info">Lọc báo cáo</button>
                            <a href="{{ url('/product-discovery-report') }}" class="btn btn-default">Xóa bộ lọc</a>
                        </div>
                    </div>
                </form>

                <div class="g-admin-report-summary">
                    <article>
                        <strong>{{ number_format($summary['unique_keywords']) }}</strong>
                        <span>Từ khóa khác nhau</span>
                    </article>
                    <article>
                        <strong>{{ number_format($summary['recommended_products']) }}</strong>
                        <span>Sản phẩm từng được gợi ý</span>
                    </article>
                    <article>
                        <strong>{{ number_format($summary['total_searches']) }}</strong>
                        <span>Tổng lượt tìm kiếm</span>
                    </article>
                    <article>
                        <strong>{{ number_format($summary['recommendation_impressions']) }}</strong>
                        <span>Tổng impression gợi ý</span>
                    </article>
                </div>
            </div>
        </section>

        <div class="g-admin-panel-grid two-col">
            <section class="g-admin-panel-card">
                <div class="g-admin-panel-head">
                    <div>
                        <div>Từ khóa tìm kiếm phổ biến</div>
                        <p>Danh sách từ khóa được khách tìm nhiều nhất, kèm số lượng kết quả trung bình.</p>
                    </div>
                </div>
                <div class="g-admin-panel-body">
                    @if ($top_keywords->isEmpty())
                        <div class="g-admin-empty-state">Chưa có dữ liệu từ khóa tìm kiếm để hiển thị.</div>
                    @else
                        <ol class="g-admin-top-list">
                            @foreach ($top_keywords as $keywordRow)
                                <li>
                                    <div>
                                        <span>{{ $keywordRow->keyword }}</span>
                                        <small class="g-admin-cell-note">
                                            Trung bình {{ number_format((float) $keywordRow->avg_results_count, 1, ',', '.') }} kết quả
                                        </small>
                                    </div>
                                    <strong>{{ number_format((int) $keywordRow->searches_count) }} lượt</strong>
                                </li>
                            @endforeach
                        </ol>
                    @endif
                </div>
            </section>

            <section class="g-admin-panel-card">
                <div class="g-admin-panel-head">
                    <div>
                        <div>Danh mục được lọc nhiều</div>
                        <p>Cho biết khách thường thu hẹp tìm kiếm vào nhóm sản phẩm nào.</p>
                    </div>
                </div>
                <div class="g-admin-panel-body">
                    @if ($top_filter_categories->isEmpty())
                        <div class="g-admin-empty-state">Chưa có dữ liệu lọc theo danh mục.</div>
                    @else
                        <ol class="g-admin-top-list">
                            @foreach ($top_filter_categories as $categoryRow)
                                <li>
                                    <span>{{ $categoryRow->category_name ?: 'Đang cập nhật' }}</span>
                                    <strong>{{ number_format((int) $categoryRow->searches_count) }} lượt</strong>
                                </li>
                            @endforeach
                        </ol>
                    @endif
                </div>
            </section>
        </div>

        <div class="g-admin-panel-grid two-col">
            <section class="g-admin-panel-card">
                <div class="g-admin-panel-head">
                    <div>
                        <div>Context gợi ý sản phẩm</div>
                        <p>Block gợi ý nào đang tạo ra nhiều impression nhất trên storefront.</p>
                    </div>
                </div>
                <div class="g-admin-panel-body">
                    @if ($recommendation_contexts->isEmpty())
                        <div class="g-admin-empty-state">Chưa có dữ liệu impression recommendation.</div>
                    @else
                        <ol class="g-admin-top-list">
                            @foreach ($recommendation_contexts as $contextRow)
                                <li>
                                    <span>{{ $contextRow->context }}</span>
                                    <strong>{{ number_format((int) $contextRow->impressions_count) }} lượt</strong>
                                </li>
                            @endforeach
                        </ol>
                    @endif
                </div>
            </section>

            <section class="g-admin-panel-card">
                <div class="g-admin-panel-head">
                    <div>
                        <div>Sản phẩm được gợi ý nhiều</div>
                        <p>Danh sách lấy từ impression thực tế của recommendation block.</p>
                    </div>
                </div>
                <div class="g-admin-panel-body">
                    <div class="table-responsive">
                        <table class="table table-striped b-t b-light g-admin-data-table">
                            <thead>
                                <tr>
                                    <th>Sản phẩm</th>
                                    <th>Danh mục</th>
                                    <th>Thương hiệu</th>
                                    <th>Lượt gợi ý</th>
                                    <th>Lần cuối</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($recommended_products_rows as $row)
                                    <tr>
                                        <td>
                                            <strong>{{ $row->product_name ?: 'Đang cập nhật' }}</strong>
                                            @if ($row->product_slug)
                                                <small class="g-admin-cell-note">{{ $row->product_slug }}</small>
                                            @endif
                                        </td>
                                        <td>{{ $row->category_name ?: 'Đang cập nhật' }}</td>
                                        <td>{{ $row->brand_name ?: 'Đang cập nhật' }}</td>
                                        <td>{{ number_format((int) $row->impressions_count) }}</td>
                                        <td>{{ $row->last_impressed_at ? \Carbon\Carbon::parse($row->last_impressed_at)->format('d/m/Y H:i') : 'N/A' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5">
                                            <div class="g-admin-empty-state">Chưa có dữ liệu sản phẩm được gợi ý trong bộ lọc hiện tại.</div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>

                        {{ $recommended_products_rows->links() }}
                    </div>
                </div>
            </section>
        </div>
    </div>
@endsection
