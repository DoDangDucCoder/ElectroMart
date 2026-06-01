<script>
    (function () {
        var debounceTimers = new WeakMap();

        function debounce(element, callback, delay) {
            window.clearTimeout(debounceTimers.get(element));
            debounceTimers.set(element, window.setTimeout(callback, delay));
        }

        function submitSearchForm(input) {
            var form = input.closest('form');

            if (!form || String(form.method).toLowerCase() !== 'get') {
                return false;
            }

            debounce(input, function () {
                var pageInput = form.querySelector('input[name="page"]');

                if (pageInput) {
                    pageInput.value = '1';
                }

                if (typeof form.requestSubmit === 'function') {
                    form.requestSubmit();
                    return;
                }

                form.submit();
            }, 450);

            return true;
        }

        function filterNearestTable(input) {
            var page = input.closest('.adm-page, .g-admin-panel-card, section') || document;
            var table = page.querySelector('table');

            if (!table) {
                return;
            }

            var keyword = input.value.trim().toLowerCase();

            table.querySelectorAll('tbody tr').forEach(function (row) {
                row.style.display = row.textContent.toLowerCase().indexOf(keyword) === -1 ? 'none' : '';
            });
        }

        document.addEventListener('input', function (event) {
            var input = event.target;

            if (!(input instanceof HTMLInputElement)) {
                return;
            }

            if (!input.matches('#main-content input[name="q"], #main-content input[name="search"], #main-content .adm-search input[type="text"], #main-content .adm-search input[type="search"]')) {
                return;
            }

            if (submitSearchForm(input)) {
                return;
            }

            debounce(input, function () {
                filterNearestTable(input);
            }, 180);
        });

        document.addEventListener('change', function (event) {
            var field = event.target;

            if (!(field instanceof HTMLSelectElement) || !field.closest('#main-content form.adm-toolbar')) {
                return;
            }

            submitSearchForm(field);
        });
    })();
</script>
