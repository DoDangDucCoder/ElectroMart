<script>
    (function () {
        'use strict';

        const chipsTrack = document.querySelector('[data-cat-chips]');
        if (chipsTrack) {
            const prev = document.querySelector('[data-cat-chips-prev]');
            const next = document.querySelector('[data-cat-chips-next]');
            const update = () => {
                if (prev) prev.disabled = chipsTrack.scrollLeft <= 4;
                if (next) next.disabled = chipsTrack.scrollLeft + chipsTrack.clientWidth >= chipsTrack.scrollWidth - 4;
            };
            if (prev) prev.addEventListener('click', () => chipsTrack.scrollBy({ left: -260, behavior: 'smooth' }));
            if (next) next.addEventListener('click', () => chipsTrack.scrollBy({ left: 260, behavior: 'smooth' }));
            chipsTrack.addEventListener('scroll', update, { passive: true });
            window.addEventListener('resize', update);
            update();
        }

        const grid = document.querySelector('[data-cat-grid]');
        document.querySelectorAll('[data-cat-view]').forEach((btn) => {
            btn.addEventListener('click', () => {
                document.querySelectorAll('[data-cat-view]').forEach((b) => b.classList.remove('is-active'));
                btn.classList.add('is-active');
                if (!grid) return;
                grid.classList.toggle('is-list', btn.getAttribute('data-cat-view') === 'list');
            });
        });

        document.querySelectorAll('[data-cat-filter-toggle]').forEach((toggle) => {
            toggle.addEventListener('click', () => {
                const target = toggle.getAttribute('data-cat-filter-toggle');
                const expanded = toggle.getAttribute('aria-expanded') === 'true';
                const panel = toggle.closest('.cat-x__panel');

                if (!panel || !target) return;

                panel.querySelectorAll(`[data-cat-filter-extra="${target}"]`).forEach((item) => {
                    item.hidden = expanded;
                });

                toggle.setAttribute('aria-expanded', expanded ? 'false' : 'true');
                toggle.textContent = expanded ? 'Xem thêm' : 'Thu gọn';
            });
        });

        try {
            const raw = localStorage.getItem('recent-products');
            const parsed = raw ? JSON.parse(raw) : [];
            if (Array.isArray(parsed) && parsed.length) {
                const wrap = document.getElementById('cat-x-recent');
                const track = wrap ? wrap.querySelector('[data-recent-track]') : null;
                if (!wrap || !track) return;

                parsed.slice(0, 12).forEach((p) => {
                    const a = document.createElement('a');
                    a.className = 'cat-x__recent-card';
                    a.href = p.url || '#';
                    a.innerHTML = `
                        <button type="button" class="like" aria-label="Yêu thích"><i class="fa fa-heart-o"></i></button>
                        <div class="img"><img src="${p.image}" alt=""></div>
                        <div class="ttl">${p.name || ''}</div>
                        <div class="pr">${p.price || ''}</div>
                        <div class="rt">
                            <span class="stars">★★★★<span class="o">★</span></span>
                            <button type="button" class="add" aria-label="Thêm vào giỏ"><i class="fa fa-shopping-cart"></i></button>
                        </div>
                    `;
                    track.appendChild(a);
                });
                wrap.style.display = '';
                const prev = wrap.querySelector('[data-recent-prev]');
                const next = wrap.querySelector('[data-recent-next]');
                if (prev) prev.addEventListener('click', () => track.scrollBy({ left: -260, behavior: 'smooth' }));
                if (next) next.addEventListener('click', () => track.scrollBy({ left: 260, behavior: 'smooth' }));
            }
        } catch (e) { /* ignore */ }
    })();
</script>

@push('scripts')
    <script>
        $(function () {
            if (!$.fn.slider || !document.getElementById('slider-range')) return;
            $('#slider-range').slider({
                range: true,
                min: {{ (int) $rangeMin }},
                max: {{ (int) max($rangeMax, $rangeMin + 1) }},
                values: [{{ (int) $selectedMin }}, {{ (int) $selectedMax }}],
                slide: function (event, ui) {
                    $('#start_price').val(ui.values[0]);
                    $('#end_price').val(ui.values[1]);
                },
                change: function (event, ui) {
                    $('#start_price').val(ui.values[0]);
                    $('#end_price').val(ui.values[1]);
                    $('#cat-x-price-form').submit();
                }
            });
        });
    </script>
@endpush
