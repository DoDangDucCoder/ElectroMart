<script type="text/javascript">
    $(function() {
        if (!$('#donut').length || typeof Morris === 'undefined') {
            return;
        }

        new Morris.Donut({
            element: 'donut',
            resize: true,
            colors: [
                '#a8328e',
                '#61a1ce',
                '#ce8f61',
                '#f5b942',
                '#4842f5'
            ],
            data: [{
                    label: "Sản phẩm",
                    value: {{ data_get($dashboard_totals ?? [], 'products', 0) }}
                },
                {
                    label: "Bài viết",
                    value: {{ data_get($dashboard_totals ?? [], 'posts', 0) }}
                },
                {
                    label: "Đơn hàng",
                    value: {{ data_get($dashboard_totals ?? [], 'orders', 0) }}
                },
                {
                    label: "Video",
                    value: {{ data_get($dashboard_totals ?? [], 'videos', 0) }}
                },
                {
                    label: "Khách hàng",
                    value: {{ data_get($dashboard_totals ?? [], 'customers', 0) }}
                }
            ]
        });
    });
</script>
