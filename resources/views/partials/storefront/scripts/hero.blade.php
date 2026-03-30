{{-- Storefront hero/slider scripts extracted from layout.blade.php. --}}
    <script type="text/javascript">
        (function() {
            function startSlider() {
                var $carousel = $('#slider-carousel');
                if (!$carousel.length) {
                    return;
                }

                var $items = $carousel.find('.carousel-inner .item');
                if ($items.length < 2) {
                    return;
                }

                if (typeof $carousel.carousel === 'function') {
                    $carousel.carousel({
                        interval: 4000,
                        pause: false,
                        wrap: true
                    });
                    $carousel.carousel('cycle');
                    return;
                }

                var index = $items.index($items.filter('.active'));
                if (index < 0) {
                    index = 0;
                    $items.removeClass('active').eq(0).addClass('active');
                }

                setInterval(function() {
                    $items.eq(index).removeClass('active');
                    index = (index + 1) % $items.length;
                    $items.eq(index).addClass('active');
                }, 4000);
            }

            $(window).on('load', function() {
                setTimeout(function() {
                    startSlider();
                }, 150);
            });
        })();
    </script>
