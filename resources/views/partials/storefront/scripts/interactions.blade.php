{{-- Storefront consolidated logic scripts. --}}

{{-- 1. Core / AJAX Setup --}}
<script>
    (function () {
        var tokenEl = document.querySelector('meta[name="csrf-token"]');
        if (!tokenEl || !window.jQuery) {
            return;
        }

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': tokenEl.getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
    })();
    function isFullHtmlResponse(data) {
        return typeof data === 'string' && /<!doctype|<html|<head|<body/i.test(data);
    }

    function renderAjaxHtml(target, data, mode) {
        if (typeof data !== 'string') {
            console.error('Unexpected non-string response for', target, data);
            return;
        }

        if (isFullHtmlResponse(data)) {
            console.error('Unexpected full HTML response for', target, data.slice(0, 200));
            return;
        }

        if (mode == 'append') {
            $(target).append(data);
        } else {
            $(target).html(data);
        }
    }
</script>

{{-- 2. AJAX Helpers --}}
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

    window.setStorefrontInlineStatus = setStorefrontInlineStatus;
    window.clearStorefrontInlineStatus = clearStorefrontInlineStatus;

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
        $('.shopw-search-hints').fadeIn();
        renderAjaxHtml('.shopw-search-hints',
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
        
        if (data.product_modal_body) {
            $('#product_quickview_body').html(data.product_modal_body);
        } else {
            // Legacy fallbacks if needed, but we're moving to product_modal_body
            $('#product_quickview_id').html(data.product_id || '');
            $('#product_quickview_price').html(data.product_price || '');
            $('#product_quickview_image').html(data.product_image || '');
            $('#product_quickview_gallery').html(data.product_gallery || '');
            $('#product_quickview_desc').html(data.product_desc || '');
            $('#product_quickview_content').html(data.product_content || '');
            $('#product_quickview_value').html(data.product_quickview_value || '');
            $('#product_quickview_button').html(data.product_button || '');
        }

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

{{-- 3. Storage Tools --}}
<script type="text/javascript">
    function readStorefrontStorage(key) {
        try {
            var parsed = JSON.parse(localStorage.getItem(key) || '[]');
            return Array.isArray(parsed) ? parsed : [];
        } catch (error) {
            return [];
        }
    }

    function writeStorefrontStorage(key, data) {
        localStorage.setItem(key, JSON.stringify(data));
    }

    function getProductSnapshotFromInputs(id, prefix) {
        var nameEl = document.getElementById(prefix + '_productname' + id);
        var priceEl = document.getElementById(prefix + '_productprice' + id);
        var imageEl = document.getElementById(prefix + '_productimage' + id);
        var urlEl = document.getElementById(prefix + '_producturl' + id);

        return {
            id: id,
            name: normalizeStorefrontLabel(nameEl ? nameEl.value : ''),
            price: priceEl ? priceEl.value : '',
            image: imageEl ? (imageEl.value || imageEl.src || '') : '',
            url: urlEl ? (urlEl.href || urlEl.value || '') : ''
        };
    }

    function normalizeStorefrontLabel(value) {
        var text = String(value || '');

        return text
            .replace(/phong\s*v[ũu]/gi, 'Đối tác phân phối')
            .replace(/phongvu(\.com)?/gi, 'Đối tác phân phối')
            .replace(/cellphone\s*s|cellphones(\.com\.vn)?/gi, 'Đối tác phân phối')
            .replace(/fpt\s*shop|fptshop/gi, 'Đối tác phân phối')
            .replace(/thế\s*giới\s*di\s*động|the\s*gioi\s*di\s*dong|thegioididong(\.com)?|tgdd/gi, 'Đối tác phân phối')
            .replace(/gearvn|hacom|hoàng\s*hà\s*mobile|hoang\s*ha\s*mobile|hnam\s*mobile|hnam/gi, 'Đối tác phân phối')
            .trim();
    }

    function normalizeStorefrontImage(image) {
        if (!image || image.indexOf('frontend/images/no-image.png') !== -1) {
            return "{{ asset('uploads/product/no-image.svg') }}";
        }

        return image;
    }

    function hasCompleteStorefrontImageSnapshot(item) {
        var image = String((item && item.image) || '');

        return image !== '' && image.indexOf('/uploads/product/no-image.svg') === -1 && image.indexOf('uploads/product/no-image.svg') === -1;
    }

    function renderCompareRow(item) {
        var image = normalizeStorefrontImage(item.image);
        var url = item.url || "#";

        return `
            <tr id="row_compare${item.id}">
                <td style="vertical-align: middle; font-weight: bold; min-width: 220px;">${escapeHtml(normalizeStorefrontLabel(item.name))}</td>
                <td style="vertical-align: middle; color: #d50018; font-weight: 800;">${escapeHtml(item.price)}</td>
                <td style="vertical-align: middle;"><img style="width: 80px; height: 80px; object-fit: contain; border-radius: 8px; background: #f8fafc; padding: 5px;" src="${escapeHtml(image)}"></td>
                <td style="vertical-align: middle; font-size: 12px; color: #64748b;">Sẵn sàng so sánh</td>
                <td style="vertical-align: middle;"><a href="${escapeHtml(url)}" class="btn btn-xs btn-info" style="border-radius: 4px; padding: 4px 8px; font-size: 11px;">Xem chi tiết</a></td>
                <td style="vertical-align: middle;"><button type="button" class="btn btn-xs btn-danger" style="border-radius: 4px; padding: 4px 8px; font-size: 11px;" onclick="delete_compare(${item.id})"><i class="fa fa-trash"></i></button></td>
            </tr>
        `;
    }

    function renderViewedRow(item, ctaText) {
        var image = normalizeStorefrontImage(item.image);

        return `
            <div class="row" style="margin:10px 0">
                <div class="col-md-4"><img width="100%" src="${escapeHtml(image)}"></div>
                <div class="col-md-8 info_wishlist">
                    <p>${escapeHtml(normalizeStorefrontLabel(item.name))}</p>
                    <p style="color:#FE980F">${escapeHtml(item.price)}</p>
                    <a href="${escapeHtml(item.url)}">${escapeHtml(ctaText)}</a>
                </div>
            </div>
        `;
    }

    function renderViewedCard(item, ctaText) {
        var image = normalizeStorefrontImage(item.image);
        var url = item.url || "{{ url('/') }}";
        var name = normalizeStorefrontLabel(item.name);
        var productId = item.id || name || 'viewed-product';
        var price = item.price
            ? `<div class="tech-sale-price">${escapeHtml(item.price)}</div>`
            : '';

        return `
            <article class="tech-product-card tech-product-card-compact shopw-cart-viewed-card" data-product-url="${escapeHtml(url)}">
                <button type="button"
                    class="tech-wishlist-btn"
                    aria-label="Yêu thích ${escapeHtml(name)}"
                    aria-pressed="false"
                    data-tech-wishlist
                    data-product-id="${escapeHtml(productId)}"
                    data-product-name="${escapeHtml(name)}"
                    data-product-price="${escapeHtml(item.price || '')}"
                    data-product-image="${escapeHtml(image)}"
                    data-product-url="${escapeHtml(url)}"
                    data-product-category-id="${escapeHtml(item.category_id || '')}"
                    data-product-category="${escapeHtml(item.category || '')}">
                    <i class="fa fa-heart-o"></i>
                </button>

                <a class="tech-product-media shopw-cart-viewed-media" href="${escapeHtml(url)}">
                    <img src="${escapeHtml(image)}" alt="${escapeHtml(name)}">
                </a>

                <div class="tech-product-body shopw-cart-viewed-copy">
                    <div class="tech-product-brand-row">
                        <span class="tech-brand-chip"><span>DD</span></span>
                        <strong>DangDuc</strong>
                    </div>

                    <h3><a href="${escapeHtml(url)}">${escapeHtml(name)}</a></h3>

                    <div class="tech-product-rating">
                        <i class="fa fa-star"></i>
                        <i class="fa fa-star"></i>
                        <i class="fa fa-star"></i>
                        <i class="fa fa-star"></i>
                        <i class="fa fa-star-o"></i>
                        <span>(128)</span>
                    </div>

                    <div class="tech-product-footer">
                        <div class="tech-price-block">${price}</div>
                        <a class="tech-add-cart-btn" href="${escapeHtml(url)}" aria-label="Xem ${escapeHtml(name)}">
                            <i class="fa fa-shopping-cart"></i>
                        </a>
                    </div>
                </div>
            </article>
        `;
    }

    function renderWishlistRow(item) {
        var image = normalizeStorefrontImage(item.image);

        return `
            <div class="row" id="wishlist_item${item.id}" style="margin:10px 0">
                <div class="col-md-4"><img width="100%" src="${escapeHtml(image)}"></div>
                <div class="col-md-8 info_wishlist">
                    <p>${escapeHtml(normalizeStorefrontLabel(item.name))}</p>
                    <p style="color:#FE980F">${escapeHtml(item.price)}</p>
                    <a href="${escapeHtml(item.url)}">Xem sản phẩm</a>
                </div>
            </div>
        `;
    }

    function renderCompareEmptyRow() {
        return `
            <tr class="shopw-compare-empty-row">
                <td colspan="6">
                    ${buildStorefrontEmptyState({
                        title: 'Chưa có sản phẩm so sánh',
                        message: 'Hãy thêm 2-4 sản phẩm để xem bảng so sánh chi tiết.',
                        icon: 'fa fa-balance-scale',
                        compact: true,
                        className: 'shopw-empty-state--table',
                        actionText: 'Khám phá sản phẩm',
                        actionUrl: "{{ url('/') }}"
                    })}
                </td>
            </tr>
        `;
    }

    function renderSidebarEmptyState(options) {
        return buildStorefrontEmptyState($.extend({
            compact: true,
            className: 'shopw-empty-state--sidebar'
        }, options || {}));
    }

    function delete_compare(id) {
        var data = readStorefrontStorage('compare').filter(function(item) {
            return String(item.id) !== String(id);
        });

        writeStorefrontStorage('compare', data);
        sosanh();
    }

    function ensureCompareModalInBody() {
        var modal = document.getElementById('sosanh');
        if (!modal) {
            return;
        }

        if (modal.parentElement !== document.body) {
            document.body.appendChild(modal);
        }
    }

    function showCompareModal() {
        ensureCompareModalInBody();

        var modal = document.getElementById('sosanh');
        if (!modal) {
            return;
        }

        $('#sosanh').modal('show');

        setTimeout(function() {
            var dialog = modal.querySelector('.modal-dialog');

            modal.classList.add('in');
            modal.style.display = 'block';
            modal.style.opacity = '1';
            modal.setAttribute('aria-hidden', 'false');

            if (dialog) {
                dialog.style.transform = 'translate(0, 0)';
            }

            if (!document.querySelector('.modal-backdrop')) {
                var backdrop = document.createElement('div');
                backdrop.className = 'modal-backdrop fade in';
                document.body.appendChild(backdrop);
            }

            document.body.classList.add('modal-open');
        }, 50);
    }

    function hideCompareModal() {
        var modal = document.getElementById('sosanh');
        if (!modal) {
            return;
        }

        modal.classList.remove('in');
        modal.style.display = 'none';
        modal.style.opacity = '';
        modal.setAttribute('aria-hidden', 'true');

        document.body.classList.remove('modal-open');
        document.querySelectorAll('.modal-backdrop').forEach(function(backdrop) {
            backdrop.remove();
        });
    }

    document.addEventListener('click', function(event) {
        var target = event.target;
        if (!target) {
            return;
        }

        if (target.classList && target.classList.contains('modal-backdrop')) {
            hideCompareModal();
            return;
        }

        if (target.matches && target.matches('#sosanh [data-dismiss="modal"], #sosanh .close')) {
            hideCompareModal();
        }
    });

    function sosanh() {
        var tableBody = $('#row_compare').find('tbody');
        if (!tableBody.length) {
            return;
        }

        tableBody.html('');
        var data = readStorefrontStorage('compare');

        if (!data.length) {
            tableBody.append(renderCompareEmptyRow());
            return;
        }

        data.forEach(function(item) {
            tableBody.append(renderCompareRow(item));
        });
    }
    sosanh();

    function add_compare(product_id) {
        ensureCompareModalInBody();

        product_id = parseInt(product_id, 10);
        if (!product_id || product_id <= 0) {
            swal("Chưa thể so sánh", "Sản phẩm không hợp lệ hoặc đã ngừng hiển thị.", "warning");
            return;
        }

        var item = getProductSnapshotFromInputs(product_id, 'wishlist');
        item.id = product_id;
        item.name = normalizeStorefrontLabel(item.name);
        item.image = normalizeStorefrontImage(item.image);
        item.url = item.url || '';

        if (!item.name || !item.url || item.url.indexOf('/chi-tiet/') === -1) {
            swal("Chưa thể so sánh", "Sản phẩm này chưa đủ thông tin để đưa vào bảng so sánh.", "warning");
            return;
        }

        var data = readStorefrontStorage('compare');
        var exists = data.some(function(entry) {
            return String(entry.id) === String(product_id);
        });

        if (exists) {
            swal("Đã có trong danh sách", "Sản phẩm này đã nằm trong bảng so sánh.", "info");
            showCompareModal();
            return;
        }

        if (data.length >= 4) {
            swal("Danh sách đầy", "Bạn chỉ có thể so sánh tối đa 4 sản phẩm cùng lúc.", "warning");
            return;
        }

        data.push(item);
        writeStorefrontStorage('compare', data);
        sosanh();
        
        swal({
            title: "Đã thêm so sánh",
            text: "Sản phẩm đã được thêm vào danh sách so sánh.",
            type: "success",
            showCancelButton: true,
            confirmButtonClass: "btn-success",
            confirmButtonText: "Xem danh sách",
            cancelButtonText: "Tiếp tục",
            closeOnConfirm: true
        }, function(isConfirm) {
            if (isConfirm) {
                showCompareModal();
            }
        });
    }

    function openCompareDetail() {
        var data = readStorefrontStorage('compare');
        var emptyMessage = 'Vui lòng chọn ít nhất 2 sản phẩm hợp lệ để so sánh chi tiết.';

        data = data.filter(function(item) {
            return item && item.id && item.name && item.url && String(item.url).indexOf('/chi-tiet/') !== -1;
        });

        if (data.length < 2) {
            showStorefrontStatus('Chưa có sản phẩm so sánh', emptyMessage, 'info');
            swal("Cần thêm sản phẩm", "Vui lòng chọn ít nhất 2 sản phẩm hợp lệ để so sánh chi tiết.", "info");
            return;
        }

        var ids = data.map(function(item) {
            return item.id;
        }).filter(Boolean).join(',');

        if (!ids) {
            showStorefrontStatus('Chưa có sản phẩm so sánh', emptyMessage, 'info');
            return;
        }

        var firstItem = data[0] || null;
        if (firstItem && firstItem.url) {
            window.location.href = firstItem.url + '?compare=' + encodeURIComponent(ids) + '#so-sanh-nhanh';
            return;
        }

        window.location.href = "{{ url('/so-sanh') }}" + '?ids=' + encodeURIComponent(ids);
    }

    function viewed() {
        var rowViewed = document.getElementById('row_viewed');
        var cartViewedGrid = document.getElementById('shopw_cart_viewed_grid');

        if (!rowViewed && !cartViewedGrid) {
            return;
        }

        var data = readStorefrontStorage('viewed').filter(hasCompleteStorefrontImageSnapshot).slice().reverse();

        if (rowViewed) {
            rowViewed.innerHTML = '';

            if (!data.length) {
                rowViewed.style.overflow = '';
                rowViewed.style.height = '';
                rowViewed.innerHTML = renderSidebarEmptyState({
                    title: 'Chưa có sản phẩm đã xem',
                    message: 'Các sản phẩm bạn vừa xem sẽ xuất hiện tại đây.',
                    icon: 'fa fa-clock-o'
                });
            } else {
                rowViewed.style.overflowY = 'auto';
                rowViewed.style.height = 'min(500px, 70vh)';

                data.forEach(function(item) {
                    $('#row_viewed').append(renderViewedRow(item, 'Xem sản phẩm'));
                });
            }
        }

        if (cartViewedGrid) {
            cartViewedGrid.innerHTML = '';

            if (!data.length) {
                cartViewedGrid.innerHTML = buildStorefrontEmptyState({
                    title: 'Chưa có sản phẩm đã xem',
                    message: 'Các sản phẩm bạn vừa xem sẽ xuất hiện tại đây để bạn quay lại nhanh hơn.',
                    icon: 'fa fa-clock-o',
                    compact: true,
                    actionText: 'Khám phá sản phẩm',
                    actionUrl: "{{ url('/') }}"
                });
            } else {
                data.slice(0, 4).forEach(function(item) {
                    cartViewedGrid.insertAdjacentHTML('beforeend', renderViewedCard(item, 'Xem sản phẩm'));
                });
            }
        }
    }
    viewed();

    function product_viewed() {
        var idProduct = $('#product_viewed_id').val();

        if (typeof idProduct === 'undefined' || idProduct === '') {
            return;
        }

        var item = getProductSnapshotFromInputs(idProduct, 'viewed');
        var data = readStorefrontStorage('viewed');
        var exists = data.some(function(entry) {
            return String(entry.id) === String(idProduct);
        });

        if (!exists) {
            data.push(item);
            writeStorefrontStorage('viewed', data);
            viewed();
        }
    }
    product_viewed();

    function view() {
        var rowWishlist = document.getElementById('row_wishlist');
        if (!rowWishlist) {
            return;
        }

        rowWishlist.innerHTML = '';

        var data = readStorefrontStorage('data').slice().reverse();

        if (!data.length) {
            rowWishlist.style.overflow = '';
            rowWishlist.style.height = '';
            rowWishlist.innerHTML = renderSidebarEmptyState({
                title: 'Chưa có sản phẩm yêu thích',
                message: 'Lưu sản phẩm yêu thích để xem lại nhanh hơn.',
                icon: 'fa fa-heart-o'
            });
            return;
        }

        rowWishlist.style.overflowY = 'auto';
        rowWishlist.style.height = 'min(500px, 70vh)';

        data.forEach(function(item) {
            $('#row_wishlist').append(renderWishlistRow(item));
        });
    }
    view();

    function add_wishlist(clicked_id) {
        var item = getProductSnapshotFromInputs(clicked_id, 'wishlist');
        var data = readStorefrontStorage('data');
        var exists = data.some(function(entry) {
            return String(entry.id) === String(clicked_id);
        });

        if (exists) {
            data = data.filter(function(entry) {
                return String(entry.id) !== String(clicked_id);
            });
            swal("Đã bỏ thích", "Sản phẩm đã được xóa khỏi danh sách yêu thích.", "info");
        } else {
            data.push(item);
            swal("Đã yêu thích", "Sản phẩm đã được thêm vào danh sách yêu thích.", "success");
        }

        writeStorefrontStorage('data', data);
        view();

        if (typeof window.syncStorefrontWishlistButtons === 'function') {
            window.syncStorefrontWishlistButtons();
        }

        window.dispatchEvent(new CustomEvent('storefront:wishlist-updated'));
    }

    function add_wistlist(clicked_id) {
        return add_wishlist(clicked_id);
    }
</script>

{{-- 4. Mobile Nav --}}
<script>
    (function($) {
        if (!$) {
            return;
        }

        var openGroupStorageKey = 'shopw.storefront.open-group';

        function isMobileNav() {
            return window.matchMedia('(max-width: 991px)').matches;
        }

        function storage() {
            try {
                return window.sessionStorage;
            } catch (error) {
                return null;
            }
        }

        function rememberOpenGroup(groupId) {
            var session = storage();

            if (!session || !groupId) {
                return;
            }

            session.setItem(openGroupStorageKey, groupId);
        }

        function preferredMobileGroupSelector(fallbackSelector) {
            if (fallbackSelector) {
                return fallbackSelector;
            }

            var $currentGroup = $('#shopw-mobile-drawer .shopw-nav-group.is-current').first();

            if ($currentGroup.length && $currentGroup.attr('id')) {
                return '#' + $currentGroup.attr('id');
            }

            var session = storage();
            var rememberedGroupId = session ? session.getItem(openGroupStorageKey) : null;

            return rememberedGroupId ? '#' + rememberedGroupId : null;
        }

        function setCategoryTriggerExpanded(expanded) {
            $('.shopw-mobile-nav-toggle[data-nav-intent="categories"]')
                .attr('aria-expanded', expanded ? 'true' : 'false')
                .toggleClass('is-active', expanded);
        }

        function syncAccountMenuState($menu) {
            if (!$menu || !$menu.length) {
                return;
            }

            var expanded = $menu.prop('open');

            $menu.find('> summary').attr('aria-expanded', expanded ? 'true' : 'false');
        }

        function syncAllAccountMenus() {
            $('.shopw-account-menu').each(function() {
                syncAccountMenuState($(this));
            });
        }

        function syncLocationMenuState($menu) {
            if (!$menu || !$menu.length) {
                return;
            }

            var expanded = $menu.prop('open');

            $menu.find('> summary').attr('aria-expanded', expanded ? 'true' : 'false');
        }

        function syncAllLocationMenus() {
            $('.shopw-location-menu').each(function() {
                syncLocationMenuState($(this));
            });
        }

        function closeAccountMenus(exceptMenuElement) {
            $('.shopw-account-menu').each(function() {
                if (exceptMenuElement && this === exceptMenuElement) {
                    syncAccountMenuState($(this));
                    return;
                }

                if (this.open) {
                    this.open = false;
                }

                syncAccountMenuState($(this));
            });
        }

        function closeLocationMenus(exceptMenuElement) {
            $('.shopw-location-menu').each(function() {
                if (exceptMenuElement && this === exceptMenuElement) {
                    syncLocationMenuState($(this));
                    return;
                }

                if (this.open) {
                    this.open = false;
                }

                syncLocationMenuState($(this));
            });
        }

        function toggleAccountMenu(menuElement, forceOpen) {
            if (!menuElement) {
                return;
            }

            var shouldOpen = typeof forceOpen === 'boolean' ? forceOpen : !menuElement.open;

            closeAccountMenus(shouldOpen ? menuElement : null);
            closeLocationMenus();
            menuElement.open = shouldOpen;
            syncAccountMenuState($(menuElement));
        }

        function toggleLocationMenu(menuElement, forceOpen) {
            if (!menuElement) {
                return;
            }

            var shouldOpen = typeof forceOpen === 'boolean' ? forceOpen : !menuElement.open;

            closeLocationMenus(shouldOpen ? menuElement : null);
            closeAccountMenus();
            menuElement.open = shouldOpen;
            syncLocationMenuState($(menuElement));
        }

        function closeDesktopGroups(exceptGroupId) {
            $('#navbar .shopw-nav-group').each(function() {
                var $group = $(this);

                if (exceptGroupId && $group.attr('id') === exceptGroupId) {
                    return;
                }

                $group.removeClass('is-open');
                $group.find('> .shopw-nav-row .shopw-submenu-toggle').attr('aria-expanded', 'false');
            });
        }

        function setGroupExpanded($group, expanded, animate) {
            if (!$group.length) {
                return;
            }

            var $submenu = $group.children('.sub-menu');

            $group.toggleClass('is-open', expanded);
            $group.find('> .shopw-nav-row .shopw-submenu-toggle').attr('aria-expanded', expanded ? 'true' : 'false');

            if (isMobileNav()) {
                if (animate) {
                    $submenu.stop(true, true)[expanded ? 'slideDown' : 'slideUp'](180);
                } else if (expanded) {
                    $submenu.css('display', 'block');
                }
            }

            if (expanded && $group.attr('id')) {
                rememberOpenGroup($group.attr('id'));
            }

        }

        function setMobileNav(open, preferredGroup) {
            $('body').toggleClass('shopw-mobile-nav-open', open);
            $('#shopw-mobile-drawer').attr('aria-hidden', open ? 'false' : 'true');

            if (open) {
                setCategoryTriggerExpanded(true);
                collapseMobileGroups(true);

                var selector = preferredMobileGroupSelector(preferredGroup);

                if (selector) {
                    openMobileGroup(selector, false);
                }
            } else {
                setCategoryTriggerExpanded(false);
                collapseMobileGroups(true);
            }
        }

        function collapseMobileGroups(resetStyles) {
            $('#shopw-mobile-drawer .shopw-nav-group').removeClass('is-open');
            $('#shopw-mobile-drawer .shopw-submenu-toggle').attr('aria-expanded', 'false');

            if (resetStyles) {
                $('#shopw-mobile-drawer .sub-menu').removeAttr('style');
            }
        }

        function openMobileGroup(groupSelector, animate) {
            var $group = $(groupSelector);

            if (!$group.length) {
                return;
            }

            closeDesktopGroups($group.attr('id'));
            collapseMobileGroups(false);
            setGroupExpanded($group, true, animate !== false);
        }

        $(document).on('click', '.shopw-mobile-nav-toggle', function(e) {
            var intent = $(this).data('navIntent');

            if (!isMobileNav()) {
                return;
            }

            e.preventDefault();
            setMobileNav(true, null);
        });

        $(document).on('click', '.shopw-account-menu > summary', function(e) {
            if (isMobileNav()) {
                return;
            }

            e.preventDefault();

            var menuElement = $(this).parent('.shopw-account-menu').get(0);

            closeDesktopGroups();
            toggleAccountMenu(menuElement);
        });

        $(document).on('click', '.shopw-location-menu > summary', function(e) {
            if (isMobileNav()) {
                return;
            }

            e.preventDefault();

            var menuElement = $(this).parent('.shopw-location-menu').get(0);

            closeDesktopGroups();
            toggleLocationMenu(menuElement);
        });

        $(document).on('click', '.shopw-account-dropdown a', function() {
            closeAccountMenus();
        });

        $(document).on('click', '.shopw-mobile-drawer-close, .shopw-mobile-nav-overlay', function(e) {
            if (!isMobileNav()) {
                return;
            }

            e.preventDefault();
            setMobileNav(false);
        });

        $(document).on('click', '#navbar .shopw-submenu-toggle', function(e) {
            e.preventDefault();

            var $group = $(this).closest('.shopw-nav-group');
            var isOpen = $group.hasClass('is-open');

            if (isMobileNav()) {
                if (isOpen) {
                    setGroupExpanded($group, false, true);
                } else {
                    openMobileGroup('#' + $group.attr('id'), true);
                }

                return;
            }

            closeAccountMenus();

            if (isOpen) {
                closeDesktopGroups();
            } else {
                closeDesktopGroups($group.attr('id'));
                setGroupExpanded($group, true, false);
            }
        });

        $(document).on('click', '#shopw-mobile-drawer a', function() {
            if (!isMobileNav()) {
                return;
            }

            var href = $(this).attr('href') || '';

            if (href && href !== '#' && href.indexOf('javascript:') !== 0) {
                setMobileNav(false);
            }
        });

        $(document).on('keydown', function(e) {
            if (e.key === 'Escape') {
                setMobileNav(false);
                closeAccountMenus();
                closeLocationMenus();
                closeDesktopGroups();
            }
        });

        $(document).on('focusin', function(e) {
            if (isMobileNav()) {
                return;
            }

            if ($(e.target).closest('.shopw-account-menu').length) {
                return;
            }

            closeAccountMenus();
        });

        $(document).on('click', function(e) {
            if (isMobileNav()) {
                return;
            }

            var $target = $(e.target);

            if ($target.closest('.shopw-mobile-nav-toggle[data-nav-intent="categories"]').length) {
                closeAccountMenus();
                return;
            }

            if ($target.closest('#navbar .shopw-nav-group').length) {
                closeAccountMenus();
                return;
            }

            if ($target.closest('.shopw-account-menu').length) {
                closeLocationMenus();
                return;
            }

            if ($target.closest('.shopw-location-menu').length) {
                closeAccountMenus();
                return;
            }

            closeAccountMenus();
            closeLocationMenus();
            closeDesktopGroups();
        });

        $(window).on('resize', function() {
            if (!isMobileNav()) {
                setMobileNav(false);
                collapseMobileGroups(true);
                closeAccountMenus();
                closeLocationMenus();
                closeDesktopGroups();
            } else {
                closeAccountMenus();
                closeLocationMenus();
                closeDesktopGroups();
            }
        });

        syncAllAccountMenus();
        syncAllLocationMenus();
    })(window.jQuery);
</script>

{{-- 5. Hero / Slider --}}
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
                    interval: 7000,
                    pause: 'hover',
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
            }, 7000);
        }

        $(window).on('load', function() {
            setTimeout(function() {
                startSlider();
            }, 150);
        });
    })();
