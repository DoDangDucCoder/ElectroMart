@php
    $__bodyClass = trim($bodyClass ?? $__env->yieldContent('body_class', ''));
    $__needsPriceSlider = str_contains($__bodyClass, 'g-search') || str_contains($__bodyClass, 'g-category');
    $__needsCheckoutEnhancements = str_contains($__bodyClass, 'checkout-quick-page');
    $__needsRecaptcha = str_contains($__bodyClass, 'auth-page');
    $__needsHomeCarousel = str_contains($__bodyClass, 'g-home');
    $__needsProductGallery = str_contains($__bodyClass, 'g-product-detail');
    $__needsVideoPlayer = str_contains($__bodyClass, 'g-video');
@endphp

{{-- Load vendor scripts individually: app-core/jquery.min are currently invalid bundles. --}}
<script src="{{ asset('frontend/js/jquery.js') }}?v=2.3"></script>
<script src="{{ asset('frontend/js/bootstrap.min.js') }}?v=2.3"></script>
<script src="{{ asset('frontend/js/jquery.scrollUp.min.js') }}?v=2.3"></script>
<script src="{{ asset('frontend/js/sweetalert.min.js') }}?v=2.3"></script>

{{-- CDN & Third-party scripts --}}
@if ($__needsRecaptcha)
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
@endif
@if ($__needsPriceSlider)
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="{{ asset('frontend/js/price-range.js') }}?v=2.2"></script>
@endif
@if ($__needsHomeCarousel)
    <script src="{{ asset('frontend/js/owl.carousel.js') }}?v=2.3"></script>
@endif
@if ($__needsProductGallery)
    <script src="{{ asset('frontend/js/lightslider.min.js') }}?v=2.2"></script>
    <script src="{{ asset('frontend/js/lightgallery-all.min.js') }}?v=2.2"></script>
@endif
@if ($__needsVideoPlayer)
    <script src="{{ asset('frontend/js/vlite.min.js') }}?v=2.2"></script>
@endif
@if ($__needsCheckoutEnhancements)
    <script src="{{ asset('frontend/vendor/select2/select2.min.js') }}?v=4.1.0-rc.0"></script>
    <script src="https://www.paypalobjects.com/api/checkout.js"></script>
@endif

{{-- Consolidated Storefront Logic --}}
@include('partials.storefront.scripts.countdown')
@include('partials.storefront.scripts.interactions')

@stack('scripts')
