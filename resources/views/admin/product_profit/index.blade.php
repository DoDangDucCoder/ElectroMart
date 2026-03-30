@extends('admin_layout')

@section('admin_content')
    <div class="g-admin-page-shell g-admin-product-profit">
        <section class="g-admin-page-hero">
            <div>
                <span class="g-admin-page-eyebrow">Báo cáo sản phẩm</span>
                <h1>Lợi nhuận sản phẩm</h1>
                <p>Theo dõi số lượng đã bán, doanh thu thực thu và lợi nhuận của từng sản phẩm dựa trên các đơn hoàn tất.</p>
            </div>
            <div class="g-admin-page-stats">
                <article>
                    <strong>{{ number_format($summary['products_count']) }}</strong>
                    <span>Sản phẩm có phát sinh doanh thu</span>
                </article>
                <article>
                    <strong>{{ number_format($summary['sold_quantity']) }}</strong>
                    <span>Tổng số lượng đã bán</span>
                </article>
            </div>
        </section>

        <section class="g-admin-panel-card">
            <header class="g-admin-panel-head">
                <div>
                    <strong>Bộ lọc báo cáo</strong>
                    <p>Chỉ tính các đơn có trạng thái hoàn tất. Coupon cấp đơn vẫn được tách riêng khỏi lợi nhuận cấp sản phẩm.</p>
                </div>
            </header>
            <div class="g-admin-panel-body">
                <form method="GET" action="{{ url('/product-profit') }}" class="g-admin-report-toolbar">
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
                        <label for="category_id">Danh mục</label>
                        <select id="category_id" name="category_id" class="form-control">
                            <option value="">Tất cả danh mục</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" {{ (string) ($filters['category_id'] ?? '') === (string) $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="brand_id">Thương hiệu</label>
                        <select id="brand_id" name="brand_id" class="form-control">
                            <option value="">Tất cả thương hiệu</option>
                            @foreach ($brands as $brand)
                                <option value="{{ $brand->id }}" {{ (string) ($filters['brand_id'] ?? '') === (string) $brand->id ? 'selected' : '' }}>
                                    {{ $brand->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>&nbsp;</label>
                        <div class="g-admin-inline-actions">
                            <button type="submit" class="btn btn-info">Lọc báo cáo</button>
                            <a href="{{ url('/product-profit') }}" class="btn btn-default">Xóa bộ lọc</a>
                        </div>
                    </div>
                </form>

                <div class="g-admin-report-summary">
                    <article>
                        <strong>{{ number_format($summary['revenue'], 0, ',', '.') }}đ</strong>
                        <span>Doanh thu thực thu</span>
                    </article>
                    <article>
                        <strong>{{ number_format($summary['cost_total'], 0, ',', '.') }}đ</strong>
                        <span>Giá vốn</span>
                    </article>
                    <article>
                        <strong>{{ number_format($summary['profit'], 0, ',', '.') }}đ</strong>
                        <span>Lợi nhuận</span>
                    </article>
                    <article>
                        <strong>{{ number_format($summary['sold_quantity']) }}</strong>
                        <span>Tổng số lượng đã bán</span>
                    </article>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped b-t b-light g-admin-data-table">
                        <thead>
                            <tr>
                                <th>Sản phẩm</th>
                                <th>Danh mục</th>
                                <th>Thương hiệu</th>
                                <th>Số lượng đã bán</th>
                                <th>Doanh thu</th>
                                <th>Giá vốn</th>
                                <th>Lợi nhuận</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($rows as $row)
                                <tr>
                                    <td>
                                        <strong>{{ $row->product_name }}</strong>
                                        @if ($row->product_slug)
                                            <small class="g-admin-cell-note">{{ $row->product_slug }}</small>
                                        @endif
                                    </td>
                                    <td>{{ $row->category_name ?: 'Đang cập nhật' }}</td>
                                    <td>{{ $row->brand_name ?: 'Đang cập nhật' }}</td>
                                    <td>{{ number_format((int) $row->sold_quantity) }}</td>
                                    <td>{{ number_format((float) $row->revenue, 0, ',', '.') }}đ</td>
                                    <td>{{ number_format((float) $row->cost_total, 0, ',', '.') }}đ</td>
                                    <td>{{ number_format((float) $row->profit, 0, ',', '.') }}đ</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7">
                                        <div class="g-admin-empty-state">Chưa có dữ liệu lợi nhuận phù hợp với bộ lọc hiện tại.</div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    {{ $rows->links() }}
                </div>
            </div>
        </section>
    </div>
@endsection
