{{-- Storefront mobile drawer interactions extracted from layout.blade.php. --}}
    <script>
        (function($) {
            if (!$) {
                return;
            }

            function isMobileNav() {
                return window.matchMedia('(max-width: 991px)').matches;
            }

            function setCategoryTriggerExpanded(expanded) {
                $('.shopw-mobile-nav-toggle[data-nav-intent="categories"]')
                    .attr('aria-expanded', expanded ? 'true' : 'false')
                    .toggleClass('is-active', expanded);
            }

            function closeDesktopCategories() {
                $('#navbar .shopw-nav-group').removeClass('is-open');
                setCategoryTriggerExpanded(false);
            }

            function setMobileNav(open) {
                $('body').toggleClass('shopw-mobile-nav-open', open);
                $('#shopw-mobile-drawer').attr('aria-hidden', open ? 'false' : 'true');
                setCategoryTriggerExpanded(open);

                if (!open) {
                    collapseMobileGroups(true);
                }
            }

            function collapseMobileGroups(resetStyles) {
                $('#shopw-mobile-drawer .shopw-nav-group').removeClass('is-open');
                $('#shopw-mobile-drawer .shopw-submenu-toggle').attr('aria-expanded', 'false');
                $('#shopw-mobile-drawer .menu-item.has-children').removeClass('is-open');
                $('#shopw-mobile-drawer .shopw-childmenu-toggle').attr('aria-expanded', 'false');

                if (resetStyles) {
                    $('#shopw-mobile-drawer .sub-menu').removeAttr('style');
                    $('#shopw-mobile-drawer .cate_sub').removeAttr('style');
                }
            }

            function openMobileGroup(groupSelector) {
                var $group = $(groupSelector);

                if (!$group.length || $group.hasClass('is-open')) {
                    return;
                }

                $group.addClass('is-open');
                $group.children('.sub-menu').stop(true, true).slideDown(180);
                $group.find('> .shopw-nav-row .shopw-submenu-toggle').attr('aria-expanded', 'true');
            }

            $(document).on('click', '.shopw-mobile-nav-toggle', function(e) {
                var intent = $(this).data('navIntent');

                if (!isMobileNav()) {
                    if (intent !== 'categories') {
                        return;
                    }

                    e.preventDefault();

                    var targetSelector = $(this).data('desktopTarget') || '#shopw-nav-group-categories';
                    var $group = $(targetSelector);

                    if (!$group.length) {
                        return;
                    }

                    var willOpen = !$group.hasClass('is-open');

                    closeDesktopCategories();

                    if (willOpen) {
                        $group.addClass('is-open');
                        setCategoryTriggerExpanded(true);
                    }

                    return;
                }

                e.preventDefault();
                setMobileNav(true);

                if (intent === 'categories') {
                    openMobileGroup('#shopw-nav-group-categories');
                }
            });

            $(document).on('click', '.shopw-mobile-drawer-close, .shopw-mobile-nav-overlay', function(e) {
                if (!isMobileNav()) {
                    return;
                }

                e.preventDefault();
                setMobileNav(false);
            });

            $(document).on('click', '#shopw-mobile-drawer .shopw-nav-row > a[href="#"]', function(e) {
                if (isMobileNav()) {
                    e.preventDefault();
                }
            });

            $(document).on('click', '#shopw-mobile-drawer .shopw-submenu-toggle', function(e) {
                if (!isMobileNav()) {
                    return;
                }

                e.preventDefault();

                var $group = $(this).closest('.shopw-nav-group');
                var $submenu = $group.children('.sub-menu');
                var isOpen = $group.hasClass('is-open');

                $group.toggleClass('is-open', !isOpen);
                $(this).attr('aria-expanded', !isOpen ? 'true' : 'false');
                $submenu.stop(true, true).slideToggle(180);
            });

            $(document).on('click', '#shopw-mobile-drawer .shopw-childmenu-toggle', function(e) {
                if (!isMobileNav()) {
                    return;
                }

                e.preventDefault();

                var $item = $(this).closest('.menu-item.has-children');
                var $submenu = $item.children('.cate_sub');
                var isOpen = $item.hasClass('is-open');

                $item.toggleClass('is-open', !isOpen);
                $(this).attr('aria-expanded', !isOpen ? 'true' : 'false');
                $submenu.stop(true, true).slideToggle(180);
            });

            $(document).on('click', '#shopw-mobile-drawer a', function() {
                if (!isMobileNav()) {
                    return;
                }

                var href = $(this).attr('href') || '';

                if (href && href !== '#' && href.indexOf('javascript:') !== 0) {
                    setMobileNav(false);
                }
            });

            $(document).on('keydown', function(e) {
                if (e.key === 'Escape') {
                    setMobileNav(false);
                    closeDesktopCategories();
                }
            });

            $(document).on('click', function(e) {
                if (isMobileNav()) {
                    return;
                }

                var $target = $(e.target);

                if ($target.closest('.shopw-mobile-nav-toggle[data-nav-intent="categories"]').length) {
                    return;
                }

                if ($target.closest('#shopw-nav-group-categories').length) {
                    return;
                }

                closeDesktopCategories();
            });

            $(window).on('resize', function() {
                if (!isMobileNav()) {
                    setMobileNav(false);
                    collapseMobileGroups(true);
                    closeDesktopCategories();
                } else {
                    closeDesktopCategories();
                }
            });
        })(window.jQuery);
    </script>

