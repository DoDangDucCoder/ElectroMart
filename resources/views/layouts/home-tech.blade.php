<!DOCTYPE html>
<html lang="vi">

@include('partials.storefront.head')

<body class="g-home tech-home">
    @include('partials.storefront.header')

    @include('partials.storefront.support-dock')

    @yield('content')

    @include('partials.storefront.compare-modal')
    @include('partials.storefront.chatbot-widget')

    @include('partials.storefront.scripts-stack')

    <script>
        (function () {
            const counters = document.querySelectorAll('[data-tech-countdown]');

            counters.forEach((counter) => {
                const end = new Date(Date.now() + 6 * 60 * 60 * 1000 + 24 * 60 * 1000 + 12 * 1000);
                const boxes = counter.querySelectorAll('[data-countdown-unit]');

                const render = () => {
                    const distance = Math.max(0, end.getTime() - Date.now());
                    const hours = Math.floor(distance / (1000 * 60 * 60));
                    const minutes = Math.floor((distance / (1000 * 60)) % 60);
                    const seconds = Math.floor((distance / 1000) % 60);
                    const values = { hours, minutes, seconds };

                    boxes.forEach((box) => {
                        const unit = box.getAttribute('data-countdown-unit');
                        box.textContent = String(values[unit] || 0).padStart(2, '0');
                    });
                };

                render();
                window.setInterval(render, 1000);
            });

            function readWishlist() {
                if (typeof window.readStorefrontStorage === 'function') {
                    return window.readStorefrontStorage('data');
                }

                try {
                    const parsed = JSON.parse(localStorage.getItem('data') || '[]');
                    return Array.isArray(parsed) ? parsed : [];
                } catch (error) {
                    return [];
                }
            }

            function writeWishlist(items) {
                if (typeof window.writeStorefrontStorage === 'function') {
                    window.writeStorefrontStorage('data', items);
                    return;
                }

                localStorage.setItem('data', JSON.stringify(items));
            }

            function syncWishlistButtons() {
                const savedIds = new Set(readWishlist().map((item) => String(item.id)));

                document.querySelectorAll('[data-tech-wishlist]').forEach((button) => {
                    const productId = String(button.getAttribute('data-product-id') || '');
                    const isActive = savedIds.has(productId);
                    const icon = button.querySelector('i');

                    button.classList.toggle('is-active', isActive);
                    button.setAttribute('aria-pressed', isActive ? 'true' : 'false');

                    if (icon) {
                        icon.classList.toggle('fa-heart', isActive);
                        icon.classList.toggle('fa-heart-o', !isActive);
                    }
                });
            }

            function toggleWishlist(button) {
                const productId = String(button.getAttribute('data-product-id') || '');

                if (!productId) {
                    return;
                }

                const product = {
                    id: productId,
                    name: button.getAttribute('data-product-name') || '',
                    price: button.getAttribute('data-product-price') || '',
                    image: button.getAttribute('data-product-image') || '',
                    url: button.getAttribute('data-product-url') || ''
                };
                let items = readWishlist();
                const exists = items.some((item) => String(item.id) === productId);

                items = exists
                    ? items.filter((item) => String(item.id) !== productId)
                    : items.concat(product);

                writeWishlist(items);

                if (typeof window.view === 'function') {
                    window.view();
                }

                syncWishlistButtons();
            }

            function initFlashSaleControls() {
                document.querySelectorAll('[data-tech-sale-viewport]').forEach((viewport) => {
                    const track = viewport.querySelector('[data-tech-sale-track]');
                    const prev = viewport.querySelector('[data-tech-sale-prev]');
                    const next = viewport.querySelector('[data-tech-sale-next]');

                    if (!track || !prev || !next) {
                        return;
                    }

                    const updateState = () => {
                        const hasOverflow = track.scrollWidth > track.clientWidth + 4;
                        const hasMoreLeft = track.scrollLeft > 4;
                        const hasMoreRight = track.scrollLeft + track.clientWidth < track.scrollWidth - 4;

                        prev.disabled = !hasOverflow || !hasMoreLeft;
                        next.disabled = !hasOverflow || !hasMoreRight;
                    };

                    prev.addEventListener('click', () => {
                        track.scrollBy({
                            left: -Math.max(track.clientWidth - 80, 260),
                            behavior: 'smooth'
                        });
                    });

                    next.addEventListener('click', () => {
                        track.scrollBy({
                            left: Math.max(track.clientWidth - 80, 260),
                            behavior: 'smooth'
                        });
                    });

                    track.addEventListener('scroll', updateState, { passive: true });
                    window.addEventListener('resize', updateState);
                    updateState();
                });
            }

            document.addEventListener('click', (event) => {
                const wishlistButton = event.target.closest('[data-tech-wishlist]');

                if (!wishlistButton) {
                    return;
                }

                event.preventDefault();
                event.stopPropagation();
                toggleWishlist(wishlistButton);
            });

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', () => {
                    syncWishlistButtons();
                    initFlashSaleControls();
                });
            } else {
                syncWishlistButtons();
                initFlashSaleControls();
            }
        })();
    </script>
</body>

</html>
