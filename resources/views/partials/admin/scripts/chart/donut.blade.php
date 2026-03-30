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
                    label: "San pham",
                    value: {{ data_get($dashboard_totals ?? [], 'products', $app_product) }}
                },
                {
                    label: "Bai viet",
                    value: {{ data_get($dashboard_totals ?? [], 'posts', $app_post) }}
                },
                {
                    label: "Don hang",
                    value: {{ data_get($dashboard_totals ?? [], 'orders', $app_order) }}
                },
                {
                    label: "Video",
                    value: {{ data_get($dashboard_totals ?? [], 'videos', $app_video) }}
                },
                {
                    label: "Khach hang",
                    value: {{ data_get($dashboard_totals ?? [], 'customers', $app_customer) }}
                }
            ]
        });
    });
</script>
