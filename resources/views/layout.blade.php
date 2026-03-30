<!DOCTYPE html>
<html lang="en">

@include('partials.storefront.head')

<body class="@yield('body_class')">

    @include('partials.storefront.header')

    <!-------------------Slider Section------------------------->
    @yield('slider')
    <!-------------------Slider Attribute------------------------->
    @yield('attribute')

    @include('partials.storefront.content-shell')

    @include('partials.storefront.compare-modal')
    @include('partials.storefront.chatbot-widget')

    @include('partials.storefront.footer')

    @include('partials.storefront.scripts-stack')

</body>

</html>
