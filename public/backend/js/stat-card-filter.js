/**
 * Admin Toolbar Enhancements
 *
 * 1. Stat Card Quick Filter – Makes `.adm-stat[data-filter-field]`
 *    and `.g-admin-kpi-card[data-filter-field]` cards clickable.
 *
 * 2. Auto-submit – Any `select.adm-select` inside `form.adm-toolbar`
 *    automatically submits the form on change (no need to click "Lọc").
 */
(function () {
    'use strict';

    /* ══════════════════════════════════════════════
       PART 1 — Stat Card Quick Filter
       ══════════════════════════════════════════════ */

    /* ── Highlight the currently active stat card ── */
    function markActive() {
        var params = new URLSearchParams(window.location.search);

        document.querySelectorAll('.adm-stat[data-filter-field], .g-admin-kpi-card[data-filter-field]').forEach(function (card) {
            var field = card.getAttribute('data-filter-field');
            var value = card.getAttribute('data-filter-value');
            var current = params.get(field);

            // "all" is active when no param is set OR param === "all"
            if (value === 'all' && (!current || current === 'all')) {
                card.classList.add('adm-stat--active');
            } else if (current === value) {
                card.classList.add('adm-stat--active');
            } else {
                card.classList.remove('adm-stat--active');
            }
        });
    }

    /* ── Stat card click handler ── */
    function handleStatClick(e) {
        var card = e.currentTarget;
        var field = card.getAttribute('data-filter-field');
        var value = card.getAttribute('data-filter-value');

        if (!field) return;

        // Try to find the closest toolbar form
        var page = card.closest('.adm-page, .g-admin-page-shell');
        var form = page ? page.querySelector('form.adm-toolbar, form[class*="adm-toolbar"]') : null;

        if (form) {
            // Modern pages – set the select value & submit
            var select = form.querySelector('[name="' + field + '"]');
            if (select) {
                select.value = value;
            }
            // Reset other filter fields when clicking "all" (Tổng)
            if (value === 'all') {
                form.querySelectorAll('select.adm-select').forEach(function (sel) {
                    if (sel.name !== field) {
                        var allOption = sel.querySelector('option[value="all"]');
                        if (allOption) sel.value = 'all';
                    }
                });
                var searchInput = form.querySelector('input[name="q"], input[name="search"]');
                if (searchInput) searchInput.value = '';
            }
            form.submit();
        } else {
            // Legacy pages (manage_order) – navigate via URL
            var url = new URL(window.location.href);
            if (value === 'all') {
                url.searchParams.delete(field);
            } else {
                url.searchParams.set(field, value);
            }
            window.location.href = url.toString();
        }
    }

    /* Bind stat cards */
    document.querySelectorAll('.adm-stat[data-filter-field], .g-admin-kpi-card[data-filter-field]').forEach(function (card) {
        card.addEventListener('click', handleStatClick);
    });

    markActive();


    /* ══════════════════════════════════════════════
       PART 2 — Auto-submit on select change
       ══════════════════════════════════════════════ */

    document.querySelectorAll('form.adm-toolbar').forEach(function (form) {
        form.querySelectorAll('select.adm-select').forEach(function (sel) {
            sel.addEventListener('change', function () {
                form.submit();
            });
        });
    });

})();
