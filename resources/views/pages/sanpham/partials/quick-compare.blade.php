@php
    $products = collect($products ?? [])->values();
    $rows = $rows ?? [];
    $currentProduct = $products->first();
@endphp

@if ($currentProduct && $products->count() > 1)
    <section class="pdx-section pdx-quick-compare" id="so-sanh-nhanh" data-current-category-id="{{ $currentProduct->primary_category_id ?? $currentProduct->primaryCategory?->id ?? '' }}">
        <div class="pdx-section-head">
            <div>
                <h2>So sánh với sản phẩm khác</h2>
                <p>Chọn tối đa 3 sản phẩm cùng nhóm để đối chiếu nhanh thông số chính.</p>
            </div>
        </div>

        <div class="pdx-compare-picker" data-pdx-compare-picker>
            @foreach ($products->skip(1) as $candidate)
                <label class="pdx-compare-option">
                    <input type="checkbox" data-pdx-compare-toggle="{{ $candidate->id }}" @checked($loop->index < 3)>
                    <span>{{ \Illuminate\Support\Str::limit(\App\Support\StorefrontLabel::neutralizeSource($candidate->product_name), 42) }}</span>
                </label>
            @endforeach
        </div>

        <div class="pdx-compare-table-wrap">
            <table class="pdx-compare-table" data-pdx-compare-table>
                <thead>
                    <tr>
                        <th>Thông số</th>
                        @foreach ($products->take(4) as $compareProduct)
                            <th data-pdx-compare-column="{{ $compareProduct->id }}" @class(['is-current' => $loop->first])>
                                {{ \Illuminate\Support\Str::limit(\App\Support\StorefrontLabel::neutralizeSource($compareProduct->product_name), 38) }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($rows as $row)
                        <tr>
                            <th>{{ $row['label'] }}</th>
                            @foreach ($products->take(4) as $compareProduct)
                                <td data-pdx-compare-column="{{ $compareProduct->id }}">{{ $row['values'][$compareProduct->id] ?? 'Đang cập nhật' }}</td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
@endif
