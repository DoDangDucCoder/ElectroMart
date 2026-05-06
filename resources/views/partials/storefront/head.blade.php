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
        $brandFavicon = asset('uploads/brand/dangduc-favicon.png');
        $brandFavicon32 = asset('uploads/brand/dangduc-favicon-32.png');
        $brandFavicon16 = asset('uploads/brand/dangduc-favicon-16.png');
        $brandShareImage = $share_image ?? asset('uploads/brand/dangduc-logo-horizontal-width-600.png');
    @endphp
    <link rel="icon" href="{{ $brandFavicon32 }}" type="image/png" sizes="32x32">
    <link rel="icon" href="{{ $brandFavicon16 }}" type="image/png" sizes="16x16">
    <link rel="shortcut icon" href="{{ $brandFavicon }}">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $meta_title }}</title>
    <link href="{{ asset('frontend/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('frontend/css/font-awesome.min.css') }}" rel="stylesheet">
    <link href="{{ asset('frontend/css/prettyPhoto.css') }}" rel="stylesheet">
    <link href="{{ asset('frontend/css/price-range.css') }}" rel="stylesheet">

    <link href="{{ asset('frontend/css/animate.css') }}" rel="stylesheet">
    <link href="{{ asset('frontend/css/sweetalert.css') }}" rel="stylesheet">
    <link href="{{ asset('frontend/css/lightgallery.min.css') }}" rel="stylesheet">
    <link href="{{ asset('frontend/css/lightslider.css') }}" rel="stylesheet">
    <link href="{{ asset('frontend/css/prettify.css') }}" rel="stylesheet">
    <link href="{{ asset('frontend/css/vlite.css') }}" rel="stylesheet">
    <link href="{{ asset('frontend/css/owl.carousel.min.css') }}" rel="stylesheet">
    <link href="{{ asset('frontend/css/owl.theme.default.min.css') }}" rel="stylesheet">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700&family=Bebas+Neue&display=swap" rel="stylesheet">
    <link href="{{ asset('frontend/css/storefront.css') }}" rel="stylesheet">
    <link href="{{ asset('frontend/css/tech-modern-polish.css') }}" rel="stylesheet">
    <link href="{{ asset('frontend/css/category-redesign.css') }}" rel="stylesheet">

    @stack('styles')

    <link href="{{ asset('frontend/css/storefront-layout.css') }}" rel="stylesheet">

    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />


    <!------------Share fb------------------>
    <meta property="og:url" content="{{ $url_canonical }}" />
    <meta property="og:type" content="articles" />
    <meta property="og:title" content="{{ $meta_title }}" />
    <meta property="og:site_name" content="{{ $meta_title }}" />
    <meta property="og:description" content="{{ $meta_desc }}" />
    <meta property="og:image" content="{{ $brandShareImage }}" />
    <!--//-------Seo--------->
</head><!--/head-->

