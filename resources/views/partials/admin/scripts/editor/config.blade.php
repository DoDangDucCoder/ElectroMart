<script>
    window.adminEditorOptions = {
        filebrowserImageBrowseUrl: 'laravel-filemanager?type=Images',
        filebrowserImageUploadUrl: 'laravel-filemanager/upload?type=Images&_token=',
        filebrowserBrowseUrl: 'laravel-filemanager?type=Files',
        filebrowserUploadUrl: 'laravel-filemanager/upload?type=Files&_token='
    };

    window.adminEditorUploadOptions = {
        filebrowserImageUploadUrl: "{{ url('uploads-ckeditor?_token=' . csrf_token()) }}",
        filebrowserBrowseUrl: "{{ url('file-browser?_token=' . csrf_token()) }}",
        filebrowserUploadMethod: 'form'
    };
</script>
