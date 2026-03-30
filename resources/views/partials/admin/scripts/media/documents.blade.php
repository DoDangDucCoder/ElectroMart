<script type="text/javascript">
    $(function() {
        if ($.fn.simpleMoneyFormat && $('.price_format').length) {
            $('.price_format').simpleMoneyFormat();
        }
    });

    $(document).on('click', '.btn-delete-document', function() {
        var product_id = $(this).data('document_id');
        var _token = $('input[name="_token"]').val();

        $.ajax({
            url: "{{ url('/delete-document') }}",
            method: "POST",
            data: {
                _token: _token,
                product_id: product_id
            },
            success: function() {
                alert('Xóa file thành công');
                location.reload();
            }
        });
    });
</script>
