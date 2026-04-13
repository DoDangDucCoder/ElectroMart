<script type="text/javascript">
    window.renderSiteInfoNotice = function(type, message) {
        var tone = type === 'error' ? 'danger' : 'success';
        $('#site_info_notice')
            .removeAttr('hidden')
            .removeClass('alert-info alert-danger alert-success')
            .addClass('alert alert-' + tone)
            .text(message);
    };

    window.resolveSiteInfoError = function(xhr, fallbackMessage) {
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

    window.delete_icons = function(id) {
        $.ajax({
            url: "{{ url('/delete-icons') }}",
            method: "DELETE",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                id: id
            },
            success: function() {
                window.list_nut();
                window.list_doitac();
                renderSiteInfoNotice('success', 'Đã xóa mục hiển thị thành công.');
            },
            error: function(xhr) {
                renderSiteInfoNotice('error', resolveSiteInfoError(xhr, 'Không thể xóa mục hiển thị trên môi trường này.'));
            }
        });
    };

    window.list_nut = function() {
        if (!$('#list_nut').length) {
            return;
        }

        $.ajax({
            url: "{{ url('/list-nut') }}",
            method: "GET",
            success: function(data) {
                $('#list_nut').html(data);
            },
            error: function(xhr) {
                renderSiteInfoNotice('error', resolveSiteInfoError(xhr, 'Không thể tải danh sách nút mạng xã hội trên môi trường này.'));
            }
        });
    };

    window.list_doitac = function() {
        if (!$('#list_doitac').length) {
            return;
        }

        $.ajax({
            url: "{{ url('/list-doitac') }}",
            method: "GET",
            success: function(data) {
                $('#list_doitac').html(data);
            },
            error: function(xhr) {
                renderSiteInfoNotice('error', resolveSiteInfoError(xhr, 'Không thể tải danh sách đối tác trên môi trường này.'));
            }
        });
    };

    $(function() {
        if ($('#list_nut').length) {
            list_nut();
        }

        if ($('#list_doitac').length) {
            list_doitac();
        }
    });

    $(document).on('click', '.add-nut', function() {
        var imageInput = $('#image_nut')[0];
        var form_data = new FormData();

        if (!imageInput || !imageInput.files || !imageInput.files[0]) {
            renderSiteInfoNotice('error', 'Vui lòng chọn hình ảnh cho nút mạng xã hội.');
            return;
        }

        form_data.append('file', imageInput.files[0]);
        form_data.append('name', $('#name').val());
        form_data.append('link', $('#link').val());

        $.ajax({
            url: "{{ url('/add-nut') }}",
            method: "POST",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            contentType: false,
            cache: false,
            processData: false,
            data: form_data,
            success: function() {
                renderSiteInfoNotice('success', 'Thêm nút thành công.');
                list_nut();
                $('#name').val('');
                $('#link').val('');
                $('#image_nut').val('');
            },
            error: function(xhr) {
                renderSiteInfoNotice('error', resolveSiteInfoError(xhr, 'Không thể thêm nút trên môi trường này.'));
            }
        });
    });

    $(document).on('click', '.add-doitac', function() {
        var imageInput = $('#image_doitac')[0];
        var form_data = new FormData();

        if (!imageInput || !imageInput.files || !imageInput.files[0]) {
            renderSiteInfoNotice('error', 'Vui lòng chọn hình ảnh cho đối tác hiển thị.');
            return;
        }

        form_data.append('file', imageInput.files[0]);
        form_data.append('name', $('#name_doitac').val());
        form_data.append('link', $('#link_doitac').val());

        $.ajax({
            url: "{{ url('/add-doitac') }}",
            method: "POST",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            contentType: false,
            cache: false,
            processData: false,
            data: form_data,
            success: function() {
                renderSiteInfoNotice('success', 'Thêm đối tác thành công.');
                list_doitac();
                $('#name_doitac').val('');
                $('#link_doitac').val('');
                $('#image_doitac').val('');
            },
            error: function(xhr) {
                renderSiteInfoNotice('error', resolveSiteInfoError(xhr, 'Không thể thêm đối tác trên môi trường này.'));
            }
        });
    });
</script>
