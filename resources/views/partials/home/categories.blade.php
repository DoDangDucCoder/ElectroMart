@php
    $featuredCategories = [
        ['name' => 'Laptop', 'icon' => 'fa-laptop', 'url' => url('/danh-muc/laptop')],
        ['name' => 'PC', 'icon' => 'fa-desktop', 'url' => url('/danh-muc/pc')],
        ['name' => 'Màn hình', 'icon' => 'fa-desktop', 'url' => url('/danh-muc/man-hinh')],
        ['name' => 'Linh kiện', 'icon' => 'fa-cogs', 'url' => url('/danh-muc/linh-kien')],
        ['name' => 'Tự chọn cấu hình', 'icon' => 'fa-tasks', 'url' => url('/danh-muc/pc')],
        ['name' => 'Card màn hình', 'icon' => 'fa-picture-o', 'url' => url('/danh-muc/card-man-hinh')],
        ['name' => 'Gaming Gear', 'icon' => 'fa-gamepad', 'url' => url('/danh-muc/gaming-gear')],
        ['name' => 'RAM', 'icon' => 'fa-list-alt', 'url' => url('/danh-muc/ram')],
        ['name' => 'SSD', 'icon' => 'fa-hdd-o', 'url' => url('/danh-muc/ssd')],
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
                <a href="{{ $cat['url'] }}" class="tech-cat-card">
                    <span class="tech-cat-icon">
                        <i class="fa {{ $cat['icon'] }}"></i>
                    </span>
                    <strong>{{ $cat['name'] }}</strong>
                </a>
            @endforeach
        </div>
    </div>
</section>
