<script type="text/javascript">
    function escapeHtml(value) {
        return $('<div/>').text(value == null ? '' : String(value)).html();
    }

    function showStorefrontStatus(title, text, type) {
        swal(title || 'Thông báo', text || '', type || 'info');
    }

    function normalizeStorefrontStatusType(type) {
        if (type === 'danger') {
            return 'error';
        }

        if (['success', 'warning', 'error', 'info'].indexOf(type) === -1) {
            return 'info';
        }

        return type;
    }

    function buildStorefrontInlineStatus(message, type) {
        var normalizedType = normalizeStorefrontStatusType(type);

        return "<div class='shopw-inline-status shopw-inline-status--" + normalizedType + "' role='status'>" +
            "<span class='shopw-inline-status__text'>" + escapeHtml(message) + "</span>" +
            "</div>";
    }

    function buildStorefrontStatePanel(message, type, options) {
        var settings = $.extend({
            title: '',
            contextClass: ''
        }, options || {});
        var normalizedType = normalizeStorefrontStatusType(type);
        var titleHtml = settings.title ?
            "<div class='shopw-ajax-state__title'>" + escapeHtml(settings.title) + "</div>" : '';
        var contextClass = settings.contextClass ? (' ' + settings.contextClass) : '';

        return "<div class='shopw-ajax-state shopw-ajax-state--" + normalizedType + contextClass + "'>" +
            titleHtml +
            "<p class='shopw-ajax-state__message'>" + escapeHtml(message) + "</p>" +
            "</div>";
    }

    function buildStorefrontEmptyState(options) {
        var settings = $.extend({
            title: 'Chưa có dữ liệu',
            message: 'Nội dung sẽ xuất hiện tại đây khi có dữ liệu phù hợp.',
            icon: 'fa fa-inbox',
            compact: false,
            center: true,
            className: '',
            actionText: '',
            actionUrl: ''
        }, options || {});
        var classNames = ['shopw-empty-state'];

        if (settings.compact) {
            classNames.push('shopw-empty-state--compact');
        }

        if (settings.center !== false) {
            classNames.push('shopw-empty-state--center');
        }

        if (settings.className) {
            classNames.push(settings.className);
        }

        var actionHtml = '';
        if (settings.actionText && settings.actionUrl) {
            actionHtml = "<a href='" + escapeHtml(settings.actionUrl) + "' class='shopw-empty-state__action'>" +
                escapeHtml(settings.actionText) + "</a>";
        }

        return "<div class='" + classNames.join(' ') + "'>" +
            "<div class='shopw-empty-state__icon' aria-hidden='true'><i class='" + escapeHtml(settings.icon) + "'></i></div>" +
            "<div class='shopw-empty-state__body'>" +
            "<h3 class='shopw-empty-state__title'>" + escapeHtml(settings.title) + "</h3>" +
            "<p class='shopw-empty-state__message'>" + escapeHtml(settings.message) + "</p>" +
            actionHtml +
            "</div></div>";
    }

    function setStorefrontInlineStatus(target, message, type, options) {
        var settings = $.extend({
            autoHideAfter: 0,
            clearOnHide: true
        }, options || {});
        var $target = $(target);

        if (!$target.length) {
            return;
        }

        $target.stop(true, true).hide().html(buildStorefrontInlineStatus(message, type)).fadeIn(120);

        if (settings.autoHideAfter > 0) {
            setTimeout(function() {
                $target.fadeOut(220, function() {
                    if (settings.clearOnHide) {
                        $(this).html('');
                    }

                    $(this).show();
                });
            }, settings.autoHideAfter);
        }
    }

    function clearStorefrontInlineStatus(target) {
        var $target = $(target);

        if (!$target.length) {
            return;
        }

        $target.stop(true, true).html('').show();
    }

    function setQuickCartStatus(message, type, options) {
        setStorefrontInlineStatus('.show_quick_cart_alert', message, type, $.extend({
            autoHideAfter: 1600
        }, options || {}));
    }

    function getAjaxErrorMessage(xhr, fallback) {
        if (xhr && xhr.responseJSON) {
            if (xhr.responseJSON.errors) {
                var firstKey = Object.keys(xhr.responseJSON.errors)[0];

                if (firstKey && xhr.responseJSON.errors[firstKey] && xhr.responseJSON.errors[firstKey].length) {
                    return xhr.responseJSON.errors[firstKey][0];
                }
            }

            if (xhr.responseJSON.message) {
                return xhr.responseJSON.message;
            }

            if (xhr.responseJSON.error) {
                return xhr.responseJSON.error;
            }
        }

        return fallback;
    }

    function loadStorefrontFragment(options) {
        var settings = $.extend({
            method: 'GET',
            data: {},
            headers: {},
            target: null,
            mode: 'replace',
            fallbackMessage: 'Không thể tải dữ liệu lúc này.',
            beforeSend: null,
            onSuccess: null,
            onError: null
        }, options || {});

        $.ajax({
            url: settings.url,
            method: settings.method,
            data: settings.data,
            headers: settings.headers,
            beforeSend: function() {
                if (typeof settings.beforeSend === 'function') {
                    settings.beforeSend();
                }
            },
            success: function(data) {
                if (settings.target) {
                    renderAjaxHtml(settings.target, data, settings.mode);
                }

                if (typeof settings.onSuccess === 'function') {
                    settings.onSuccess(data);
                }
            },
            error: function(xhr) {
                if (typeof settings.onError === 'function') {
                    settings.onError(xhr);
                    return;
                }

                showStorefrontStatus('Chưa thể xử lý', getAjaxErrorMessage(xhr, settings.fallbackMessage), 'error');
            }
        });
    }

    function loadStorefrontJson(options) {
        var settings = $.extend({
            method: 'GET',
            dataType: 'JSON',
            data: {},
            headers: {},
            fallbackMessage: 'Không thể xử lý yêu cầu lúc này.',
            beforeSend: null,
            onSuccess: null,
            onError: null
        }, options || {});

        $.ajax({
            url: settings.url,
            method: settings.method,
            dataType: settings.dataType,
            data: settings.data,
            headers: settings.headers,
            beforeSend: function() {
                if (typeof settings.beforeSend === 'function') {
                    settings.beforeSend();
                }
            },
            success: function(data) {
                if (typeof settings.onSuccess === 'function') {
                    settings.onSuccess(data);
                }
            },
            error: function(xhr) {
                if (typeof settings.onError === 'function') {
                    settings.onError(xhr);
                    return;
                }

                showStorefrontStatus('Chưa thể xử lý', getAjaxErrorMessage(xhr, settings.fallbackMessage), 'error');
            }
        });
    }

    function renderTabsErrorState(message) {
        renderAjaxHtml('#tabs_product',
            '<div class="tab-content"><div class="tab-pane fade active in"><div class="row"><div class="col-xs-12">' +
            buildStorefrontStatePanel(message, 'error', {
                title: 'Chưa thể tải tab sản phẩm',
                contextClass: 'shopw-ajax-state--center'
            }) +
            '</div></div></div></div>');
    }

    function renderCommentThreadError(message) {
        renderAjaxHtml('#comment_show',
            '<div class="style_comment"><div class="row"><div class="col-md-12">' +
            buildStorefrontStatePanel(message, 'error', {
                title: 'Chưa thể tải bình luận',
                contextClass: 'shopw-ajax-state--compact'
            }) +
            '</div></div></div>');
    }

    function renderSearchSuggestErrorState(message) {
        $('#search_ajax').fadeIn();
        renderAjaxHtml('#search_ajax',
            "<div class='shopw-search-suggest-panel'>" +
            buildStorefrontStatePanel(message, 'error', {
                title: 'Chưa thể tải gợi ý tìm kiếm',
                contextClass: 'shopw-ajax-state--compact'
            }) +
            "</div>");
    }

    function populateQuickviewModal(data) {
        $('#product_quickview_title').html(data.product_name || '');
        $('#product_quickview_title_body').html(data.product_name || '');
        $('#product_quickview_id').html(data.product_id || '');
        $('#product_quickview_price').html(data.product_price || '');
        $('#product_quickview_image').html(data.product_image || '');
        $('#product_quickview_gallery').html(data.product_gallery || '');
        $('#product_quickview_desc').html(data.product_desc || '');
        $('#product_quickview_content').html(data.product_content || '');
        $('#product_quickview_value').html(data.product_quickview_value || '');
        $('#product_quickview_button').html(data.product_button || '');

        if ($('#product_quickview_info').length) {
            $('#product_quickview_info').html(data.product_info || '');
        }
    }

    function requestQuickview(productId) {
        var _token = $('input[name="_token"]').val();

        loadStorefrontJson({
            url: "{{ url('/quickview') }}",
            method: 'POST',
            data: {
                product_id: productId,
                _token: _token
            },
            fallbackMessage: 'Không thể tải xem nhanh sản phẩm.',
            onSuccess: function(data) {
                populateQuickviewModal(data);
            }
        });
    }
</script>

