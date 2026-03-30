<script type="text/javascript">
    $(function() {
        if (!$('#category_order').length) {
            return;
        }

        $('#category_order').sortable({
            placeholder: 'ui-state-highlight',
            update: function() {
                var page_id_array = [];
                var _token = $('input[name="_token"]').val();

                $('#category_order tr').each(function() {
                    page_id_array.push($(this).attr('id'));
                });

                $.ajax({
                    url: "{{ url('/arrange-category') }}",
                    method: "POST",
                    data: {
                        page_id_array: page_id_array,
                        _token: _token
                    },
                    success: function(data) {
                        alert(data);
                    }
                });
            }
        });
    });
</script>
