@php
    $brandHighlights = collect($brandHighlights ?? [])->values();
    $serviceHighlights = collect($serviceHighlights ?? [])->values();
    $contentHighlights = collect($contentHighlights ?? [])->values();
    $showBrandColumn = $brandHighlights->count() >= 3;
    $ecosystemLayoutClass = $showBrandColumn
        ? 'shopw-home-ecosystem-grid--with-brand'
        : 'shopw-home-ecosystem-grid--without-brand';
@endphp

<section class="features_items shopw-home-section shopw-home-ecosystem-section">
    <div class="shopw-section-head">
        <div>
            <span class="shopw-section-kicker">Thương hiệu, dịch vụ và nội dung</span>
            <h2 class="title text-center">Bổ sung điểm tin cậy quanh hành vi mua</h2>
            <p class="shopw-section-copy">Không gian mua sắm không chỉ có sản phẩm. Nó còn cần cho thấy thương hiệu đang có, cách được hỗ trợ và nội dung nào đáng xem tiếp.</p>
        </div>
    </div>

    <div class="shopw-home-ecosystem-grid {{ $ecosystemLayoutClass }}">
        @if ($showBrandColumn)
            <section class="shopw-home-ecosystem-panel shopw-home-brand-cloud">
                <div class="shopw-home-panel-head">
                    <span class="shopw-home-panel-kicker">Thương hiệu đang có</span>
                    <h3>Chọn theo hãng quen thuộc</h3>
                </div>
                <div class="shopw-home-brand-grid">
                    @foreach ($brandHighlights as $brandItem)
                        <a href="{{ $brandItem['url'] }}" class="shopw-home-brand-chip">
                            <span class="shopw-home-brand-logo">
                                @if (!empty($brandItem['image']))
                                    <img src="{{ $brandItem['image'] }}" alt="{{ $brandItem['name'] }}">
                                @else
                                    <span>{{ $brandItem['initials'] }}</span>
                                @endif
                            </span>
                            <strong>{{ $brandItem['name'] }}</strong>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

        <section class="shopw-home-ecosystem-panel shopw-home-service-panel">
            <div class="shopw-home-panel-head">
                <span class="shopw-home-panel-kicker">Dịch vụ cửa hàng</span>
                <h3>Đi tới đúng điểm hỗ trợ</h3>
            </div>
            <div class="shopw-home-service-grid">
                @foreach ($serviceHighlights as $service)
                    @if (($service['type'] ?? 'link') === 'button')
                        <button type="button" class="shopw-home-service-card" data-chatbot-launch>
                            <span class="shopw-home-service-icon"><i class="fa {{ $service['icon'] }}"></i></span>
                            <span class="shopw-home-service-copy">
                                <strong>{{ $service['title'] }}</strong>
                                <small>{{ $service['description'] }}</small>
                            </span>
                        </button>
                    @else
                        <a href="{{ $service['url'] }}" class="shopw-home-service-card">
                            <span class="shopw-home-service-icon"><i class="fa {{ $service['icon'] }}"></i></span>
                            <span class="shopw-home-service-copy">
                                <strong>{{ $service['title'] }}</strong>
                                <small>{{ $service['description'] }}</small>
                            </span>
                        </a>
                    @endif
                @endforeach
            </div>
        </section>

        <section class="shopw-home-ecosystem-panel shopw-home-content-panel">
            <div class="shopw-home-panel-head">
                <span class="shopw-home-panel-kicker">Nội dung đáng xem</span>
                <h3>Đọc tiếp trước khi quyết định</h3>
            </div>
            <div class="shopw-home-content-list">
                @foreach ($contentHighlights as $content)
                    <a href="{{ $content['url'] }}" class="shopw-home-content-card">
                        <span class="shopw-home-content-kicker">{{ $content['eyebrow'] }}</span>
                        <strong>{{ $content['title'] }}</strong>
                        <p>{{ $content['description'] }}</p>
                    </a>
                @endforeach
            </div>
        </section>
    </div>
</section>

