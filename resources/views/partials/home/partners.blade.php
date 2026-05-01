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
                @foreach ($brandItems as $brand)
                    <a href="{{ $brand['url'] }}" class="tech-partner-logo" title="{{ $brand['name'] }}">
                        @if (!empty($brand['logo']))
                            <img src="{{ $brand['logo'] }}" alt="{{ $brand['name'] }}">
                        @else
                            <span>{{ $brand['initials'] }}</span>
                        @endif
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</section>
