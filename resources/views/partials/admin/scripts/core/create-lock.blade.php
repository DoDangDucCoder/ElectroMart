@if (config('admin_features.create_locked', false))
    <script>
        (function () {
            var currentPath = @json(trim(request()->path(), '/'));
            var lockedFormPaths = @json(array_values((array) config('admin_features.create_form_paths', [])));
            var lockedPostPaths = @json(array_values((array) config('admin_features.create_post_paths', [])));
            var message = @json(config('admin_features.create_locked_message', 'Chức năng thêm mới đang tạm khóa.'));

            function wildcardToRegExp(pattern) {
                var escaped = pattern.replace(/[.+?^${}()|[\]\\]/g, '\\$&').replace(/\*/g, '.*');

                return new RegExp('^' + escaped + '$');
            }

            function matchesAny(path, patterns) {
                return patterns.some(function (pattern) {
                    return wildcardToRegExp(String(pattern).replace(/^\/+|\/+$/g, '')).test(path);
                });
            }

            function normalizeActionPath(action) {
                if (!action || action === '#') {
                    return '';
                }

                var url = new URL(action, window.location.origin);

                return url.pathname.replace(/^\/+|\/+$/g, '');
            }

            function disableForm(form) {
                form.dataset.adminCreateLocked = 'true';
                form.addEventListener('submit', function (event) {
                    event.preventDefault();
                    event.stopPropagation();
                    alert(message);
                });

                form.querySelectorAll('input, select, textarea, button').forEach(function (control) {
                    control.disabled = true;
                    control.setAttribute('aria-disabled', 'true');
                });
            }

            var fullPageLocked = matchesAny(currentPath, lockedFormPaths);

            document.querySelectorAll('#main-content form').forEach(function (form) {
                var method = String(form.getAttribute('method') || 'get').toLowerCase();
                var actionPath = normalizeActionPath(form.getAttribute('action'));

                if (fullPageLocked || (method !== 'get' && matchesAny(actionPath, lockedPostPaths))) {
                    disableForm(form);
                }
            });

            document
                .querySelectorAll('#btn-add-video, .add-nut, .add-doitac, #btn-add-feeship')
                .forEach(function (button) {
                    button.disabled = true;
                    button.setAttribute('aria-disabled', 'true');
                    button.title = message;
                });
        })();
    </script>
@endif
