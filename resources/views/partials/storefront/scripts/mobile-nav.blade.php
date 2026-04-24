{{-- Storefront mobile drawer interactions extracted from layout.blade.php. --}}
    <script>
        (function($) {
            if (!$) {
                return;
            }

            var openGroupStorageKey = 'shopw.storefront.open-group';
            var desktopCategoryCloseTimer = null;

            function isMobileNav() {
                return window.matchMedia('(max-width: 991px)').matches;
            }

            function storage() {
                try {
                    return window.sessionStorage;
                } catch (error) {
                    return null;
                }
            }

            function rememberOpenGroup(groupId) {
                var session = storage();

                if (!session || !groupId) {
                    return;
                }

                session.setItem(openGroupStorageKey, groupId);
            }

            function preferredMobileGroupSelector(fallbackSelector) {
                if (fallbackSelector) {
                    return fallbackSelector;
                }

                var $currentGroup = $('#shopw-mobile-drawer .shopw-nav-group.is-current').first();

                if ($currentGroup.length && $currentGroup.attr('id')) {
                    return '#' + $currentGroup.attr('id');
                }

                var session = storage();
                var rememberedGroupId = session ? session.getItem(openGroupStorageKey) : null;

                return rememberedGroupId ? '#' + rememberedGroupId : null;
            }

            function setCategoryTriggerExpanded(expanded) {
                $('.shopw-mobile-nav-toggle[data-nav-intent="categories"]')
                    .attr('aria-expanded', expanded ? 'true' : 'false')
                    .toggleClass('is-active', expanded);
            }

            function clearDesktopCategoryCloseTimer() {
                if (desktopCategoryCloseTimer) {
                    window.clearTimeout(desktopCategoryCloseTimer);
                    desktopCategoryCloseTimer = null;
                }
            }

            function syncAccountMenuState($menu) {
                if (!$menu || !$menu.length) {
                    return;
                }

                var expanded = $menu.prop('open');

                $menu.find('> summary').attr('aria-expanded', expanded ? 'true' : 'false');
            }

            function syncAllAccountMenus() {
                $('.shopw-account-menu').each(function() {
                    syncAccountMenuState($(this));
                });
            }

            function closeAccountMenus(exceptMenuElement) {
                $('.shopw-account-menu').each(function() {
                    if (exceptMenuElement && this === exceptMenuElement) {
                        syncAccountMenuState($(this));
                        return;
                    }

                    if (this.open) {
                        this.open = false;
                    }

                    syncAccountMenuState($(this));
                });
            }

            function toggleAccountMenu(menuElement, forceOpen) {
                if (!menuElement) {
                    return;
                }

                var shouldOpen = typeof forceOpen === 'boolean' ? forceOpen : !menuElement.open;

                closeAccountMenus(shouldOpen ? menuElement : null);
                menuElement.open = shouldOpen;
                syncAccountMenuState($(menuElement));
            }

            function categoryDesktopPanelSelector() {
                var selector = $('.shopw-mobile-nav-toggle[data-nav-intent="categories"]').first().data('desktopPanel');

                return selector || '#shopw-desktop-category-panel';
            }

            function closeDesktopCategoryPanel() {
                if (isMobileNav()) {
                    return;
                }

                clearDesktopCategoryCloseTimer();

                var $panel = $(categoryDesktopPanelSelector());

                if (!$panel.length) {
                    setCategoryTriggerExpanded(false);
                    return;
                }

                $panel.attr('hidden', true);
                setCategoryTriggerExpanded(false);
            }

            function toggleDesktopCategoryPanel(forceOpen) {
                if (isMobileNav()) {
                    return;
                }

                clearDesktopCategoryCloseTimer();

                var $panel = $(categoryDesktopPanelSelector());

                if (!$panel.length) {
                    return;
                }

                var shouldOpen = typeof forceOpen === 'boolean' ? forceOpen : $panel.is('[hidden]');

                $panel.attr('hidden', !shouldOpen);
                setCategoryTriggerExpanded(shouldOpen);
            }

            function openDesktopCategoryPanel() {
                if (isMobileNav()) {
                    return;
                }

                closeDesktopGroups();
                toggleDesktopCategoryPanel(true);
                activateFirstDesktopCategoryPane();
            }

            function queueDesktopCategoryClose() {
                if (isMobileNav()) {
                    return;
                }

                clearDesktopCategoryCloseTimer();

                desktopCategoryCloseTimer = window.setTimeout(function() {
                    closeDesktopCategoryPanel();
                }, 120);
            }

            function setDesktopCategoryPane(selector) {
                var $panel = $(categoryDesktopPanelSelector());

                if (!$panel.length || !selector) {
                    return;
                }

                var $triggers = $panel.find('.shopw-desktop-category-trigger');
                var $panes = $panel.find('.shopw-desktop-category-pane');
                var $target = $panel.find(selector).first();

                if (!$target.length) {
                    return;
                }

                $triggers.removeClass('is-active').attr('aria-selected', 'false');
                $panes.removeClass('is-active').attr('hidden', true);

                $panel.find('.shopw-desktop-category-trigger[data-category-pane="' + selector + '"]')
                    .addClass('is-active')
                    .attr('aria-selected', 'true');

                $target.addClass('is-active').attr('hidden', false);
            }

            function activateFirstDesktopCategoryPane() {
                var $panel = $(categoryDesktopPanelSelector());

                if (!$panel.length) {
                    return;
                }

                var $activeTrigger = $panel.find('.shopw-desktop-category-trigger.is-active').first();
                var selector = $activeTrigger.data('categoryPane')
                    || $panel.find('.shopw-desktop-category-trigger').first().data('categoryPane');

                if (selector) {
                    setDesktopCategoryPane(selector);
                }
            }

            function closeDesktopGroups(exceptGroupId) {
                $('#navbar .shopw-nav-group').each(function() {
                    var $group = $(this);

                    if (exceptGroupId && $group.attr('id') === exceptGroupId) {
                        return;
                    }

                    $group.removeClass('is-open');
                    $group.find('> .shopw-nav-row .shopw-submenu-toggle').attr('aria-expanded', 'false');
                });
            }

            function setGroupExpanded($group, expanded, animate) {
                if (!$group.length) {
                    return;
                }

                var $submenu = $group.children('.sub-menu');

                $group.toggleClass('is-open', expanded);
                $group.find('> .shopw-nav-row .shopw-submenu-toggle').attr('aria-expanded', expanded ? 'true' : 'false');

                if (isMobileNav()) {
                    if (animate) {
                        $submenu.stop(true, true)[expanded ? 'slideDown' : 'slideUp'](180);
                    } else if (expanded) {
                        $submenu.css('display', 'block');
                    }
                }

                if (expanded && $group.attr('id')) {
                    rememberOpenGroup($group.attr('id'));
                }

            }

            function setMobileNav(open, preferredGroup) {
                $('body').toggleClass('shopw-mobile-nav-open', open);
                $('#shopw-mobile-drawer').attr('aria-hidden', open ? 'false' : 'true');

                if (open) {
                    setCategoryTriggerExpanded(true);
                    collapseMobileGroups(true);

                    var selector = preferredMobileGroupSelector(preferredGroup);

                    if (selector) {
                        openMobileGroup(selector, false);
                    }
                } else {
                    setCategoryTriggerExpanded(false);
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

            function openMobileGroup(groupSelector, animate) {
                var $group = $(groupSelector);

                if (!$group.length) {
                    return;
                }

                closeDesktopGroups($group.attr('id'));
                collapseMobileGroups(false);
                setGroupExpanded($group, true, animate !== false);
            }

            $(document).on('click', '.shopw-mobile-nav-toggle', function(e) {
                var intent = $(this).data('navIntent');

                if (!isMobileNav()) {
                    if (intent !== 'categories') {
                        return;
                    }

                    e.preventDefault();
                    closeAccountMenus();
                    openDesktopCategoryPanel();
                    return;
                }

                e.preventDefault();
                setMobileNav(true, intent === 'categories' ? '#shopw-nav-group-categories' : null);
            });

            $(document).on('mouseenter focusin', '.shopw-header-category-shell', function() {
                if (isMobileNav()) {
                    return;
                }

                closeAccountMenus();
                openDesktopCategoryPanel();
            });

            $(document).on('click', '.shopw-account-menu > summary', function(e) {
                if (isMobileNav()) {
                    return;
                }

                e.preventDefault();

                var menuElement = $(this).parent('.shopw-account-menu').get(0);

                closeDesktopCategoryPanel();
                closeDesktopGroups();
                toggleAccountMenu(menuElement);
            });

            $(document).on('click', '.shopw-account-dropdown a', function() {
                closeAccountMenus();
            });

            $(document).on('mouseleave', '.shopw-header-category-shell', function() {
                if (isMobileNav()) {
                    return;
                }

                queueDesktopCategoryClose();
            });

            $(document).on('focusout', '.shopw-header-category-shell', function() {
                if (isMobileNav()) {
                    return;
                }

                var $shell = $(this);

                window.setTimeout(function() {
                    if (!$shell.find(document.activeElement).length) {
                        closeDesktopCategoryPanel();
                    }
                }, 0);
            });

            $(document).on('click', '.shopw-mobile-drawer-close, .shopw-mobile-nav-overlay', function(e) {
                if (!isMobileNav()) {
                    return;
                }

                e.preventDefault();
                setMobileNav(false);
            });

            $(document).on('click', '#navbar .shopw-submenu-toggle', function(e) {
                e.preventDefault();

                var $group = $(this).closest('.shopw-nav-group');
                var isOpen = $group.hasClass('is-open');

                if (isMobileNav()) {
                    if (isOpen) {
                        setGroupExpanded($group, false, true);
                    } else {
                        openMobileGroup('#' + $group.attr('id'), true);
                    }

                    return;
                }

                closeAccountMenus();
                closeDesktopCategoryPanel();

                if (isOpen) {
                    closeDesktopGroups();
                } else {
                    closeDesktopGroups($group.attr('id'));
                    setGroupExpanded($group, true, false);
                }
            });

            $(document).on('mouseenter focusin click', '.shopw-desktop-category-trigger', function(e) {
                if (isMobileNav()) {
                    return;
                }

                closeAccountMenus();
                clearDesktopCategoryCloseTimer();

                if (e.type === 'click') {
                    e.preventDefault();
                }

                setDesktopCategoryPane($(this).data('categoryPane'));
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
                    closeAccountMenus();
                    closeDesktopCategoryPanel();
                    closeDesktopGroups();
                }
            });

            $(document).on('focusin', function(e) {
                if (isMobileNav()) {
                    return;
                }

                if ($(e.target).closest('.shopw-account-menu').length) {
                    return;
                }

                closeAccountMenus();
            });

            $(document).on('click', function(e) {
                if (isMobileNav()) {
                    return;
                }

                var $target = $(e.target);

                if ($target.closest('.shopw-mobile-nav-toggle[data-nav-intent="categories"]').length) {
                    closeAccountMenus();
                    return;
                }

                if ($target.closest(categoryDesktopPanelSelector()).length) {
                    closeAccountMenus();
                    return;
                }

                if ($target.closest('#navbar .shopw-nav-group').length) {
                    closeAccountMenus();
                    return;
                }

                if ($target.closest('.shopw-account-menu').length) {
                    return;
                }

                closeAccountMenus();
                closeDesktopCategoryPanel();
                closeDesktopGroups();
            });

            $(window).on('resize', function() {
                if (!isMobileNav()) {
                    setMobileNav(false);
                    collapseMobileGroups(true);
                    closeAccountMenus();
                    closeDesktopCategoryPanel();
                    closeDesktopGroups();
                } else {
                    closeAccountMenus();
                    closeDesktopCategoryPanel();
                    closeDesktopGroups();
                }
            });

            syncAllAccountMenus();
        })(window.jQuery);
    </script>

