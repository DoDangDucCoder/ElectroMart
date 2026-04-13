<script type="text/javascript">
    window.renderDocumentNotice = function(type, message) {
        var tone = type === 'error' ? 'danger' : 'success';
        $('#document_notice')
            .removeAttr('hidden')
            .removeClass('alert-info alert-danger alert-success')
            .addClass('alert alert-' + tone)
            .text(message);
    };

    window.resolveDocumentError = function(xhr, fallbackMessage) {
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

    $(function() {
        if ($.fn.simpleMoneyFormat && $('.price_format').length) {
            $('.price_format').simpleMoneyFormat();
        }
    });

    $(document).on('click', '.btn-delete-document', function() {
        var $button = $(this);
        var product_id = $button.data('document_id');
        var _token = $('input[name="_token"]').val();

        $.ajax({
            url: "{{ url('/delete-document') }}",
            method: "POST",
            data: {
                _token: _token,
                product_id: product_id
            },
            success: function() {
                renderDocumentNotice('success', 'Xóa file thành công.');
                var $card = $button.closest('[data-document-card]');
                if ($card.length) {
                    $card.remove();
                }

                $('[data-document-empty-state]').removeAttr('hidden');
            },
            error: function(xhr) {
                renderDocumentNotice('error', resolveDocumentError(xhr, 'Không thể xóa tài liệu trên môi trường này.'));
            }
        });
    });
</script>
