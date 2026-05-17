<section class="tech-section tech-brand-policy-section">
    <div class="container">
        <div class="tech-brand-panel">
            <div class="tech-section-head tech-section-head-center">
                <div>
                    <span class="tech-eyebrow">Thương hiệu</span>
                    <h2>Đối tác công nghệ nổi bật</h2>
                </div>
            </div>

            <div class="tech-brand-row">
                @foreach ($brandItems as $brand)
                    <a href="{{ $brand['url'] }}" class="tech-brand-logo-card" title="{{ $brand['name'] }}">
                        @if (!empty($brand['logo']))
                            <img src="{{ $brand['logo'] }}" alt="{{ $brand['name'] }}" loading="lazy" decoding="async">
                        @else
                            <span>{{ $brand['initials'] }}</span>
                        @endif
                        <strong>{{ $brand['name'] }}</strong>
                    </a>
                @endforeach
            </div>
        </div>

        <div class="row tech-policy-grid">
            @foreach ($policyItems as $policy)
                <div class="col-xs-12 col-sm-6 col-md-3">
                    <article class="tech-policy-card">
                        @if (!empty($policy['image'] ?? null))
                            <span class="tech-policy-icon tech-policy-icon--image">
                                <img src="{{ $policy['image'] }}" alt="{{ $policy['title'] }}" loading="lazy" decoding="async">
                            </span>
                        @else
                            <span class="tech-policy-icon tech-policy-icon--symbol">
                                <i class="fa {{ $policy['icon'] }}"></i>
                            </span>
                        @endif
                        <div class="tech-policy-body">
                            <h3>{{ $policy['title'] }}</h3>
                            <p>{{ $policy['description'] }}</p>
                        </div>
                    </article>
                </div>
            @endforeach
        </div>
    </div>
</section>
