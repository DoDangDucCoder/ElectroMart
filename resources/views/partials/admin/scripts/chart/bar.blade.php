<script type="text/javascript">
    window.renderDashboardChartNotice = function(type, message) {
        var $notice = $('#dashboard_chart_notice');

        if (!$notice.length) {
            return;
        }

        var alertClass = 'alert-info';

        if (type === 'error') {
            alertClass = 'alert-danger';
        } else if (type === 'success') {
            alertClass = 'alert-success';
        }

        $notice
            .removeClass('alert-info alert-danger alert-success')
            .addClass('alert ' + alertClass)
            .text(message)
            .prop('hidden', false);
    };

    window.clearDashboardChartNotice = function() {
        var $notice = $('#dashboard_chart_notice');

        if (!$notice.length) {
            return;
        }

        $notice
            .removeClass('alert-danger alert-success')
            .addClass('alert alert-info')
            .text('')
            .prop('hidden', true);
    };

    window.resolveDashboardChartError = function(xhr, fallbackMessage) {
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

    window.applyDashboardChartData = function(data, emptyMessage) {
        if (!window.adminDashboardChart) {
            return;
        }

        var normalizedData = Array.isArray(data) ? data : [];
        window.adminDashboardChart.setData(normalizedData);

        if (normalizedData.length === 0 && emptyMessage) {
            renderDashboardChartNotice('info', emptyMessage);
            return;
        }

        clearDashboardChartNotice();
    };

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
                applyDashboardChartData(data, 'Chưa có dữ liệu thống kê để hiển thị trong 365 ngày qua.');
            },
            error: function(xhr) {
                if (window.adminDashboardChart) {
                    window.adminDashboardChart.setData([]);
                }

                renderDashboardChartNotice('error', resolveDashboardChartError(
                    xhr,
                    'Không thể tải dữ liệu tổng quan trên môi trường này.'
                ));
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

            if (dashboard_value === '') {
                chart365daysorder();
                return;
            }

            $.ajax({
                url: "{{ url('/dashboard-filter') }}",
                method: "POST",
                dataType: "JSON",
                data: {
                    dashboard_value: dashboard_value,
                    _token: _token
                },
                success: function(data) {
                    applyDashboardChartData(data, 'Không có dữ liệu cho bộ lọc đã chọn.');
                },
                error: function(xhr) {
                    if (window.adminDashboardChart) {
                        window.adminDashboardChart.setData([]);
                    }

                    renderDashboardChartNotice('error', resolveDashboardChartError(
                        xhr,
                        'Không thể lọc dữ liệu tổng quan trên môi trường này.'
                    ));
                }
            });
        });

        $(document).on('click', '#btn-dashboard-filter', function() {
            var _token = $('input[name="_token"]').val();
            var from_date = $('#datepicker').val();
            var to_date = $('#datepicker2').val();

            if ((from_date && !to_date) || (!from_date && to_date)) {
                renderDashboardChartNotice('error', 'Vui lòng chọn đầy đủ cả từ ngày và đến ngày trước khi lọc.');
                return;
            }

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
                    applyDashboardChartData(data, 'Không có dữ liệu đơn hàng trong khoảng ngày đã chọn.');
                },
                error: function(xhr) {
                    if (window.adminDashboardChart) {
                        window.adminDashboardChart.setData([]);
                    }

                    renderDashboardChartNotice('error', resolveDashboardChartError(
                        xhr,
                        'Không thể lọc dữ liệu theo khoảng ngày trên môi trường này.'
                    ));
                }
            });
        });
    });
</script>