</script>

{{-- 6. Main Interactions --}}
<script type="text/javascript">
    $(document).on('click', '.js-cancel-order-trigger', function(event) {
        event.preventDefault();
        event.stopPropagation();

        var orderCode = $(this).data('orderCode') || $(this).data('order-code') || '';

        $('#cancel-order-submit').attr('data-order-code', orderCode);
        $('#cancel-order-code-preview').text(orderCode ? ('#' + orderCode) : '');
        $('.lydohuydon').val('');

        if (typeof $('#cancelOrderModal').modal === 'function') {
            $('#cancelOrderModal').modal('show');
        }
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
        $('#load_more_button').html('<b>Đang tải...</b>');
        load_more_product(id);


    })
</script>

<script type="text/javascript">
    // When the user scrolls the page, execute sticky_navbar
    window.onscroll = function() {
        sticky_navbar()
    };

    // Get the navbar
    var navbar = document.getElementById("navbar");

    // Get the offset position of the navbar
    var sticky = navbar ? navbar.offsetTop : 0;

    // Add the sticky class to the navbar when you reach its scroll position. Remove "sticky" when you leave the scroll position
    function sticky_navbar() {
        if (!navbar) return;
        if (window.pageYOffset >= sticky) {
            navbar.classList.add("sticky")
        } else {
            navbar.classList.remove("sticky");
        }
    }
