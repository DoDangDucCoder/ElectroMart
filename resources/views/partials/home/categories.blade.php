@php
    $featuredCategories = [
        ['name' => 'Laptop', 'icon' => 'fa-laptop', 'query' => 'laptop'],
        ['name' => 'PC - Linh kiện', 'icon' => 'fa-cogs', 'query' => 'linh kiện'],
        ['name' => 'Màn hình', 'icon' => 'fa-desktop', 'query' => 'màn hình'],
        ['name' => 'Bàn phím', 'icon' => 'fa-keyboard-o', 'query' => 'bàn phím'],
        ['name' => 'Chuột', 'icon' => 'fa-location-arrow', 'query' => 'chuột'],
        ['name' => 'Tai nghe', 'icon' => 'fa-headphones', 'query' => 'tai nghe'],
        ['name' => 'Ổ cứng', 'icon' => 'fa-hdd-o', 'query' => 'ổ cứng'],
        ['name' => 'Phụ kiện khác', 'icon' => 'fa-puzzle-piece', 'query' => 'phụ kiện'],
    ];
@endphp

<section class="tech-section tech-categories-section tech-category-section">
    <div class="container">
        <div class="tech-section-head">
            <div>
                <span class="tech-eyebrow">Danh mục nổi bật</span>
            </div>
            <a href="{{ url('/san-pham') }}" class="tech-section-link">Xem tất cả danh mục <i class="fa fa-angle-right" aria-hidden="true"></i></a>
        </div>

        <div class="tech-categories-grid">
            @foreach ($featuredCategories as $cat)
                <a href="{{ url('/tim-kiem?keywords_submit=' . $cat['query']) }}" class="tech-cat-card">
                    <span class="tech-cat-icon">
                        <i class="fa {{ $cat['icon'] }}"></i>
                    </span>
                    <strong>{{ $cat['name'] }}</strong>
                </a>
            @endforeach
        </div>
    </div>
</section>
