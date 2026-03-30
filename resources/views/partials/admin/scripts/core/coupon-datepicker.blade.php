<script type="text/javascript">
    $(function() {
        var datepickerConfig = {
            prevText: "Tháng trước",
            nextText: "Tháng sau",
            dateFormat: "dd/mm/yy",
            dayNamesMin: ["Thứ 2", "Thứ 3", "Thứ 4", "Thứ 5", "Thứ 6", "Thứ 7", "Chủ nhật"],
            duration: "slow"
        };

        if ($('#start_coupon').length) {
            $("#start_coupon").datepicker(datepickerConfig);
        }

        if ($('#end_coupon').length) {
            $("#end_coupon").datepicker(datepickerConfig);
        }
    });
</script>
