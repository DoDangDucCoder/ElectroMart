@php
    $meta_desc = $meta_desc ?? 'Cửa hàng công nghệ chuyên laptop, điện thoại và phụ kiện chính hãng.';
    $meta_keywords = $meta_keywords ?? 'laptop, dien thoai, iphone, samsung, macbook, phu kien cong nghe';
    $meta_title = $meta_title ?? config('app.name', 'DangDuc');
    $url_canonical = $url_canonical ?? url()->current();
    $share_image = $share_image ?? asset('uploads/contact/laptop-symbol-azy3.png');
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

@include('partials.storefront.head')

<body class="@yield('body_class', 'g-auth-page')">
    @include('partials.storefront.header')

    @include('partials.storefront.support-dock')

    <main class="shopw-auth-page-main">
        @yield('content')
    </main>

    @include('partials.storefront.compare-modal')
    @include('partials.storefront.chatbot-widget')
    @include('partials.home.footer', ['paymentItems' => []])

    @include('partials.storefront.scripts-stack')
</body>

</html>
