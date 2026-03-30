<script type="text/javascript">
    $(function() {
        if (typeof CKEDITOR === 'undefined') {
            return;
        }

        if ($('#my-editor').length) {
            CKEDITOR.replace('my-editor', window.adminEditorOptions);
        }

        ['ckeditor', 'id4'].forEach(function(editorId) {
            if ($('#' + editorId).length) {
                CKEDITOR.replace(editorId);
            }
        });

        ['ckeditor1', 'ckeditor2', 'ckeditor3'].forEach(function(editorId) {
            if ($('#' + editorId).length) {
                CKEDITOR.replace(editorId, window.adminEditorUploadOptions);
            }
        });
    });
</script>
