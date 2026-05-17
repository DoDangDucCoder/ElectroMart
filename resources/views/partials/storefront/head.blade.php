<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    @php
        $meta_desc = $meta_desc ?? 'DangDuc - Chuyên cung cấp laptop, linh kiện PC và phụ kiện công nghệ chính hãng.';
        $meta_keywords = $meta_keywords ?? 'DangDuc, laptop, linh kiện, phụ kiện, công nghệ';
        $url_canonical = $url_canonical ?? url()->current();
        $meta_title = $meta_title ?? 'DangDuc - Laptop & Công nghệ';
    @endphp

    <!---------Seo--------->
    <meta name="description" content="{{ $meta_desc }}">
    <meta name="keywords" content="{{ $meta_keywords }}" />
    <meta name="robots" content="INDEX,FOLLOW" />
    <link rel="canonical" href="{{ $url_canonical }}" />
    <meta name="author" content="">

    @php
        $brandFavicon = asset('uploads/brand/dangduc-favicon-32.png');
        $brandFavicon32 = asset('uploads/brand/dangduc-favicon-32.png');
        $brandFavicon16 = asset('uploads/brand/dangduc-favicon-16.png');
        $brandShareImage = $share_image ?? asset('uploads/brand/dangduc-logo-horizontal-width-600.png');
    @endphp
    <link rel="icon" href="{{ $brandFavicon32 }}" type="image/png" sizes="32x32">
    <link rel="icon" href="{{ $brandFavicon16 }}" type="image/png" sizes="16x16">
    <link rel="shortcut icon" href="{{ $brandFavicon }}">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $meta_title }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700&family=Bebas+Neue&display=swap" rel="stylesheet">
    {{-- Vendor core: Bootstrap 3 + FontAwesome + SweetAlert --}}
    <link href="{{ asset('frontend/css/vendor-core.min.css') }}?v=2.3" rel="stylesheet">
    {{-- Design tokens + shared components --}}
    <link href="{{ asset('frontend/css/storefront-tokens.min.css') }}?v=2.2" rel="stylesheet">
    <link href="{{ asset('frontend/css/storefront-components.min.css') }}?v=2.2" rel="stylesheet">
    {{-- Theme: Header, Footer, Nav, Product cards, Grid, etc. --}}
    <link href="{{ asset('frontend/css/storefront-theme.min.css') }}?v=2.2" rel="stylesheet">
    {{-- Layout rhythm: shared backgrounds, containers, breadcrumbs across all pages --}}
    <link href="{{ asset('frontend/css/storefront-layout.min.css') }}?v=2.2" rel="stylesheet">
    {{-- Pagination styles --}}
    <link href="{{ asset('frontend/css/shop-pagination.min.css') }}?v=2.2" rel="stylesheet">

    @stack('styles')
    <link href="{{ asset('frontend/css/wishlist-state.css') }}?v=1.1" rel="stylesheet">

    {{-- Auto-load page CSS based on body_class (avoids editing every page blade) --}}
    @php
        $__bodyClass = trim($__env->yieldContent('body_class', ''));
        $__needsPriceSlider = str_contains($__bodyClass, 'g-search') || str_contains($__bodyClass, 'g-category');
        $__needsCheckoutEnhancements = str_contains($__bodyClass, 'checkout-quick-page');
        $__needsHomeCarousel = str_contains($__bodyClass, 'g-home');
        $__needsProductGallery = str_contains($__bodyClass, 'g-product-detail');
        $__needsVideoPlayer = str_contains($__bodyClass, 'g-video');
    @endphp
    @if (str_contains($__bodyClass, 'accp-page') || str_contains($__bodyClass, 'auth-page'))
        <link href="{{ asset('frontend/css/account-shared.css') }}?v=2.1" rel="stylesheet">
    @endif
    @if (str_contains($__bodyClass, 'policy-page'))
        <link href="{{ asset('frontend/css/policy-page.css') }}?v=2.1" rel="stylesheet">
    @endif
    @if ($__needsPriceSlider)
        <link href="{{ asset('frontend/css/price-range.css') }}?v=2.3" rel="stylesheet">
        <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    @endif
    @if ($__needsHomeCarousel)
        <link href="{{ asset('frontend/css/owl.carousel.min.css') }}?v=2.3" rel="stylesheet">
        <link href="{{ asset('frontend/css/owl.theme.default.min.css') }}?v=2.3" rel="stylesheet">
    @endif
    @if ($__needsProductGallery)
        <link href="{{ asset('frontend/css/lightslider.css') }}?v=2.3" rel="stylesheet">
        <link href="{{ asset('frontend/css/lightgallery.min.css') }}?v=2.3" rel="stylesheet">
    @endif
    @if ($__needsVideoPlayer)
        <link href="{{ asset('frontend/css/vlite.css') }}?v=2.3" rel="stylesheet">
    @endif
    @if ($__needsCheckoutEnhancements)
        <link href="{{ asset('frontend/vendor/select2/select2.min.css') }}?v=4.1.0-rc.0" rel="stylesheet" />
    @endif


    <!------------Share fb------------------>
    <meta property="og:url" content="{{ $url_canonical }}" />
    <meta property="og:type" content="articles" />
    <meta property="og:title" content="{{ $meta_title }}" />
    <meta property="og:site_name" content="{{ $meta_title }}" />
    <meta property="og:description" content="{{ $meta_desc }}" />
    <meta property="og:image" content="{{ $brandShareImage }}" />
    <!--//-------Seo--------->
</head><!--/head-->

