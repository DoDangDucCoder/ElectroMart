<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

@include('partials.storefront.head')

<body class="@yield('body_class')">

    @php
        $isMinimal = str_contains($__env->yieldContent('body_class'), 'minimal-layout');
    @endphp

    @if(!$isMinimal)
        @include('partials.storefront.header')
        @include('partials.storefront.support-dock')
    @endif

    <!-------------------Slider Section------------------------->
    @yield('slider')
    <!-------------------Slider Attribute------------------------->
    @yield('attribute')

    @include('partials.storefront.content-shell')

    @if(!$isMinimal)
        @include('partials.storefront.compare-modal')
        @include('partials.storefront.chatbot-widget')
        @include('partials.storefront.footer')
    @endif

    @include('partials.storefront.scripts-stack')

</body>

</html>
