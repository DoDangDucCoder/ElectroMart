<script type="text/javascript">
    window.renderGalleryNotice = function(type, message) {
        var tone = type === 'error' ? 'danger' : 'success';
        $('#gallery_notify').html('<span class="text text-' + tone + '">' + message + '</span>');
    };

    window.resolveGalleryError = function(xhr, fallbackMessage) {
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

    window.load_gallery = function() {
        if (!$('#gallery_load').length || !$('.pro_id').length) {
            return;
        }

        var pro_id = $('.pro_id').val();
        var _token = $('input[name="_token"]').val();

        $.ajax({
            url: "{{ url('/select-gallery') }}",
            method: "POST",
            data: {
                pro_id: pro_id,
                _token: _token
            },
            success: function(data) {
                $('#gallery_load').html(data);
            },
            error: function(xhr) {
                renderGalleryNotice('error', resolveGalleryError(xhr, 'Không thể tải thư viện ảnh trên môi trường này.'));
            }
        });
    };

    $(function() {
        if ($('#gallery_load').length && $('.pro_id').length) {
            load_gallery();
        }
    });

    $(document).on('change', '#file', function() {
        var error = '';
        var files = $('#file')[0].files;

        if (files.length > 5) {
            error += '<p>Bạn chọn tối đa chỉ được 5 ảnh</p>';
        } else if (files.length === 0) {
            error += '<p>Bạn không được bỏ trống ảnh</p>';
        } else {
            for (var i = 0; i < files.length; i++) {
                if (files[i].size > 2000000) {
                    error += '<p>File ảnh không được lớn hơn 2MB</p>';
                    break;
                }
            }
        }

        if (error !== '') {
            $('#file').val('');
            $('#error_gallery').html('<span class="text-danger">' + error + '</span>');
            renderGalleryNotice('error', 'Dữ liệu ảnh tải lên chưa hợp lệ.');
            return false;
        }

        $('#error_gallery').html('');
    });

    $(document).on('blur', '.edit_gal_name', function() {
        var gal_id = $(this).data('gal_id');
        var gal_text = $(this).text();
        var _token = $('input[name="_token"]').val();

        $.ajax({
            url: "{{ url('/update-gallery-name') }}",
            method: "POST",
            data: {
                gal_id: gal_id,
                gal_text: gal_text,
                _token: _token
            },
            success: function() {
                load_gallery();
                renderGalleryNotice('success', 'Cập nhật tên hình ảnh thành công.');
            },
            error: function(xhr) {
                renderGalleryNotice('error', resolveGalleryError(xhr, 'Không thể cập nhật tên hình ảnh trên môi trường này.'));
            }
        });
    });

    $(document).on('click', '.delete-gallery', function() {
        var gal_id = $(this).data('gal_id');
        var _token = $('input[name="_token"]').val();

        if (!confirm('Bạn muốn xóa hình ảnh này không?')) {
            return;
        }

        $.ajax({
            url: "{{ url('/delete-gallery') }}",
            method: "POST",
            data: {
                gal_id: gal_id,
                _token: _token
            },
            success: function() {
                load_gallery();
                renderGalleryNotice('success', 'Xóa hình ảnh thành công.');
            },
            error: function(xhr) {
                renderGalleryNotice('error', resolveGalleryError(xhr, 'Không thể xóa hình ảnh trên môi trường này.'));
            }
        });
    });

    $(document).on('change', '.file_image', function() {
        var gal_id = $(this).data('gal_id');
        var fileInput = document.getElementById('file-' + gal_id);
        var form_data = new FormData();

        if (!fileInput || !fileInput.files || !fileInput.files[0]) {
            renderGalleryNotice('error', 'Bạn chưa chọn hình ảnh để cập nhật gallery.');
            return;
        }

        form_data.append('file', fileInput.files[0]);
        form_data.append('gal_id', gal_id);

        $.ajax({
            url: "{{ url('/update-gallery') }}",
            method: "POST",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: form_data,
            contentType: false,
            cache: false,
            processData: false,
            success: function() {
                load_gallery();
                renderGalleryNotice('success', 'Cập nhật hình ảnh thành công.');
            },
            error: function(xhr) {
                renderGalleryNotice('error', resolveGalleryError(xhr, 'Không thể cập nhật hình ảnh trên môi trường này.'));
            }
        });
    });
</script>
