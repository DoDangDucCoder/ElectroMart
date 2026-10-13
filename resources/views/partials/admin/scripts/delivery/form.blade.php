<script type="text/javascript">
    $(document).on('click', '.add_delivery', function() {
        var province_code = $('.province').val();
        var ward_code = $('.wards').val();
        var fee_ship = $('.fee_ship').val();
        var _token = $('input[name="_token"]').val();

        $.ajax({
            url: '{{ url('/insert-delivery') }}',
            method: 'POST',
            data: {
                province_code: province_code,
                ward_code: ward_code,
                _token: _token,
                fee_ship: fee_ship
            },
            success: function() {
                $('.province').val('');
                $('#wards').html('<option value="">Để trống nếu dùng phí theo tỉnh hoặc mặc định</option>');
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

        $.ajax({
            url: '{{ url('/select-delivery') }}',
            method: 'POST',
            data: {
                action: action,
                ma_id: ma_id,
                _token: _token
            },
            success: function(data) {
                $('#wards').html(data);
            },
            error: function(xhr) {
                $('#wards').html('<option value="">Để trống nếu dùng phí theo tỉnh hoặc mặc định</option>');
                renderDeliveryNotice('error', resolveDeliveryError(xhr, 'Không thể tải danh sách địa giới trên môi trường này.'));
            }
        });
    });
</script>
