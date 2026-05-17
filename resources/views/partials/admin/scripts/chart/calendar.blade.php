<script type="text/javascript">
    $(window).on('load', function() {
        if ($.fn.monthly && $('#mycalendar').length) {
            $('#mycalendar').monthly({
                mode: 'event'
            });
        }

        if ($.fn.monthly && $('#mycalendar2').length) {
            $('#mycalendar2').monthly({
                mode: 'picker',
                target: '#mytarget',
                setWidth: '250px',
                startHidden: true,
                showTrigger: '#mytarget',
                stylePast: true,
                disablePast: true
            });
        }

        switch (window.location.protocol) {
            case 'http:':
            case 'https:':
                break;
            case 'file:':
                console.warn('Lịch sự kiện sẽ không hoạt động đầy đủ khi mở trực tiếp từ file cục bộ.');
        }
    });
</script>
