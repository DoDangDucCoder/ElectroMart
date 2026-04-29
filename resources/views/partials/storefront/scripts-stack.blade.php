<script src="{{ asset('frontend/js/jquery.js') }}"></script>
<script src="{{ asset('frontend/js/bootstrap.min.js') }}"></script>

<script src="{{ asset('frontend/js/jquery.scrollUp.min.js') }}"></script>
<script src="{{ asset('frontend/js/price-range.js') }}"></script>
<script src="{{ asset('frontend/js/jquery.prettyPhoto.js') }}"></script>

<script src="{{ asset('frontend/js/sweetalert.min.js') }}"></script>
<script src="{{ asset('frontend/js/lightgallery-all.min.js') }}"></script>
<script src="{{ asset('frontend/js/lightslider.js') }}"></script>
<script src="{{ asset('frontend/js/prettify.js') }}"></script>
<script src="{{ asset('frontend/js/vlite.js') }}"></script>
<script src="{{ asset('frontend/js/simple.money.format.js') }}"></script>
<script src="{{ asset('frontend/js/owl.carousel.js') }}"></script>

{{-- CDN & Third-party scripts --}}
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://sp.zalo.me/plugins/sdk.js"></script>
<script src="https://www.paypalobjects.com/api/checkout.js"></script>

{{-- Consolidated Storefront Logic --}}
@include('partials.storefront.scripts.interactions')

@stack('scripts')
