<section id="slider"><!--slider-->
    <div class="container">
        @php
            $storefrontLabel = \App\Support\StorefrontLabel::class;
            $stringHelper = \Illuminate\Support\Str::class;
            $preferredFallbackOrder = [
                'uploads/slider/banner342.jpg',
                'uploads/slider/banner190.jpg',
                'uploads/slider/banner-laptop-sinh-vien-scaled31.jpg',
                'uploads/slider/slider275.jpg',
                'uploads/slider/091216-casualfalloutifts-slider-2png18.png',
            ];
            $fallbackHeroFiles = array_merge(
                glob(public_path('uploads/slider/slider*.{jpg,jpeg,png,webp,avif,gif}'), GLOB_BRACE) ?: [],
                glob(public_path('uploads/slider/banner*.{jpg,jpeg,png,webp,avif,gif}'), GLOB_BRACE) ?: []
            );
            $fallbackHeroPaths = [];

            foreach ($fallbackHeroFiles as $file) {
                if (is_file($file)) {
                    $fallbackHeroPaths[] = 'uploads/slider/' . basename($file);
                }
            }

            if (empty($fallbackHeroPaths)) {
                $fallbackHeroPaths = $preferredFallbackOrder;
            }

            $categoryIcons = [
                'laptop' => 'fa-laptop',
                'laptop-gaming' => 'fa-gamepad',
                'laptop-van-phong' => 'fa-briefcase',
                'dien-thoai' => 'fa-mobile',
                'iphone' => 'fa-apple',
                'samsung-galaxy' => 'fa-android',
                'android-khac' => 'fa-android',
                'phu-kien' => 'fa-headphones',
                'sac-cap' => 'fa-plug',
                'op-lung-kinh-cuong-luc' => 'fa-shield',
            ];
            $heroRatio = isset($hero_ratio) && $hero_ratio
                ? number_format((float) $hero_ratio, 3, '.', '')
                : null;
            $supportConfig = config('services.storefront_support', []);
            $supportChannels = [
                [
                    'variant' => 'facebook',
                    'title' => 'Chat Facebook',
                    'meta' => $supportConfig['facebook_hours'] ?? '8h-21h',
                    'href' => $supportConfig['facebook_url'] ?? 'https://m.me/100332251915013',
                    'icon' => 'M',
                    'external' => true,
                ],
                [
                    'variant' => 'zalo',
                    'title' => 'Chat Zalo',
                    'meta' => $supportConfig['zalo_hours'] ?? '8h-21h',
                    'href' => filled($supportConfig['zalo_url'] ?? null) ? $supportConfig['zalo_url'] : url('/lien-he'),
                    'icon' => 'Zalo',
                    'external' => filled($supportConfig['zalo_url'] ?? null),
                ],
                [
                    'variant' => 'hotline',
                    'title' => $supportConfig['contact_phone_display'] ?? '1900.0000',
                    'meta' => $supportConfig['contact_hours'] ?? '8h-21h',
                    'href' => 'tel:' . ($supportConfig['contact_phone_href'] ?? '19000000'),
                    'icon_class' => 'fa fa-phone',
                    'external' => false,
                ],
            ];
            $heroSupportCta = $supportConfig['chat_cta'] ?? 'Hỏi sản phẩm, giá sale, bảo hành';
            $heroProfiles = [
                'mobile' => [
                    'tone' => 'mobile',
                    'kicker' => 'Điện thoại nổi bật',
                    'headline' => 'Nâng cấp smartphone theo cách nhẹ nhàng hơn',
                    'summary' => 'Tập trung vào dòng máy đang được quan tâm nhiều, ưu đãi rõ ràng và lựa chọn dễ so sánh ngay trên trang chủ.',
                    'meta' => ['Điện thoại chính hãng', 'Ưu đãi mỗi ngày'],
                ],
                'gaming' => [
                    'tone' => 'gaming',
                    'kicker' => 'Hiệu năng nổi bật',
                    'headline' => 'Dàn máy cho nhu cầu gaming và học tập',
                    'summary' => 'Ưu tiên những cấu hình đang có sức hút tốt, phối màu mạnh hơn và giữ nhịp đọc rõ ngay từ hero đầu trang.',
                    'meta' => ['Cấu hình mạnh', 'Lên deal theo mùa'],
                ],
                'lifestyle' => [
                    'tone' => 'lifestyle',
                    'kicker' => 'Khuyến mãi chọn lọc',
                    'headline' => 'Deal công nghệ dễ chọn, nhìn là muốn bấm',
                    'summary' => 'Hero được dùng như sân khấu chính cho các chiến dịch ngắn hạn, nhấn vào sản phẩm dễ tiếp cận nhất ở thời điểm hiện tại.',
                    'meta' => ['Giao nhanh', 'Mức giá dễ tiếp cận'],
                ],
                'accessory' => [
                    'tone' => 'accessory',
                    'kicker' => 'Phụ kiện đáng chú ý',
                    'headline' => 'Những món nhỏ nhưng nâng trải nghiệm rất nhiều',
                    'summary' => 'Phù hợp cho các slide phụ kiện, sạc cáp hoặc combo bổ trợ khi người dùng chưa sẵn sàng mua sản phẩm lớn ngay.',
                    'meta' => ['Phụ kiện cần thiết', 'Ghép combo dễ hơn'],
                ],
            ];
            $resolveHeroProfile = function (string $sourceText, int $index, ?string $toneOverride = null) use ($heroProfiles, $stringHelper) {
                $normalized = $stringHelper::lower($sourceText);
                $fallbackKeys = array_keys($heroProfiles);
                $fallbackProfile = $heroProfiles[$fallbackKeys[$index % count($fallbackKeys)]];

                if ($toneOverride && isset($heroProfiles[$toneOverride])) {
                    $profile = $heroProfiles[$toneOverride];
                } elseif ($stringHelper::contains($normalized, ['iphone', 'samsung', 'galaxy', 'dien thoai', 'android'])) {
                    $profile = $heroProfiles['mobile'];
                } elseif ($stringHelper::contains($normalized, ['gaming', 'laptop', 'notebook', 'pc'])) {
                    $profile = $heroProfiles['gaming'];
                } elseif ($stringHelper::contains($normalized, ['phu kien', 'sac', 'cap', 'tai nghe', 'chuot', 'ban phim'])) {
                    $profile = $heroProfiles['accessory'];
                } else {
                    $profile = $fallbackProfile;
                }

                $headline = preg_match('/^banner nổi bật \d+$/i', trim($sourceText))
                    ? $profile['headline']
                    : $sourceText;

                return array_merge($profile, [
                    'headline' => $headline,
                ]);
            };

            $heroSlides = collect($slider)
                ->values()
                ->map(function ($slide, $index) use ($resolveHeroProfile, $stringHelper) {
                    $slidePath = 'uploads/slider/' . $slide->slider_image;
                    if (! file_exists(public_path($slidePath))) {
                        return null;
                    }

                    $tone = in_array($slide->slider_tone ?? null, ['mobile', 'gaming', 'lifestyle', 'accessory'], true)
                        ? $slide->slider_tone
                        : null;
                    $headlineSource = $slide->slider_alt
                        ?: ((filled($slide->slider_desc) && $stringHelper::length((string) $slide->slider_desc) <= 72)
                            ? $slide->slider_desc
                            : 'Banner nổi bật ' . ($index + 1));
                    $profile = $resolveHeroProfile($headlineSource, $index, $tone);
                    $summary = filled($slide->slider_alt) && filled($slide->slider_desc)
                        ? $slide->slider_desc
                        : ($profile['summary'] ?? null);
                    $alt = $slide->slider_alt ?: $slide->slider_name ?: $profile['headline'];

                    return [
                        'src' => asset($slidePath),
                        'alt' => $alt,
                        'tone' => $profile['tone'],
                        'kicker' => $profile['kicker'],
                        'headline' => $profile['headline'],
                        'summary' => $summary,
                        'meta' => $profile['meta'],
                    ];
                })
                ->filter()
                ->values();

            if ($heroSlides->isEmpty()) {
                $orderedFallbackSlides = collect($preferredFallbackOrder)
                    ->filter(static fn ($path) => file_exists(public_path($path)));

                $remainingFallbackSlides = collect(array_unique(array_filter($fallbackHeroPaths)))
                    ->reject(fn ($path) => $orderedFallbackSlides->contains($path));

                $fallbackSlides = $orderedFallbackSlides
                    ->concat($remainingFallbackSlides)
                    ->take(3)
                    ->values();

                if ($fallbackSlides->isEmpty()) {
                    $fallbackSlides = collect($preferredFallbackOrder)
                        ->filter(static fn ($path) => file_exists(public_path($path)))
                        ->take(3)
                        ->values();
                }

                $heroSlides = $fallbackSlides->map(function ($path, $index) use ($resolveHeroProfile) {
                    $headlineSource = 'Banner nổi bật ' . ($index + 1);
                    $profile = $resolveHeroProfile($headlineSource, $index);

                    return [
                        'src' => asset($path),
                        'alt' => $profile['headline'],
                        'tone' => $profile['tone'],
                        'kicker' => $profile['kicker'],
                        'headline' => $profile['headline'],
                        'summary' => $profile['summary'],
                        'meta' => $profile['meta'],
                    ];
                });
            }
        @endphp
        <div class="shopw-hero-shell" @if ($heroRatio) style="--shopw-hero-ratio: {{ $heroRatio }};" @endif>
            <div class="shopw-hero" data-hero-layout="split">
                <aside class="shopw-hero-sidebar" aria-label="Danh mục nổi bật">
                    <div class="shopw-hero-categories">
                        <div class="shopw-hero-sidebar-head">
                            <span class="shopw-hero-sidebar-kicker">Khám phá nhanh</span>
                            <strong class="shopw-hero-sidebar-title">Danh mục nổi bật hôm nay</strong>
                            <p class="shopw-hero-sidebar-note">
                                Chọn nhanh nhóm sản phẩm đang được quan tâm nhiều nhất để đi thẳng vào danh mục phù hợp.
                            </p>
                        </div>
                        <ul class="shopw-cat-list">
                            @foreach ($category as $key => $cate)
                                @if ($cate->category_parent == 0)
                                    @php
                                        $slug = $cate->slug_category_product;
                                    @endphp
                                    <li>
                                        <a href="{{ URL::to('/danh-muc/' . $cate->slug_category_product) }}">
                                            <i class="fa {{ $categoryIcons[$cate->slug_category_product] ?? 'fa-tag' }}"></i>
                                            <span>{{ $storefrontLabel::category($cate->category_name, $cate->slug_category_product) }}</span>
                                        </a>
                                    </li>
                                @endif
                            @endforeach
                            <li class="shopw-cat-extra">
                                <a href="{{ url('/lien-he') }}">
                                    <i class="fa fa-gift"></i>
                                    <span>Dịch vụ và thông tin khác</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </aside>
                <div class="shopw-hero-main">
                    <div id="slider-carousel" class="carousel slide hero-slot" data-ride="carousel" data-interval="4000"
                        data-pause="false" data-hero-rotate="carousel">
                        @if ($heroSlides->count() > 1)
                            <ol class="carousel-indicators">
                                @foreach ($heroSlides as $key => $slide)
                                    <li data-target="#slider-carousel" data-slide-to="{{ $key }}"
                                        class="{{ $key === 0 ? 'active' : '' }}"></li>
                                @endforeach
                            </ol>
                        @endif

                        <div class="carousel-inner">
                            @foreach ($heroSlides as $slide)
                                <div class="item shopw-hero-slide shopw-hero-slide--{{ $slide['tone'] }} {{ $loop->first ? 'active' : '' }}"
                                    style="background-image: url('{{ $slide['src'] }}');">
                                    <div class="shopw-hero-slide-copy">
                                        <span class="shopw-hero-slide-kicker">{{ $slide['kicker'] }}</span>
                                        <div class="shopw-hero-slide-meta" aria-hidden="true">
                                            @foreach ($slide['meta'] as $meta)
                                                <span>{{ $meta }}</span>
                                            @endforeach
                                        </div>
                                        <strong>{{ $slide['headline'] }}</strong>
                                        <p>{{ $slide['summary'] }}</p>
                                    </div>
                                    <span class="sr-only">{{ $slide['alt'] }}</span>
                                </div>
                            @endforeach
                        </div>

                        @if ($heroSlides->count() > 1)
                            <a href="#slider-carousel" class="left control-carousel hidden-xs" data-slide="prev">
                                <i class="fa fa-angle-left"></i>
                            </a>
                            <a href="#slider-carousel" class="right control-carousel hidden-xs" data-slide="next">
                                <i class="fa fa-angle-right"></i>
                            </a>
                        @endif
                    </div>
                </div>
                <aside class="shopw-hero-support shopw-hero-support--dock" aria-label="Kênh hỗ trợ nhanh"
                    data-support-placement="fixed-right"
                    data-support-rail="icon-first">
                    <div class="shopw-hero-support-head">
                        <span class="shopw-hero-support-kicker">{{ config('storefront.labels.support_quick', 'Hỗ trợ nhanh') }}</span>
                        <strong class="shopw-hero-support-title">{{ config('storefront.labels.support_connect_title', 'Kết nối với cửa hàng') }}</strong>
                    </div>
                    <div class="shopw-hero-support-stack">
                        @foreach ($supportChannels as $channel)
                            <a href="{{ $channel['href'] }}"
                                class="shopw-hero-support-card shopw-hero-support-card--{{ $channel['variant'] }}"
                                aria-label="{{ $channel['title'] }} - {{ $channel['meta'] }}"
                                @if ($channel['external']) target="_blank" rel="noopener noreferrer" @endif>
                                <span class="shopw-hero-support-icon" aria-hidden="true">
                                    @if (!empty($channel['icon_class']))
                                        <i class="{{ $channel['icon_class'] }}"></i>
                                    @else
                                        <span>{{ $channel['icon'] }}</span>
                                    @endif
                                </span>
                                <span class="shopw-hero-support-copy">
                                    <strong>{{ $channel['title'] }}</strong>
                                    <small>{{ $channel['meta'] }}</small>
                                </span>
                                <span class="shopw-hero-support-arrow" aria-hidden="true">
                                    <i class="fa fa-angle-right"></i>
                                </span>
                            </a>
                        @endforeach
                    </div>
                    <a href="{{ url('/lien-he') }}" class="shopw-hero-support-cta"
                        aria-label="{{ config('storefront.labels.chat_assistant', 'Trợ lý bán hàng') }} - {{ $heroSupportCta }}"
                        data-chatbot-launch>
                        <span class="shopw-hero-support-cta-main">
                            <span class="shopw-hero-support-cta-icon" aria-hidden="true">
                                <i class="fa fa-comments-o"></i>
                            </span>
                            <span class="shopw-hero-support-cta-copy">
                                <strong>{{ config('storefront.labels.chat_assistant', 'Trợ lý bán hàng') }}</strong>
                                <small>{{ $heroSupportCta }}</small>
                            </span>
                        </span>
                        <span class="shopw-hero-support-cta-tail" aria-hidden="true">
                            <i class="fa fa-angle-up"></i>
                        </span>
                    </a>
                </aside>
            </div>
        </div>
    </div>
</section><!--/slider-->

