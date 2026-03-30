<script type="text/javascript">
    $(function() {
        var datepickerConfig = {
            prevText: "Tháng trước",
            nextText: "Tháng sau",
            dateFormat: "yy-mm-dd",
            dayNamesMin: ["Thứ 2", "Thứ 3", "Thứ 4", "Thứ 5", "Thứ 6", "Thứ 7", "Chủ nhật"],
            duration: "slow"
        };

        if ($('#datepicker').length) {
            $('#datepicker').datepicker(datepickerConfig);
        }

        if ($('#datepicker2').length) {
            $('#datepicker2').datepicker(datepickerConfig);
        }
    });
</script>