</script>

<script type="text/javascript">
    if (typeof $.fn.owlCarousel === 'function') {
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
    }
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

<script type="text/javascript">
    $(document).ready(function() {
        if (!$('#slider-range').length || typeof $.fn.slider !== 'function') {
            return;
        }

        $("#slider-range").slider({
            orientation: "horizontal",
            range: true,

            min: {{ $min_price_range ?? 0 }},
            max: {{ $max_price_range ?? 100000000 }},

            step: 10000,
            values: [{{ $min_price ?? 0 }}, {{ $max_price ?? 100000000 }}],

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
            var val = $(this).val();
            if (val && (val.indexOf('?') !== -1 || val.indexOf('/') !== -1 || val.indexOf('http') === 0)) {
                window.location = val;
                return false;
            }
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
                if (data == 'Hoàn tất.') {
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
        if ($('.comment_product_id').length > 0) {
            load_comment();
        }

        function load_comment() {
            var product_id = $('.comment_product_id').val();
            var _token = $('input[name="_token"]').val();
            if (!product_id) return;
            
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
            if (!product_id) return;
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
    var searchSuggestSelector = '.shopw-search-hints';

    function initSearchPlaceholderRotation() {
        var $searchInput = $('#keywords');
        var $searchRotator = $('[data-search-rotator]');
        var $searchRotatorWord = $('[data-search-rotator-word]');

        if (!$searchInput.length || !$searchRotator.length || !$searchRotatorWord.length) {
            return;
        }

        var basePlaceholder = $searchInput.attr('data-placeholder-base') || $searchInput.attr('placeholder') || '';
        var trendingKeywords = [];

        try {
            trendingKeywords = JSON.parse($searchInput.attr('data-search-trends') || '[]');
        } catch (error) {
            trendingKeywords = [];
        }

        if (!Array.isArray(trendingKeywords)) {
            trendingKeywords = [];
        }

        var fallbackKeyword = $.trim($searchRotatorWord.text());

        if (trendingKeywords.length === 0 && fallbackKeyword !== '') {
            trendingKeywords = [fallbackKeyword];
        }

        var activeIndex = 0;
        var swapDelay = 220;

        function syncRotatorState() {
            var hasValue = $.trim($searchInput.val()) !== '';
            $searchInput.attr('placeholder', basePlaceholder);
            $searchRotator
                .addClass('is-visible')
                .toggleClass('is-condensed', hasValue);

            return hasValue;
        }

        function renderTrendingPlaceholder(forceImmediate) {
            var isTyping = syncRotatorState();

            if (isTyping || trendingKeywords.length === 0) {
                return;
            }

            if (forceImmediate) {
                $searchRotatorWord.text(trendingKeywords[activeIndex]);
                activeIndex = (activeIndex + 1) % trendingKeywords.length;
                return;
            }

            $searchRotatorWord.addClass('is-swapping');

            window.setTimeout(function() {
                var stillTyping = syncRotatorState();

                if (stillTyping) {
                    return;
                }

                $searchRotatorWord.text(trendingKeywords[activeIndex]);
                activeIndex = (activeIndex + 1) % trendingKeywords.length;

                window.requestAnimationFrame(function() {
                    $searchRotatorWord.removeClass('is-swapping');
                });
            }, swapDelay);
        }

        syncRotatorState();

        if (trendingKeywords.length > 0) {
            renderTrendingPlaceholder(true);
        }

        if (searchPlaceholderTimer) {
            clearInterval(searchPlaceholderTimer);
        }

        if (trendingKeywords.length > 1) {
            searchPlaceholderTimer = setInterval(function() {
                renderTrendingPlaceholder(false);
            }, 6200);
        }

        $searchInput.on('focus', function() {
            syncRotatorState();
        });

        $searchInput.on('input', function() {
            syncRotatorState();
        });

        $searchInput.on('blur', function() {
            syncRotatorState();
        });
    }

    function closeSearchSuggest() {
        if (searchSuggestTimer) {
            clearTimeout(searchSuggestTimer);
            searchSuggestTimer = null;
        }

        $(searchSuggestSelector).stop(true, true).fadeOut(120, function() {
            $(this).html('');
        });
    }

    function renderSearchSuggestHtml(html) {
        if ($.trim(html) === '') {
            closeSearchSuggest();
            return;
        }

        $(searchSuggestSelector).stop(true, true).fadeIn(120);
        renderAjaxHtml(searchSuggestSelector, html);
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

    $('#keywords').on('focus click', function() {
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

    $('.shopw-search').on('submit', function() {
        closeSearchSuggest();
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
        var countdown = $countdown.get(0);

        if (!countdown) {
            return;
        }

        if (window.StorefrontCountdown && typeof window.StorefrontCountdown.init === 'function') {
            window.StorefrontCountdown.init(countdown, $('[data-flash-sale-countdown]').index(countdown));
            return;
        }

        var targetValue = $countdown.data('flashSaleCountdown');
        var cycleMs = 24 * 60 * 60 * 1000;
        var targetTime = new Date(targetValue).getTime();

        if (!targetValue || Number.isNaN(targetTime)) {
            targetTime = Date.now() + cycleMs - 1000;
        }

        var render = function() {
            if (targetTime <= Date.now()) {
                targetTime = Date.now() + cycleMs - 1000;
            }

            var diff = Math.max(0, targetTime - Date.now());
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
        setInterval(function() {
            render();
        }, 1000);
    }

    $('[data-flash-sale-countdown]').each(function() {
        initFlashSaleCountdown($(this));
    });

    function initFlashSaleCarousel($carousel) {
        var $track = $carousel.find('[data-flash-sale-track]');
        var $prev = $carousel.find('[data-flash-nav="prev"]');
        var $next = $carousel.find('[data-flash-nav="next"]');
        var suppressClick = false;
        var interactionTimer = null;
        var dragState = {
            active: false,
            pointerId: null,
            startX: 0,
            startY: 0,
            startScrollLeft: 0,
            axis: null,
            moved: false
        };

        if (!$track.length || !$prev.length || !$next.length) {
            return;
        }

        function getTrackNode() {
            return $track.get(0);
        }

        function getNativeEvent(event) {
            return event && event.originalEvent ? event.originalEvent : event;
        }

        function getPointerPayload(event) {
            var nativeEvent = getNativeEvent(event) || {};

            return {
                button: typeof nativeEvent.button === 'number' ? nativeEvent.button : 0,
                clientX: typeof nativeEvent.clientX === 'number' ? nativeEvent.clientX : 0,
                clientY: typeof nativeEvent.clientY === 'number' ? nativeEvent.clientY : 0,
                pointerId: nativeEvent.pointerId ?? null,
                pointerType: nativeEvent.pointerType || 'mouse'
            };
        }

        function getCardWidth() {
            return $track.find('.shopw-flash-sale-card').first().outerWidth(true) || 0;
        }

        function setCarouselEngaged(active, ttl) {
            if (interactionTimer) {
                window.clearTimeout(interactionTimer);
                interactionTimer = null;
            }

            $carousel.toggleClass('is-engaged', !!active);

            if (active && ttl) {
                interactionTimer = window.setTimeout(function() {
                    $carousel.removeClass('is-engaged');
                    interactionTimer = null;
                }, ttl);
            }
        }

        function getStep() {
            var track = getTrackNode();
            var cardWidth = getCardWidth();

            if (!track) {
                return 0;
            }

            if (!cardWidth) {
                return Math.max(0, track.clientWidth - 40);
            }

            var visibleCards = Math.max(1, Math.floor(track.clientWidth / cardWidth));
            return Math.max(cardWidth, Math.min(track.clientWidth - 28, cardWidth * visibleCards));
        }

        function syncButtons() {
            var track = getTrackNode();

            if (!track) {
                return;
            }

            var hasOverflow = track.scrollWidth > track.clientWidth + 4;
            var isAtStart = track.scrollLeft <= 4;
            var isAtEnd = track.scrollLeft + track.clientWidth >= track.scrollWidth - 4;

            $prev.prop('disabled', !hasOverflow || isAtStart);
            $next.prop('disabled', !hasOverflow || isAtEnd);
        }

        function scrollByStep(direction) {
            var track = getTrackNode();
            var step = getStep();

            if (!track || !step) {
                return;
            }

            if (direction === 'next' && track.scrollLeft + track.clientWidth >= track.scrollWidth - 4) {
                track.scrollTo({
                    left: 0,
                    behavior: 'smooth'
                });
                return;
            }

            if (direction === 'prev' && track.scrollLeft <= 4) {
                track.scrollTo({
                    left: Math.max(0, track.scrollWidth - track.clientWidth),
                    behavior: 'smooth'
                });
                return;
            }

            track.scrollBy({
                left: (direction === 'prev' ? -1 : 1) * step,
                behavior: 'smooth'
            });
        }

        function resetDragState() {
            dragState.active = false;
            dragState.pointerId = null;
            dragState.axis = null;
            dragState.moved = false;
            $track.removeClass('is-dragging');
        }

        function endDrag() {
            var track = getTrackNode();

            if (!dragState.active) {
                return;
            }

            if (track && dragState.pointerId !== null && typeof track.releasePointerCapture === 'function') {
                try {
                    track.releasePointerCapture(dragState.pointerId);
                } catch (error) {
                    // Pointer capture can already be released; ignore and continue cleanup.
                }
            }

            if (dragState.moved) {
                window.setTimeout(function() {
                    suppressClick = false;
                }, 140);
            }

            resetDragState();
            syncButtons();
        }

        $prev.on('click', function() {
            setCarouselEngaged(true, 1400);
            scrollByStep('prev');
        });

        $next.on('click', function() {
            setCarouselEngaged(true, 1400);
            scrollByStep('next');
        });

        $track.on('pointerdown', function(event) {
            var track = getTrackNode();
            var pointer = getPointerPayload(event);

            if (!track || (pointer.pointerType === 'mouse' && pointer.button !== 0)) {
                return;
            }

            dragState.active = true;
            dragState.pointerId = pointer.pointerId;
            dragState.startX = pointer.clientX;
            dragState.startY = pointer.clientY;
            dragState.startScrollLeft = track.scrollLeft;
            dragState.axis = null;
            dragState.moved = false;
            suppressClick = false;
            setCarouselEngaged(true, 1800);
        });

        $(document).on('pointermove.flashSaleCarousel', function(event) {
            var track = getTrackNode();
            var pointer = getPointerPayload(event);
            var deltaX;
            var deltaY;

            if (!dragState.active || !track) {
                return;
            }

            if (dragState.pointerId !== null && pointer.pointerId !== null && dragState.pointerId !== pointer.pointerId) {
                return;
            }

            deltaX = pointer.clientX - dragState.startX;
            deltaY = pointer.clientY - dragState.startY;

            if (!dragState.axis) {
                if (Math.abs(deltaX) < 4 && Math.abs(deltaY) < 4) {
                    return;
                }

                dragState.axis = Math.abs(deltaX) > Math.abs(deltaY) ? 'x' : 'y';

                if (dragState.axis === 'x') {
                    $track.addClass('is-dragging');
                    setCarouselEngaged(true, 1800);
                }
            }

            if (dragState.axis !== 'x') {
                return;
            }

            dragState.moved = Math.abs(deltaX) > 8;
            suppressClick = dragState.moved;
            track.scrollLeft = dragState.startScrollLeft - deltaX;

            syncButtons();
            event.preventDefault();
        });

        $(document).on('pointerup.flashSaleCarousel pointercancel.flashSaleCarousel', function(event) {
            var pointer = getPointerPayload(event);

            if (dragState.pointerId !== null && pointer.pointerId !== null && dragState.pointerId !== pointer.pointerId) {
                return;
            }

            endDrag();
        });

        $track.on('dragstart', 'a, img', function(event) {
            event.preventDefault();
        });

        $track.on('click', 'a', function(event) {
            if (!suppressClick) {
                return;
            }

            event.preventDefault();
            event.stopImmediatePropagation();
            suppressClick = false;
        });

        $carousel.on('mouseenter focusin', function() {
            setCarouselEngaged(true);
        });

        $carousel.on('mouseleave focusout', function() {
            if (!dragState.active) {
                setCarouselEngaged(false);
            }
        });

        $track.on('scroll', syncButtons);
        $(window).on('resize', function() {
            endDrag();
            syncButtons();
        });

        window.setTimeout(syncButtons, 80);
    }

    $('[data-flash-sale-carousel]').each(function() {
        initFlashSaleCarousel($(this));
    });
</script>

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
                if (typeof vlitejs !== 'undefined') {
                    var playerYT = new vlitejs({
                        selector: '#my_yt_video',
                        options: {
                            autoplay: false,
                            controls: true,
                            playPause: true,
                            progressBar: true,
                            time: true,
                            volume: true,
                            fullscreen: true,
                            poster: null,
                            bigPlay: true,
                            autoHide: false,
                            nativeControlsForTouch: false
                        }
                    });
                }
            }

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
        loadStorefrontJson({
            url: '{{ url('/remove-item') }}',
            method: 'DELETE',
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

    function updateStorefrontCartBadges(count) {
        var normalizedCount = parseInt(count, 10);

        if (isNaN(normalizedCount) || normalizedCount < 0) {
            return;
        }

        $('.shopw-cart-count').text(normalizedCount);
        $('.show-cart').html('<span class="badges">' + normalizedCount + '</span>');
        $('.show-cart .badges, span.badges').text(normalizedCount);
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
            method: 'DELETE',
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
                if (response && (response.cart_count !== undefined || response.cart_total_items !== undefined)) {
                    updateStorefrontCartBadges(response.cart_count !== undefined
                        ? response.cart_count
                        : response.cart_total_items);
                }

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

    window.addProductToCart = addProductToCart;
    window.refreshStorefrontCartUi = refreshStorefrontCartUi;

    $(document).ready(function() {
        $('.add-to-cart').click(function() {
            if ($(this).is('[data-pdx-add-cart]')) {
                return;
            }

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
                if (typeof $.fn.modal === 'function') {
                    $('#quick-cart').modal();
                }
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

    $(document).on('click', '.tech-add-cart-btn[data-cart-product-id]', function(event) {
        event.preventDefault();
        event.stopPropagation();

        Addtocart($(this).data('cartProductId'));
    });
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
        return new Intl.NumberFormat('vi-VN').format(amount) + 'đ';
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

    function renderCheckoutOrderNotice(message, type) {
        var cssClass = 'alert-info';

        if (type === 'success') {
            cssClass = 'alert-success';
        } else if (type === 'error') {
            cssClass = 'alert-danger';
        } else if (type === 'warning') {
            cssClass = 'alert-warning';
        }

        $('#checkout_order_notice').html('<div class="alert ' + cssClass + '" style="margin:12px 0 0;padding:10px 12px;border-radius:8px;font-size:13px;">' + escapeHtml(message) + '</div>');
    }

    function clearCheckoutFieldErrors() {
        $('[data-checkout-error-for]').text('');
    }

    function renderCheckoutFieldErrors(errors) {
        clearCheckoutFieldErrors();

        if (!errors) {
            return;
        }

        Object.keys(errors).forEach(function(field) {
            var message = errors[field] && errors[field][0] ? errors[field][0] : '';
            $('[data-checkout-error-for="' + field + '"]').text(message);
        });
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

    function setCheckoutShippingResolved(isResolved) {
        $('.shipping_fee_resolved').val(isResolved ? '1' : '0');
        updateCheckoutSubmitState();
    }

    function isCheckoutShippingResolved() {
        return $('.shipping_fee_resolved').val() === '1';
    }

    function hasCheckoutRequiredAddress() {
        return !!($('.shipping_email').val() &&
            $('.shipping_name').val() &&
            $('.shipping_address').val() &&
            $('.shipping_phone').val() &&
            $('.province').val() &&
            $('.wards').val());
    }

    function updateCheckoutSubmitState() {
        var canSubmit = hasCheckoutRequiredAddress() && isCheckoutShippingResolved();
        $('.send_order').prop('disabled', !canSubmit);
    }

    function shippingFeeMessage(response) {
        var label = response && response.label ? response.label : 'Theo khu vực giao hàng';
        var fee = response && typeof response.fee !== 'undefined' ? response.fee : 0;

        return label + ': ' + formatCheckoutCurrency(fee) + '. Tổng tiền đã được cập nhật.';
    }

    function calculateCheckoutDeliveryFee(options) {
        var settings = $.extend({
            silent: false,
            button: null
        }, options || {});
        var $button = settings.button || $('.calculate_delivery');
        var vietnam_province_code = $('.province').val();
        var vietnam_ward_code = $('.wards').val();
        var _token = $('input[name="_token"]').val();

        if (vietnam_province_code == '' || vietnam_ward_code == '') {
            setCheckoutShippingResolved(false);
            syncCheckoutDeliveryTotals(0);
            renderCheckoutDeliveryNotice('Vui lòng chọn đầy đủ tỉnh/thành phố và xã/phường để hệ thống tự tính phí vận chuyển.', 'warning');

            if (!settings.silent) {
                showStorefrontStatus('Thiếu thông tin giao hàng',
                    'Vui lòng chọn đầy đủ tỉnh/thành phố và xã/phường để hệ thống tính phí vận chuyển.', 'warning');
            }

            return;
        }

        loadStorefrontJson({
            url: '{{ url('/calculate-fee') }}',
            method: 'POST',
            beforeSend: function() {
                setCheckoutShippingResolved(false);
                $button.prop('disabled', true);
                $button.data('originalHtml', $button.html());
                $button.html('<i class="fa fa-spinner fa-spin"></i> Đang tính phí');
            },
            data: {
                vietnam_province_code: vietnam_province_code,
                vietnam_ward_code: vietnam_ward_code,
                _token: _token
            },
            fallbackMessage: 'Không thể tính phí vận chuyển.',
            onSuccess: function(response) {
                var fee = response && typeof response.fee !== 'undefined' ? response.fee : 0;

                syncCheckoutDeliveryTotals(fee);
                setCheckoutShippingResolved(true);
                renderCheckoutDeliveryNotice(shippingFeeMessage(response), 'success');

                if (!settings.silent) {
                    showStorefrontStatus('Phí vận chuyển', shippingFeeMessage(response), 'success');
                }
            },
            onError: function(xhr) {
                var message = getAjaxErrorMessage(xhr, 'Không thể tính phí vận chuyển.');
                setCheckoutShippingResolved(false);
                renderCheckoutDeliveryNotice(message, 'error');

                if (!settings.silent) {
                    showStorefrontStatus('Chưa thể tính phí vận chuyển', message, 'error');
                }
            },
            complete: function() {
                $button.prop('disabled', false);
                $button.html($button.data('originalHtml') || '<i class="fa fa-refresh"></i> Tính lại phí vận chuyển');
                updateCheckoutSubmitState();
            }
        });
    }

    function initSelect2Interactions() {
        if (typeof $.fn.select2 === 'function') {
            $('.city, .province, .wards').select2({
                width: '100%',
                placeholder: '--- Chọn nếu có dữ liệu ---',
                allowClear: true,
                language: {
                    noResults: function() {
                        return "Không có dữ liệu địa chỉ để chọn.";
                    }
                }
            });
        }
    }

    $(document).ready(function() {
        initSelect2Interactions();
        setCheckoutShippingResolved(false);
        updateCheckoutSubmitState();

        $('.shipping_email, .shipping_name, .shipping_address, .shipping_phone').on('input change', function() {
            updateCheckoutSubmitState();
        });

        $('.send_order').click(function() {
            var $button = $(this);
            if ($button.data('submitting')) {
                return;
            }

            var originalHtml = $button.data('originalHtml') || $button.html();
            var shipping_email = $('.shipping_email').val();
            var shipping_name = $('.shipping_name').val();
            var shipping_address = $('.shipping_address').val();
            var shipping_phone = $('.shipping_phone').val();
            var shipping_notes = $('.shipping_notes').val();
            var shipping_method = $('.payment_select:checked').val() || 'cod';
            var vietnam_province_code = $('.province').val();
            var vietnam_ward_code = $('.wards').val();
            var _token = $('input[name="_token"]').val();

            clearCheckoutFieldErrors();

            if (shipping_email == '' || shipping_name == '' || shipping_address == '' || shipping_phone == '') {
                renderCheckoutOrderNotice('Vui lòng điền đầy đủ tên, email, số điện thoại và địa chỉ nhận hàng.', 'error');
                swal("Thiếu thông tin", "Vui lòng điền đầy đủ tên, email, số điện thoại và địa chỉ nhận hàng.", "error");
                return;
            }

            if (vietnam_province_code == '' || vietnam_ward_code == '') {
                renderCheckoutFieldErrors({
                    vietnam_province_code: vietnam_province_code == '' ? ['Vui lòng chọn tỉnh/thành phố giao hàng.'] : [],
                    vietnam_ward_code: vietnam_ward_code == '' ? ['Vui lòng chọn xã/phường/đặc khu giao hàng.'] : []
                });
                renderCheckoutOrderNotice('Vui lòng chọn tỉnh/thành phố và xã/phường/đặc khu giao hàng.', 'error');
                swal("Thiếu thông tin giao hàng", "Vui lòng chọn tỉnh/thành phố và xã/phường/đặc khu giao hàng.", "error");
                return;
            }

            if (!isCheckoutShippingResolved()) {
                renderCheckoutOrderNotice('Vui lòng chọn đầy đủ địa chỉ để hệ thống tính phí vận chuyển trước khi đặt hàng.', 'error');
                renderCheckoutDeliveryNotice('Phí vận chuyển chưa được tính. Vui lòng kiểm tra lại tỉnh/thành phố và xã/phường.', 'warning');
                swal("Chưa tính phí vận chuyển", "Vui lòng chọn đầy đủ địa chỉ để hệ thống tính phí vận chuyển trước khi đặt hàng.", "warning");
                return;
            }

            loadStorefrontJson({
                url: '{{ url('/confirm-order') }}',
                method: 'POST',
                beforeSend: function() {
                    $('#checkout_order_notice').html('');
                    $button.data('submitting', true);
                    $button.data('originalHtml', originalHtml);
                    $button.prop('disabled', true);
                    $button.html('<i class="fa fa-spinner fa-spin"></i> Đang xử lý...');
                },
                data: {
                    shipping_email: shipping_email,
                    shipping_name: shipping_name,
                    shipping_address: shipping_address,
                    shipping_phone: shipping_phone,
                    shipping_notes: shipping_notes,
                    shipping_method: shipping_method,
                    vietnam_province_code: vietnam_province_code,
                    vietnam_ward_code: vietnam_ward_code,
                    checkout_idempotency_key: $('#checkout_idempotency_key').val() || '',
                    _token: _token
                },
                onSuccess: function(data) {
                    var orderCode = (data && data.order_code) ? data.order_code : '';
                    location.href = "{{ url('/view-history-order') }}/" + orderCode + "?just_placed=1";
                },
                onError: function(xhr) {
                    $button.data('submitting', false);
                    $button.prop('disabled', false);
                    $button.html($button.data('originalHtml') || originalHtml);
                    var message = getAjaxErrorMessage(xhr, 'Không thể gửi đơn hàng lúc này.');
                    renderCheckoutFieldErrors(xhr && xhr.responseJSON ? xhr.responseJSON.errors : null);
                    renderCheckoutOrderNotice(message, 'error');
                    swal("Lỗi đặt hàng", message, "error");
                }
            });
        });

        $('.choose').on('change', function() {
            var action = $(this).attr('id');
            var ma_id = $(this).val();
            var _token = $('input[name="_token"]').val();
            var result = 'wards';

            setCheckoutShippingResolved(false);
            syncCheckoutDeliveryTotals(0);
            renderCheckoutDeliveryNotice('Vui lòng chọn xã/phường để hệ thống tự tính phí vận chuyển.', 'warning');

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
                    initSelect2Interactions();
                    updateCheckoutSubmitState();
                }
            });
        });

        $(document).on('change', '.wards', function() {
            calculateCheckoutDeliveryFee({ silent: true });
        });
    });
</script>
<script type="text/javascript">
    $(document).ready(function() {
        $('.calculate_delivery').click(function() {
            calculateCheckoutDeliveryFee({
                silent: false,
                button: $(this)
            });
        });
    });
</script>

<script>
    $(document).ready(function() {
        if (typeof $.fn.lightSlider === 'function' && !$("#imageGallery").is('[hidden]')) {
            var slider = $("#imageGallery").lightSlider({
                gallery: true,
                item: 1,
                loop: true,
                thumbItem: 4,
                slideMargin: 0,
                enableDrag: false,
                currentPagerPosition: 'left',
                onSliderLoad: function(el) {
                    if (typeof el.lightGallery === 'function') {
                        el.lightGallery({
                            selector: '#imageGallery .lslide'
                        });
                    }
                }
            });
        }

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
            var $addCartButtons = $('[data-pdx-add-cart]');
            var $buyNowButtons = $('[data-pdx-buy-now]');
            var $buttonLabel = $('.shopw-pdp-purchase-btn-label');
            var defaultLabel = $purchase.data('defaultLabel') || 'Thêm giỏ hàng';
            var nextLabel = label || defaultLabel;
            var currentValue = parseInt($quantityInput.val() || 1, 10);

            $quantityInput.attr('max', Math.max(1, availableStock));
            $quantityInput.attr('data-available-stock', availableStock);

            if (availableStock <= 0) {
                $quantityInput.val(1).prop('disabled', false);
                $addCartButtons.prop('disabled', false);
                $buyNowButtons.prop('disabled', true);
            } else {
                $quantityInput.prop('disabled', false);
                $quantityInput.val(Math.min(Math.max(1, currentValue || 1), availableStock));
                $addCartButtons.prop('disabled', false);
                $buyNowButtons.prop('disabled', false);
            }

            $buttonLabel.text(nextLabel);
        }

        const defaultPrice = $('.cart_product_price').val() || $('.cart_price_attribute').val();
        const defaultStock = parseInt($('.shopw-pdp-purchase').data('defaultStock') || $('.cart_product_qty').attr('max') || 0, 10);
        $('#price_after_click').hide();
        $('.cart_price_attribute').val(defaultPrice);
        syncProductDetailPurchaseState(defaultStock);

        $('.pro_attr_image_click').click(function() {

            var key_image = $(this).data('key_image');
            var newPrice = $(this).data('price_attribute');

            var attributeStock = parseInt($(this).data('qty_attribute') || 0, 10);
            var newIdAttribute = $(this).data('id_attribute');

            $('.cart_product_id_attribute').val(0);

            if ($(this).hasClass('active')) {
                $('#price_after_click').hide();
                $('#price_init').show();
                $('.cart_price_attribute').val(defaultPrice);
                syncProductDetailPurchaseState(defaultStock);
                $(this).removeClass('active');
                if (slider) slider.goToSlide(0);
            } else {

                $('.cart_product_id_attribute').val(newIdAttribute);
                syncProductDetailPurchaseState(attributeStock, 'Thêm giỏ hàng');

                $('#price_after_click').text('Giá ' + formatPrice(newPrice)).show();
                $('#price_init').hide();

                $('.pro_attr_image_click').removeClass('active');
                $(this).addClass('active');

                $('[class^="pro_attr_image_"]').removeClass('active');
                $(`.pro_attr_image_${key_image}`).addClass('active');

                if (slider) slider.goToSlide(key_image);
            }
        });
    });
</script>

{{-- 7. Chatbot --}}
<script>
    (function () {
        const widget = document.querySelector('[data-chatbot-widget]');

        if (!widget) {
            return;
        }

        const panel = widget.querySelector('[data-chatbot-panel]');
        const toggle = widget.querySelector('[data-chatbot-toggle]');
        const closeButton = widget.querySelector('[data-chatbot-close]');
        const form = widget.querySelector('[data-chatbot-form]');
        const input = widget.querySelector('[data-chatbot-input]');
        const submitButton = form ? form.querySelector('button[type="submit"]') : null;
        const messagesContainer = widget.querySelector('[data-chatbot-messages]');
        const quickActions = widget.querySelectorAll('[data-chatbot-prompt]');
        const launchers = document.querySelectorAll('[data-chatbot-launch]');

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        const fallbackErrorMessage = 'Hiện chưa thể kết nối tư vấn viên, vui lòng thử lại sau.';

        let loaded = false;
        let isSending = false;
        let conversationId = 0;
        let pollTimer = null;

        function setExpanded(isExpanded) {
            document.body.classList.toggle('shopw-chatbot-open', isExpanded);

            if (toggle) {
                toggle.setAttribute('aria-expanded', isExpanded ? 'true' : 'false');
            }

            launchers.forEach((launcher) => {
                launcher.setAttribute('role', 'button');
                launcher.setAttribute('aria-controls', 'shopw-chatbot-panel');
                launcher.setAttribute('aria-expanded', isExpanded ? 'true' : 'false');
                launcher.classList.toggle('is-open', isExpanded);
            });
        }

        function openPanel() {
            if (!panel) {
                return;
            }

            panel.removeAttribute('hidden');
            panel.hidden = false;
            panel.style.display = 'flex';
            panel.style.opacity = '1';
            panel.style.visibility = 'visible';
            panel.style.pointerEvents = 'auto';
            panel.setAttribute('aria-hidden', 'false');
            widget.classList.add('is-open');
            setExpanded(true);
        }

        function closePanel() {
            if (!panel) {
                return;
            }

            panel.setAttribute('hidden', 'hidden');
            panel.hidden = true;
            panel.style.removeProperty('display');
            panel.style.removeProperty('opacity');
            panel.style.removeProperty('visibility');
            panel.style.removeProperty('pointer-events');
            panel.setAttribute('aria-hidden', 'true');
            widget.classList.remove('is-open');
            setExpanded(false);
            stopPolling();
        }

        function renderProducts(products) {
            if (!Array.isArray(products) || products.length === 0) {
                return '';
            }

            return `
                <div class="shopw-chatbot-product-list">
                    ${products.map(product => `
                        <a class="shopw-chatbot-product" href="${escapeHtml(product.url)}">
                            <img src="${escapeHtml(product.image)}" alt="${escapeHtml(product.name)}">
                            <span class="shopw-chatbot-product-copy">
                                <strong>${escapeHtml(product.name)}</strong>
                                <span class="shopw-chatbot-product-price-row">
                                    ${product.original_price_formatted ? `<small>${escapeHtml(product.original_price_formatted)}</small>` : ''}
                                    <span>${escapeHtml(product.price_formatted || '')}</span>
                                </span>
                                ${product.sale_label ? `<em>${escapeHtml(product.sale_label)}</em>` : ''}
                            </span>
                        </a>
                    `).join('')}
                </div>
            `;
        }

        function renderActions(actions) {
            if (!Array.isArray(actions) || actions.length === 0) {
                return '';
            }

            return `
                <div class="shopw-chatbot-actions">
                    ${actions.map(action => `
                        <a href="${escapeHtml(action.url)}" class="shopw-chatbot-action-link">${escapeHtml(action.label)}</a>
                    `).join('')}
                </div>
            `;
        }

        function renderMessage(message) {
            const wrapper = document.createElement('article');
            wrapper.className = `shopw-chatbot-message shopw-chatbot-message-${message.sender}`;
            wrapper.setAttribute('data-message-id', String(message.id || ''));
            wrapper.innerHTML = `
                <div class="shopw-chatbot-bubble">
                    <p>${escapeHtml(message.message || '')}</p>
                    ${renderProducts(message.products)}
                    ${renderActions(message.actions)}
                </div>
            `;

            return wrapper;
        }

        function scrollToBottom() {
            if (messagesContainer) messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }

        function setMessages(messages) {
            if (!messagesContainer) {
                return;
            }

            messagesContainer.innerHTML = '';

            messages.forEach((message) => {
                messagesContainer.appendChild(renderMessage(message));
            });

            scrollToBottom();
        }

        function appendMessages(messages) {
            if (!Array.isArray(messages) || messages.length === 0) {
                return;
            }

            messages.forEach((message) => {
                messagesContainer.appendChild(renderMessage(message));
            });

            scrollToBottom();
        }

        function getLastMessageId() {
            if (!messagesContainer) {
                return 0;
            }

            const lastMessage = messagesContainer.querySelector('[data-message-id]:last-child');

            return lastMessage ? Number(lastMessage.getAttribute('data-message-id')) : 0;
        }

        function showTypingState() {
            if (!messagesContainer) {
                return;
            }

            const typing = document.createElement('article');
            typing.className = 'shopw-chatbot-message shopw-chatbot-message-bot';
            typing.setAttribute('data-chatbot-typing', 'true');
            typing.innerHTML = `
                <div class="shopw-chatbot-bubble">
                    <p>Đang soạn phản hồi...</p>
                </div>
            `;

            messagesContainer.appendChild(typing);
            scrollToBottom();
        }

        function clearTypingState() {
            if (!messagesContainer) {
                return;
            }

            const typing = messagesContainer.querySelector('[data-chatbot-typing="true"]');

            if (typing) {
                typing.remove();
            }
        }

        function appendErrorMessage(message) {
            if (!messagesContainer) {
                return;
            }

            messagesContainer.appendChild(renderMessage({
                sender: 'bot',
                message: message || fallbackErrorMessage,
                products: [],
                actions: []
            }));
            scrollToBottom();
        }

        function setSendingState(sending) {
            isSending = sending;
            widget.classList.toggle('is-sending', sending);

            if (input) {
                input.disabled = sending;
            }

            if (submitButton) {
                submitButton.disabled = sending;
                submitButton.setAttribute('aria-busy', sending ? 'true' : 'false');
                const submitLabel = submitButton.querySelector('span');
                if (submitLabel) {
                    submitLabel.textContent = sending ? 'Đang gửi...' : 'Gửi';
                }
            }
        }

        async function bootstrapConversation() {
            if (loaded) {
                return;
            }

            const response = await fetch('/chatbot/conversation', {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (!response.ok) {
                throw new Error('Chatbot conversation request failed');
            }

            const payload = await response.json();
            conversationId = Number(payload.data?.id || 0);
            setMessages(payload.data?.messages || []);
            loaded = true;
        }

        async function pollConversation() {
            if (!loaded || !conversationId || panel.hasAttribute('hidden')) {
                return;
            }

            const lastMessageId = getLastMessageId();
            const response = await fetch(`/chatbot/conversation?after_id=${lastMessageId}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (!response.ok) {
                return;
            }

            const payload = await response.json();
            appendMessages(payload.data?.messages || []);
        }

        function startPolling() {
            stopPolling();
            pollTimer = window.setInterval(pollConversation, 5000);
        }

        function stopPolling() {
            if (pollTimer) {
                window.clearInterval(pollTimer);
                pollTimer = null;
            }
        }

        async function sendMessage(message) {
            if (isSending || !message.trim()) {
                return;
            }

            setSendingState(true);
            showTypingState();

            try {
                const response = await fetch('/chatbot/messages', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        message: message.trim()
                    })
                });

                if (!response.ok) {
                    throw new Error('Chatbot message request failed');
                }

                const payload = await response.json();
                clearTypingState();
                conversationId = Number(payload.data?.conversation?.id || conversationId);
                setMessages(payload.data?.conversation?.messages || []);
            } catch (error) {
                clearTypingState();
                appendErrorMessage(fallbackErrorMessage);
            } finally {
                setSendingState(false);
            }
        }

        if (toggle) {
            toggle.addEventListener('mouseenter', function () {
                widget.classList.add('is-hovered');
            });

            toggle.addEventListener('mouseleave', function () {
                widget.classList.remove('is-hovered');
            });

            toggle.addEventListener('focus', function () {
                widget.classList.add('is-hovered');
            });

            toggle.addEventListener('blur', function () {
                widget.classList.remove('is-hovered');
            });

            toggle.addEventListener('click', async function (event) {
                event.preventDefault();
                event.stopPropagation();

                const isOpen = !panel.hasAttribute('hidden');

                if (isOpen) {
                    closePanel();
                    return;
                }

                openPanel();
                toggle.blur();
                try {
                    await bootstrapConversation();
                    startPolling();
                } catch (error) {
                    appendErrorMessage(fallbackErrorMessage);
                }
                if (input) input.focus();
            });
        }

        launchers.forEach((launcher) => {
            launcher.setAttribute('role', 'button');
            launcher.setAttribute('aria-controls', 'shopw-chatbot-panel');
            launcher.setAttribute('aria-expanded', 'false');

            launcher.addEventListener('click', async function (event) {
                event.preventDefault();
                event.stopPropagation();

                const isOpen = !panel.hasAttribute('hidden');

                if (isOpen) {
                    closePanel();
                    launcher.blur();
                    return;
                }

                openPanel();
                launcher.blur();
                try {
                    await bootstrapConversation();
                    startPolling();
                } catch (error) {
                    appendErrorMessage(fallbackErrorMessage);
                }

                if (input) input.focus();
            });
        });

        if (closeButton) {
            closeButton.addEventListener('click', function (event) {
                event.preventDefault();
                event.stopPropagation();
                closePanel();
            });
        }

        if (form) {
            form.addEventListener('submit', async function (event) {
                event.preventDefault();

                if (!input) {
                    return;
                }

                const value = input.value;

                if (!value.trim()) {
                    return;
                }

                input.value = '';
                await sendMessage(value);
                if (input) input.focus();
            });
        }

        quickActions.forEach((button) => {
            button.addEventListener('click', async function () {
                const prompt = button.getAttribute('data-chatbot-prompt') || '';

                if (!prompt) {
                    return;
                }

                if (panel.hasAttribute('hidden')) {
                    openPanel();
                    try {
                        await bootstrapConversation();
                        startPolling();
                    } catch (error) {
                        appendErrorMessage(fallbackErrorMessage);
                    }
                }

                await sendMessage(prompt);
            });
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && panel && !panel.hasAttribute('hidden')) {
                closePanel();
            }
        });

        document.addEventListener('click', function (event) {
            if (!panel || panel.hasAttribute('hidden')) {
                return;
            }

            if (!widget.contains(event.target) && !event.target.closest('[data-chatbot-launch]')) {
                closePanel();
            }
        });

        if (window.Echo && typeof window.Echo.channel === 'function') {
            window.Echo.channel('chatbot')
                .listen('.ChatMessageCreated', function (event) {
                    if (!event || Number(event.conversation_id || 0) !== conversationId) {
                        return;
                    }

                    appendMessages([event.message]);
                });
        }
    })();
</script>

{{-- 8. Legacy Main.js logic --}}
<script>
    (function () {
        var scrollButtons = document.querySelectorAll('[data-scroll-top]');

        if (!scrollButtons.length) {
            return;
        }

        var threshold = 300;

        function updateScrollTopButtons() {
            var shouldShow = window.pageYOffset > threshold || document.documentElement.scrollTop > threshold;

            scrollButtons.forEach(function (button) {
                button.hidden = !shouldShow;
                button.classList.toggle('is-visible', shouldShow);
                button.setAttribute('aria-hidden', shouldShow ? 'false' : 'true');
            });
        }

        scrollButtons.forEach(function (button) {
            button.addEventListener('click', function (event) {
                event.preventDefault();

                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            });
        });

        window.addEventListener('scroll', updateScrollTopButtons, { passive: true });
        window.addEventListener('resize', updateScrollTopButtons);
        updateScrollTopButtons();
    })();

    $(document).ready(function(){
        if (typeof $.fn.slider === 'function') {
            $('#sl2').slider();
        }
    });
</script>
