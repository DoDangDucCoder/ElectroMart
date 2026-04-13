<script type="text/javascript">
    window.renderCommentNotice = function(type, message) {
        var tone = type === 'error' ? 'danger' : 'success';
        $('#notify_comment').html('<span class="text text-' + tone + '">' + message + '</span>');
    };

    window.resolveCommentError = function(xhr, fallbackMessage) {
        if (xhr && xhr.responseJSON && xhr.responseJSON.message) {
            return xhr.responseJSON.message;
        }

        return fallbackMessage;
    };

    window.persistCommentNotice = function(type, message) {
        if (window.sessionStorage) {
            window.sessionStorage.setItem('admin_comment_notice', JSON.stringify({
                type: type,
                message: message
            }));
        }
    };

    $(function() {
        if (!window.sessionStorage) {
            return;
        }

        var cachedNotice = window.sessionStorage.getItem('admin_comment_notice');

        if (!cachedNotice) {
            return;
        }

        try {
            var payload = JSON.parse(cachedNotice);
            renderCommentNotice(payload.type || 'success', payload.message || '');
        } catch (e) {
        }

        window.sessionStorage.removeItem('admin_comment_notice');
    });

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
                persistCommentNotice('success', alertText);
                location.reload();
            },
            error: function(xhr) {
                renderCommentNotice('error', resolveCommentError(xhr, 'Không thể cập nhật trạng thái bình luận trên môi trường này.'));
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
                persistCommentNotice('success', 'Trả lời bình luận thành công');
                location.reload();
            },
            error: function(xhr) {
                renderCommentNotice('error', resolveCommentError(xhr, 'Không thể trả lời bình luận trên môi trường này.'));
            }
        });
    });
</script>
