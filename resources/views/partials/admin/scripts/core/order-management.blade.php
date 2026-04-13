<script type="text/javascript">
    window.renderOrderManagementNotice = function(type, message) {
        var tone = type === 'error' ? 'danger' : 'success';
        $('#order_management_notice')
            .removeAttr('hidden')
            .removeClass('alert-info alert-danger alert-success')
            .addClass('alert alert-' + tone)
            .text(message);
    };

    window.resolveOrderManagementError = function(xhr, fallbackMessage) {
        if (xhr && xhr.responseJSON) {
            if (xhr.responseJSON.message) {
                return xhr.responseJSON.message;
            }

            if (xhr.responseJSON.errors) {
                var firstKey = Object.keys(xhr.responseJSON.errors)[0];
                if (firstKey && xhr.responseJSON.errors[firstKey] && xhr.responseJSON.errors[firstKey][0]) {
                    return xhr.responseJSON.errors[firstKey][0];
                }
            }
        }

        return fallbackMessage;
    };

    $(document).on('click', '.update_quantity_order', function() {
        var order_product_id = $(this).data('product_id');
        var order_qty = $('.order_qty_' + order_product_id).val();
        var order_code = $('.order_code').val();
        var _token = $('input[name="_token"]').val();

        $.ajax({
            url: '{{ url('/update-qty') }}',
            method: 'POST',
            data: {
                _token: _token,
                order_product_id: order_product_id,
                order_qty: order_qty,
                order_code: order_code
            },
            success: function() {
                renderOrderManagementNotice('success', 'Cập nhật số lượng thành công.');
                window.setTimeout(function() {
                    window.location.reload();
                }, 350);
            },
            error: function(xhr) {
                renderOrderManagementNotice('error', resolveOrderManagementError(xhr, 'Không thể cập nhật số lượng trên môi trường này.'));
            }
        });
    });

    $(document).on('change', '.order_details', function() {
        var order_status = $(this).val();
        var order_id = $(this).children(':selected').attr('id');
        var _token = $('input[name="_token"]').val();
        var quantity = [];
        var order_product_id = [];
        var invalidCount = 0;

        $("input[name='product_sales_quantity']").each(function() {
            quantity.push($(this).val());
        });

        $("input[name='order_product_id']").each(function() {
            order_product_id.push($(this).val());
        });

        for (var i = 0; i < order_product_id.length; i++) {
            var order_qty = $('.order_qty_' + order_product_id[i]).val();
            var order_qty_storage = $('.order_qty_storage_' + order_product_id[i]).val();

            if (parseInt(order_qty) > parseInt(order_qty_storage)) {
                invalidCount++;

                if (invalidCount === 1) {
                    renderOrderManagementNotice('error', 'Số lượng bán trong kho không đủ.');
                }

                $('.color_qty_' + order_product_id[i]).css('background', '#000');
            }
        }

        if (invalidCount > 0) {
            return;
        }

        $.ajax({
            url: '{{ url('/update-order-qty') }}',
            method: 'POST',
            data: {
                _token: _token,
                order_status: order_status,
                order_id: order_id,
                quantity: quantity,
                order_product_id: order_product_id
            },
            success: function() {
                renderOrderManagementNotice('success', 'Thay đổi tình trạng đơn hàng thành công.');
                window.setTimeout(function() {
                    window.location.reload();
                }, 350);
            },
            error: function(xhr) {
                renderOrderManagementNotice('error', resolveOrderManagementError(xhr, 'Không thể cập nhật trạng thái đơn hàng trên môi trường này.'));
            }
        });
    });
</script>
