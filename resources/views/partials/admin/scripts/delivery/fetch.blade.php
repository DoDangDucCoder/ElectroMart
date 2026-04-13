<script type="text/javascript">
    window.renderDeliveryNotice = function(type, message) {
        var tone = type === 'error' ? 'danger' : 'success';
        $('#delivery_notify').html('<span class="text text-' + tone + '">' + message + '</span>');
    };

    window.resolveDeliveryError = function(xhr, fallbackMessage) {
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
            },
            error: function(xhr) {
                renderDeliveryNotice('error', resolveDeliveryError(xhr, 'Không thể tải cấu hình phí vận chuyển trên môi trường này.'));
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
                renderDeliveryNotice('success', 'Đã cập nhật phí vận chuyển.');
            },
            error: function(xhr) {
                renderDeliveryNotice('error', resolveDeliveryError(xhr, 'Không thể cập nhật phí vận chuyển trên môi trường này.'));
            }
        });
    });
</script>
