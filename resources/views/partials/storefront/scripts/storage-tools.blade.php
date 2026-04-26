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
        var price = item.price
            ? `<p class="shopw-cart-viewed-price">${escapeHtml(item.price)}</p>`
            : '';

        return `
            <article class="shopw-cart-viewed-card">
                <a class="shopw-cart-viewed-media" href="${escapeHtml(url)}">
                    <img src="${escapeHtml(image)}" alt="${escapeHtml(item.name)}">
                </a>
                <div class="shopw-cart-viewed-copy">
                    <h3><a href="${escapeHtml(url)}">${escapeHtml(normalizeStorefrontLabel(item.name))}</a></h3>
                    ${price}
                    <a class="shopw-cart-viewed-link" href="${escapeHtml(url)}">${escapeHtml(ctaText)}</a>
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

        window.location.href = "{{ url('/so-sanh') }}" + '?ids=' + encodeURIComponent(ids);
    }

    function viewed() {
        var rowViewed = document.getElementById('row_viewed');
        var cartViewedGrid = document.getElementById('shopw_cart_viewed_grid');

        if (!rowViewed && !cartViewedGrid) {
            return;
        }

        var data = readStorefrontStorage('viewed').slice().reverse();

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
    }

    function add_wistlist(clicked_id) {
        return add_wishlist(clicked_id);
    }
</script>
