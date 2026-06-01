<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>In báo cáo doanh thu sản phẩm</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; font-family: Arial, sans-serif; color: #111827; background: #f3f4f6; }
        .print-shell { max-width: 1120px; margin: 24px auto; background: #fff; padding: 28px; border: 1px solid #e5e7eb; }
        .print-head { display: flex; justify-content: space-between; gap: 24px; border-bottom: 2px solid #111827; padding-bottom: 18px; margin-bottom: 18px; }
        .print-head h1 { margin: 0 0 8px; font-size: 24px; text-transform: uppercase; }
        .print-head p { margin: 3px 0; color: #4b5563; font-size: 13px; }
        .print-actions { display: flex; gap: 8px; justify-content: flex-end; margin-bottom: 16px; }
        .print-actions button { border: 1px solid #d1d5db; background: #fff; padding: 8px 12px; border-radius: 4px; cursor: pointer; }
        .summary { display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px; margin: 18px 0; }
        .summary article { border: 1px solid #e5e7eb; padding: 12px; }
        .summary strong { display: block; font-size: 18px; margin-bottom: 4px; }
        .summary span { color: #6b7280; font-size: 12px; text-transform: uppercase; }
        table { width: 100%; border-collapse: collapse; font-size: 12px; }
        th, td { border: 1px solid #d1d5db; padding: 8px; vertical-align: top; }
        th { background: #f3f4f6; text-align: left; }
        .text-right { text-align: right; }
        .muted { color: #6b7280; font-size: 12px; }
        .empty { text-align: center; padding: 24px; color: #6b7280; }
        @media print {
            body { background: #fff; }
            .print-shell { margin: 0; max-width: none; border: 0; padding: 0; }
            .print-actions { display: none; }
            a { color: inherit; text-decoration: none; }
        }
    </style>
</head>
<body>
    <main class="print-shell">
        <div class="print-actions">
            <button type="button" onclick="window.print()">In báo cáo</button>
            <button type="button" onclick="window.close()">Đóng</button>
        </div>

        <header class="print-head">
            <div>
                <h1>Báo cáo doanh thu sản phẩm</h1>
                <p>Chỉ tính các đơn hàng đã hoàn tất.</p>
                <p>
                    Kỳ báo cáo:
                    {{ $filters['date_from'] ?? 'Tất cả' }}
                    -
                    {{ $filters['date_to'] ?? 'Tất cả' }}
                </p>
            </div>
            <div class="text-right">
                <p><strong>DangDuc.vn</strong></p>
                <p>Ngày in: {{ $printedAt->format('d/m/Y H:i') }}</p>
                <p>Số sản phẩm: {{ number_format($summary['products_count']) }}</p>
            </div>
        </header>

        @unless ($summary['tables_ready'] ?? false)
            <div class="empty">Dữ liệu báo cáo chưa sẵn sàng.</div>
        @else
            <section class="summary">
                <article>
                    <strong>{{ number_format($summary['revenue'], 0, ',', '.') }}đ</strong>
                    <span>Doanh thu</span>
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
                    <span>Số lượng bán</span>
                </article>
            </section>

            <table>
                <thead>
                    <tr>
                        <th style="width: 32px;">#</th>
                        <th>Sản phẩm</th>
                        <th>Danh mục</th>
                        <th>Thương hiệu</th>
                        <th class="text-right">SL bán</th>
                        <th class="text-right">Doanh thu</th>
                        <th class="text-right">Giá vốn</th>
                        <th class="text-right">Lợi nhuận</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($rows as $row)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <strong>{{ $row->product_name }}</strong>
                                @if ($row->product_slug)
                                    <div class="muted">{{ $row->product_slug }}</div>
                                @endif
                            </td>
                            <td>{{ $row->category_name ?: 'Đang cập nhật' }}</td>
                            <td>{{ $row->brand_name ?: 'Đang cập nhật' }}</td>
                            <td class="text-right">{{ number_format((int) $row->sold_quantity) }}</td>
                            <td class="text-right">{{ number_format((float) $row->revenue, 0, ',', '.') }}đ</td>
                            <td class="text-right">{{ number_format((float) $row->cost_total, 0, ',', '.') }}đ</td>
                            <td class="text-right">{{ number_format((float) $row->profit, 0, ',', '.') }}đ</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="empty">Chưa có dữ liệu phù hợp với bộ lọc hiện tại.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        @endunless
    </main>

    <script>
        window.addEventListener('load', function() {
            window.print();
        });
    </script>
</body>
</html>
