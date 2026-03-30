<script type="text/javascript">
    $(document).on('click', '.comment_duyet_btn', function() {
        var comment_status = $(this).data('comment_status');
        var comment_id = $(this).data('comment_id');
        var comment_product_id = $(this).attr('id');
        var alertText = comment_status == 0
            ? 'Thay đổi thành duyệt thành công'
            : 'Thay đổi thành không duyệt thành công';

        $.ajax({
            url: "{{ url('/allow-comment') }}",
            method: "POST",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                comment_status: comment_status,
                comment_id: comment_id,
                comment_product_id: comment_product_id
            },
            success: function() {
                location.reload();
                $('#notify_comment').html('<span class="text text-alert">' + alertText + '</span>');
            }
        });
    });

    $(document).on('click', '.btn-reply-comment', function() {
        var comment_id = $(this).data('comment_id');
        var comment = $('.reply_comment_' + comment_id).val();
        var comment_product_id = $(this).data('product_id');

        $.ajax({
            url: "{{ url('/reply-comment') }}",
            method: "POST",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                comment: comment,
                comment_id: comment_id,
                comment_product_id: comment_product_id
            },
            success: function() {
                $('.reply_comment_' + comment_id).val('');
                $('#notify_comment').html('<span class="text text-alert">Trả lời bình luận thành công</span>');
            }
        });
    });
</script>
