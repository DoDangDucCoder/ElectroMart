<script type="text/javascript">
    window.renderVideoNotice = function(type, message) {
        var tone = type === 'error' ? 'danger' : 'success';
        $('#notify').html('<span class="text text-' + tone + '">' + message + '</span>');
    };

    window.setVideoBusyState = function($trigger, isBusy, busyHtml) {
        if (!$trigger || !$trigger.length) {
            return;
        }

        if (isBusy) {
            $trigger.data('originalHtml', $trigger.html());
            $trigger.prop('disabled', true).html(busyHtml);
            return;
        }

        $trigger.prop('disabled', false).html($trigger.data('originalHtml') || $trigger.html());
    };

    window.resolveVideoError = function(xhr, fallbackMessage) {
        if (xhr && xhr.responseJSON && xhr.responseJSON.message) {
            return xhr.responseJSON.message;
        }

        return fallbackMessage;
    };

    window.load_video = function() {
        if (!$('#video_load').length) {
            return;
        }

        $.ajax({
            url: "{{ url('/select-video') }}",
            method: "POST",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(data) {
                $('#video_load').html(data);
            },
            error: function(xhr) {
                renderVideoNotice('error', resolveVideoError(xhr, 'Không thể tải danh sách video trên môi trường này.'));
            }
        });
    };

    $(function() {
        if ($('#video_load').length) {
            load_video();
        }
    });

    $(document).on('click', '#btn-add-video', function() {
        var $button = $(this);
        var fileInput = document.getElementById('file_img_video');
        var form_data = new FormData();

        form_data.append('video_title', $.trim($('.video_title').val()));
        form_data.append('video_slug', $.trim($('.video_slug').val()));
        form_data.append('video_desc', $.trim($('.video_desc').val()));
        form_data.append('video_link', $.trim($('.video_link').val()));

        if (fileInput && fileInput.files && fileInput.files[0]) {
            form_data.append('file', fileInput.files[0]);
        }

        $.ajax({
            url: "{{ url('/insert-video') }}",
            method: "POST",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: form_data,
            contentType: false,
            cache: false,
            processData: false,
            beforeSend: function() {
                setVideoBusyState($button, true, '<i class="fa fa-spinner fa-spin"></i> Đang thêm video');
            },
            success: function() {
                $('.g-admin-video-form').trigger('reset');
                load_video();
                renderVideoNotice('success', 'Đã thêm video thành công');
            },
            error: function(xhr) {
                renderVideoNotice('error', resolveVideoError(xhr, 'Không thể thêm video trên môi trường này.'));
            },
            complete: function() {
                setVideoBusyState($button, false);
            }
        });
    });

    $(document).on('click', '.btn-delete-video', function() {
        var video_id = $(this).data('video_id');

        if (!confirm('Bạn muốn xóa video này không?')) {
            return;
        }

        $.ajax({
            url: "{{ url('/delete-video') }}",
            method: "POST",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                video_id: video_id
            },
            success: function() {
                load_video();
                renderVideoNotice('success', 'Xóa video thành công');
            },
            error: function(xhr) {
                renderVideoNotice('error', resolveVideoError(xhr, 'Không thể xóa video trên môi trường này.'));
            }
        });
    });

    $(document).on('focus', '.video_edit', function() {
        $(this).data('videoOriginalValue', $.trim($(this).text()));
    });

    $(document).on('blur', '.video_edit', function() {
        var $field = $(this);
        var video_type = $field.data('video_type');
        var video_id = $field.data('video_id');
        var video_edit = $.trim($('#' + video_type + '_' + video_id).text());
        var originalValue = $.trim($field.data('videoOriginalValue') || '');

        if (video_edit === originalValue) {
            return;
        }

        $.ajax({
            url: "{{ url('/update-video') }}",
            method: "POST",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                video_check: video_type,
                video_edit: video_edit,
                video_id: video_id
            },
            success: function() {
                load_video();
                renderVideoNotice('success', 'Cập nhật video thành công');
            },
            error: function(xhr) {
                renderVideoNotice('error', resolveVideoError(xhr, 'Không thể cập nhật video trên môi trường này.'));
            }
        });
    });

    $(document).on('change', '.file_img_video', function() {
        var video_id = $(this).data('video_id');
        var fileInput = document.getElementById('file-video-' + video_id);
        var form_data = new FormData();

        if (!fileInput || !fileInput.files || !fileInput.files[0]) {
            renderVideoNotice('error', 'Bạn chưa chọn hình ảnh video để cập nhật.');
            return;
        }

        form_data.append('file', fileInput.files[0]);
        form_data.append('video_id', video_id);

        $.ajax({
            url: "{{ url('/update-video-image') }}",
            method: "POST",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: form_data,
            contentType: false,
            cache: false,
            processData: false,
            success: function() {
                load_video();
                renderVideoNotice('success', 'Cập nhật hình ảnh video thành công');
            },
            error: function(xhr) {
                renderVideoNotice('error', resolveVideoError(xhr, 'Không thể cập nhật hình ảnh video trên môi trường này.'));
            }
        });
    });
</script>
