<script type="text/javascript">
    window.fetch_delivery = function() {
        if (!$('#load_delivery').length) {
            return;
        }

        var _token = $('input[name="_token"]').val();

        $.ajax({
            url: '{{ url('/select-feeship') }}',
            method: 'POST',
            data: {
                _token: _token
            },
            success: function(data) {
                $('#load_delivery').html(data);
            }
        });
    };

    $(function() {
        if ($('#load_delivery').length) {
            fetch_delivery();
        }
    });

    $(document).on('blur', '.fee_feeship_edit', function() {
        var feeship_id = $(this).data('feeship_id');
        var fee_value = $(this).text();
        var _token = $('input[name="_token"]').val();

        $.ajax({
            url: '{{ url('/update-delivery') }}',
            method: 'POST',
            data: {
                feeship_id: feeship_id,
                fee_value: fee_value,
                _token: _token
            },
            success: function() {
                fetch_delivery();
            }
        });
    });
</script>
