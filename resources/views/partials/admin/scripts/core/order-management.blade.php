<script type="text/javascript">
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
                alert('Cập nhật số lượng thành công');
                location.reload();
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
                    alert('Số lượng bán trong kho không đủ');
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
                alert('Thay đổi tình trạng đơn hàng thành công');
                location.reload();
            }
        });
    });
</script>
