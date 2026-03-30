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
            name: nameEl ? nameEl.value : '',
            price: priceEl ? priceEl.value : '',
            image: imageEl ? (imageEl.value || imageEl.src || '') : '',
            url: urlEl ? (urlEl.href || urlEl.value || '') : ''
        };
    }

    function normalizeStorefrontImage(image) {
        if (!image || image.indexOf('frontend/images/no-image.png') !== -1) {
            return "{{ asset('uploads/product/no-image.svg') }}";
        }

        return image;
    }

    function renderCompareRow(item) {
        var image = normalizeStorefrontImage(item.image);

        return `
            <tr id="row_compare${item.id}">
                <td>${escapeHtml(item.name)}</td>
                <td>${escapeHtml(item.price)}</td>
                <td><img width="200px" src="${escapeHtml(image)}"></td>
                <td>Đã sẵn sàng để so sánh chi tiết</td>
                <td><a href="${escapeHtml(item.url)}">Xem sản phẩm</a></td>
                <td><a style="cursor:pointer" onclick="delete_compare(${item.id})">Bỏ khỏi danh sách</a></td>
            </tr>
        `;
    }

    function renderViewedRow(item, ctaText) {
        return `
            <div class="row" style="margin:10px 0">
                <div class="col-md-4"><img width="100%" src="${escapeHtml(item.image)}"></div>
                <div class="col-md-8 info_wishlist">
                    <p>${escapeHtml(item.name)}</p>
                    <p style="color:#FE980F">${escapeHtml(item.price)}</p>
                    <a href="${escapeHtml(item.url)}">${escapeHtml(ctaText)}</a>
                </div>
            </div>
        `;
    }

    function renderWishlistRow(item) {
        return `
            <div class="row" id="wishlist_item${item.id}" style="margin:10px 0">
                <div class="col-md-4"><img width="100%" src="${escapeHtml(item.image)}"></div>
                <div class="col-md-8 info_wishlist">
                    <p>${escapeHtml(item.name)}</p>
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

        var item = getProductSnapshotFromInputs(product_id, 'wishlist');
        var data = readStorefrontStorage('compare');
        var exists = data.some(function(entry) {
            return String(entry.id) === String(product_id);
        });

        if (!exists && data.length <= 3) {
            data.push(item);
            writeStorefrontStorage('compare', data);
            sosanh();
        }

        showCompareModal();
    }

    function openCompareDetail() {
        var data = readStorefrontStorage('compare');
        var emptyMessage = 'Vui lòng thêm sản phẩm vào danh sách so sánh trước.';

        if (!data.length) {
            showStorefrontStatus('Chưa có sản phẩm so sánh', emptyMessage, 'info');
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
        if (!rowViewed) {
            return;
        }

        rowViewed.innerHTML = '';

        var data = readStorefrontStorage('viewed').slice().reverse();

        if (!data.length) {
            rowViewed.style.overflow = '';
            rowViewed.style.height = '';
            rowViewed.innerHTML = renderSidebarEmptyState({
                title: 'Chưa có sản phẩm đã xem',
                message: 'Các sản phẩm bạn vừa xem sẽ xuất hiện tại đây.',
                icon: 'fa fa-clock-o'
            });
            return;
        }

        rowViewed.style.overflow = 'scroll';
        rowViewed.style.height = '500px';

        data.forEach(function(item) {
            $('#row_viewed').append(renderViewedRow(item, 'Xem sản phẩm'));
        });
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

        rowWishlist.style.overflow = 'scroll';
        rowWishlist.style.height = '500px';

        data.forEach(function(item) {
            $('#row_wishlist').append(renderWishlistRow(item));
        });
    }
    view();

    function add_wistlist(clicked_id) {
        var item = getProductSnapshotFromInputs(clicked_id, 'wishlist');
        var data = readStorefrontStorage('data');
        var exists = data.some(function(entry) {
            return String(entry.id) === String(clicked_id);
        });

        if (exists) {
            data = data.filter(function(entry) {
                return String(entry.id) !== String(clicked_id);
            });
        } else {
            data.push(item);
        }

        writeStorefrontStorage('data', data);
        view();
    }
</script>

