<script type="text/javascript">
    $(function() {
        var $categoryOrder = $('#category_order');

        if (!$categoryOrder.length) {
            return;
        }

        function renderCategorySortNotice(message, type) {
            var $target = $('#category_sort_notice');

            if (!$target.length) {
                return;
            }

            var alertClass = type === 'error' ? 'alert-danger' : 'alert-success';
            $target.html('<div class="alert ' + alertClass + '">' + message + '</div>');
        }

        if (String($categoryOrder.data('categorySortEnabled')) !== 'true') {
            return;
        }

        $categoryOrder.sortable({
            placeholder: 'ui-state-highlight',
            update: function() {
                var page_id_array = [];
                var _token = $('input[name="_token"]').val();

                $categoryOrder.find('tr').each(function() {
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
                        renderCategorySortNotice(data || 'Đã cập nhật thứ tự danh mục.', 'success');
                    },
                    error: function(xhr) {
                        var message = 'Không thể cập nhật thứ tự danh mục lúc này.';

                        if (xhr && xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        }

                        renderCategorySortNotice(message, 'error');
                    }
                });
            }
        });
    });
</script>
