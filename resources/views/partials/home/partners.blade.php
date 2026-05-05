@php
    $visibleBrands = collect($brandItems ?? [])
        ->filter(fn ($brand) => ! empty($brand['logo']))
        ->values();
@endphp

@if ($visibleBrands->isNotEmpty())
<section class="tech-section tech-partners-section tech-brand-section">
    <div class="container">
        <div class="tech-partners-shell">
            <div class="tech-section-head">
                <div>
                    <span class="tech-eyebrow">Thương hiệu đối tác</span>
                </div>
                <a href="{{ url('/san-pham') }}" class="tech-section-link">Xem tất cả</a>
            </div>

            <div class="tech-partners-row">
                @foreach ($visibleBrands as $brand)
                    <a href="{{ $brand['url'] }}" class="tech-partner-logo" title="{{ $brand['name'] }}">
                        <img src="{{ $brand['logo'] }}" alt="{{ $brand['name'] }}">
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</section>
@endif
