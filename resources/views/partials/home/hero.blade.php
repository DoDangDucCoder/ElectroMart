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
    $heroBestSellingUrl = $hero_best_selling_url ?? url('/san-pham-ban-chay');
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
            'meta' => ['Cấu hình mạnh', 'Lên deal theo mùa'],
        ],
        'lifestyle' => [
            'tone' => 'lifestyle',
            'kicker' => 'Khuyến mãi chọn lọc',
            'headline' => 'Deal công nghệ dễ chọn, nhìn là muốn bấm',
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
        <div class="shopw-hero-shell" @if ($heroRatio) style="--shopw-hero-ratio: {{ $heroRatio }};" @endif>
            <div class="shopw-hero" data-hero-layout="wide">
                <div class="shopw-hero-main">
                    <div id="slider-carousel"
                        class="carousel slide hero-slot"
                        data-ride="carousel"
                        data-interval="4000"
                        data-pause="false"
                        data-hero-rotate="carousel"
                        data-hero-slide-count="{{ $heroSlides->count() }}">
                        @if ($heroSlides->count() > 1)
                            <ol class="carousel-indicators">
                                @foreach ($heroSlides as $key => $slide)
                                    <li data-target="#slider-carousel"
                                        data-slide-to="{{ $key }}"
                                        class="{{ $key === 0 ? 'active' : '' }}"></li>
                                @endforeach
                            </ol>
                        @endif

                        <div class="carousel-inner">
                            @foreach ($heroSlides as $slide)
                                <div class="item {{ $loop->first ? 'active' : '' }}">
                                    <a href="{{ $slide['url'] }}"
                                        class="shopw-hero-slide shopw-hero-slide--{{ $slide['tone'] }}"
                                        style="background-image: url('{{ $slide['src'] }}');"
                                        aria-label="{{ !empty($slide['highlight_product_name']) ? $slide['alt'] . ' - mở danh sách bán chạy, ưu tiên ' . $slide['highlight_product_name'] : $slide['alt'] . ' - mở danh sách bán chạy' }}">
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
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
