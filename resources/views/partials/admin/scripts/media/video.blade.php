<script type="text/javascript">
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
            }
        });
    };

    $(function() {
        if ($('#video_load').length) {
            load_video();
        }
    });

    $(document).on('click', '#btn-add-video', function() {
        var fileInput = document.getElementById('file_img_video');
        var form_data = new FormData();

        form_data.append('file', fileInput ? fileInput.files[0] : null);
        form_data.append('video_title', $('.video_title').val());
        form_data.append('video_slug', $('.video_slug').val());
        form_data.append('video_desc', $('.video_desc').val());
        form_data.append('video_link', $('.video_link').val());

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
            success: function() {
                load_video();
                $('#notify').html('<span class="text text-success">Đã thêm video thành công</span>');
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
                $('#notify').html('<span class="text text-success">Xóa video thành công</span>');
            }
        });
    });

    $(document).on('blur', '.video_edit', function() {
        var video_type = $(this).data('video_type');
        var video_id = $(this).data('video_id');
        var video_edit = $('#' + video_type + '_' + video_id).text();

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
                $('#notify').html('<span class="text text-success">Cập nhật video thành công</span>');
            }
        });
    });

    $(document).on('change', '.file_img_video', function() {
        var video_id = $(this).data('video_id');
        var fileInput = document.getElementById('file-video-' + video_id);
        var form_data = new FormData();

        form_data.append('file', fileInput ? fileInput.files[0] : null);
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
                $('#notify').html('<span class="text text-success">Cập nhật hình ảnh video thành công</span>');
            }
        });
    });
</script>
