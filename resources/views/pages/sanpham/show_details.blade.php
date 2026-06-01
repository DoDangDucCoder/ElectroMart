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
        $primaryImage = \App\Support\AssetUrl::productVariant($product->product_image, 'large');
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
            $attributeSource = ! empty($attribute->image) ? 'uploads/attribute/' . $attribute->image : null;
            $attributeImage = \App\Support\AssetUrl::productVariant($attributeSource, 'medium');
            $attributeSlides[] = [
                'thumb' => \App\Support\AssetUrl::productVariant($attributeSource, 'thumb'),
                'src' => $attributeImage,
                'alt' => $storefrontLabel::neutralizeSource($attribute->name ?: $productName),
                'attribute_key' => $index,
            ];
        }

        $gallerySlides = [];
        foreach ($gallery as $media) {
            $gallerySource = $media->file_path ?? (! empty($media->gallery_image) ? 'uploads/gallery/' . $media->gallery_image : ($media->url ?? null));
            $galleryUrl = $media->url
                ?? \App\Support\AssetUrl::resolve(
                    $media->file_path ?? (! empty($media->gallery_image) ? 'uploads/gallery/' . $media->gallery_image : null),
                    null,
                );

            if (! $galleryUrl) {
                continue;
            }

            $gallerySlides[] = [
                'thumb' => \App\Support\AssetUrl::productVariant($gallerySource, 'thumb'),
                'src' => \App\Support\AssetUrl::productVariant($gallerySource, 'medium'),
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
                    return \App\Support\AssetUrl::productVariant($localPath, 'thumb');
                }

                if (! str_contains($localPath, '/') && file_exists(public_path('uploads/product/' . $localPath))) {
                    return \App\Support\AssetUrl::productVariant('uploads/product/' . $localPath, 'thumb');
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
            ->take(8)
            ->values();

        $relatedLimit = $relatedProductCards->count() >= 8 ? 8 : min(4, $relatedProductCards->count());
        $relatedProductCards = $relatedProductCards->take($relatedLimit)->values();

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

        // Fix #11: Lấy bài viết liên quan từ DB thật thay vì hardcode static
        // Title và URL cùng một bản ghi → không bị lệch
        $newsItems = collect(\Illuminate\Support\Facades\DB::table('blog_posts')
            ->where('status', 'published')
            ->orderByDesc('published_at')
            ->limit(3)
            ->get(['id', 'title', 'slug', 'excerpt', 'published_at', 'featured_image_path']))
            ->map(fn($post) => [
                'title'        => $post->title,
                'url'          => url('/bai-viet/' . $post->slug),
                'summary'      => $post->excerpt ?? '',
                'published_at' => $post->published_at ? \Carbon\Carbon::parse($post->published_at) : now(),
                'thumbnail'    => \App\Support\AssetUrl::resolve($post->featured_image_path, 'assets/news/anker-735-review-1-year.svg'),
            ]);
        // Fallback nếu không có bài viết trong DB
        if ($newsItems->isEmpty()) {
            $newsItems = collect();
        }

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

        .pdx-wrap { max-width: 1240px; margin: 0 auto; padding: 0 16px; }

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

        /* Main grid: gallery | product summary | service sidebar */
        .pdx-grid {
            display: grid;
            grid-template-columns: minmax(0, 400px) minmax(0, 1fr) minmax(260px, 280px);
            gap: 16px;
            align-items: stretch;
        }
        @media (max-width: 1199px) {
            .pdx-grid { grid-template-columns: minmax(0, 1fr) minmax(0, 1fr); }
            .pdx-side { grid-column: 1 / -1; display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        }
        @media (max-width: 767px) {
            .pdx-grid, .pdx-side { grid-template-columns: 1fr; }
        }
        .pdx-left-stack,
        .pdx-right-stack {
            min-width: 0;
            height: 100%;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        /* ===== Gallery card ===== */
        .pdx-gallery {
            background: #fff;
            border-radius: 12px;
            border: 1px solid #eef2f7;
            padding: 12px;
            position: relative;
            height: 100%;
            display: flex;
            flex-direction: column;
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

        .pdx-main-image {
            flex: 1 1 auto;
            display: grid;
            place-items: center;
            min-height: 328px;
            border: 1px solid #eef2f7;
            border-radius: 12px;
            background: #fff;
            overflow: hidden;
        }
        .pdx-main-image img {
            display: block;
            width: 100%;
            max-height: 350px;
            object-fit: contain;
            aspect-ratio: 1 / 1;
        }
        .pdx-thumbs {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 8px;
            margin-top: 10px;
            align-items: stretch;
        }
        .pdx-thumb {
            display: grid;
            place-items: center;
            height: 72px;
            min-height: 72px;
            max-height: 72px;
            padding: 6px;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            background: #fff;
            cursor: pointer;
            overflow: hidden;
        }
        .pdx-thumb.is-active,
        .pdx-thumb:hover {
            border-color: #14b8a6;
        }
        .pdx-thumb img {
            display: block;
            width: 100%;
            height: 100% !important;
            max-width: 100%;
            max-height: 100% !important;
            object-fit: contain !important;
        }
        .pdx-gallery-list { list-style: none; margin: 0; padding: 0; }
        .pdx-gallery-list[hidden] { display: none !important; }
        .pdx-gallery-list > li { list-style: none; }
        .pdx-gallery-list:not(.lightSlider) > li { display: none; }
        .pdx-gallery-list:not(.lightSlider) > li:first-child { display: block; }
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
        .pdx-gallery .lSGallery img {
            width: 100%;
            height: 76px;
            object-fit: contain;
            border: 1px solid #eef2f7;
            border-radius: 8px;
            background: #fff;
        }

        .pdx-spec-card,
        .pdx-review-card {
            background: #fff;
            border: 1px solid #eef2f7;
            border-radius: 12px;
            padding: 14px;
        }
        .pdx-card-title {
            margin: 0 0 12px;
            color: #0f172a;
            font-size: 16px;
            font-weight: 800;
            text-transform: uppercase;
        }
        .pdx-review-score {
            display: grid;
            grid-template-columns: auto 1fr;
            gap: 18px;
            align-items: center;
        }
        .pdx-review-big {
            color: #0f172a;
            font-size: 48px;
            font-weight: 800;
            line-height: 1;
        }
        .pdx-review-big span {
            color: #475569;
            font-size: 28px;
        }
        .pdx-review-bars {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        .pdx-review-bar {
            display: grid;
            grid-template-columns: 32px 1fr 34px;
            gap: 8px;
            align-items: center;
            color: #64748b;
            font-size: 12px;
        }
        .pdx-review-track {
            height: 8px;
            overflow: hidden;
            border-radius: 99px;
            background: #e5e7eb;
        }
        .pdx-review-fill {
            display: block;
            height: 100%;
            border-radius: inherit;
            background: #f59e0b;
        }

        /* ===== Info card ===== */
        .pdx-info {
            background: #fff;
            border-radius: 12px;
            border: 1px solid #eef2f7;
            padding: 18px;
            min-height: 100%;
            display: flex; flex-direction: column; gap: 12px;
            justify-content: flex-start;
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
            background: #fff5f5; border-radius: 10px; padding: 12px 14px;
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
            margin-top: 2px !important;
            gap: 12px !important;
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
            min-height: 56px;
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
            margin-top: auto; padding-top: 12px; border-top: 1px solid #eef2f7;
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
        .pdx-side {
            display: flex;
            flex-direction: column;
            gap: 16px;
            height: 100%;
            align-self: stretch;
        }
        .pdx-side-card {
            background: #fff; border: 1px solid #eef2f7; border-radius: 12px;
            padding: 16px;
            display: flex; flex-direction: column; gap: 14px;
        }
        .pdx-side-card:first-child { flex: 1 1 auto; }
        .pdx-side-card:last-child { flex: 0 0 auto; }
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
            display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)) minmax(260px, 280px); gap: 16px;
            margin: 16px 0;
            align-items: stretch;
        }
        .pdx-policies .pdx-policy:last-child {
            grid-column: 4;
        }
        @media (max-width: 1199px) {
            .pdx-policies { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .pdx-policies .pdx-policy:last-child { grid-column: auto; }
        }
        @media (max-width: 575px) { .pdx-policies { grid-template-columns: 1fr; } }
        .pdx-policy {
            background: #fff; border: 1px solid #eef2f7; border-radius: 12px;
            min-height: 78px;
            padding: 14px;
            display: flex; align-items: center; gap: 10px;
        }
        .pdx-policy-icon {
            flex: 0 0 42px; width: 42px; height: 42px; border-radius: 12px;
            background: #f0fdfa; color: #0d9488;
            display: inline-flex; align-items: center; justify-content: center;
            font-size: 18px;
            position: relative;
        }
        .pdx-policy-icon .fa {
            display: block !important;
            width: auto !important;
            height: auto !important;
            margin: 0 !important;
            font-size: 18px !important;
            line-height: 1 !important;
            position: absolute !important;
            left: 50% !important;
            top: 50% !important;
            transform: translate(-50%, -50%) !important;
        }
        .pdx-policy strong { display: block; font-size: 14px; color: #0f172a; margin-bottom: 4px; }
        .pdx-policy span { font-size: 12.5px; color: #64748b; line-height: 1.45; display: block; }

        /* ===== Vertical Tabs section ===== */
        .pdx-tabs {
            background: #fff; border: 1px solid #eef2f7; border-radius: 12px;
            margin-bottom: 24px; overflow: hidden;
            display: grid;
            grid-template-columns: 220px 1fr;
            min-height: 320px;
        }
        /* Sidebar nav */
        .pdx-tabs-nav {
            display: flex;
            flex-direction: column;
            gap: 0;
            border-right: 1px solid #eef2f7;
            padding: 8px 0;
            background: #f8fafc;
            border-radius: 12px 0 0 12px;
        }
        .pdx-tab-btn {
            background: transparent;
            border: none;
            border-left: 3px solid transparent;
            padding: 14px 20px;
            font-size: 14px; font-weight: 600; color: #64748b;
            cursor: pointer;
            text-align: left;
            white-space: nowrap;
            display: flex; align-items: center; gap: 10px;
            transition: background .15s, color .15s, border-color .15s;
            line-height: 1.4;
        }
        .pdx-tab-btn i.pdx-tab-icon {
            font-size: 15px;
            width: 18px;
            text-align: center;
            flex-shrink: 0;
            color: #94a3b8;
            transition: color .15s;
        }
        .pdx-tab-btn:hover {
            background: #f0fdfa;
            color: #0d9488;
            border-left-color: #99f6e4;
        }
        .pdx-tab-btn:hover i.pdx-tab-icon { color: #0d9488; }
        .pdx-tab-btn.is-active {
            background: #f0fdfa;
            color: #0d9488;
            border-left-color: #14b8a6;
            font-weight: 700;
        }
        .pdx-tab-btn.is-active i.pdx-tab-icon { color: #14b8a6; }
        /* Content area */
        .pdx-tabs-content {
            min-width: 0;
        }
        .pdx-tab-pane { display: none; padding: 28px 28px 24px; max-height: none; overflow: visible; }
        .pdx-tab-pane.is-active { display: block; }
        .pdx-tab-grid {
            display: grid; grid-template-columns: 1fr; gap: 24px;
        }
        .pdx-tab-desc :is(h2, h3, h4) { margin: 18px 0 12px; color: #0f172a; font-size: 16px; }
        .pdx-tab-desc :is(p, ul, ol) { margin: 0 0 14px; color: #334155; font-size: 14px; line-height: 1.78; text-align: justify; }
        .pdx-tab-desc ul li { margin-bottom: 6px; }

        /* Mobile: accordion */
        @media (max-width: 767px) {
            .pdx-tabs {
                grid-template-columns: 1fr;
                display: block;
            }
            .pdx-tabs-nav {
                flex-direction: column;
                border-right: none;
                border-bottom: 1px solid #eef2f7;
                border-radius: 12px 12px 0 0;
                padding: 0;
                background: #f8fafc;
            }
            .pdx-tab-btn {
                border-left: none;
                border-bottom: 1px solid #eef2f7;
                padding: 14px 18px;
                justify-content: space-between;
                white-space: normal;
            }
            .pdx-tab-btn:last-child { border-bottom: none; }
            .pdx-tab-btn::after {
                content: '\f107';
                font-family: FontAwesome;
                font-size: 14px;
                color: #94a3b8;
                transition: transform .2s;
                flex-shrink: 0;
            }
            .pdx-tab-btn.is-active::after {
                transform: rotate(180deg);
                color: #14b8a6;
            }
            .pdx-tab-btn.is-active {
                border-left: none;
                border-left-color: transparent;
            }
            .pdx-tab-pane { padding: 20px 16px; }
            .pdx-tabs-content {
                display: block;
            }
        }

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

            {{-- Main 3-column detail layout --}}
            <div class="pdx-grid">
                <div class="pdx-left-stack">
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
                            data-product-url="{{ url('/chi-tiet/' . $product->product_slug) }}"
                            data-product-category-id="{{ $product->primary_category_id ?? ($primaryCategory ?? null)?->id }}"
                            data-product-category="{{ $productCategoryLabel }}">
                            <i class="fa fa-heart-o"></i>
                        </button>
                        @php $visibleSlides = collect($imageSlides)->take(4)->values(); @endphp
                        <div class="pdx-main-image">
                            <img id="pdx-main-product-image"
                                src="{{ $imageSlides[0]['src'] }}"
                                alt="{{ $imageSlides[0]['alt'] }}"
                                loading="eager"
                                decoding="async">
                        </div>
                        <div class="pdx-thumbs" aria-label="Ảnh sản phẩm">
                            @foreach ($visibleSlides as $thumbIndex => $slide)
                                <button type="button"
                                    class="pdx-thumb {{ $thumbIndex === 0 ? 'is-active' : '' }}"
                                    data-pdx-thumb
                                    data-pdx-slide-key="{{ $slide['attribute_key'] ?? $thumbIndex }}"
                                    data-pdx-image="{{ $slide['src'] }}"
                                    data-pdx-alt="{{ $slide['alt'] }}"
                                    aria-label="Xem ảnh {{ $thumbIndex + 1 }}">
                                    <img src="{{ $slide['thumb'] }}" alt="{{ $slide['alt'] }}" loading="lazy" decoding="async">
                                </button>
                            @endforeach
                        </div>

                        <ul id="imageGallery" class="pdx-gallery-list" hidden>
                            @foreach ($imageSlides as $slide)
                                <li
                                    @if ($slide['attribute_key'] !== null) class="pro_attr_image_{{ $slide['attribute_key'] }}" @endif
                                    data-thumb="{{ $slide['thumb'] }}" data-src="{{ $slide['src'] }}">
                                <img src="{{ $slide['src'] }}" alt="{{ $slide['alt'] }}" loading="lazy" decoding="async">
                                </li>
                            @endforeach
                        </ul>
                    </div>

                </div>

                <div class="pdx-right-stack">
                    {{-- RIGHT — Info --}}
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
                                    data-available-stock="{{ max(0, (int) $product->product_quantity) }}"
                                    class="cart_product_qty"
                                    value="1">
                                <button type="button" onclick="pdxIncreaseQty()" aria-label="Tăng số lượng">+</button>
                            </div>
                        </div>

                        <div class="pdx-actions">
                            <button type="button" class="pdx-btn pdx-btn--ghost add-to-cart" data-pdx-add-cart>
                                <i class="fa fa-shopping-cart"></i>
                                <span class="shopw-pdp-purchase-btn-label">Thêm vào giỏ hàng</span>
                            </button>
                            <button type="button" class="pdx-btn pdx-btn--primary" data-pdx-buy-now {{ $stockAvailable ? '' : 'disabled' }}>
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
                        <a href="{{ url('/lien-he') }}" class="pdx-support-link">
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

            {{-- Vertical Tabs --}}
            <div class="pdx-tabs">
                {{-- Sidebar menu dọc --}}
                <nav class="pdx-tabs-nav" role="tablist" aria-label="Thông tin sản phẩm">
                    <button type="button" class="pdx-tab-btn is-active" data-pdx-tab="mota"
                        role="tab" aria-selected="true" aria-controls="mota">
                        <i class="fa fa-file-text-o pdx-tab-icon"></i>
                        Mô tả sản phẩm
                    </button>
                    <button type="button" class="pdx-tab-btn" data-pdx-tab="thongso"
                        role="tab" aria-selected="false" aria-controls="thongso">
                        <i class="fa fa-list-alt pdx-tab-icon"></i>
                        Thông số kỹ thuật
                    </button>
                    <button type="button" class="pdx-tab-btn" data-pdx-tab="danhgia"
                        role="tab" aria-selected="false" aria-controls="danhgia">
                        <i class="fa fa-star-o pdx-tab-icon"></i>
                        Đánh giá ({{ $reviewCount }})
                    </button>
                    {{-- Tab Hỏi đáp ẩn tạm vì chức năng chưa hoàn thiện --}}
                    {{-- <button type="button" class="pdx-tab-btn" data-pdx-tab="hoidap" role="tab">Hỏi đáp</button> --}}
                </nav>

                {{-- Vùng nội dung bên phải --}}
                <div class="pdx-tabs-content">
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
            </div>

            @include('pages.sanpham.partials.quick-compare', [
                'products' => $quick_compare_products ?? collect(),
                'rows' => $quick_compare_rows ?? [],
            ])

            {{-- Related products --}}
            @if ($relatedProductCards->isNotEmpty())
                <section class="pdx-section related-products">
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

        function pdxSetStatus(message, type) {
            if (typeof window.setStorefrontInlineStatus === 'function') {
                window.setStorefrontInlineStatus('#product-detail-purchase-status', message, type || 'info');
                return;
            }
            var status = document.getElementById('product-detail-purchase-status');
            if (status) status.textContent = message || '';
        }

        function pdxClearStatus() {
            if (typeof window.clearStorefrontInlineStatus === 'function') {
                window.clearStorefrontInlineStatus('#product-detail-purchase-status');
                return;
            }
            var status = document.getElementById('product-detail-purchase-status');
            if (status) status.textContent = '';
        }

        function pdxCartPayload() {
            var qtyInput = document.querySelector('.cart_product_qty');
            return {
                id_product: (document.querySelector('.product_id') || {}).value || '',
                id_attribute: (document.querySelector('.cart_product_id_attribute') || {}).value || '0',
                cart_product_qty: (qtyInput || {}).value || '1',
                _token: (document.querySelector('input[name="_token"]') || {}).value || ''
            };
        }

        function pdxAvailableStock() {
            var qtyInput = document.querySelector('.cart_product_qty');
            return parseInt(qtyInput ? qtyInput.getAttribute('data-available-stock') || '0' : '0', 10);
        }

        function pdxRestorePurchaseButtons() {
            var availableStock = pdxAvailableStock();
            document.querySelectorAll('[data-pdx-add-cart]').forEach(function (button) {
                button.disabled = false;
            });
            document.querySelectorAll('[data-pdx-buy-now]').forEach(function (button) {
                button.disabled = availableStock <= 0;
            });
        }

        function pdxValidateCartQuantity(redirectToCart) {
            var qtyInput = document.querySelector('.cart_product_qty');
            var availableStock = pdxAvailableStock();
            var maxQuantity = parseInt(qtyInput ? qtyInput.getAttribute('max') || '1' : '1', 10);
            var quantity = parseInt(qtyInput ? qtyInput.value || '1' : '1', 10);

            pdxClearStatus();

            if (availableStock <= 0) {
                if (redirectToCart) {
                    pdxSetStatus('Sản phẩm hiện đã hết hàng nên chưa thể mua ngay.', 'warning');
                    return false;
                }

                if (qtyInput) qtyInput.value = 1;
                return true;
            }

            if (!maxQuantity) {
                pdxSetStatus('Sản phẩm hiện chưa sẵn sàng để mua.', 'warning');
                return false;
            }

            if (quantity > maxQuantity) {
                if (qtyInput) qtyInput.value = maxQuantity;
                pdxSetStatus('Sản phẩm chỉ còn ' + maxQuantity + ' sản phẩm.', 'warning');
                return false;
            }

            return true;
        }

        function pdxSubmitCart(redirectToCart) {
            if (!pdxValidateCartQuantity(redirectToCart)) return;

            var payload = pdxCartPayload();
            var buttons = document.querySelectorAll('[data-pdx-add-cart], [data-pdx-buy-now]');
            buttons.forEach(function (button) { button.disabled = true; });

            if (typeof window.addProductToCart === 'function') {
                window.addProductToCart(payload, {
                    fallbackMessage: 'Không thể thêm sản phẩm vào giỏ hàng lúc này.',
                    beforeSend: function () {
                        buttons.forEach(function (button) { button.disabled = true; });
                    },
                    onSuccess: function () {
                        pdxRestorePurchaseButtons();
                        if (typeof window.refreshStorefrontCartUi === 'function') {
                            window.refreshStorefrontCartUi();
                        }
                        if (redirectToCart) {
                            window.location.href = '{{ url('/gio-hang') }}';
                            return;
                        }
                        pdxSetStatus('Đã thêm sản phẩm vào giỏ hàng.', 'success');
                    },
                    onError: function (xhr) {
                        pdxRestorePurchaseButtons();
                        var message = 'Không thể thêm sản phẩm vào giỏ hàng lúc này.';
                        if (typeof window.getAjaxErrorMessage === 'function') {
                            message = window.getAjaxErrorMessage(xhr, message);
                        } else if (xhr && xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        }
                        pdxSetStatus(message, 'error');
                    }
                });
                return;
            }

            fetch('{{ url('/add-cart-ajax') }}', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': (document.querySelector('meta[name="csrf-token"]') || {}).content || payload._token,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify(payload)
            })
                .then(function (response) {
                    return response.json().then(function (data) {
                        if (!response.ok || !data.success) {
                            throw new Error(data.message || 'Không thể thêm sản phẩm vào giỏ hàng.');
                        }
                        return data;
                    });
                })
                .then(function () {
                    if (typeof window.refreshStorefrontCartUi === 'function') {
                        window.refreshStorefrontCartUi();
                    }
                    if (redirectToCart) {
                        window.location.href = '{{ url('/gio-hang') }}';
                        return;
                    }
                    pdxSetStatus('Đã thêm sản phẩm vào giỏ hàng.', 'success');
                })
                .catch(function (error) {
                    pdxSetStatus(error.message || 'Không thể thêm sản phẩm vào giỏ hàng lúc này.', 'error');
                })
                .finally(function () {
                    pdxRestorePurchaseButtons();
                });
        }

        function pdxBuyNow() {
            pdxSubmitCart(true);
        }

        document.addEventListener('click', function (event) {
            var addButton = event.target.closest ? event.target.closest('[data-pdx-add-cart]') : null;
            var buyButton = event.target.closest ? event.target.closest('[data-pdx-buy-now]') : null;

            if (!addButton && !buyButton) {
                return;
            }

            event.preventDefault();
            event.stopPropagation();
            if (typeof event.stopImmediatePropagation === 'function') {
                event.stopImmediatePropagation();
            }

            pdxSubmitCart(!!buyButton);
        }, true);

        // Vertical Tabs
        document.querySelectorAll('.pdx-tab-btn').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var target = this.getAttribute('data-pdx-tab');
                // Cập nhật trạng thái nút
                document.querySelectorAll('.pdx-tab-btn').forEach(function (b) {
                    b.classList.remove('is-active');
                    b.setAttribute('aria-selected', 'false');
                });
                // Ẩn tất cả pane
                document.querySelectorAll('.pdx-tab-pane').forEach(function (p) { p.classList.remove('is-active'); });
                // Kích hoạt nút và pane được chọn
                this.classList.add('is-active');
                this.setAttribute('aria-selected', 'true');
                var pane = document.getElementById(target);
                if (pane) pane.classList.add('is-active');
            });
        });

        (function () {
            var mainImage = document.getElementById('pdx-main-product-image');
            var thumbs = Array.prototype.slice.call(document.querySelectorAll('[data-pdx-thumb]'));

            function showImage(button) {
                if (!mainImage || !button) return;
                mainImage.src = button.getAttribute('data-pdx-image') || mainImage.src;
                mainImage.alt = button.getAttribute('data-pdx-alt') || mainImage.alt;
                thumbs.forEach(function (thumb) { thumb.classList.remove('is-active'); });
                button.classList.add('is-active');
            }

            thumbs.forEach(function (thumb) {
                thumb.addEventListener('click', function () { showImage(thumb); });
            });

            document.querySelectorAll('.pro_attr_image_click').forEach(function (option) {
                option.addEventListener('click', function () {
                    var key = String(option.getAttribute('data-key_image') || '');
                    var matched = thumbs.find(function (thumb) {
                        return String(thumb.getAttribute('data-pdx-slide-key') || '') === key;
                    });
                    if (matched) showImage(matched);
                });
            });
        })();

        document.querySelectorAll('[data-pdx-compare-toggle]').forEach(function (toggle) {
            toggle.addEventListener('change', function () {
                var id = this.getAttribute('data-pdx-compare-toggle');
                document.querySelectorAll('[data-pdx-compare-column="' + id + '"]').forEach(function (cell) {
                    cell.hidden = !toggle.checked;
                });
            });
            toggle.dispatchEvent(new Event('change'));
        });

        (function () {
            var compareSection = document.querySelector('[data-current-category-id]');
            var picker = document.querySelector('[data-pdx-compare-picker]');
            var table = document.querySelector('[data-pdx-compare-table]');

            if (!compareSection || !picker || !table) return;

            var currentCategoryId = String(compareSection.getAttribute('data-current-category-id') || '');
            if (!currentCategoryId) return;

            var existingIds = new Set(Array.from(table.querySelectorAll('[data-pdx-compare-column]'))
                .map(function (cell) { return String(cell.getAttribute('data-pdx-compare-column') || ''); }));
            var wishlist = [];

            try {
                wishlist = JSON.parse(localStorage.getItem('data') || '[]');
            } catch (error) {
                wishlist = [];
            }

            wishlist
                .filter(function (item) {
                    return item && String(item.category_id || '') === currentCategoryId && item.id && !existingIds.has(String(item.id));
                })
                .slice(0, 3)
                .forEach(function (item) {
                    var id = String(item.id);
                    var label = document.createElement('label');
                    var input = document.createElement('input');
                    var span = document.createElement('span');
                    label.className = 'pdx-compare-option pdx-compare-option--wishlist';
                    input.type = 'checkbox';
                    input.setAttribute('data-pdx-compare-toggle', id);
                    span.textContent = item.name || 'Sản phẩm yêu thích';
                    label.appendChild(input);
                    label.appendChild(span);
                    picker.appendChild(label);

                    var headCell = document.createElement('th');
                    headCell.hidden = true;
                    headCell.setAttribute('data-pdx-compare-column', id);
                    headCell.textContent = item.name || 'Sản phẩm yêu thích';
                    table.querySelector('thead tr').appendChild(headCell);

                    table.querySelectorAll('tbody tr').forEach(function (row, index) {
                        var cell = document.createElement('td');
                        cell.hidden = true;
                        cell.setAttribute('data-pdx-compare-column', id);
                        cell.textContent = index === 0 ? (item.price || 'Đang cập nhật') : (index === 2 ? (item.category || 'Cùng nhóm') : 'Từ yêu thích');
                        row.appendChild(cell);
                    });

                    input.addEventListener('change', function () {
                        document.querySelectorAll('[data-pdx-compare-column="' + id + '"]').forEach(function (cell) {
                            cell.hidden = !input.checked;
                        });
                    });
                });
        })();
    </script>
@endsection
