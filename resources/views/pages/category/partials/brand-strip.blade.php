@if (($brandCollection ?? collect())->isNotEmpty())
    <div class="cat-x__brands">
        <div class="cat-x__brands-head">
            <i class="fa fa-handshake-o"></i>THƯƠNG HIỆU ĐỐI TÁC
        </div>
        <div class="cat-x__brands-grid">
            @foreach ($brandCollection->take(8) as $brandItem)
                @php
                    $brandLogoUrl = $brandItem->logo_path ? \App\Support\AssetUrl::resolve($brandItem->logo_path, null) : null;
                @endphp
                @if ($brandLogoUrl)
                    <img src="{{ $brandLogoUrl }}" alt="{{ $brandItem->brand_name }}">
                @else
                    <span class="brand-text">{{ strtoupper($brandItem->brand_name) }}</span>
                @endif
            @endforeach
        </div>
    </div>
@endif
