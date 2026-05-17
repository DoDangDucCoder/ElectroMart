<script type="text/javascript">
    $(function() {
        if ($.fn.DataTable && $('#myTable').length) {
            var $table = $('#myTable');
            var columnCount = $table.find('thead tr:first th').length;
            var canUseDataTable = columnCount > 0;

            $table.find('tbody tr').each(function() {
                var $cells = $(this).children('td, th');

                if ($cells.filter('[colspan], [rowspan]').length || $cells.length !== columnCount) {
                    canUseDataTable = false;
                    return false;
                }
            });

            if (canUseDataTable) {
                $table.DataTable();
            }
        }
    });
</script>
