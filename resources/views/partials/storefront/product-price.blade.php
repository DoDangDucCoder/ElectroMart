@php
    $currency = $currency ?? 'VND';
    $currencyLabel = $currency === 'VND' ? 'đ' : $currency;
    $showExplanation = $showExplanation ?? true;
    $compact = $compact ?? false;
    $finalPrice = (float) ($product->final_price ?? $product->product_price ?? $product->price ?? 0);
    $originalPrice = (float) ($product->price ?? $product->product_price ?? $finalPrice);
    $saleAmount = (float) ($product->sale_amount ?? 0);
    $saleType = $product->sale_type ?? null;
    $saleValue = $product->sale_value ?? null;
    $saleExplanation = $product->sale_explanation ?? null;
@endphp

<span class="shopw-price-block{{ $compact ? ' is-compact' : '' }}">
    @if ($saleAmount > 0)
        <span class="shopw-price-row">
            <span class="shopw-price-final">{{ number_format($finalPrice, 0, ',', '.') }}{{ $currencyLabel }}</span>
            <span class="shopw-price-original">{{ number_format($originalPrice, 0, ',', '.') }}{{ $currencyLabel }}</span>
        </span>
        <span class="shopw-price-meta-row">
            <span class="shopw-price-badge">
                @if ($saleType === 'percent')
                    -{{ rtrim(rtrim(number_format((float) $saleValue, 2, '.', ''), '0'), '.') }}%
                @else
                    -{{ number_format((float) $saleValue, 0, ',', '.') }}đ
                @endif
            </span>
        </span>
        @if ($showExplanation && $saleExplanation)
            <span class="shopw-price-explanation">{{ $saleExplanation }}</span>
        @endif
    @else
        <span class="shopw-price-final">{{ number_format($finalPrice, 0, ',', '.') }}{{ $currencyLabel }}</span>
    @endif
</span>

