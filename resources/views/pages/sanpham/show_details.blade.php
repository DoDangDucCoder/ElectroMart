@extends('layouts.home-tech')

@section('body_class', 'g-product-detail pdx-page')

@section('content')
    @php
        use Illuminate\Support\Str;

        $storefrontLabel = \App\Support\StorefrontLabel::class;
        $product = $product_details->first();
        $productName = $storefrontLabel::neutralizeSource($product->product_name);
        $productBrandLabel = $storefrontLabel::neutralizeSource($product->brand_name ?: 'DangDuc');
        $productDescriptionHtml = $storefrontLabel::neutralizeSourceHtml($product->product_desc);
        $productContentHtml = $storefrontLabel::neutralizeSourceHtml($product->product_content);
        $primaryImage = \App\Support\AssetUrl::product($product->product_image);
        $categoryUrl = $cate_slug ? url('/danh-muc/' . $cate_slug) : null;
        $shortDescription = trim(strip_tags($productDescriptionHtml));
        $supportConfig = config('services.storefront_support', []);
        $supportPhoneDisplay = $supportConfig['phone_display'] ?? '1900 1234';
        $supportPhoneHref = $supportConfig['phone_href'] ?? 'tel:19001234';
        $stockAvailable = (int) $product->product_quantity > 0;
        $productCategoryLabel = $storefrontLabel::category(
            $product_cate ?: $product->category_name,
            $cate_slug,
            'Sản phẩm',
        );
        $finalPrice = (float) ($product->final_price ?? $product->product_price ?? 0);
        $originalPrice = (float) ($product->price ?? $product->product_price ?? $finalPrice);
        $discountPercent = 0;
        if ($originalPrice > 0 && $finalPrice > 0 && $finalPrice < $originalPrice) {
            $discountPercent = (int) round((($originalPrice - $finalPrice) / $originalPrice) * 100);
        }
        $installmentMonthly = $finalPrice > 0 ? (int) round($finalPrice / 12) : 0;
        $reviewCount = (int) ($product->product_views ?? 128);
        $soldCount = (int) ($product->sold_count ?? 1245);
        $ratingValue = (float) ($rating ?? 4.8);

        // 4 feature lines from product description
        $featureLines = collect(preg_split('/\r\n|\r|\n|\.\s+/u', $shortDescription))
            ->map(fn ($line) => trim((string) $line, " \t\n\r\0\x0B.\u{2022}-"))
            ->filter(fn ($line) => mb_strlen($line) >= 12 && mb_strlen($line) <= 140)
            ->take(4)
            ->values();

        $attributeOptions = collect($product_attribute ?? [])->values();

        // ========== Smart-group variants by detected type (Storage / RAM / Color / Other) ==========
        $colorKeywords = ['đen','trắng','bạc','xám','vàng','hồng','xanh','đỏ','tím','cam','nâu','midnight','silver','starlight','space gray','space','gray','grey','white','black','blue','red','pink','gold','rose','green','yellow'];
        $colorSwatches = [
            'đen' => '#0f172a','black' => '#0f172a','midnight' => '#1f2937',
            'trắng' => '#f1f5f9','white' => '#f1f5f9','starlight' => '#f5f0e1',
            'bạc' => '#cbd5e1','silver' => '#cbd5e1',
            'xám' => '#94a3b8','gray' => '#9ca3af','grey' => '#9ca3af','space gray' => '#52525b','space' => '#52525b',
            'vàng' => '#facc15','gold' => '#fde68a','yellow' => '#facc15',
            'hồng' => '#f9a8d4','pink' => '#f9a8d4','rose' => '#fda4af',
            'xanh' => '#3b82f6','blue' => '#3b82f6','green' => '#22c55e',
            'đỏ' => '#ef4444','red' => '#ef4444',
            'tím' => '#a855f7','purple' => '#a855f7',
            'cam' => '#f97316','orange' => '#f97316',
            'nâu' => '#92400e','brown' => '#92400e',
        ];

        $detectVariantType = function ($name) use ($colorKeywords) {
            $low = mb_strtolower((string) $name);
            // Detect color
            foreach ($colorKeywords as $kw) {
                if (str_contains($low, $kw)) {
                    return 'color';
                }
            }
            // Detect storage (TB or GB ≥ 64)
            if (preg_match('/(\d+)\s*tb/i', $low)) {
                return 'storage';
            }
            if (preg_match('/(\d+)\s*gb/i', $low, $m)) {
                $gb = (int) $m[1];
                return $gb >= 64 ? 'storage' : 'ram';
            }
            if (preg_match('/(\d+)\s*mb/i', $low)) {
                return 'ram';
            }
            return 'other';
        };

        $resolveColorSwatch = function ($name) use ($colorSwatches) {
            $low = mb_strtolower((string) $name);
            // Check longer keys first
            $sortedKeys = collect($colorSwatches)->keys()->sortByDesc(fn ($k) => mb_strlen($k))->values();
            foreach ($sortedKeys as $key) {
                if (str_contains($low, $key)) {
                    return $colorSwatches[$key];
                }
            }
            return '#cbd5e1';
        };

        $variantBuckets = ['storage' => [], 'ram' => [], 'color' => [], 'other' => []];
        foreach ($attributeOptions as $index => $attribute) {
            $name = $storefrontLabel::neutralizeSource($attribute->name ?: '');
            $type = $detectVariantType($name);
            $variantBuckets[$type][] = ['index' => $index, 'attribute' => $attribute, 'label' => $name];
        }

        $bucketLabels = [
            'storage' => 'Dung lượng lưu trữ',
            'ram' => 'Bộ nhớ RAM',
            'color' => 'Màu sắc',
            'other' => 'Phiên bản',
        ];

        // ========== Image slides (attribute thumbs + gallery) ==========
        $attributeSlides = [];
        foreach ($attributeOptions as $index => $attribute) {
            $attributeImage = \App\Support\AssetUrl::resolve(
                ! empty($attribute->image) ? 'uploads/attribute/' . $attribute->image : null,
                \App\Support\AssetUrl::PRODUCT_FALLBACK,
            );
            $attributeSlides[] = [
                'thumb' => $attributeImage,
                'src' => $attributeImage,
                'alt' => $storefrontLabel::neutralizeSource($attribute->name ?: $productName),
                'attribute_key' => $index,
            ];
        }

        $gallerySlides = [];
        foreach ($gallery as $media) {
            $galleryUrl = $media->url
                ?? \App\Support\AssetUrl::resolve(
                    $media->file_path ?? (! empty($media->gallery_image) ? 'uploads/gallery/' . $media->gallery_image : null),
                    null,
                );

            if (! $galleryUrl) {
                continue;
            }

            $gallerySlides[] = [
                'thumb' => $galleryUrl,
                'src' => $galleryUrl,
                'alt' => $storefrontLabel::neutralizeSource($media->gallery_name ?? $productName),
                'attribute_key' => null,
            ];
        }

        $imageSlides = array_merge($attributeSlides, $gallerySlides);
        if ($imageSlides === []) {
            $imageSlides[] = [
                'thumb' => $primaryImage,
                'src' => $primaryImage,
                'alt' => $productName,
                'attribute_key' => null,
            ];
        }

        $resolveLocalProductImage = function ($related) {
            $candidates = collect([
                $related->thumbnail_path ?? null,
                $related->product_image ?? null,
            ])
                ->filter()
                ->map(fn ($path) => trim((string) $path))
                ->reject(fn ($path) => $path === '' || str_contains($path, 'no-image'))
                ->values();

            foreach ($candidates as $path) {
                if (\App\Support\AssetUrl::isAbsoluteUrl($path)) {
                    continue;
                }

                $localPath = ltrim($path, '/\\');
                $publicPath = public_path($localPath);

                if (file_exists($publicPath) && is_file($publicPath)) {
                    return asset($localPath);
                }

                if (! str_contains($localPath, '/') && file_exists(public_path('uploads/product/' . $localPath))) {
                    return asset('uploads/product/' . $localPath);
                }
            }

            return null;
        };

        // ========== Related products mapping for tech-product-card ==========
        $relatedProductCards = collect($relate ?? [])
            ->map(function ($related) use ($storefrontLabel, $resolveLocalProductImage) {
                $relatedImage = $resolveLocalProductImage($related);
                if (! $relatedImage) {
                    return null;
                }

                $relatedFinal = (float) ($related->final_price ?? $related->product_price ?? 0);
                $relatedOriginal = (float) ($related->price ?? $related->product_price ?? $relatedFinal);
                $relatedDiscount = 0;
                if ($relatedOriginal > 0 && $relatedFinal > 0 && $relatedFinal < $relatedOriginal) {
                    $relatedDiscount = (int) round((($relatedOriginal - $relatedFinal) / $relatedOriginal) * 100);
                }
                return [
                    'id' => $related->product_id ?? $related->id,
                    'name' => $storefrontLabel::neutralizeSource($related->product_name),
                    'url' => url('/chi-tiet/' . $related->product_slug),
                    'image' => $relatedImage,
                    'brand' => $storefrontLabel::neutralizeSource($related->brand_name ?? 'DangDuc'),
                    'brand_initials' => mb_strtoupper(mb_substr($related->brand_name ?? 'DD', 0, 2)),
                    'original_price' => $relatedOriginal,
                    'price' => $relatedFinal,
                    'discount_percent' => $relatedDiscount,
                ];
            })
            ->filter()
            ->take(5)
            ->values();

        // ========== Sidebar promo & deal data ==========
        $promoCards = [
            ['icon' => 'fa-credit-card', 'title' => 'Trả góp 0%', 'sub' => 'Duyệt nhanh online'],
            ['icon' => 'fa-shield', 'title' => 'Bảo hành chính hãng', 'sub' => '12 tháng tại ' . $productBrandLabel . ' Việt Nam'],
            ['icon' => 'fa-refresh', 'title' => '1 đổi 1 trong 30 ngày', 'sub' => 'Lỗi kỹ thuật'],
            ['icon' => 'fa-truck', 'title' => 'Giao hàng nhanh', 'sub' => 'Nội thành Hà Nội'],
        ];

        $dealItems = [
            ['icon' => 'fa-gift', 'title' => 'Tặng túi chống sốc cao cấp', 'sub' => 'Trị giá 490.000đ'],
            ['icon' => 'fa-credit-card', 'title' => 'Giảm thêm 300.000đ', 'sub' => 'Khi thanh toán online'],
            ['icon' => 'fa-check-circle', 'title' => 'Miễn phí cài đặt', 'sub' => 'Phần mềm cơ bản'],
        ];

        $servicePolicies = [
            ['icon' => 'fa-truck',       'title' => 'Miễn phí giao hàng',  'description' => 'Áp dụng cho đơn hàng công nghệ đủ điều kiện trong nội thành.'],
            ['icon' => 'fa-refresh',     'title' => 'Đổi trả hàng',         'description' => 'Hỗ trợ đổi trả nhanh chính sách trong 30 ngày nếu có lỗi do NSX.'],
            ['icon' => 'fa-shield',      'title' => 'Thanh toán an toàn',   'description' => 'Bảo vệ thông tin toàn vẹn và an toàn hàng đầu.'],
            ['icon' => 'fa-headphones',  'title' => 'Hỗ trợ tư vấn 24/7',  'description' => 'Tư vấn chọn laptop, điện thoại và phụ kiện phù hợp nhu cầu.'],
        ];

        // News + brands + tags
        $newsItems = collect([
            [
                'title' => 'Đánh giá ' . $productName . ' sau 1 năm sử dụng: Vẫn là lựa chọn đáng giá?',
                'url' => url('/bai-viet/sony-ht-s40r-600w-trai-nghiem-am-thanh-tai-gia'),
                'summary' => 'Trải nghiệm thực tế sau thời gian dài sử dụng và những điểm cộng còn lại.',
                'published_at' => now()->subDays(2),
                'thumbnail' => asset('assets/news/anker-735-review-1-year.svg'),
            ],
            [
                'title' => 'So sánh ' . $productName . ' với phiên bản trước: Nên chọn máy nào?',
                'url' => url('/bai-viet/kinh-nghiem-chon-man-hinh-gaming-choi-game-muot'),
                'summary' => 'So sánh chi tiết SSD và HDD: ưu nhược điểm và lời khuyên cho từng nhu cầu sử dụng.',
                'published_at' => now()->subDays(5),
                'thumbnail' => asset('assets/news/anker-735-compare.svg'),
            ],
            [
                'title' => '7 mẹo tối ưu pin MacBook giúp tăng thời lượng sử dụng',
                'url' => url('/bai-viet/meo-toi-uu-pin-macbook-khi-lam-viec-di-dong'),
                'summary' => 'Cấu hình macOS hợp lý và thói quen sạc đúng cách giúp pin bền lâu.',
                'published_at' => now()->subDays(8),
                'thumbnail' => asset('assets/news/macbook-battery-tips.svg'),
            ],
        ]);

        $brandConfig = collect(config('homepage.partner_brands', []));
        $brandItems = collect(['apple', 'samsung', 'asus', 'acer', 'dell', 'hp', 'lenovo', 'anker', 'baseus', 'jbl'])
            ->map(function (string $key) use ($brandConfig) {
                $brand = $brandConfig->get($key, [
                    'name' => Str::headline($key),
                    'logo' => 'assets/home/brands/' . $key . '.svg',
                ]);
                $logoPath = $brand['logo'] ?? null;

                if (! $logoPath || ! file_exists(public_path($logoPath))) {
                    return null;
                }

                return [
                    'name' => $brand['name'],
                    'url' => url('/thuong-hieu/' . Str::slug($brand['name'])),
                    'logo' => asset($logoPath),
                    'initials' => mb_strtoupper(mb_substr($brand['name'], 0, 2)),
                ];
            })
            ->filter()
            ->values();

        $tagList = collect(explode(',', (string) $product->product_tags))
            ->map(static fn($tag) => trim($tag))
            ->filter()
            ->values();
    @endphp

    <style>
        /* ============================================================
           PRODUCT DETAIL X — pdx-* prefix, fully self-contained styles
           ============================================================ */
        body.pdx-page { background: #f4f6fa; }

        .pdx-wrap { max-width: 1280px; margin: 0 auto; padding: 0 16px; }

        /* Breadcrumb */
        .pdx-crumbs {
            display: flex; align-items: center; gap: 8px;
            font-size: 13px; color: #64748b;
            padding: 16px 0 14px; flex-wrap: wrap;
        }
        .pdx-crumbs a { color: #64748b; text-decoration: none; }
        .pdx-crumbs a:hover { color: #0d9488; }
        .pdx-crumbs .sep { color: #cbd5e1; }
        .pdx-crumbs .current { color: #0f172a; font-weight: 500; }

        /* Main grid: gallery | info | sidebar */
        .pdx-grid {
            display: grid;
            grid-template-columns: minmax(0, 420px) minmax(0, 1fr) minmax(0, 280px);
            gap: 20px;
            align-items: start;
        }
        @media (max-width: 1199px) {
            .pdx-grid { grid-template-columns: minmax(0, 1fr) minmax(0, 1fr); }
            .pdx-side { grid-column: 1 / -1; display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        }
        @media (max-width: 767px) {
            .pdx-grid, .pdx-side { grid-template-columns: 1fr; }
        }

        /* ===== Gallery card ===== */
        .pdx-gallery {
            background: #fff;
            border-radius: 12px;
            border: 1px solid #eef2f7;
            padding: 20px;
            position: relative;
        }
        .pdx-wishlist {
            position: absolute; top: 14px; right: 14px;
            width: 36px; height: 36px; border-radius: 50%;
            border: 1px solid #e2e8f0; background: #fff; color: #f43f5e;
            display: inline-flex; align-items: center; justify-content: center;
            cursor: pointer; z-index: 5;
            transition: background .15s, transform .15s;
        }
        .pdx-wishlist:hover { background: #fff1f2; transform: scale(1.05); }
        .pdx-wishlist.is-active { background: #f43f5e; color: #fff; border-color: #f43f5e; }

        .pdx-gallery-list { list-style: none; margin: 0; padding: 0; }
        .pdx-gallery-list > li { list-style: none; }
        .pdx-gallery-list img {
            width: 100%; height: auto;
            object-fit: contain; border-radius: 8px; background: #fff;
            aspect-ratio: 1 / 1;
        }

        /* lightSlider polish — circular nav arrows */
        .pdx-gallery .lSAction > a {
            width: 36px; height: 36px; border-radius: 50%;
            background: rgba(255,255,255,.95); box-shadow: 0 2px 8px rgba(15,23,42,.12);
            top: 45%; opacity: 1;
        }
        .pdx-gallery .lSAction > a::before {
            content: ''; display: block;
            width: 10px; height: 10px;
            border-top: 2px solid #475569; border-right: 2px solid #475569;
            margin: 12px auto 0;
            transform: rotate(45deg);
        }
        .pdx-gallery .lSAction > .lSPrev::before {
            transform: rotate(-135deg); margin-top: 12px; margin-left: 14px;
        }
        .pdx-gallery .lSAction > .lSNext::before {
            margin-left: 10px;
        }
        .pdx-gallery .lSGallery li.active,
        .pdx-gallery .lSGallery li:hover { border-radius: 8px; }
        .pdx-gallery .lSGallery img { border-radius: 8px; }

        /* ===== Info card ===== */
        .pdx-info {
            background: #fff;
            border-radius: 12px;
            border: 1px solid #eef2f7;
            padding: 24px;
            display: flex; flex-direction: column; gap: 16px;
        }
        .pdx-brand {
            font-size: 12px; font-weight: 700; letter-spacing: .08em;
            color: #ef4444; text-transform: uppercase;
        }
        .pdx-title {
            font-size: 22px; font-weight: 700; line-height: 1.3; color: #0f172a;
            margin: -6px 0 0;
        }
        .pdx-meta-row {
            display: flex; align-items: center; gap: 10px;
            font-size: 13px; color: #64748b; flex-wrap: wrap;
        }
        .pdx-stars { color: #f59e0b; font-size: 14px; letter-spacing: 1px; }
        .pdx-rating-num { color: #0f172a; font-weight: 600; }
        .pdx-meta-dot { width: 4px; height: 4px; border-radius: 50%; background: #cbd5e1; }

        /* Price block */
        .pdx-price-card {
            background: #fff5f5; border-radius: 10px; padding: 14px 16px;
            display: flex; flex-direction: column; gap: 6px;
            margin-top: 2px;
        }
        .pdx-price-current {
            font-size: 30px; font-weight: 800; color: #ef4444; line-height: 1.1;
        }
        .pdx-price-meta { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
        .pdx-price-old {
            font-size: 14px; color: #94a3b8; text-decoration: line-through;
        }
        .pdx-price-badge {
            background: #ef4444; color: #fff;
            padding: 2px 8px; border-radius: 6px;
            font-size: 12px; font-weight: 700;
        }

        .pdx-installment {
            font-size: 13px; color: #475569;
            display: inline-flex; align-items: center; gap: 6px;
        }
        .pdx-installment strong { color: #0f172a; font-weight: 600; }
        .pdx-installment .info-i {
            display: inline-flex; align-items: center; justify-content: center;
            width: 16px; height: 16px; border-radius: 50%;
            background: #e2e8f0; color: #64748b; font-size: 10px; cursor: help;
        }

        /* Feature list */
        .pdx-features {
            list-style: none; padding: 0; margin: 0;
            display: flex; flex-direction: column; gap: 8px;
        }
        .pdx-features li {
            display: flex; align-items: flex-start; gap: 10px;
            font-size: 13.5px; color: #334155; line-height: 1.5;
        }
        .pdx-features i { color: #14b8a6; font-size: 14px; margin-top: 3px; flex-shrink: 0; }

        /* Variant groups */
        .shopw-pdp-purchase {
            width: 100% !important;
            margin-top: 6px !important;
            gap: 18px !important;
            align-items: stretch !important;
        }
        .pdx-variant { width: 100%; display: flex; flex-direction: column; align-items: flex-start; gap: 9px; }
        .pdx-variant-label {
            font-size: 13px; font-weight: 600; color: #475569;
        }
        .pdx-chips { display: flex; flex-wrap: wrap; gap: 8px; }

        .pdx-chip {
            border: 1.5px solid #e2e8f0; background: #fff;
            border-radius: 8px;
            padding: 8px 16px;
            font-size: 13px; color: #334155; font-weight: 500;
            cursor: pointer;
            transition: border-color .15s, color .15s, background .15s;
            display: inline-flex; align-items: center; gap: 8px;
        }
        .pdx-chip:hover { border-color: #14b8a6; color: #0d9488; }
        .pdx-chip.active,
        .pdx-chip[aria-pressed="true"] {
            border-color: #14b8a6; background: #f0fdfa; color: #0d9488;
        }
        .pdx-chip strong { font-weight: 600; }

        .pdx-chip--color {
            padding: 6px 12px 6px 8px;
        }
        .pdx-chip-swatch {
            display: inline-block;
            width: 18px; height: 18px; border-radius: 50%;
            border: 2px solid #fff;
            box-shadow: 0 0 0 1px #cbd5e1;
        }
        .pdx-chip--color.active .pdx-chip-swatch { box-shadow: 0 0 0 2px #14b8a6; }

        /* Quantity stepper */
        .pdx-qty {
            display: inline-flex; align-items: center;
            border: 1.5px solid #e2e8f0; border-radius: 8px;
            overflow: hidden; width: fit-content;
            background: #fff;
            align-self: flex-start;
        }
        .pdx-qty button {
            width: 42px; height: 42px; background: #fff; border: 0;
            font-size: 18px; font-weight: 600; color: #475569; cursor: pointer;
        }
        .pdx-qty button:hover { background: #f1f5f9; }
        .pdx-qty input {
            width: 58px; height: 42px; border: 0;
            border-left: 1px solid #e2e8f0; border-right: 1px solid #e2e8f0;
            text-align: center; font-size: 14px; font-weight: 600; color: #0f172a;
            -moz-appearance: textfield;
        }
        .pdx-qty input::-webkit-outer-spin-button,
        .pdx-qty input::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }

        /* CTA buttons */
        .pdx-actions {
            width: 100%;
            display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 16px;
            justify-items: stretch;
        }
        @media (max-width: 480px) { .pdx-actions { grid-template-columns: 1fr; } }
        .pdx-btn {
            width: 100%;
            min-height: 64px;
            border-radius: 12px; padding: 12px 18px;
            font-size: 15px; font-weight: 700; cursor: pointer;
            display: inline-flex; align-items: center; justify-content: center; gap: 8px;
            line-height: 1.2; text-align: center;
            transition: background .15s, border-color .15s, transform .15s;
        }
        .pdx-btn:hover { transform: translateY(-1px); }
        .pdx-btn:disabled { opacity: .55; cursor: not-allowed; transform: none; }
        .pdx-btn--ghost {
            background: #fff; border: 1.5px solid #14b8a6; color: #0d9488;
        }
        .pdx-btn--ghost:hover { background: #f0fdfa; }
        .pdx-btn--primary {
            background: #14b8a6; color: #fff; border: 1.5px solid #14b8a6;
            flex-direction: column; gap: 2px;
        }
        .pdx-btn--primary:hover { background: #0d9488; border-color: #0d9488; }
        .pdx-btn-strong { font-weight: 700; font-size: 15px; }
        .pdx-btn-sub { font-size: 12px; font-weight: 500; opacity: .9; }
        .pdx-status { font-size: 13px; color: #b91c1c; min-height: 18px; }

        /* Support row */
        .pdx-support {
            display: flex; align-items: center; gap: 14px; flex-wrap: wrap;
            margin-top: 2px; padding-top: 16px; border-top: 1px solid #eef2f7;
            font-size: 13px; color: #475569;
        }
        .pdx-support-item { display: inline-flex; align-items: center; gap: 6px; }
        .pdx-support-item i { color: #14b8a6; }
        .pdx-support-item a { color: #0d9488; font-weight: 600; text-decoration: none; }
        .pdx-support-item small { color: #94a3b8; }
        .pdx-support-sep { width: 1px; height: 14px; background: #cbd5e1; }
        .pdx-support-link { color: #0d9488; font-weight: 600; text-decoration: none; cursor: pointer; }

        /* Tags */
        .pdx-tags {
            display: flex; align-items: center; gap: 8px; flex-wrap: wrap;
            font-size: 13px; color: #64748b;
        }
        .pdx-tag-chip {
            padding: 4px 10px; background: #f1f5f9; color: #475569;
            border-radius: 6px; text-decoration: none; font-size: 12px;
        }
        .pdx-tag-chip:hover { background: #e2e8f0; color: #0f172a; }

        /* ===== Right sidebar ===== */
        .pdx-side { display: flex; flex-direction: column; gap: 16px; }
        .pdx-side-card {
            background: #fff; border: 1px solid #eef2f7; border-radius: 12px;
            padding: 18px;
            display: flex; flex-direction: column; gap: 14px;
        }
        .pdx-side-row { display: flex; align-items: flex-start; gap: 12px; }
        .pdx-side-icon {
            flex: 0 0 46px; width: 46px; height: 46px; border-radius: 12px;
            background: #f0fdfa; color: #0d9488;
            display: inline-flex; align-items: center; justify-content: center;
            font-size: 22px;
        }
        .pdx-side-icon .fa {
            display: block !important;
            width: auto !important;
            height: auto !important;
            margin: 0 !important;
            font-size: inherit !important;
            line-height: 1 !important;
        }
        .pdx-side-text strong {
            display: block; font-size: 13px; font-weight: 600; color: #0f172a; margin-bottom: 2px;
        }
        .pdx-side-text span { font-size: 12.5px; color: #64748b; line-height: 1.4; }
        .pdx-side-title {
            font-size: 14px; font-weight: 700; color: #0f172a; margin: 0 0 4px;
        }

        /* ===== Service policy strip (4 cards) ===== */
        .pdx-policies {
            display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px;
            margin: 24px 0;
        }
        @media (max-width: 991px) { .pdx-policies { grid-template-columns: repeat(2, 1fr); } }
        @media (max-width: 575px) { .pdx-policies { grid-template-columns: 1fr; } }
        .pdx-policy {
            background: #fff; border: 1px solid #eef2f7; border-radius: 12px;
            padding: 20px;
            display: flex; align-items: center; gap: 16px;
        }
        .pdx-policy-icon {
            flex: 0 0 66px; width: 66px; height: 66px; border-radius: 16px;
            background: #f0fdfa; color: #0d9488;
            display: inline-flex; align-items: center; justify-content: center;
            font-size: 30px;
            position: relative;
        }
        .pdx-policy-icon .fa {
            display: block !important;
            width: auto !important;
            height: auto !important;
            margin: 0 !important;
            font-size: 30px !important;
            line-height: 1 !important;
            position: absolute !important;
            left: 50% !important;
            top: 50% !important;
            transform: translate(-50%, -50%) !important;
        }
        .pdx-policy strong { display: block; font-size: 14px; color: #0f172a; margin-bottom: 4px; }
        .pdx-policy span { font-size: 12.5px; color: #64748b; line-height: 1.45; display: block; }

        /* ===== Tabs section ===== */
        .pdx-tabs {
            background: #fff; border: 1px solid #eef2f7; border-radius: 12px;
            margin-bottom: 24px; overflow: visible;
        }
        .pdx-tabs-nav {
            display: flex; gap: 0; border-bottom: 1px solid #eef2f7;
            padding: 0 24px;
            overflow-x: auto;
            scrollbar-width: none;
        }
        .pdx-tabs-nav::-webkit-scrollbar { display: none; }
        .pdx-tab-btn {
            background: 0; border: 0;
            padding: 16px 20px; font-size: 14px; font-weight: 600; color: #64748b;
            cursor: pointer; position: relative; white-space: nowrap;
        }
        .pdx-tab-btn:hover { color: #0d9488; }
        .pdx-tab-btn.is-active { color: #0f172a; }
        .pdx-tab-btn.is-active::after {
            content: ''; position: absolute; left: 16px; right: 16px; bottom: -1px;
            height: 2px; background: #14b8a6; border-radius: 2px;
        }
        .pdx-tab-pane { display: none; padding: 24px; max-height: none; overflow: visible; }
        .pdx-tab-pane.is-active { display: block; }
        .pdx-tab-grid {
            display: grid; grid-template-columns: 1fr; gap: 24px;
        }
        @media (max-width: 991px) { .pdx-tab-grid { grid-template-columns: 1fr; } }
        .pdx-tab-desc :is(h2, h3, h4) { margin: 18px 0 12px; color: #0f172a; font-size: 16px; }
        .pdx-tab-desc :is(p, ul, ol) { margin: 0 0 14px; color: #334155; font-size: 14px; line-height: 1.78; text-align: justify; }
        .pdx-tab-desc ul li { margin-bottom: 6px; }

        .pdx-spec-table {
            width: 100%; border-collapse: collapse; border: 1px solid #eef2f7; border-radius: 8px; overflow: hidden;
        }
        .pdx-spec-table td, .pdx-spec-table th {
            padding: 12px 14px; font-size: 13.5px; text-align: left;
            border-bottom: 1px solid #eef2f7;
        }
        .pdx-spec-table tr:last-child td, .pdx-spec-table tr:last-child th { border-bottom: 0; }
        .pdx-spec-table tr td:first-child, .pdx-spec-table tr th:first-child {
            background: #f8fafc; color: #475569; font-weight: 600; width: 36%;
        }
        .pdx-spec-table tr td:last-child { color: #0f172a; }

        .pdx-tab-spec :is(table) {
            width: 100%; border-collapse: collapse; border: 1px solid #eef2f7;
            border-radius: 8px; overflow: hidden;
        }
        .pdx-tab-spec :is(p, ul, ol) {
            margin: 0 0 14px;
            color: #334155;
            font-size: 14px;
            line-height: 1.78;
            text-align: justify;
        }
        .pdx-tab-spec :is(td, th) {
            padding: 12px 14px; font-size: 13.5px; text-align: left;
            border-bottom: 1px solid #eef2f7;
        }
        .pdx-tab-spec tr:last-child :is(td, th) { border-bottom: 0; }
        .pdx-tab-spec tr :first-child {
            background: #f8fafc; color: #475569; font-weight: 600; width: 36%;
        }

        .pdx-muted { color: #94a3b8; font-size: 14px; }

        /* Reviews */
        .pdx-review-summary {
            display: flex; align-items: baseline; gap: 8px;
            padding: 14px 16px; background: #fffbeb; border-radius: 8px; margin-bottom: 18px;
        }
        .pdx-review-summary strong { color: #b45309; font-size: 15px; }
        .pdx-review-summary span { color: #b45309; font-size: 13px; }
        .pdx-review-form { padding: 16px; background: #f8fafc; border-radius: 10px; }
        .pdx-review-form h4 { font-size: 15px; font-weight: 700; color: #0f172a; margin: 0 0 12px; }
        .pdx-review-fields { display: flex; flex-direction: column; gap: 10px; margin-bottom: 12px; }
        .pdx-review-fields input, .pdx-review-fields textarea {
            width: 100%; padding: 10px 12px;
            border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px;
            font-family: inherit;
        }
        .pdx-review-fields textarea { min-height: 96px; resize: vertical; }
        .pdx-review-fields input:focus, .pdx-review-fields textarea:focus {
            outline: 0; border-color: #14b8a6; box-shadow: 0 0 0 3px rgba(20,184,166,.12);
        }

        /* ===== Related products section header ===== */
        .pdx-section { margin: 0 0 24px; }
        .pdx-section-head {
            display: flex; align-items: center; justify-content: space-between;
            margin: 8px 0 16px; gap: 12px; flex-wrap: wrap;
        }
        .pdx-section-head h2 {
            font-size: 18px; font-weight: 700; color: #0f172a; margin: 0;
        }
        .pdx-section-link {
            color: #0d9488; font-size: 13px; font-weight: 600;
            text-decoration: none; display: inline-flex; align-items: center; gap: 4px;
        }
        .pdx-section-link:hover { color: #0f766e; }
    </style>

    <main class="pdx-main">
        {{-- Legacy compat hooks (hidden, kept for any external integrations) --}}
        <input type="hidden" id="product_viewed_id" value="{{ $product->product_id }}">
        <input type="hidden" id="viewed_productname{{ $product->product_id }}" value="{{ $productName }}">
        <input type="hidden" id="viewed_producturl{{ $product->product_id }}"
            value="{{ url('/chi-tiet/' . $product->product_slug) }}">
        <input type="hidden" id="viewed_productimage{{ $product->product_id }}" value="{{ $primaryImage }}">
        <input type="hidden" id="viewed_productprice{{ $product->product_id }}"
            value="{{ number_format($finalPrice, 0, ',', '.') }}đ">

        <div class="pdx-wrap">
            @include('partials.storefront.breadcrumb', [
                'class' => 'pdx-crumbs',
                'items' => array_filter([
                    [
                        'label' => 'Sản phẩm',
                        'url' => url('/san-pham'),
                    ],
                    $categoryUrl ? [
                        'label' => $productCategoryLabel,
                        'url' => $categoryUrl,
                    ] : null,
                    [
                        'label' => $productName,
                        'current' => true,
                    ],
                ]),
            ])

            {{-- Main 3-column grid --}}
            <div class="pdx-grid">
                {{-- LEFT — Gallery --}}
                <div class="pdx-gallery">
                    <button type="button"
                        class="pdx-wishlist"
                        aria-label="Yêu thích {{ $productName }}"
                        aria-pressed="false"
                        data-tech-wishlist
                        data-product-id="{{ $product->product_id }}"
                        data-product-name="{{ $productName }}"
                        data-product-price="{{ number_format($finalPrice, 0, ',', '.') }}đ"
                        data-product-image="{{ $primaryImage }}"
                        data-product-url="{{ url('/chi-tiet/' . $product->product_slug) }}">
                        <i class="fa fa-heart-o"></i>
                    </button>
                    <ul id="imageGallery" class="pdx-gallery-list">
                        @foreach ($imageSlides as $slide)
                            <li
                                @if ($slide['attribute_key'] !== null) class="pro_attr_image_{{ $slide['attribute_key'] }}" @endif
                                data-thumb="{{ $slide['thumb'] }}" data-src="{{ $slide['src'] }}">
                                <img src="{{ $slide['src'] }}" alt="{{ $slide['alt'] }}">
                            </li>
                        @endforeach
                    </ul>
                </div>

                {{-- CENTER — Info --}}
                <div class="pdx-info">
                    <span class="pdx-brand">{{ mb_strtoupper($productBrandLabel) }}</span>
                    <h1 class="pdx-title">{{ $productName }}</h1>

                    <div class="pdx-meta-row">
                        <div class="pdx-stars" aria-label="Đánh giá trung bình {{ number_format($ratingValue, 1) }} trên 5">
                            @for ($i = 1; $i <= 5; $i++)
                                @if ($i <= floor($ratingValue))
                                    <i class="fa fa-star"></i>
                                @elseif ($i - $ratingValue < 1)
                                    <i class="fa fa-star-half-o"></i>
                                @else
                                    <i class="fa fa-star-o"></i>
                                @endif
                            @endfor
                        </div>
                        <span class="pdx-rating-num">{{ number_format($ratingValue, 1) }}</span>
                        <span>({{ number_format($reviewCount, 0, ',', '.') }} đánh giá)</span>
                        <span class="pdx-meta-dot"></span>
                        <span>Đã bán {{ number_format($soldCount, 0, ',', '.') }}</span>
                    </div>

                    <div class="pdx-price-card" id="price_init">
                        <span class="pdx-price-current">{{ number_format($finalPrice, 0, ',', '.') }}đ</span>
                        @if ($originalPrice > $finalPrice)
                            <div class="pdx-price-meta">
                                <span class="pdx-price-old">{{ number_format($originalPrice, 0, ',', '.') }}đ</span>
                                @if ($discountPercent > 0)
                                    <span class="pdx-price-badge">-{{ $discountPercent }}%</span>
                                @endif
                            </div>
                        @endif
                    </div>
                    <div class="pdx-price-card" id="price_after_click" aria-live="polite" style="display:none;"></div>

                    @if ($installmentMonthly > 0)
                        <div class="pdx-installment">
                            Trả góp 0% chỉ từ <strong>{{ number_format($installmentMonthly, 0, ',', '.') }}đ/tháng</strong>
                            <span class="info-i" title="Áp dụng cho thẻ tín dụng đủ điều kiện">i</span>
                        </div>
                    @endif

                    @if ($featureLines->isNotEmpty())
                        <ul class="pdx-features">
                            @foreach ($featureLines as $feature)
                                <li><i class="fa fa-check-circle"></i><span>{{ $feature }}</span></li>
                            @endforeach
                        </ul>
                    @endif

                    <form class="shopw-pdp-purchase"
                        data-default-stock="{{ max(0, (int) $product->product_quantity) }}"
                        data-default-label="Thêm vào giỏ hàng"
                        onsubmit="return false;"
                        style="display: flex; flex-direction: column; gap: 16px; margin-top: 4px;">
                        @csrf
                        <input type="hidden" class="cart_product_id_attribute" value="0">
                        <input type="hidden" class="product_id" value="{{ $product->product_id }}">
                        <input type="hidden" class="cart_product_price" value="{{ $finalPrice }}">
                        <input type="hidden" class="cart_price_attribute" value="{{ $finalPrice }}">

                        {{-- Variant groups (smart-detected) --}}
                        @foreach (['storage', 'ram', 'color', 'other'] as $bucketKey)
                            @if (! empty($variantBuckets[$bucketKey]))
                                <div class="pdx-variant">
                                    <span class="pdx-variant-label">{{ $bucketLabels[$bucketKey] }}</span>
                                    <div class="pdx-chips">
                                        @foreach ($variantBuckets[$bucketKey] as $entry)
                                            @php
                                                $idx = $entry['index'];
                                                $attribute = $entry['attribute'];
                                                $label = $entry['label'];
                                                $isColor = $bucketKey === 'color';
                                                $swatch = $isColor ? $resolveColorSwatch($label) : null;
                                            @endphp
                                            <button type="button"
                                                class="pdx-chip pro_attr_image_click {{ $isColor ? 'pdx-chip--color' : '' }}"
                                                data-key_image="{{ $idx }}"
                                                data-id_attribute="{{ $attribute->id }}"
                                                data-name_attribute="{{ $label }}"
                                                data-price_attribute="{{ $attribute->price }}"
                                                data-qty_attribute="{{ $attribute->quantity }}"
                                                data-image_attribute="{{ $attribute->image }}">
                                                @if ($isColor)
                                                    <span class="pdx-chip-swatch" style="background: {{ $swatch }};"></span>
                                                @endif
                                                <strong>{{ $label }}</strong>
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        @endforeach

                        <div class="pdx-variant">
                            <span class="pdx-variant-label">Số lượng</span>
                            <div class="pdx-qty">
                                <button type="button" onclick="pdxDecreaseQty()" aria-label="Giảm số lượng">−</button>
                                <input id="product-detail-qty" name="qty" type="number" min="1"
                                    max="{{ max(1, (int) $product->product_quantity) }}"
                                    class="cart_product_qty"
                                    value="{{ $stockAvailable ? 1 : 0 }}"
                                    {{ $stockAvailable ? '' : 'disabled' }}>
                                <button type="button" onclick="pdxIncreaseQty()" aria-label="Tăng số lượng">+</button>
                            </div>
                        </div>

                        <div class="pdx-actions">
                            <button type="button" class="pdx-btn pdx-btn--ghost add-to-cart" {{ $stockAvailable ? '' : 'disabled' }}>
                                <i class="fa fa-shopping-bag"></i>
                                <span class="shopw-pdp-purchase-btn-label">Thêm vào giỏ hàng</span>
                            </button>
                            <button type="button" class="pdx-btn pdx-btn--primary" {{ $stockAvailable ? '' : 'disabled' }} onclick="pdxBuyNow()">
                                <span class="pdx-btn-strong">Mua ngay</span>
                                <span class="pdx-btn-sub">Giao hàng tận nơi</span>
                            </button>
                        </div>

                        <div id="product-detail-purchase-status" class="pdx-status shopw-pdp-purchase-status" aria-live="polite"></div>
                    </form>

                    <div class="pdx-support">
                        <span class="pdx-support-item">
                            <i class="fa fa-phone"></i>
                            Hỗ trợ mua hàng:
                            <a href="{{ $supportPhoneHref }}">{{ $supportPhoneDisplay }}</a>
                            <small>(8:00 - 21:00)</small>
                        </span>
                        <span class="pdx-support-sep"></span>
                        <a href="#" class="pdx-support-link" data-chatbot-launch>
                            <i class="fa fa-comments-o"></i>
                            Chat với chúng tôi
                        </a>
                    </div>

                    @if ($tagList->isNotEmpty())
                        <div class="pdx-tags">
                            <span>Từ khóa:</span>
                            @foreach ($tagList as $tag)
                                <a href="{{ url('/tag/' . Str::slug($tag)) }}" class="pdx-tag-chip">{{ $tag }}</a>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- RIGHT — Sidebar --}}
                <aside class="pdx-side">
                    <div class="pdx-side-card">
                        @foreach ($promoCards as $promo)
                            <div class="pdx-side-row">
                                <span class="pdx-side-icon"><i class="fa {{ $promo['icon'] }}"></i></span>
                                <div class="pdx-side-text">
                                    <strong>{{ $promo['title'] }}</strong>
                                    <span>{{ $promo['sub'] }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="pdx-side-card">
                        <h3 class="pdx-side-title">Ưu đãi khi mua</h3>
                        @foreach ($dealItems as $deal)
                            <div class="pdx-side-row">
                                <span class="pdx-side-icon"><i class="fa {{ $deal['icon'] }}"></i></span>
                                <div class="pdx-side-text">
                                    <strong>{{ $deal['title'] }}</strong>
                                    <span>{{ $deal['sub'] }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </aside>
            </div>

            {{-- Service policies strip --}}
            <div class="pdx-policies">
                @foreach ($servicePolicies as $policy)
                    <div class="pdx-policy">
                        <span class="pdx-policy-icon"><i class="fa {{ $policy['icon'] }}"></i></span>
                        <div>
                            <strong>{{ $policy['title'] }}</strong>
                            <span>{{ $policy['description'] }}</span>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Tabs --}}
            <div class="pdx-tabs">
                <div class="pdx-tabs-nav" role="tablist">
                    <button type="button" class="pdx-tab-btn is-active" data-pdx-tab="mota" role="tab">Mô tả sản phẩm</button>
                    <button type="button" class="pdx-tab-btn" data-pdx-tab="thongso" role="tab">Thông số kỹ thuật</button>
                    <button type="button" class="pdx-tab-btn" data-pdx-tab="danhgia" role="tab">Đánh giá ({{ $reviewCount }})</button>
                    <button type="button" class="pdx-tab-btn" data-pdx-tab="hoidap" role="tab">Hỏi đáp (36)</button>
                </div>

                {{-- Tab: Mô tả --}}
                <div class="pdx-tab-pane is-active" id="mota" role="tabpanel">
                    <div class="pdx-tab-grid">
                        <div class="pdx-tab-desc">
                            @if ($productDescriptionHtml)
                                {!! $productDescriptionHtml !!}
                            @else
                                <p class="pdx-muted">Hiện chưa có mô tả sản phẩm.</p>
                            @endif
                        </div>
                        @if ($productContentHtml)
                            <div class="pdx-tab-spec">
                                {!! $productContentHtml !!}
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Tab: Thông số --}}
                <div class="pdx-tab-pane" id="thongso" role="tabpanel">
                    <div class="pdx-tab-spec">
                        @if ($productContentHtml)
                            {!! $productContentHtml !!}
                        @else
                            <p class="pdx-muted">Hiện chưa có thông số kỹ thuật.</p>
                        @endif
                    </div>
                </div>

                {{-- Tab: Đánh giá --}}
                <div class="pdx-tab-pane" id="danhgia" role="tabpanel">
                    <div class="pdx-review-summary">
                        <strong>Đánh giá trung bình: {{ number_format($ratingValue, 1) }}/5</strong>
                        <span>({{ number_format($reviewCount, 0, ',', '.') }} đánh giá)</span>
                    </div>

                    <div class="shopw-pdp-review-thread-shell" style="margin-bottom: 18px;">
                        @csrf
                        <input type="hidden" name="comment_product_id" class="comment_product_id" value="{{ $product->product_id }}">
                        <div id="comment_show"></div>
                    </div>

                    <div class="pdx-review-form shopw-pdp-review-form-shell">
                        <h4>Viết đánh giá của bạn</h4>
                        <ul class="list-inline rating" style="padding:0; list-style:none; display:flex; gap:6px; margin:0 0 12px;" title="Đánh giá trung bình">
                            @for ($count = 1; $count <= 5; $count++)
                                @php
                                    $color = $count <= $ratingValue ? 'color:#f59e0b;' : 'color:#cbd5e1;';
                                @endphp
                                <li title="Chọn {{ $count }} sao" id="{{ $product->product_id }}-{{ $count }}"
                                    data-index="{{ $count }}" data-product_id="{{ $product->product_id }}"
                                    data-rating="{{ $ratingValue }}" class="rating"
                                    style="cursor:pointer; {{ $color }} font-size:26px;">&#9733;</li>
                            @endfor
                        </ul>
                        <div class="pdx-review-fields">
                            <input type="text" class="comment_name" placeholder="Tên của bạn">
                            <textarea name="comment" class="comment_content" placeholder="Nội dung đánh giá"></textarea>
                        </div>
                        <div id="notify_comment" style="margin-bottom:8px;"></div>
                        <button type="button" class="pdx-btn pdx-btn--primary send-comment" style="display:inline-flex; max-width:240px;">
                            <span class="pdx-btn-strong">Gửi đánh giá</span>
                        </button>
                    </div>
                </div>

                {{-- Tab: Hỏi đáp --}}
                <div class="pdx-tab-pane" id="hoidap" role="tabpanel">
                    <p class="pdx-muted">Tính năng hỏi đáp đang được hoàn thiện. Vui lòng quay lại sau hoặc liên hệ hotline {{ $supportPhoneDisplay }} để được hỗ trợ.</p>
                </div>
            </div>

            {{-- Related products --}}
            @if ($relatedProductCards->isNotEmpty())
                <section class="pdx-section">
                    <div class="pdx-section-head">
                        <h2>Sản phẩm liên quan</h2>
                        <a href="{{ url('/san-pham') }}" class="pdx-section-link">Xem tất cả <i class="fa fa-angle-right"></i></a>
                    </div>
                    <div class="row tech-product-grid">
                        @foreach ($relatedProductCards as $relatedCard)
                            <div class="col-xs-12 col-sm-6 col-md-3 col-lg-2-4">
                                @include('partials.home.product-card', [
                                    'product' => $relatedCard,
                                ])
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif
        </div>

        @include('partials.home.news', ['newsItems' => $newsItems])
        @include('partials.home.partners', ['brandItems' => $brandItems])
    </main>

    <script>
        function pdxDecreaseQty() {
            var input = document.getElementById('product-detail-qty');
            if (input && parseInt(input.value, 10) > 1) {
                input.value = parseInt(input.value, 10) - 1;
            }
        }
        function pdxIncreaseQty() {
            var input = document.getElementById('product-detail-qty');
            if (!input) return;
            var max = parseInt(input.getAttribute('max'), 10) || 1;
            if (parseInt(input.value, 10) < max) {
                input.value = parseInt(input.value, 10) + 1;
            }
        }

        function pdxBuyNow() {
            if (typeof $ === 'undefined' || typeof addProductToCart !== 'function') {
                alert('Chức năng mua ngay tạm thời không khả dụng.');
                return;
            }
            var id_product = $('.product_id').val();
            var id_attribute = $('.cart_product_id_attribute').val();
            var maxQuantity = parseInt($('.cart_product_qty').attr('max') || 0, 10);
            var cart_product_qty = parseInt($('.cart_product_qty').val() || 1, 10);
            var _token = $('input[name="_token"]').val();

            if (typeof clearStorefrontInlineStatus === 'function') {
                clearStorefrontInlineStatus('#product-detail-purchase-status');
            }
            if (!maxQuantity) {
                if (typeof setStorefrontInlineStatus === 'function') {
                    setStorefrontInlineStatus('#product-detail-purchase-status',
                        'Sản phẩm hiện chưa sẵn sàng để mua.', 'warning');
                }
                return;
            }
            if (cart_product_qty > maxQuantity) {
                $('.cart_product_qty').val(maxQuantity);
                if (typeof setStorefrontInlineStatus === 'function') {
                    setStorefrontInlineStatus('#product-detail-purchase-status',
                        'Sản phẩm chỉ còn ' + maxQuantity + ' sản phẩm.', 'warning');
                }
                return;
            }
            addProductToCart({
                id_product: id_product,
                id_attribute: id_attribute,
                cart_product_qty: cart_product_qty,
                _token: _token
            }, {
                onSuccess: function () {
                    if (typeof refreshStorefrontCartUi === 'function') {
                        refreshStorefrontCartUi();
                    }
                    window.location.href = '{{ url('/gio-hang') }}';
                },
                onError: function (xhr) {
                    var errorMessage = 'Không thể thêm sản phẩm vào giỏ hàng lúc này.';
                    if (xhr && xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    if (typeof setStorefrontInlineStatus === 'function') {
                        setStorefrontInlineStatus('#product-detail-purchase-status', errorMessage, 'error');
                    }
                }
            });
        }

        // Tabs
        document.querySelectorAll('.pdx-tab-btn').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var target = this.getAttribute('data-pdx-tab');
                document.querySelectorAll('.pdx-tab-btn').forEach(function (b) { b.classList.remove('is-active'); });
                document.querySelectorAll('.pdx-tab-pane').forEach(function (p) { p.classList.remove('is-active'); });
                this.classList.add('is-active');
                var pane = document.getElementById(target);
                if (pane) pane.classList.add('is-active');
            });
        });
    </script>
@endsection
