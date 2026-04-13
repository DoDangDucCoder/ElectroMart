{{-- Storefront interaction scripts extracted from layout.blade.php. --}}
    <script type="text/javascript">
        $(document).on('click', '[data-target="#cancelOrderModal"]', function() {
            var orderCode = $(this).data('orderCode') || $(this).data('order-code') || '';

            $('#cancel-order-submit').attr('data-order-code', orderCode);
            $('#cancel-order-code-preview').text(orderCode ? ('#' + orderCode) : '');
            $('.lydohuydon').val('');
        });

        $(document).on('click', '#cancel-order-submit', function() {
            Huydonhang($(this).data('orderCode') || $(this).attr('data-order-code'));
        });

        function Huydonhang(id) {
            var order_code = id || $('#cancel-order-submit').attr('data-order-code');
            var lydo = $.trim($('.lydohuydon').val());
            var _token = $('input[name="_token"]').first().val();

            if (!order_code) {
                swal("Chưa thể hủy", "Không tìm thấy mã đơn hàng cần hủy.", "error");
                return;
            }

            if (!lydo) {
                swal("Thiếu lý do", "Vui lòng nhập lý do hủy đơn hàng.", "warning");
                return;
            }

            $.ajax({
                url: '{{ url('/huy-don-hang') }}',
                method: "POST",

                data: {
                    order_code: order_code,
                    lydo: lydo,
                    _token: _token
                },
                success: function(data) {
                    $('#cancelOrderModal').modal('hide');
                    swal("Đơn hàng", (data && data.message) ? data.message : "Hủy đơn hàng thành công", "success");
                    window.setTimeout(function() {
                        location.reload();
                    }, 1200);
                },
                error: function(xhr) {
                    swal("Chưa thể hủy đơn", getAjaxErrorMessage(xhr,
                        'Không thể hủy đơn hàng ở thời điểm này.'), "error");
                }

            });
        }
    </script>
    <script type="text/javascript">
        if ($('#all_product').length) {
            load_more_product();
        }

        if ($('#cart_session').length) {
            cart_session();
        }

        syncHomeCartButtons();

        function cart_session() {
            loadStorefrontFragment({
                url: '{{ url('/cart-session') }}',
                method: 'GET',
                target: '#cart_session',
                fallbackMessage: 'Không thể đồng bộ giỏ hàng lúc này.',
                onSuccess: function() {
                    syncHomeCartButtons();
                }
            });
        }
        $(window).on('load', function() {
            syncHomeCartButtons();
        });

        function syncHomeLatestGridLayout() {
            var $grid = $('#all_product');

            if (!$grid.length) {
                return;
            }

            var productCardCount = $grid.children().filter(function() {
                return $(this).find('.shopw-product-card').length > 0;
            }).length;

            var featuredCardCount = $grid.children('.shopw-home-featured-card').length;

            $grid.toggleClass('shopw-home-latest-grid-single', productCardCount === 1 && featuredCardCount === 1);
            $grid.toggleClass('shopw-home-latest-grid-duo', productCardCount === 2);
        }

        function syncHomeCartButtons() {
            var ids = [];

            $(".cart_id").each(function() {
                ids.push($(this).val());
            });

            $('[class^="home_cart_"], [class*=" home_cart_"]').show();
            $('[class^="rm_home_cart_"], [class*=" rm_home_cart_"]').hide();

            for (var i = 0; i < ids.length; i++) {
                $('.home_cart_' + ids[i]).hide();
                $('.rm_home_cart_' + ids[i]).show();
            }
        }

        function load_more_product(id = '') {
            loadStorefrontFragment({
                url: '{{ url('/load-more-product') }}',
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    id: id
                },
                target: '#all_product',
                mode: 'append',
                fallbackMessage: 'Không thể tải thêm sản phẩm lúc này.',
                onSuccess: function() {
                    $('#load_more_button').remove();
                    syncHomeLatestGridLayout();
                    syncHomeCartButtons();
                }
            });
        }
        $(document).on('click', '#load_more_button', function() {
            var id = $(this).data('id');
            $('#load_more_button').html('<b>Loading...</b>');
            load_more_product(id);


        })
    </script>
    <script type="text/javascript">
        // When the user scrolls the page, execute myFunction
        window.onscroll = function() {
            sticky_navbar()
        };

        // Get the navbar
        var navbar = document.getElementById("navbar");

        // Get the offset position of the navbar
        var sticky = navbar.offsetTop;

        // Add the sticky class to the navbar when you reach its scroll position. Remove "sticky" when you leave the scroll position
        function sticky_navbar() {
            if (window.pageYOffset >= sticky) {
                navbar.classList.add("sticky")
            } else {
                navbar.classList.remove("sticky");
            }
        }
    </script>
    <script type="text/javascript">
        $('.owl-carousel').owlCarousel({
            loop: true,
            margin: 10,
            nav: true,
            dots: false,
            responsive: {
                0: {
                    items: 1
                },
                600: {
                    items: 3
                },
                1000: {
                    items: 5
                }
            }
        })
    </script>
    <script>
        (function() {
            var usdInput = document.getElementById("vnd_to_usd");
            var paypalTarget = document.getElementById("paypal-button");
            if (!usdInput || !paypalTarget || !window.paypal || !paypal.Button) {
                return;
            }

            var usd = usdInput.value;
            paypal.Button.render({

            // Configure environment
            env: 'sandbox',

            client: {
                sandbox: 'AXrZz-zLToqfMr-uQpcv3efjJ03lkeRJZf1XkivTFGTvF1Ul7qBf6fZNauuByqW9jtni0wrygLUE8gkQ',
                production: 'demo_production_client_id'
            },
            // Customize button (optional)
            locale: 'en_US',
            style: {
                size: 'small',
                color: 'gold',
                shape: 'pill',
            },

            // Enable Pay Now checkout flow (optional)
            commit: true,

            // Set up a payment
            payment: function(data, actions) {
                return actions.payment.create({
                    transactions: [{
                        amount: {
                            total: `${usd}`,
                            currency: 'USD'
                        }
                    }]
                });
            },
            // Execute the payment
            onAuthorize: function(data, actions) {
                return actions.payment.execute().then(function() {
                    showStorefrontStatus('Thanh toán thành công',
                        'Cảm ơn bạn đã mua hàng của chúng tôi!', 'success');
                });
            }
            }, '#paypal-button');
        })();
    </script>
    <div id="fb-root"></div>

    <script async defer crossorigin="anonymous"
        src="https://connect.facebook.net/vi_VN/sdk.js#xfbml=1&version=v6.0&appId=2339123679735877&autoLogAppEvents=1">
    </script>

    <!-- Load Facebook SDK for JavaScript -->
    <div id="fb-root"></div>
    <script>
        window.fbAsyncInit = function() {
            FB.init({
                xfbml: true,
                version: 'v9.0'
            });
        };

        (function(d, s, id) {
            var js, fjs = d.getElementsByTagName(s)[0];
            if (d.getElementById(id)) return;
            js = d.createElement(s);
            js.id = id;
            js.src = 'https://connect.facebook.net/vi_VN/sdk/xfbml.customerchat.js';
            fjs.parentNode.insertBefore(js, fjs);
        }(document, 'script', 'facebook-jssdk'));
    </script>

    <!-- Your Chat Plugin code -->
    <div class="fb-customerchat" attribution=setup_tool page_id="100332251915013" theme_color="#0A7CFF"
        logged_in_greeting="Chào bạn,shop có thể giúp gì được cho bạn?"
        logged_out_greeting="Chào bạn,shop có thể giúp gì được cho bạn?">
    </div>
    <script type="text/javascript">
        $(document).ready(function() {

            $("#slider-range").slider({
                orientation: "horizontal",
                range: true,

                min: {{ $min_price_range }},
                max: {{ $max_price_range }},

                steps: 10000,
                values: [{{ $min_price }}, {{ $max_price }}],

                slide: function(event, ui) {
                    $("#amount_start").val(ui.values[0]).simpleMoneyFormat();
                    $("#amount_end").val(ui.values[1]).simpleMoneyFormat();


                    $("#start_price").val(ui.values[0]);
                    $("#end_price").val(ui.values[1]);

                }

            });

            $("#amount_start").val($("#slider-range").slider("values", 0)).simpleMoneyFormat();
            $("#amount_end").val($("#slider-range").slider("values", 1)).simpleMoneyFormat();

        });
    </script>
    <script type="text/javascript">
        $(document).ready(function() {

            $('#sort').on('change', function() {

                var url = $(this).val();
                if (url) {
                    window.location = url;
                }
                return false;
            });

        });
    </script>
    <script type="text/javascript">
        $(document).ready(function() {
            if (!$('.tabs_pro').length || !$('#tabs_product').length) {
                return;
            }

            var cate_id = $('.tabs_pro').data('id');
            var _token = $('input[name="_token"]').val();
            loadStorefrontFragment({
                url: '{{ url('/product-tabs') }}',
                method: 'POST',
                data: {
                    cate_id: cate_id,
                    _token: _token
                },
                target: '#tabs_product',
                fallbackMessage: 'Không thể tải tab sản phẩm lúc này.',
                onSuccess: function() {
                    syncHomeCartButtons();
                },
                error: function(xhr) {
                    renderTabsErrorState(getAjaxErrorMessage(xhr, 'Không thể tải tab sản phẩm lúc này.'));
                }
            });

            $('.tabs_pro').click(function() {

                var cate_id = $(this).data('id');
                var _token = $('input[name="_token"]').val();
                loadStorefrontFragment({
                    url: '{{ url('/product-tabs') }}',
                    method: 'POST',
                    data: {
                        cate_id: cate_id,
                        _token: _token
                    },
                    target: '#tabs_product',
                    fallbackMessage: 'Không thể tải tab sản phẩm lúc này.',
                    onSuccess: function() {
                        syncHomeCartButtons();
                    },
                    error: function(xhr) {
                        renderTabsErrorState(getAjaxErrorMessage(xhr, 'Không thể tải tab sản phẩm lúc này.'));
                    }
                });

            });



        });
    </script>

    <script type="text/javascript">
        function remove_background(product_id) {
            for (var count = 1; count <= 5; count++) {
                $('#' + product_id + '-' + count).css('color', '#ccc');
            }
        }
        //hover chuột đánh giá sao
        $(document).on('mouseenter', '.rating', function() {
            var index = $(this).data("index");
            var product_id = $(this).data('product_id');
            remove_background(product_id);
            for (var count = 1; count <= index; count++) {
                $('#' + product_id + '-' + count).css('color', '#ffcc00');
            }
        });
        //nhả chuột ko đánh giá
        $(document).on('mouseleave', '.rating', function() {
            var index = $(this).data("index");
            var product_id = $(this).data('product_id');
            var rating = $(this).data("rating");
            remove_background(product_id);
            for (var count = 1; count <= rating; count++) {
                $('#' + product_id + '-' + count).css('color', '#ffcc00');
            }
        });

        //click đánh giá sao
        $(document).on('click', '.rating', function() {
            var index = $(this).data("index");
            var product_id = $(this).data('product_id');
            var _token = $('input[name="_token"]').val();
            $.ajax({
                url: "{{ url('insert-rating') }}",
                method: "POST",
                data: {
                    index: index,
                    product_id: product_id,
                    _token: _token
                },
                success: function(data) {
                    if (data == 'done') {
                        showStorefrontStatus('Đánh giá thành công',
                            'Bạn đã đánh giá ' + index + ' trên 5 sao.', 'success');
                    } else {
                        showStorefrontStatus('Chưa thể đánh giá',
                            'Hệ thống chưa ghi nhận được đánh giá của bạn.', 'error');
                    }
                },
                error: function(xhr) {
                    showStorefrontStatus('Chưa thể đánh giá',
                        getAjaxErrorMessage(xhr, 'Không thể gửi đánh giá lúc này.'), 'error');
                }
            });

        });
    </script>
    <script type="text/javascript">
        $(document).ready(function() {

            load_comment();

            function load_comment() {
                var product_id = $('.comment_product_id').val();
                var _token = $('input[name="_token"]').val();
                $.ajax({
                    url: "{{ url('/load-comment') }}",
                    method: "POST",
                    data: {
                        product_id: product_id,
                        _token: _token
                    },
                    success: function(data) {

                        renderAjaxHtml('#comment_show', data);
                    },
                    error: function(xhr) {
                        renderCommentThreadError(getAjaxErrorMessage(xhr,
                            'Không thể tải bình luận lúc này.'));
                    }
                });
            }
            $('.send-comment').click(function() {
                var product_id = $('.comment_product_id').val();
                var comment_name = $('.comment_name').val();
                var comment_content = $('.comment_content').val();
                var _token = $('input[name="_token"]').val();
                $.ajax({
                    url: "{{ url('/send-comment') }}",
                    method: "POST",
                    data: {
                        product_id: product_id,
                        comment_name: comment_name,
                        comment_content: comment_content,
                        _token: _token
                    },
                    success: function(data) {
                        setStorefrontInlineStatus('#notify_comment',
                            'Thêm bình luận thành công, bình luận đang chờ duyệt', 'success', {
                                autoHideAfter: 4500
                            });
                        load_comment();
                        $('.comment_name').val('');
                        $('.comment_content').val('');
                    },
                    error: function(xhr) {
                        setStorefrontInlineStatus('#notify_comment',
                            getAjaxErrorMessage(xhr, 'Không thể gửi bình luận lúc này.'), 'error');
                    }
                });
            });
        });
    </script>
    <script type="text/javascript">
        function XemNhanh(id) {
            requestQuickview(id);
        }
    </script>
    <script type="text/javascript">
        $(document).on('click', '.xemnhanh', function() {
            var product_id = $(this).data('id_product');
            requestQuickview(product_id);
        });
    </script>
    <script type="text/javascript">
        var searchSuggestTimer = null;
        var activeSearchSuggestRequest = null;
        var searchPlaceholderTimer = null;

        function initSearchPlaceholderRotation() {
            var $searchInput = $('#keywords');
            var $searchRotator = $('[data-search-rotator]');
            var $searchRotatorWord = $('[data-search-rotator-word]');

            if (!$searchInput.length) {
                return;
            }

            var basePlaceholder = $searchInput.attr('data-placeholder-base') || $searchInput.attr('placeholder') || '';
            var trendingKeywords = [];

            try {
                trendingKeywords = JSON.parse($searchInput.attr('data-search-trends') || '[]');
            } catch (error) {
                trendingKeywords = [];
            }

            if (!Array.isArray(trendingKeywords) || trendingKeywords.length === 0 || !$searchRotator.length || !$searchRotatorWord.length) {
                return;
            }

            var activeIndex = 0;
            var swapDelay = 220;

            function showRotator() {
                if ($.trim($searchInput.val()) !== '') {
                    hideRotator();
                    return;
                }

                $searchInput.attr('placeholder', '');
                $searchRotator.addClass('is-visible');
            }

            function hideRotator() {
                $searchInput.attr('placeholder', basePlaceholder);
                $searchRotator.removeClass('is-visible');
            }

            function renderTrendingPlaceholder(forceImmediate) {
                var isTyping = $.trim($searchInput.val()) !== '';

                if (isTyping) {
                    hideRotator();
                    return;
                }

                showRotator();

                if (forceImmediate) {
                    $searchRotatorWord.text(trendingKeywords[activeIndex]);
                    activeIndex = (activeIndex + 1) % trendingKeywords.length;
                    return;
                }

                $searchRotatorWord.addClass('is-swapping');

                window.setTimeout(function() {
                    var stillTyping = $.trim($searchInput.val()) !== '';

                    if (stillTyping) {
                        hideRotator();
                        return;
                    }

                    $searchRotatorWord.text(trendingKeywords[activeIndex]);
                    activeIndex = (activeIndex + 1) % trendingKeywords.length;

                    window.requestAnimationFrame(function() {
                        $searchRotatorWord.removeClass('is-swapping');
                    });
                }, swapDelay);
            }

            renderTrendingPlaceholder(true);

            if (searchPlaceholderTimer) {
                clearInterval(searchPlaceholderTimer);
            }

            searchPlaceholderTimer = setInterval(function() {
                renderTrendingPlaceholder(false);
            }, 6200);

            $searchInput.on('focus', function() {
                if ($.trim($searchInput.val()) === '') {
                    showRotator();
                }
            });

            $searchInput.on('input', function() {
                if ($.trim($searchInput.val()) === '') {
                    showRotator();
                } else {
                    hideRotator();
                }
            });

            $searchInput.on('blur', function() {
                if ($.trim($searchInput.val()) === '') {
                    showRotator();
                }
            });
        }

        function closeSearchSuggest() {
            if (searchSuggestTimer) {
                clearTimeout(searchSuggestTimer);
                searchSuggestTimer = null;
            }

            $('#search_ajax').stop(true, true).fadeOut(120, function() {
                $(this).html('');
            });
        }

        function renderSearchSuggestHtml(html) {
            if ($.trim(html) === '') {
                closeSearchSuggest();
                return;
            }

            $('#search_ajax').stop(true, true).fadeIn(120);
            renderAjaxHtml('#search_ajax', html);
        }

        function requestSearchSuggest(query) {
            var _token = $('input[name="_token"]').val();

            if (activeSearchSuggestRequest && typeof activeSearchSuggestRequest.abort === 'function') {
                activeSearchSuggestRequest.abort();
            }

            activeSearchSuggestRequest = $.ajax({
                url: "{{ url('/autocomplete-ajax') }}",
                method: "POST",
                data: {
                    query: query,
                    _token: _token
                },
                success: function(data) {
                    renderSearchSuggestHtml(data);
                },
                error: function(xhr) {
                    if (xhr && xhr.statusText === 'abort') {
                        return;
                    }

                    renderSearchSuggestErrorState(getAjaxErrorMessage(xhr,
                        'Không thể tải gợi ý tìm kiếm.'));
                },
                complete: function() {
                    activeSearchSuggestRequest = null;
                }
            });
        }

        function scheduleSearchSuggest(query, immediate) {
            if (searchSuggestTimer) {
                clearTimeout(searchSuggestTimer);
            }

            if (immediate) {
                requestSearchSuggest(query);
                return;
            }

            searchSuggestTimer = setTimeout(function() {
                requestSearchSuggest(query);
            }, query.length >= 2 ? 140 : 0);
        }

        $('#keywords').on('focus', function() {
            scheduleSearchSuggest($.trim($(this).val()), true);
        });

        $('#keywords').on('input', function() {
            scheduleSearchSuggest($.trim($(this).val()), false);
        });

        $('#keywords').on('keydown', function(event) {
            if (event.key === 'Escape') {
                closeSearchSuggest();
                $(this).blur();
            }
        });

        $(document).on('click', '.shopw-search-suggest-chip.li_search_ajax', function(event) {
            event.preventDefault();

            var keyword = $(this).data('searchKeyword') || $.trim($(this).text());
            $('#keywords').val(keyword);
            scheduleSearchSuggest(keyword, true);
        });

        $(document).on('click', '.shopw-search-suggest-list .li_search_ajax', function() {
            $('#keywords').val($(this).data('searchKeyword') || $.trim($(this).text()));
            closeSearchSuggest();
        });

        $(document).on('click', function(event) {
            if (!$(event.target).closest('.shopw-search-shell').length) {
                closeSearchSuggest();
            }
        });

        initSearchPlaceholderRotation();

        function initFlashSaleCountdown($countdown) {
            var targetValue = $countdown.data('flashSaleCountdown');
            if (!targetValue) {
                return;
            }

            var targetTime = new Date(targetValue);
            if (Number.isNaN(targetTime.getTime())) {
                return;
            }

            var render = function() {
                var diff = Math.max(0, targetTime.getTime() - Date.now());
                var totalSeconds = Math.floor(diff / 1000);
                var days = Math.floor(totalSeconds / 86400);
                var hours = Math.floor((totalSeconds % 86400) / 3600);
                var minutes = Math.floor((totalSeconds % 3600) / 60);
                var seconds = totalSeconds % 60;

                $countdown.find('[data-flash-unit="days"]').text(String(days).padStart(2, '0'));
                $countdown.find('[data-flash-unit="hours"]').text(String(hours).padStart(2, '0'));
                $countdown.find('[data-flash-unit="minutes"]').text(String(minutes).padStart(2, '0'));
                $countdown.find('[data-flash-unit="seconds"]').text(String(seconds).padStart(2, '0'));
            };

            render();
            var timer = setInterval(function() {
                render();

                if (targetTime.getTime() <= Date.now()) {
                    clearInterval(timer);
                }
            }, 1000);
        }

        $('[data-flash-sale-countdown]').each(function() {
            initFlashSaleCountdown($(this));
        });
    </script>

    {{-- <script type="text/javascript">
        $(document).ready(function() {
            $('#imageGallery').lightSlider({

                gallery: true,
                item: 1,
                loop: true,
                thumbItem: 3,
                slideMargin: 0,
                enableDrag: false,
                currentPagerPosition: 'left',
                onSliderLoad: function(el) {
                    el.lightGallery({
                        selector: '#imageGallery .lslide'
                    });
                }

            });
        });
    </script> --}}

    <script type="text/javascript">
        $(document).on('click', '.watch-video', function() {
            var video_id = $(this).attr('id');
            var _token = $('input[name="_token"]').val();
            $.ajax({
                url: '{{ url('/watch-video') }}',
                method: "POST",
                dataType: "JSON",
                data: {
                    video_id: video_id,
                    _token: _token
                },
                success: function(data) {
                    $('#video_title').html(data.video_title);
                    $('#video_link').html(data.video_link);
                    $('#video_desc').html(data.video_desc);
                    var playerYT = new vlitejs({
                        selector: '#my_yt_video',
                        options: {
                            // auto play
                            autoplay: false,

                            // enable controls
                            controls: true,

                            // enables play/pause buttons
                            playPause: true,

                            // shows progress bar
                            progressBar: true,

                            // shows time
                            time: true,

                            // shows volume control
                            volume: true,

                            // shows fullscreen button
                            fullscreen: true,

                            // path to poster image
                            poster: null,

                            // shows play button
                            bigPlay: true,

                            // hide the control bar if the user is inactive
                            autoHide: false,

                            // keeps native controls for touch devices
                            nativeControlsForTouch: false
                        },
                        onReady: (player) => {
                            // callback function here
                        }
                    });

                }

            });
        });
    </script>

    <script type="text/javascript">
        $(document).ready(function() {
            $('.send_order').click(function() {
                var total_after = $('.total_after').val();
                swal({
                        title: "Xác nhận đơn hàng",
                        text: "Đơn hàng sẽ không được hoàn trả khi đặt,bạn có muốn đặt không?",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonClass: "btn-danger",
                        confirmButtonText: "Cảm ơn, Mua hàng",

                        cancelButtonText: "Đóng,chưa mua",
                        closeOnConfirm: false,
                        closeOnCancel: false
                    },
                    function(isConfirm) {
                        if (isConfirm) {
                            var shipping_email = $('.shipping_email').val();
                            var shipping_name = $('.shipping_name').val();
                            var shipping_address = $('.shipping_address').val();
                            var shipping_phone = $('.shipping_phone').val();
                            var shipping_notes = $('.shipping_notes').val();
                            var shipping_method = $('.payment_select').val();
                            var city_id = $('.city').val();
                            var province_id = $('.province').val();
                            var ward_id = $('.wards').val();

                            var order_fee = $('.order_fee').val();
                            var order_coupon = $('.order_coupon').val();
                            var _token = $('input[name="_token"]').val();

                            $.ajax({
                                url: '{{ url('/confirm-order') }}',
                                method: 'POST',
                                data: {
                                    shipping_email: shipping_email,
                                    shipping_name: shipping_name,
                                    shipping_address: shipping_address,
                                    shipping_phone: shipping_phone,
                                    shipping_notes: shipping_notes,
                                    _token: _token,
                                    order_fee: order_fee,
                                    order_coupon: order_coupon,
                                    shipping_method: shipping_method,
                                    city_id: city_id,
                                    province_id: province_id,
                                    ward_id: ward_id
                                },
                                success: function(response) {
                                    swal("Đơn hàng",
                                        "Đơn hàng của bạn đã được gửi thành công",
                                        "success");

                                    window.setTimeout(function() {
                                        window.location.href = "{{ url('/history') }}";
                                    }, 3000);
                                },
                                error: function(xhr) {
                                    var message = 'Không thể gửi đơn hàng. Vui lòng kiểm tra lại thông tin.';

                                    if (xhr && xhr.responseJSON && xhr.responseJSON.message) {
                                        message = xhr.responseJSON.message;
                                    }

                                    swal("Chưa thể đặt hàng", message, "error");
                                }
                            });

                        } else {
                            swal("Đóng", "Đơn hàng chưa được gửi, làm ơn hoàn tất đơn hàng", "error");

                        }

                    });


            });
        });
    </script>
    <script type="text/javascript">
        hover_cart();
        show_cart();

        function hover_cart() {
            loadStorefrontFragment({
                url: '{{ url('/hover-cart') }}',
                method: 'GET',
                target: '.giohang-hover',
                fallbackMessage: 'Không thể tải giỏ hàng mini lúc này.'
            });
        }

        //show cart quantity
        function show_cart() {
            loadStorefrontFragment({
                url: '{{ url('/show-cart') }}',
                method: 'GET',
                target: '.show-cart',
                fallbackMessage: 'Không thể tải số lượng giỏ hàng lúc này.'
            });
        }

        function Deletecart(id) {
            var id = id;
            loadStorefrontJson({
                url: '{{ url('/remove-item') }}',
                method: 'GET',
                data: {
                    id: id
                },
                fallbackMessage: 'Không thể xóa sản phẩm khỏi giỏ hàng.',
                onSuccess: function() {
                    $('.home_cart_' + id).show();
                    $('.rm_home_cart_' + id).hide();
                    refreshStorefrontCartUi();
                    showStorefrontStatus('Giỏ hàng', 'Xóa sản phẩm khỏi giỏ hàng thành công.', 'success');
                }
            });
        }

        function refreshStorefrontCartUi() {
            show_cart();
            hover_cart();
            cart_session();
            syncHomeCartButtons();
        }

        function removeSessionCartItem(session_id, options) {
            var settings = $.extend({
                successMessage: 'Đã xóa sản phẩm khỏi giỏ hàng.',
                fallbackMessage: 'Không thể xóa sản phẩm khỏi giỏ hàng.',
                showQuickCartAfterSuccess: false,
                showQuickCartStatus: false,
                onSuccess: null
            }, options || {});

            loadStorefrontJson({
                url: '{{ url('/del-product') }}' + '/' + session_id,
                method: 'GET',
                data: {
                    _token: $('input[name="_token"]').val()
                },
                fallbackMessage: settings.fallbackMessage,
                onSuccess: function(response) {
                    var message = (response && response.message) ? response.message : settings.successMessage;

                    refreshStorefrontCartUi();

                    if (settings.showQuickCartAfterSuccess) {
                        show_quick_cart();
                    }

                    if (settings.showQuickCartStatus) {
                        setQuickCartStatus(message, 'success');
                    } else {
                        showStorefrontStatus('Giỏ hàng', message, 'success');
                    }

                    if (typeof settings.onSuccess === 'function') {
                        settings.onSuccess(response);
                    }
                },
                onError: function(xhr) {
                    var message = getAjaxErrorMessage(xhr, settings.fallbackMessage);

                    if (settings.showQuickCartStatus) {
                        setQuickCartStatus(message, 'error');
                    } else {
                        showStorefrontStatus('Giỏ hàng', message, 'error');
                    }
                }
            });
        }

        $(document).on('click', '[data-hover-cart-remove]', function(event) {
            event.preventDefault();

            removeSessionCartItem($(this).data('sessionId'), {
                fallbackMessage: 'Không thể xóa sản phẩm khỏi giỏ hàng mini.'
            });
        });

        function openCartSuccessDialog(title, text) {
            swal({
                    title: title,
                    text: text,
                    showCancelButton: true,
                    cancelButtonText: "Xem tiếp",
                    confirmButtonClass: "btn-success",
                    confirmButtonText: "Đi đến giỏ hàng",
                    closeOnConfirm: false
                },
                function() {
                    window.location.href = "/gio-hang";
                });
        }

        function addProductToCart(payload, options) {
            var settings = $.extend({
                fallbackMessage: 'Có lỗi xảy ra khi thêm sản phẩm vào giỏ hàng.',
                beforeSend: null,
                onSuccess: null,
                onError: null
            }, options || {});

            loadStorefrontJson({
                url: '{{ url('/add-cart-ajax') }}',
                method: 'POST',
                data: payload,
                fallbackMessage: settings.fallbackMessage,
                beforeSend: settings.beforeSend,
                onSuccess: function(response) {
                    if (typeof settings.onSuccess === 'function') {
                        settings.onSuccess(response);
                    }
                },
                onError: function(xhr) {
                    if (typeof settings.onError === 'function') {
                        settings.onError(xhr);
                        return;
                    }

                    showStorefrontStatus('Giỏ hàng', getAjaxErrorMessage(xhr, settings.fallbackMessage), 'error');
                }
            });
        }
        $(document).ready(function() {
            $('.add-to-cart').click(function() {
                var id_product = $('.product_id').val();
                var id_attribute = $('.cart_product_id_attribute').val();
                var maxQuantity = parseInt($('.cart_product_qty').attr('max') || 0, 10);
                var cart_product_qty = parseInt($('.cart_product_qty').val() || 1, 10);
                var _token = $('input[name="_token"]').val();
                var selectedAttributeLabel = $('.pro_attr_image_click.active').find('strong').first().text() || 'Phiên bản đã chọn';

                clearStorefrontInlineStatus('#product-detail-purchase-status');

                if (!maxQuantity) {
                    setStorefrontInlineStatus('#product-detail-purchase-status',
                        'Sản phẩm hiện chưa sẵn sàng để thêm vào giỏ hàng.', 'warning');
                    showStorefrontStatus('Tạm hết hàng',
                        'Sản phẩm hiện chưa sẵn sàng để thêm vào giỏ hàng.', 'warning');
                    return;
                }

                if (cart_product_qty > maxQuantity) {
                    var quantityMessage = (id_attribute && parseInt(id_attribute, 10) > 0)
                        ? selectedAttributeLabel + ' chỉ còn ' + maxQuantity + ' sản phẩm.'
                        : 'Sản phẩm chỉ còn ' + maxQuantity + ' sản phẩm.';

                    $('.cart_product_qty').val(maxQuantity);
                    setStorefrontInlineStatus('#product-detail-purchase-status', quantityMessage, 'warning');
                    showStorefrontStatus('Số lượng chưa hợp lệ', quantityMessage, 'warning');
                    return;
                }

                addProductToCart({
                    id_product: id_product,
                    id_attribute: id_attribute,
                    cart_product_qty: cart_product_qty,
                    _token: _token
                }, {
                    onSuccess: function() {
                        refreshStorefrontCartUi();
                        openCartSuccessDialog(
                            "Đã thêm sản phẩm vào giỏ hàng",
                            "Bạn có thể mua hàng tiếp hoặc tới giỏ hàng để tiến hành thanh toán"
                        );
                    }
                });
            });
        });
    </script>

    <script type="text/javascript">
        function show_quick_cart() {
            loadStorefrontFragment({
                url: '{{ url('/show_quick_cart') }}',
                method: 'GET',
                target: '#show_quick_cart',
                fallbackMessage: 'Không thể tải popup giỏ hàng lúc này.',
                onSuccess: function() {
                    $('#quick-cart').modal();
                }
            });
        }

        function DeleteItemCart($session_id) {
            removeSessionCartItem($session_id, {
                fallbackMessage: 'Không thể xóa sản phẩm khỏi popup giỏ hàng.',
                showQuickCartAfterSuccess: true,
                showQuickCartStatus: true
            });
        }

        $(document).on('click', '[data-quick-cart-remove]', function(event) {
            event.preventDefault();
            DeleteItemCart($(this).data('sessionId'));
        });

        $(document).on('input', '.cart_qty_update', function() {

            var quantity = $(this).val();
            var session_id = $(this).data('session_id');

            var _token = $('input[name="_token"]').val();
            loadStorefrontJson({
                url: '{{ url('/update-quick-cart') }}',
                method: 'POST',
                data: {
                    quantity: quantity,
                    session_id: session_id,
                    _token: _token
                },
                fallbackMessage: 'Không thể cập nhật nhanh giỏ hàng lúc này.',
                onSuccess: function() {
                    show_quick_cart();
                }
            });
        })

        function Addtocart($product_id) {
            var product_id = $product_id;
            var _token = $('input[name="_token"]').val();

            addProductToCart({
                id_product: product_id,
                id_attribute: null,
                cart_product_qty: 1,
                _token: _token
            }, {
                onSuccess: function() {
                    $('.home_cart_' + product_id).hide();
                    $('.rm_home_cart_' + product_id).show();

                    refreshStorefrontCartUi();
                    openCartSuccessDialog(
                        "Đã thêm sản phẩm vào giỏ hàng",
                        "Bạn có thể tiếp tục mua sắm hoặc đến giỏ hàng để thanh toán"
                    );
                }
            });
        }
    </script>
    <!--add to  cart quickview-->
    <script type="text/javascript">
        $(document).on('click', '.add-to-cart-quickview', function() {

            var id = $(this).data('id_product');
            var cart_product_id = $('.cart_product_id_' + id).val();
            var cart_product_quantity = $('.cart_product_quantity_' + id).val();
            var cart_product_qty = $('.cart_product_qty_' + id).val();
            var _token = $('input[name="_token"]').val();

            if (parseInt(cart_product_qty) > parseInt(cart_product_quantity)) {
                var warningMessage = 'Làm ơn đặt nhỏ hơn ' + cart_product_quantity;

                setStorefrontInlineStatus('#beforesend_quickview', warningMessage, 'warning');
                showStorefrontStatus('Số lượng chưa hợp lệ', warningMessage, 'warning');
            } else {
                addProductToCart({
                    id_product: cart_product_id,
                    id_attribute: null,
                    cart_product_qty: cart_product_qty,
                    _token: _token
                }, {
                    beforeSend: function() {
                        setStorefrontInlineStatus('#beforesend_quickview',
                            'Đang thêm sản phẩm vào giỏ hàng', 'info');
                    },
                    onSuccess: function() {
                        setStorefrontInlineStatus('#beforesend_quickview',
                            'Sản phẩm đã thêm vào giỏ hàng', 'success', {
                                autoHideAfter: 2400
                            });
                        refreshStorefrontCartUi();
                    },
                    onError: function(xhr) {
                        setStorefrontInlineStatus('#beforesend_quickview',
                            getAjaxErrorMessage(xhr, 'Không thể thêm sản phẩm vào giỏ hàng lúc này.'),
                            'error');
                        showStorefrontStatus('Giỏ hàng',
                            getAjaxErrorMessage(xhr, 'Không thể thêm sản phẩm vào giỏ hàng lúc này.'),
                            'error');
                    }
                });
            }


        });
        $(document).on('click', '.redirect-cart', function() {
            window.location.href = "{{ url('/gio-hang') }}";
        });
    </script>
    <script type="text/javascript">
        function formatCheckoutCurrency(amount) {
            return new Intl.NumberFormat('vi-VN').format(amount) + ' VND';
        }

        function renderCheckoutDeliveryNotice(message, type) {
            var cssClass = 'alert-info';

            if (type === 'success') {
                cssClass = 'alert-success';
            } else if (type === 'error') {
                cssClass = 'alert-danger';
            } else if (type === 'warning') {
                cssClass = 'alert-warning';
            }

            $('#checkout_delivery_notice').html('<div class="alert ' + cssClass + '">' + message + '</div>');
        }

        function syncCheckoutDeliveryTotals(fee) {
            var numericFee = Math.max(0, parseFloat(fee || 0));
            var baseTotal = Math.max(0, parseFloat($('.checkout_base_total').val() || 0));
            var grandTotal = baseTotal + numericFee;

            $('.order_fee').val(numericFee);
            $('.total_after').val(grandTotal);
            $('[data-checkout-fee-display]').text(formatCheckoutCurrency(numericFee));
            $('[data-checkout-grand-total-display]').text(formatCheckoutCurrency(grandTotal));
        }

        $(document).ready(function() {

            $('.choose').on('change', function() {
                var action = $(this).attr('id');
                var ma_id = $(this).val();
                var _token = $('input[name="_token"]').val();
                var result = '';

                if (action == 'city') {
                    result = 'province';
                } else {
                    result = 'wards';
                }
                loadStorefrontFragment({
                    url: '{{ url('/select-delivery-home') }}',
                    method: 'POST',
                    data: {
                        action: action,
                        ma_id: ma_id,
                        _token: _token
                    },
                    target: '#' + result,
                    fallbackMessage: 'Không thể tải danh sách địa chỉ.',
                    onSuccess: function() {
                        if (result === 'province') {
                            $('#wards').html('<option value="">--- Chọn phường / xã ---</option>');
                        }
                    }
                });
            });
        });
    </script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('.calculate_delivery').click(function() {
                var $button = $(this);
                var matp = $('.city').val();
                var maqh = $('.province').val();
                var xaid = $('.wards').val();
                var _token = $('input[name="_token"]').val();
                if (matp == '' && maqh == '' && xaid == '') {
                    renderCheckoutDeliveryNotice('Làm ơn chọn địa chỉ để tính phí vận chuyển.', 'warning');
                    showStorefrontStatus('Thiếu thông tin giao hàng',
                        'Làm ơn chọn địa chỉ để tính phí vận chuyển.', 'warning');
                } else {
                loadStorefrontJson({
                    url: '{{ url('/calculate-fee') }}',
                    method: 'POST',
                    beforeSend: function() {
                        $button.prop('disabled', true);
                        $button.data('originalHtml', $button.html());
                        $button.html('<i class="fa fa-spinner fa-spin"></i>Đang tính phí');
                    },
                    data: {
                        matp: matp,
                        maqh: maqh,
                        xaid: xaid,
                        city_id: matp,
                        province_id: maqh,
                        ward_id: xaid,
                        _token: _token
                    },
                    fallbackMessage: 'Không thể tính phí vận chuyển.',
                    onSuccess: function(response) {
                        var fee = response && typeof response.fee !== 'undefined' ? response.fee : 0;

                        syncCheckoutDeliveryTotals(fee);
                        renderCheckoutDeliveryNotice('Đã cập nhật phí vận chuyển theo khu vực bạn chọn.', 'success');
                        showStorefrontStatus('Phí vận chuyển',
                            'Đã cập nhật phí vận chuyển theo khu vực bạn chọn.', 'success');
                    },
                    onError: function(xhr) {
                        var message = getAjaxErrorMessage(xhr, 'Không thể tính phí vận chuyển.');
                        renderCheckoutDeliveryNotice(message, 'error');
                        showStorefrontStatus('Chưa thể tính phí vận chuyển', message, 'error');
                    },
                    complete: function() {
                        $button.prop('disabled', false);
                        $button.html($button.data('originalHtml') || '<i class="fa fa-truck"></i>Tính phí vận chuyển');
                    }
                });
                }
            });
        });
    </script>
    <!-- JavaScript Code -->
    <script>
        $(document).ready(function() {
            // Initialize lightSlider
            var slider = $("#imageGallery").lightSlider({
                gallery: true,
                item: 1,
                loop: true,
                thumbItem: 3,
                slideMargin: 0,
                enableDrag: false,
                currentPagerPosition: 'left',
                onSliderLoad: function(el) {
                    el.lightGallery({
                        selector: '#imageGallery .lslide'
                    });
                }
            });

            // Function to format price
            function formatPrice(price) {
                return new Intl.NumberFormat('vi-VN', {
                    style: 'currency',
                    currency: 'VND'
                }).format(price);
            }

            function syncProductDetailPurchaseState(stock, label) {
                var availableStock = Math.max(0, parseInt(stock || 0, 10));
                var $purchase = $('.shopw-pdp-purchase');
                var $quantityInput = $('.cart_product_qty');
                var $addButton = $('.add-to-cart');
                var $buttonLabel = $('.shopw-pdp-purchase-btn-label');
                var defaultLabel = $purchase.data('defaultLabel') || 'Thêm giỏ hàng';
                var nextLabel = label || (availableStock > 0 ? defaultLabel : 'Tạm hết hàng');
                var currentValue = parseInt($quantityInput.val() || 1, 10);

                $quantityInput.attr('max', Math.max(1, availableStock));

                if (availableStock <= 0) {
                    $quantityInput.val(0).prop('disabled', true);
                    $addButton.prop('disabled', true);
                } else {
                    $quantityInput.prop('disabled', false);
                    $quantityInput.val(Math.min(Math.max(1, currentValue || 1), availableStock));
                    $addButton.prop('disabled', false);
                }

                $buttonLabel.text(nextLabel);
            }

            // Initialize default state
            const defaultPrice = $('.cart_product_price').val() || $('.cart_price_attribute').val();
            const defaultStock = parseInt($('.shopw-pdp-purchase').data('defaultStock') || $('.cart_product_qty').attr('max') || 0, 10);
            $('#price_after_click').hide();
            $('.cart_price_attribute').val(defaultPrice);
            syncProductDetailPurchaseState(defaultStock);

            // Click event for attribute selection
            $('.pro_attr_image_click').click(function() {

                var key_image = $(this).data('key_image'); // Get the key_image value
                var newPrice = $(this).data('price_attribute'); // Get the price of the clicked attribute

                var attributeStock = parseInt($(this).data('qty_attribute') || 0, 10);
                var newIdAttribute = $(this).data('id_attribute');

                // Set the value of the hidden input

                $('.cart_product_id_attribute').val(
                    0);

                // Toggle behavior: if already clicked (active), unclick it
                if ($(this).hasClass('active')) {
                    // If already active, hide price_after_click and show price_init again
                    $('#price_after_click').hide();
                    $('#price_init').show();
                    $('.cart_price_attribute').val(defaultPrice);
                    syncProductDetailPurchaseState(defaultStock);
                    // Remove active class
                    $(this).removeClass('active');

                    // Optionally, you can also reset the slider if needed
                    slider.goToSlide(0); // Go back to the first slide
                } else {

                    $('.cart_product_id_attribute').val(
                        newIdAttribute);
                    syncProductDetailPurchaseState(attributeStock, attributeStock > 0 ? 'Thêm giỏ hàng' : 'Phiên bản tạm hết hàng');

                    // If not active, update price and show the clicked price
                    $('#price_after_click').text('Giá ' + formatPrice(newPrice)).show();
                    $('#price_init').hide(); // Hide the initial price

                    // Highlight the clicked element
                    $('.pro_attr_image_click').removeClass('active');
                    $(this).addClass('active');

                    // Remove 'active' class from all slider items
                    $('[class^="pro_attr_image_"]').removeClass('active');

                    // Add 'active' class to the corresponding slide
                    $(`.pro_attr_image_${key_image}`).addClass('active');

                    // Navigate to the corresponding slide
                    slider.goToSlide(key_image);
                }
            });
        });
    </script>
