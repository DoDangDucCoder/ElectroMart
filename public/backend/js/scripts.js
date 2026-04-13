(function ($) {
    "use strict";
    $(document).ready(function () {
        /*==Left Navigation Accordion ==*/
        var $adminSidebarNav = $('[data-admin-sidebar-nav]');

        if ($.fn.dcAccordion && ! $adminSidebarNav.length) {
            $('#nav-accordion').dcAccordion({
                eventType: 'click',
                autoClose: true,
                saveState: true,
                disableLink: true,
                speed: 'slow',
                showCount: false,
                autoExpand: true,
                classExpand: 'dcjq-current-parent'
            });
        }
        /*==Slim Scroll ==*/
        if ($.fn.slimScroll) {
            $('.event-list').slimscroll({
                height: '305px',
                wheelStep: 20
            });
            $('.conversation-list').slimscroll({
                height: '360px',
                wheelStep: 35
            });
            $('.to-do-list').slimscroll({
                height: '300px',
                wheelStep: 35
            });
        }
        /*==Nice Scroll ==*/
        if ($.fn.niceScroll) {


            $(".leftside-navigation").niceScroll({
                cursorcolor: "#8b5c7e",
                cursorborder: "0px solid #fff",
                cursorborderradius: "0px",
                cursorwidth: "3px"
            });

            $(".leftside-navigation").getNiceScroll().resize();
            if ($('#sidebar').hasClass('hide-left-bar')) {
                $(".leftside-navigation").getNiceScroll().hide();
            }
            $(".leftside-navigation").getNiceScroll().show();

            $(".right-stat-bar").niceScroll({
                cursorcolor: "#fa9770",
                cursorborder: "0px solid #fff",
                cursorborderradius: "0px",
                cursorwidth: "3px"
            });

        }

        /*==Easy Pie chart ==*/
        if ($.fn.easyPieChart) {

            $('.notification-pie-chart').easyPieChart({
                onStep: function (from, to, percent) {
                    $(this.el).find('.percent').text(Math.round(percent));
                },
                barColor: "#39b6ac",
                lineWidth: 3,
                size: 50,
                trackColor: "#efefef",
                scaleColor: "#cccccc"

            });

            $('.pc-epie-chart').easyPieChart({
                onStep: function(from, to, percent) {
                    $(this.el).find('.percent').text(Math.round(percent));
                },
                barColor: "#5bc6f0",
                lineWidth: 3,
                size:50,
                trackColor: "#32323a",
                scaleColor:"#cccccc"

            });

        }

        /*== SPARKLINE==*/
        if ($.fn.sparkline) {

            $(".d-pending").sparkline([3, 1], {
                type: 'pie',
                width: '40',
                height: '40',
                sliceColors: ['#e1e1e1', '#8175c9']
            });



            var sparkLine = function () {
                $(".sparkline").each(function () {
                    var $data = $(this).data();
                    ($data.type == 'pie') && $data.sliceColors && ($data.sliceColors = eval($data.sliceColors));
                    ($data.type == 'bar') && $data.stackedBarColor && ($data.stackedBarColor = eval($data.stackedBarColor));

                    $data.valueSpots = {
                        '0:': $data.spotColor
                    };
                    $(this).sparkline($data.data || "html", $data);


                    if ($(this).data("compositeData")) {
                        $spdata.composite = true;
                        $spdata.minSpotColor = false;
                        $spdata.maxSpotColor = false;
                        $spdata.valueSpots = {
                            '0:': $spdata.spotColor
                        };
                        $(this).sparkline($(this).data("compositeData"), $spdata);
                    };
                });
            };

            var sparkResize;
            $(window).resize(function (e) {
                clearTimeout(sparkResize);
                sparkResize = setTimeout(function () {
                    sparkLine(true)
                }, 500);
            });
            sparkLine(false);



        }



        if ($.fn.plot) {
            var datatPie = [30, 50];
            // DONUT
            $.plot($(".target-sell"), datatPie, {
                series: {
                    pie: {
                        innerRadius: 0.6,
                        show: true,
                        label: {
                            show: false

                        },
                        stroke: {
                            width: .01,
                            color: '#fff'

                        }
                    }




                },

                legend: {
                    show: true
                },
                grid: {
                    hoverable: true,
                    clickable: true
                },

                colors: ["#ff6d60", "#cbcdd9"]
            });
        }



        /*==Collapsible==*/
        $('.widget-head').click(function (e) {
            var widgetElem = $(this).children('.widget-collapse').children('i');

            $(this)
                .next('.widget-container')
                .slideToggle('slow');
            if ($(widgetElem).hasClass('ico-minus')) {
                $(widgetElem).removeClass('ico-minus');
                $(widgetElem).addClass('ico-plus');
            } else {
                $(widgetElem).removeClass('ico-plus');
                $(widgetElem).addClass('ico-minus');
            }
            e.preventDefault();
        });




        /*==Sidebar Toggle==*/

        var adminSidebarBreakpoint = 991;
        var $body = $('body');
        var $adminShell = $('[data-admin-shell]');
        var $adminSidebar = $('#sidebar');
        var $adminSidebarToggle = $('[data-admin-sidebar-toggle]');
        var adminSidebarGroupStorageKey = 'g-admin-sidebar-open-group';
        var adminShellCollapsedStorageKey = 'g-admin-shell-collapsed';
        var $adminSidebarGroups = $adminSidebarNav.find('[data-admin-sidebar-group]');

        function isAdminSidebarMobileMode() {
            return window.matchMedia('(max-width: ' + adminSidebarBreakpoint + 'px)').matches;
        }

        function getStoredAdminSidebarGroup() {
            try {
                return window.sessionStorage.getItem(adminSidebarGroupStorageKey);
            } catch (error) {
                return null;
            }
        }

        function setStoredAdminSidebarGroup(groupKey) {
            try {
                if (groupKey) {
                    window.sessionStorage.setItem(adminSidebarGroupStorageKey, groupKey);
                } else {
                    window.sessionStorage.removeItem(adminSidebarGroupStorageKey);
                }
            } catch (error) {
                // Ignore storage failures and keep navigation functional.
            }
        }

        function getStoredAdminShellCollapsed() {
            try {
                return window.sessionStorage.getItem(adminShellCollapsedStorageKey) === 'true';
            } catch (error) {
                return false;
            }
        }

        function setStoredAdminShellCollapsed(isCollapsed) {
            try {
                window.sessionStorage.setItem(adminShellCollapsedStorageKey, isCollapsed ? 'true' : 'false');
            } catch (error) {
                // Ignore storage failures and keep shell navigation functional.
            }
        }

        function findRouteActiveAdminSidebarGroup() {
            var $currentGroup = $adminSidebarNav.find('[aria-current="page"]').closest('[data-admin-sidebar-group]');

            return $currentGroup.length ? $currentGroup.data('admin-sidebar-group') : null;
        }

        function getAdminSidebarGroup(groupKey) {
            return $adminSidebarGroups.filter('[data-admin-sidebar-group="' + groupKey + '"]');
        }

        function setAdminSidebarGroupState($group, isOpen, animate) {
            if (! $group.length) {
                return;
            }

            var $button = $group.children('[data-admin-sidebar-trigger]');
            var $submenu = $group.children('ul.sub');

            $group.toggleClass('is-open', isOpen);
            $button.attr('aria-expanded', isOpen ? 'true' : 'false');

            if (! $submenu.length) {
                return;
            }

            if (animate) {
                $submenu.stop(true, true)[isOpen ? 'slideDown' : 'slideUp'](180);
                return;
            }

            $submenu.css('display', isOpen ? 'block' : 'none');
        }

        function syncAdminSidebarGroups() {
            if (! $adminSidebarGroups.length) {
                return;
            }

            var routeActiveGroup = findRouteActiveAdminSidebarGroup();

            $adminSidebarGroups.each(function () {
                var $group = $(this);
                var groupKey = $group.data('admin-sidebar-group');
                var shouldOpen = !!routeActiveGroup && groupKey === routeActiveGroup;

                setAdminSidebarGroupState($group, shouldOpen, false);
            });

            setStoredAdminSidebarGroup(routeActiveGroup);
        }

        function toggleAdminSidebarGroup(groupKey) {
            var $targetGroup = getAdminSidebarGroup(groupKey);

            if (! $targetGroup.length) {
                return;
            }

            var shouldOpen = ! $targetGroup.hasClass('is-open');

            $adminSidebarGroups.each(function () {
                var $group = $(this);
                setAdminSidebarGroupState($group, shouldOpen && $group.is($targetGroup), true);
            });

            setStoredAdminSidebarGroup(shouldOpen ? groupKey : null);

            if (shouldOpen) {
                var groupTop = $targetGroup.position();
                if (groupTop && typeof groupTop.top !== 'undefined') {
                    $(".leftside-navigation").scrollTo(groupTop.top - 80, 300);
                }
            }
        }

        function updateAdminSidebarToggleState(isExpanded) {
            $adminSidebarToggle.attr('aria-expanded', isExpanded ? 'true' : 'false');
        }

        function updateAdminSidebarToggleLabel() {
            if (! $adminSidebarToggle.length) {
                return;
            }

            var isMobile = isAdminSidebarMobileMode();
            var isExpanded = isMobile
                ? $body.hasClass('g-admin-sidebar-mobile-open')
                : ! $adminShell.hasClass('is-collapsed');
            var nextLabel = '';

            if (isMobile) {
                nextLabel = isExpanded
                    ? $adminSidebarToggle.attr('data-admin-sidebar-label-mobile-open')
                    : $adminSidebarToggle.attr('data-admin-sidebar-label-mobile-closed');
            } else {
                nextLabel = isExpanded
                    ? $adminSidebarToggle.attr('data-admin-sidebar-label-expanded')
                    : $adminSidebarToggle.attr('data-admin-sidebar-label-collapsed');
            }

            if (nextLabel) {
                $adminSidebarToggle.attr('aria-label', nextLabel);
            }
        }

        function showAdminSidebarScrollbar() {
            if ($.fn.niceScroll && $(".leftside-navigation").length) {
                $(".leftside-navigation").getNiceScroll().show();
            }
        }

        function hideAdminSidebarScrollbar() {
            if ($.fn.niceScroll && $(".leftside-navigation").length) {
                $(".leftside-navigation").getNiceScroll().hide();
            }
        }

        function closeAdminMobileSidebar() {
            $body.removeClass('g-admin-sidebar-mobile-open');
            updateAdminSidebarToggleState(false);
            updateAdminSidebarToggleLabel();
            hideAdminSidebarScrollbar();
        }

        function setAdminShellCollapsed(isCollapsed) {
            if (! $adminShell.length) {
                return;
            }

            $adminShell.toggleClass('is-collapsed', isCollapsed);
            updateAdminSidebarToggleState(!isCollapsed);
            updateAdminSidebarToggleLabel();

            if (isCollapsed) {
                hideAdminSidebarScrollbar();
            } else {
                showAdminSidebarScrollbar();
            }
        }

        function syncAdminSidebarMode() {
            if (isAdminSidebarMobileMode()) {
                $adminShell.removeClass('is-collapsed');
                $adminSidebar.removeClass('hide-left-bar');
                if ($body.hasClass('g-admin-sidebar-mobile-open')) {
                    showAdminSidebarScrollbar();
                } else {
                    hideAdminSidebarScrollbar();
                }
                updateAdminSidebarToggleState($body.hasClass('g-admin-sidebar-mobile-open'));
                updateAdminSidebarToggleLabel();
                return;
            }

            $body.removeClass('g-admin-sidebar-mobile-open');
            $adminSidebar.removeClass('hide-left-bar');
            setAdminShellCollapsed(getStoredAdminShellCollapsed());
        }

        $(document).on('click', '[data-admin-sidebar-trigger]', function (e) {
            var groupKey = $(this).data('admin-sidebar-trigger');

            e.preventDefault();
            e.stopPropagation();

            toggleAdminSidebarGroup(groupKey);
        });

        $(document).on('click', '[data-admin-sidebar-group] a[data-admin-sidebar-key]', function () {
            var groupKey = $(this).closest('[data-admin-sidebar-group]').data('admin-sidebar-group');

            if (groupKey) {
                setStoredAdminSidebarGroup(groupKey);
            }
        });



        $adminSidebarToggle.click(function (e) {
            if ($.fn.niceScroll) {
                $(".leftside-navigation").niceScroll({
                    cursorcolor: "#1FB5AD",
                    cursorborder: "0px solid #fff",
                    cursorborderradius: "0px",
                    cursorwidth: "3px"
                });
            }

            if (isAdminSidebarMobileMode()) {
                $body.toggleClass('g-admin-sidebar-mobile-open');
                updateAdminSidebarToggleState($body.hasClass('g-admin-sidebar-mobile-open'));
                updateAdminSidebarToggleLabel();

                if ($body.hasClass('g-admin-sidebar-mobile-open')) {
                    showAdminSidebarScrollbar();
                } else {
                    hideAdminSidebarScrollbar();
                }
            } else {
                var shouldCollapseShell = ! $adminShell.hasClass('is-collapsed');
                setAdminShellCollapsed(shouldCollapseShell);
                setStoredAdminShellCollapsed(shouldCollapseShell);
            }

            e.stopPropagation();
            e.preventDefault();
            if ($('#container').hasClass('open-right-panel')) {
                $('#container').removeClass('open-right-panel')
            }
            if ($('.right-sidebar').hasClass('open-right-bar')) {
                $('.right-sidebar').removeClass('open-right-bar')
            }

            if ($('.header').hasClass('merge-header')) {
                $('.header').removeClass('merge-header')
            }


        });
        $('.toggle-right-box .fa-bars').click(function (e) {
            $('#container').toggleClass('open-right-panel');
            $('.right-sidebar').toggleClass('open-right-bar');
            $('.header').toggleClass('merge-header');

            e.stopPropagation();
        });

        $('.header,#main-content,#sidebar').click(function () {
            if ($('#container').hasClass('open-right-panel')) {
                $('#container').removeClass('open-right-panel')
            }
            if ($('.right-sidebar').hasClass('open-right-bar')) {
                $('.right-sidebar').removeClass('open-right-bar')
            }

            if ($('.header').hasClass('merge-header')) {
                $('.header').removeClass('merge-header')
            }


        });

        $(document).click(function (e) {
            if (! isAdminSidebarMobileMode() || ! $body.hasClass('g-admin-sidebar-mobile-open')) {
                return;
            }

            if ($(e.target).closest('#sidebar, [data-admin-sidebar-toggle]').length === 0) {
                closeAdminMobileSidebar();
            }
        });

        $(document).on('keydown', function (e) {
            if (e.key === 'Escape' && isAdminSidebarMobileMode()) {
                closeAdminMobileSidebar();
            }
        });

        $(window).on('resize.adminSidebar', function () {
            syncAdminSidebarMode();
        });

        syncAdminSidebarMode();
        syncAdminSidebarGroups();


        $('.panel .tools .fa').click(function () {
            var el = $(this).parents(".panel").children(".panel-body");
            if ($(this).hasClass("fa-chevron-down")) {
                $(this).removeClass("fa-chevron-down").addClass("fa-chevron-up");
                el.slideUp(200);
            } else {
                $(this).removeClass("fa-chevron-up").addClass("fa-chevron-down");
                el.slideDown(200); }
        });



        $('.panel .tools .fa-times').click(function () {
            $(this).parents(".panel").parent().remove();
        });

        // tool tips

        $('.tooltips').tooltip();

        // popovers

        $('.popovers').popover();


    });


})(jQuery);
