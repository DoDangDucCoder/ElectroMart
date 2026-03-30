<script type="text/javascript">
    window.chart365daysorder = function() {
        if (!$('#chart').length || typeof Morris === 'undefined') {
            return;
        }

        if (!window.adminDashboardChart) {
            window.adminDashboardChart = new Morris.Bar({
                element: 'chart',
                lineColors: ['#819C79', '#fc8710', '#FF6541', '#A4ADD3', '#766B56'],
                parseTime: false,
                hideHover: 'auto',
                xkey: 'period',
                ykeys: ['order', 'sales', 'profit', 'quantity'],
                labels: ['đơn hàng', 'doanh số', 'lợi nhuận', 'số lượng']
            });
        }

        var _token = $('input[name="_token"]').val();

        $.ajax({
            url: "{{ url('/days-order') }}",
            method: "POST",
            dataType: "JSON",
            data: {
                _token: _token
            },
            success: function(data) {
                window.adminDashboardChart.setData(data || []);
            }
        });
    };

    $(function() {
        if (!$('#chart').length) {
            return;
        }

        chart365daysorder();

        $(document).on('change', '.dashboard-filter', function() {
            var dashboard_value = $(this).val();
            var _token = $('input[name="_token"]').val();

            $.ajax({
                url: "{{ url('/dashboard-filter') }}",
                method: "POST",
                dataType: "JSON",
                data: {
                    dashboard_value: dashboard_value,
                    _token: _token
                },
                success: function(data) {
                    if (window.adminDashboardChart) {
                        window.adminDashboardChart.setData(data || []);
                    }
                }
            });
        });

        $(document).on('click', '#btn-dashboard-filter', function() {
            var _token = $('input[name="_token"]').val();
            var from_date = $('#datepicker').val();
            var to_date = $('#datepicker2').val();

            $.ajax({
                url: "{{ url('/filter-by-date') }}",
                method: "POST",
                dataType: "JSON",
                data: {
                    from_date: from_date,
                    to_date: to_date,
                    _token: _token
                },
                success: function(data) {
                    if (window.adminDashboardChart) {
                        window.adminDashboardChart.setData(data || []);
                    }
                }
            });
        });
    });
</script>
