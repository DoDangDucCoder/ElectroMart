@php
    $pricingService = app(\App\Application\Catalog\Services\ProductPricingService::class);
    $productModel = $product ?? null;

    $normalizeCurrency = static function ($value): ?float {
        if ($value === null) {
            return null;
        }

        if (is_numeric($value)) {
            return (float) $value;
        }

        $trimmed = trim((string) $value);
        if ($trimmed === '') {
            return null;
        }

        $normalized = preg_replace('/[^0-9]/', '', $trimmed);

        return $normalized === '' ? null : (float) $normalized;
    };

    $normalizeDecimal = static function ($value): ?float {
        if ($value === null) {
            return null;
        }

        if (is_numeric($value)) {
            return (float) $value;
        }

        $trimmed = trim((string) $value);
        if ($trimmed === '') {
            return null;
        }

        $normalized = preg_replace('/[^0-9.,]/', '', $trimmed);
        $normalized = str_replace(',', '.', $normalized);

        return $normalized === '' ? null : (float) $normalized;
    };

    $productPriceRaw = old('product_price', $productModel?->price ?? $productModel?->product_price);
    $costPriceRaw = old('price_cost', $productModel?->price_cost ?? $productModel?->cost_price);
    $saleTypeRaw = old('sale_type', $productModel?->sale_type);
    $saleValueRaw = old('sale_value', $productModel?->sale_value);

    $priceValue = $normalizeCurrency($productPriceRaw) ?? 0;
    $costValue = $normalizeCurrency($costPriceRaw) ?? 0;
    $saleTypeValue = $saleTypeRaw ?: null;
    $saleValueNumber = $saleTypeValue === 'percent'
        ? ($normalizeDecimal($saleValueRaw) ?? 0)
        : ($normalizeCurrency($saleValueRaw) ?? 0);
    $pricingPayload = $pricingService->derivePricingPayload($priceValue, $saleTypeValue, $saleValueNumber);

    $displayProductPrice = $productPriceRaw !== null && $productPriceRaw !== ''
        ? $productPriceRaw
        : ($priceValue > 0 ? number_format($priceValue, 0, ',', '.') : '');
    $displayCostPrice = $costPriceRaw !== null && $costPriceRaw !== ''
        ? $costPriceRaw
        : ($costValue > 0 ? number_format($costValue, 0, ',', '.') : '');
    $displaySaleValue = $saleValueRaw !== null && $saleValueRaw !== ''
        ? $saleValueRaw
        : ($saleValueNumber > 0
            ? ($saleTypeValue === 'percent'
                ? rtrim(rtrim(number_format($saleValueNumber, 2, '.', ''), '0'), '.')
                : number_format($saleValueNumber, 0, ',', '.'))
            : '');
@endphp

<div class="g-admin-pricing-grid">
    <div class="form-group">
        <label for="product_price">Giá bán gốc (đ)</label>
        <input type="text" name="product_price" id="product_price"
            class="form-control @error('product_price') is-invalid @enderror price_format"
            value="{{ $displayProductPrice }}" placeholder="Ví dụ: 1.500.000" required>
    </div>

    <div class="form-group">
        <label for="price_cost">Giá nhập (đ)</label>
        <input type="text" name="price_cost" id="price_cost"
            class="form-control @error('price_cost') is-invalid @enderror price_format"
            value="{{ $displayCostPrice }}" placeholder="Ví dụ: 1.200.000">
    </div>

    <div class="form-group">
        <label for="sale_type">Kiểu sale</label>
        <select name="sale_type" id="sale_type"
            class="form-control @error('sale_type') is-invalid @enderror">
            <option value="">Không sale</option>
            <option value="percent" {{ $saleTypeValue === 'percent' ? 'selected' : '' }}>Giảm theo %</option>
            <option value="fixed" {{ $saleTypeValue === 'fixed' ? 'selected' : '' }}>Giảm số tiền</option>
        </select>
    </div>

    <div class="form-group">
        <label for="sale_value">Giá trị sale</label>
        <input type="text" name="sale_value" id="sale_value"
            class="form-control @error('sale_value') is-invalid @enderror"
            value="{{ $displaySaleValue }}"
            placeholder="Nhập % hoặc số tiền giảm">
        <small class="text-muted">Nếu chọn sale %, nhập trong khoảng 0-100. Nếu chọn sale số tiền, nhập theo đơn vị đồng.</small>
    </div>

    <div class="form-group">
        <label for="sale_price_preview">Giá sau sale</label>
        <input type="text" id="sale_price_preview" class="form-control" readonly
            value="{{ number_format((float) ($pricingPayload['final_price'] ?? $priceValue), 0, ',', '.') }}">
    </div>

    <div class="form-group">
        <label for="profit_preview">Lợi nhuận đơn vị dự kiến</label>
        <input type="text" id="profit_preview" class="form-control" readonly
            value="{{ number_format($pricingService->calculateProfit((float) ($pricingPayload['final_price'] ?? $priceValue), $costValue), 0, ',', '.') }}">
    </div>
</div>

<div class="g-admin-pricing-explanation" id="sale_explanation_preview">
    {{ $pricingService->buildSaleExplanation($priceValue, $saleTypeValue, $saleValueNumber) ?: 'Không áp dụng khuyến mãi cho sản phẩm này.' }}
</div>
