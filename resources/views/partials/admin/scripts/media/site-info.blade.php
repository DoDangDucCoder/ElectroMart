<script type="text/javascript">
    window.delete_icons = function(id) {
        $.ajax({
            url: "{{ url('/delete-icons') }}",
            method: "GET",
            data: {
                id: id
            },
            success: function() {
                window.list_nut();
                window.list_doitac();
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

        form_data.append('file', imageInput ? imageInput.files[0] : null);
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
                alert('Thêm nút thành công');
                list_nut();
                $('#name').val('');
                $('#link').val('');
            }
        });
    });

    $(document).on('click', '.add-doitac', function() {
        var imageInput = $('#image_doitac')[0];
        var form_data = new FormData();

        form_data.append('file', imageInput ? imageInput.files[0] : null);
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
                alert('Thêm đối tác thành công');
                list_doitac();
            }
        });
    });
</script>
