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
                $('.wards').val('');
                $('.fee_ship').val('');
                fetch_delivery();
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
            }
        });
    });
</script>
