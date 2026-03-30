{{-- Storefront AJAX helpers extracted from layout.blade.php. --}}
    <script>
        (function () {
            var tokenEl = document.querySelector('meta[name="csrf-token"]');
            if (!tokenEl || !window.jQuery) {
                return;
            }

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': tokenEl.getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
        })();
        function isFullHtmlResponse(data) {
            return typeof data === 'string' && /<!doctype|<html|<head|<body/i.test(data);
        }

        function renderAjaxHtml(target, data, mode) {
            if (typeof data !== 'string') {
                console.error('Unexpected non-string response for', target, data);
                return;
            }

            if (isFullHtmlResponse(data)) {
                console.error('Unexpected full HTML response for', target, data.slice(0, 200));
                return;
            }

            if (mode == 'append') {
                $(target).append(data);
            } else {
                $(target).html(data);
            }
        }
    </script>
