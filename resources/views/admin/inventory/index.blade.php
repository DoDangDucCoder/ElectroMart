@extends('admin_layout')

@section('admin_content')
    @php
        $statusLabels = [
            'all' => 'Tất cả',
            'in_stock' => 'Còn hàng',
            'low_stock' => 'Sắp hết',
            'out_of_stock' => 'Hết hàng',
        ];

        $formatMoney = static fn ($value): string => number_format((float) $value, 0, ',', '.') . 'đ';
        $stockStatus = static function ($product) use ($fallbackThreshold): array {
            $stock = (int) ($product->stock_quantity ?? 0);
            $threshold = (int) ($product->stock_alert_threshold ?: $fallbackThreshold);

            if ($stock <= 0) {
                return ['label' => 'Hết hàng', 'class' => 'danger'];
            }

            if ($stock <= $threshold) {
                return ['label' => 'Sắp hết', 'class' => 'warning'];
            }

            return ['label' => 'Còn hàng', 'class' => 'success'];
        };
    @endphp

    <div class="g-admin-page-shell">
        <section class="g-admin-page-hero">
            <div>
                <span class="g-admin-page-eyebrow">Sản phẩm / Tồn kho</span>
                <h1>Tồn kho sản phẩm</h1>
                <p>Bảng tổng quan chỉ đọc ở cấp sản phẩm, dùng dữ liệu từ sản phẩm và biến thể sản phẩm. Chưa phải tồn kho theo từng kho hàng.</p>
            </div>
            <div class="g-admin-page-stats">
                <article>
                    <strong>{{ number_format($stats['total_products']) }}</strong>
                    <span>Sản phẩm</span>
                </article>
                <article>
                    <strong>{{ number_format($stats['total_skus']) }}</strong>
                    <span>SKU sản phẩm/biến thể</span>
                </article>
                <article>
                    <strong>{{ number_format($stats['total_stock_quantity']) }}</strong>
                    <span>Tổng tồn</span>
                </article>
            </div>
        </section>

        <section class="g-admin-panel-card">
            <header class="g-admin-panel-head">
                <div>
                    <strong>Tổng quan tồn kho</strong>
                    <p>Giá trị tồn kho = số lượng tồn * giá vốn. Ngưỡng cảnh báo mặc định là {{ $fallbackThreshold }} nếu sản phẩm chưa cấu hình.</p>
                </div>
                <span class="g-admin-status-chip">Chỉ đọc</span>
            </header>
            <div class="g-admin-panel-body">
                <div class="row" style="row-gap:12px;">
                    <div class="col-md-3">
                        <div class="well" style="margin-bottom:0;">
                            <strong>{{ number_format($stats['product_stock_quantity']) }}</strong><br>
                            <span>Tồn sản phẩm gốc</span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="well" style="margin-bottom:0;">
                            <strong>{{ number_format($stats['variant_stock_quantity']) }}</strong><br>
                            <span>Tồn biến thể</span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="well" style="margin-bottom:0;">
                            <strong>{{ number_format($stats['low_stock']) }}</strong><br>
                            <span>Sản phẩm sắp hết</span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="well" style="margin-bottom:0;">
                            <strong>{{ $formatMoney($stats['total_inventory_value']) }}</strong><br>
                            <span>Giá trị tồn ước tính</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="g-admin-panel-card" style="margin-top:18px;">
            <header class="g-admin-panel-head">
                <div>
                    <strong>Bộ lọc tồn kho</strong>
                    <p>Tìm theo tên sản phẩm, SKU hoặc barcode. Không có filter warehouse vì chưa có bảng warehouse-level inventory.</p>
                </div>
            </header>
            <div class="g-admin-panel-body">
                <form method="get" action="{{ route('admin.inventory') }}" class="row" style="row-gap:12px;">
                    <div class="col-md-3">
                        <label for="q">Từ khóa</label>
                        <input type="search" name="q" id="q" value="{{ $filters['q'] }}" class="form-control" placeholder="Tên, SKU, barcode...">
                    </div>
                    <div class="col-md-2">
                        <label for="status">Trạng thái tồn</label>
                        <select name="status" id="status" class="form-control">
                            @foreach ($statusLabels as $value => $label)
                                <option value="{{ $value }}" @selected($filters['status'] === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="category_id">Danh mục</label>
                        <select name="category_id" id="category_id" class="form-control">
                            <option value="0">Tất cả danh mục</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" @selected((int) $filters['category_id'] === (int) $category->id)>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="brand_id">Thương hiệu</label>
                        <select name="brand_id" id="brand_id" class="form-control">
                            <option value="0">Tất cả thương hiệu</option>
                            @foreach ($brands as $brand)
                                <option value="{{ $brand->id }}" @selected((int) $filters['brand_id'] === (int) $brand->id)>
                                    {{ $brand->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="per_page">Mỗi trang</label>
                        <select name="per_page" id="per_page" class="form-control">
                            @foreach ([10, 15, 25, 50] as $size)
                                <option value="{{ $size }}" @selected((int) $filters['per_page'] === $size)>{{ $size }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-12" style="display:flex;gap:8px;align-items:center;">
                        <button type="submit" class="btn btn-info">
                            <i class="fa fa-search"></i> Lọc tồn kho
                        </button>
                        <a href="{{ route('admin.inventory') }}" class="btn btn-default">Xóa lọc</a>
                    </div>
                </form>
            </div>
        </section>

        <section class="g-admin-panel-card" style="margin-top:18px;">
            <header class="g-admin-panel-head">
                <div>
                    <strong>Danh sách tồn kho cấp sản phẩm</strong>
                    <p>Chỉ đọc. Muốn thay đổi số lượng, dùng luồng quản lý sản phẩm hiện có hoặc thiết kế movement log riêng.</p>
                </div>
                <span class="g-admin-status-chip">{{ number_format($products->total()) }} kết quả</span>
            </header>
            <div class="g-admin-panel-body">
                @if ($products->isEmpty())
                    <div class="g-admin-empty-state">
                        <h3>Không có sản phẩm phù hợp</h3>
                        <p>Thử thay đổi bộ lọc hoặc kiểm tra dữ liệu sản phẩm hiện tại.</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th style="width:120px;">SKU</th>
                                    <th style="width:120px;">Barcode</th>
                                    <th>Sản phẩm</th>
                                    <th>Thương hiệu</th>
                                    <th>Danh mục</th>
                                    <th style="width:95px;">Tồn</th>
                                    <th style="width:95px;">Ngưỡng</th>
                                    <th style="width:130px;">Giá vốn</th>
                                    <th style="width:145px;">Giá trị tồn</th>
                                    <th style="width:110px;">Trạng thái</th>
                                    <th style="width:100px;">Biến thể</th>
                                    <th style="width:140px;">Cập nhật</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($products as $product)
                                    @php
                                        $stock = (int) ($product->stock_quantity ?? 0);
                                        $threshold = (int) ($product->stock_alert_threshold ?: $fallbackThreshold);
                                        $costPrice = (float) ($product->cost_price ?? 0);
                                        $inventoryValue = $stock * $costPrice;
                                        $status = $stockStatus($product);
                                    @endphp
                                    <tr>
                                        <td>{{ $product->sku ?: 'Chưa có SKU' }}</td>
                                        <td>{{ $product->barcode ?: 'Không có' }}</td>
                                        <td>
                                            <strong>{{ $product->name }}</strong>
                                            @if (! $product->is_active)
                                                <span class="label label-default" style="margin-left:6px;">Ẩn</span>
                                            @endif
                                        </td>
                                        <td>{{ $product->brand?->name ?: 'Chưa gán' }}</td>
                                        <td>{{ $product->primaryCategory?->name ?: 'Chưa gán' }}</td>
                                        <td><strong>{{ number_format($stock) }}</strong></td>
                                        <td>{{ number_format($threshold) }}</td>
                                        <td>{{ $formatMoney($costPrice) }}</td>
                                        <td><strong>{{ $formatMoney($inventoryValue) }}</strong></td>
                                        <td>
                                            <span class="label label-{{ $status['class'] }}">{{ $status['label'] }}</span>
                                        </td>
                                        <td>
                                            {{ number_format((int) $product->variants_count) }}
                                            @if ((int) ($product->variant_stock_quantity ?? 0) > 0)
                                                <br><small>{{ number_format((int) $product->variant_stock_quantity) }} tồn biến thể</small>
                                            @endif
                                        </td>
                                        <td>{{ $product->updated_at ? $product->updated_at->format('d/m/Y H:i') : 'Không rõ' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div style="display:flex;justify-content:space-between;gap:12px;align-items:center;flex-wrap:wrap;">
                        <span>
                            Hiển thị {{ number_format($products->firstItem()) }}-{{ number_format($products->lastItem()) }}
                            của {{ number_format($products->total()) }} sản phẩm
                        </span>
                        {{ $products->links() }}
                    </div>
                @endif
            </div>
        </section>
    </div>
@endsection
