@php
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

    $heroRatio = isset($hero_ratio) && $hero_ratio
        ? number_format((float) $hero_ratio, 3, '.', '')
        : null;
    $heroMaxSlides = max(5, (int) config('storefront.hero_slider.max_slides', 5));
    $heroBestSellingUrl = $hero_best_selling_url ?? url('/san-pham');
    $heroSlideTargets = $hero_slide_targets ?? [];
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
            'meta' => ['Cấu hình mạnh', 'Ưu đãi theo mùa'],
        ],
        'lifestyle' => [
            'tone' => 'lifestyle',
            'kicker' => 'Khuyến mãi chọn lọc',
            'headline' => 'Ưu đãi công nghệ dễ chọn, nhìn là muốn bấm',
            'summary' => 'Hero dùng như sân khấu chính cho các chiến dịch ngắn hạn, nhấn vào sản phẩm dễ tiếp cận nhất.',
            'meta' => ['Giao nhanh', 'Mức giá dễ tiếp cận'],
        ],
        'accessory' => [
            'tone' => 'accessory',
            'kicker' => 'Phụ kiện đáng chú ý',
            'headline' => 'Những món nhỏ nhưng nâng trải nghiệm rất nhiều',
            'summary' => 'Phù hợp cho phụ kiện, sạc cáp hoặc combo bổ trợ khi khách chưa sẵn sàng mua sản phẩm lớn.',
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

    $heroSlides = collect($slider ?? [])
        ->values()
        ->map(function ($slide, $index) use ($resolveHeroProfile, $stringHelper, $heroSlideTargets, $heroBestSellingUrl) {
            $imageName = trim((string) ($slide->slider_image ?? ''));
            $slidePath = 'uploads/slider/' . $imageName;

            if ($imageName === '' || ! file_exists(public_path($slidePath))) {
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
            $imageKey = basename($slidePath);
            $slideTarget = $heroSlideTargets[$imageKey] ?? null;

            return [
                'path' => $slidePath,
                'image_key' => $imageKey,
                'src' => asset($slidePath),
                'alt' => $alt,
                'tone' => $profile['tone'],
                'kicker' => $profile['kicker'],
                'headline' => $profile['headline'],
                'summary' => $summary,
                'meta' => $profile['meta'],
                'url' => $slideTarget['url'] ?? $heroBestSellingUrl,
                'highlight_product_name' => $slideTarget['highlight_product_name'] ?? null,
            ];
        })
        ->filter()
        ->values();

    $orderedFallbackSlides = collect($preferredFallbackOrder)
        ->filter(static fn ($path) => file_exists(public_path($path)));

    $remainingFallbackSlides = collect(array_unique(array_filter($fallbackHeroPaths)))
        ->reject(fn ($path) => $orderedFallbackSlides->contains($path));

    $fallbackPool = $orderedFallbackSlides
        ->concat($remainingFallbackSlides)
        ->unique()
        ->values();

    if ($fallbackPool->count() < $heroMaxSlides) {
        $fallbackPool = $fallbackPool
            ->concat($preferredFallbackOrder)
            ->unique()
            ->values();
    }

    if ($heroSlides->count() < $heroMaxSlides) {
        $usedPaths = $heroSlides->pluck('path')
            ->filter()
            ->values();
        $missingSlides = max(0, $heroMaxSlides - $heroSlides->count());

        $fallbackSlides = $fallbackPool
            ->reject(fn ($path) => $usedPaths->contains($path))
            ->take($missingSlides)
            ->values()
            ->map(function ($path, $index) use ($resolveHeroProfile, $heroSlides, $heroBestSellingUrl) {
                $absoluteIndex = $heroSlides->count() + $index;
                $headlineSource = 'Banner nổi bật ' . ($absoluteIndex + 1);
                $profile = $resolveHeroProfile($headlineSource, $absoluteIndex);

                return [
                    'path' => $path,
                    'image_key' => basename($path),
                    'src' => asset($path),
                    'alt' => $profile['headline'],
                    'tone' => $profile['tone'],
                    'kicker' => $profile['kicker'],
                    'headline' => $profile['headline'],
                    'summary' => $profile['summary'],
                    'meta' => $profile['meta'],
                    'url' => $heroBestSellingUrl,
                    'highlight_product_name' => null,
                ];
            });

        $heroSlides = $heroSlides->concat($fallbackSlides)
            ->take($heroMaxSlides)
            ->values();
    }
@endphp

<section id="slider" class="tech-home-hero-slider">
    <div class="container">
        <div class="shopw-home-hero">
            <div class="shopw-home-hero__content">
                <span class="shopw-home-hero__pill">ƯU ĐÃI CÔNG NGHỆ</span>
                <h1 class="shopw-home-hero__title">Săn công nghệ mới,<br>nhận ưu đãi khủng</h1>
                <p class="shopw-home-hero__desc">Khám phá laptop, điện thoại và phụ kiện chính hãng với mức giá tốt mỗi ngày.</p>
                <div class="shopw-home-hero__actions">
                    <a href="{{ url('/san-pham') }}" class="btn-shopw btn-shopw--primary">Mua ngay</a>
                    <a href="{{ $heroBestSellingUrl }}" class="btn-shopw btn-shopw--outline">Xem ưu đãi</a>
                </div>
            </div>

            <div class="shopw-home-hero__slider">
                <div id="slider-carousel"
                    class="carousel slide"
                    data-ride="carousel"
                    data-interval="4000"
                    data-pause="false"
                    data-hero-slide-count="{{ $heroSlides->count() }}">

                    <div class="carousel-inner">
                        @foreach ($heroSlides as $slide)
                            <div class="item {{ $loop->first ? 'active' : '' }}">
                                <a href="{{ $slide['url'] }}" class="shopw-home-hero__slide-link" aria-label="{{ $slide['alt'] }}">
                                    <div class="shopw-home-hero__image-wrapper">
                                        <img src="{{ $slide['src'] }}" alt="{{ $slide['alt'] }}" class="shopw-home-hero__image">

                                        @if(filled($slide['headline']) && !str_starts_with($slide['headline'], 'Banner nổi bật'))
                                            <div class="shopw-home-hero__caption">
                                                <strong>{{ $slide['headline'] }}</strong>
                                                @if(filled($slide['summary']))
                                                    <p>{{ $slide['summary'] }}</p>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>

                    @if ($heroSlides->count() > 1)
                        <a class="left carousel-control" href="#slider-carousel" data-slide="prev" aria-label="Slide trước">
                            <i class="fa fa-angle-left" aria-hidden="true"></i>
                        </a>
                        <a class="right carousel-control" href="#slider-carousel" data-slide="next" aria-label="Slide sau">
                            <i class="fa fa-angle-right" aria-hidden="true"></i>
                        </a>
                    @endif
                </div>

                @if ($heroSlides->count() > 1)
                    <ol class="carousel-indicators shopw-home-hero__indicators">
                        @foreach ($heroSlides as $key => $slide)
                            <li data-target="#slider-carousel"
                                data-slide-to="{{ $key }}"
                                class="{{ $key === 0 ? 'active' : '' }}"></li>
                        @endforeach
                    </ol>
                @endif
            </div>
        </div>
    </div>
</section>
