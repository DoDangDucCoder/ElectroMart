<script type="text/javascript">
    $(document).on('click', '.add_delivery', function() {
        var city = $('.city').val();
        var province = $('.province').val();
        var wards = $('.wards').val();
        var fee_ship = $('.fee_ship').val();
        var _token = $('input[name="_token"]').val();

        $.ajax({
            url: '{{ url('/insert-delivery') }}',
            method: 'POST',
            data: {
                city: city,
                province: province,
                _token: _token,
                wards: wards,
                fee_ship: fee_ship
            },
            success: function() {
                $('.city').val('');
                $('.province').val('');
                $('#province').html('<option value="">--Chọn quận huyện--</option>');
                $('#wards').html('<option value="">--Chọn xã phường--</option>');
                $('.fee_ship').val('');
                fetch_delivery();
                renderDeliveryNotice('success', 'Đã lưu phí vận chuyển cho khu vực đã chọn.');
            },
            error: function(xhr) {
                renderDeliveryNotice('error', resolveDeliveryError(xhr, 'Không thể lưu phí vận chuyển trên môi trường này.'));
            }
        });
    });

    $(document).on('change', '.choose', function() {
        var action = $(this).attr('id');
        var ma_id = $(this).val();
        var _token = $('input[name="_token"]').val();
        var result = action === 'city' ? 'province' : 'wards';

        $.ajax({
            url: '{{ url('/select-delivery') }}',
            method: 'POST',
            data: {
                action: action,
                ma_id: ma_id,
                _token: _token
            },
            success: function(data) {
                $('#' + result).html(data);
            },
            error: function(xhr) {
                $('#' + result).html(
                    result === 'province'
                        ? '<option value="">--Chọn quận huyện--</option>'
                        : '<option value="">--Chọn xã phường--</option>'
                );
                renderDeliveryNotice('error', resolveDeliveryError(xhr, 'Không thể tải danh sách địa giới trên môi trường này.'));
            }
        });
    });
</script>
