<!DOCTYPE html>
<html lang="vi">

@include('partials.storefront.head')

@php
    $bodyClass = trim($__env->yieldContent('body_class', 'g-home tech-home'));
    $isMinimal = str_contains($bodyClass, 'minimal-layout');
@endphp

<body class="{{ $bodyClass }}">
    @if (!$isMinimal)
        @include('partials.storefront.header')
        @include('partials.storefront.support-dock')
    @endif

    @if ($errors->any())
        <section class="container" style="margin-top: 16px;">
            <div class="alert alert-danger">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        </section>
    @endif

    @yield('content')

    @if (!$isMinimal)
        @include('partials.home.footer', ['paymentItems' => $paymentItems ?? []])
        @include('partials.storefront.compare-modal')
        @include('partials.storefront.chatbot-widget')
    @endif

    @include('partials.storefront.scripts-stack')

    <script>
        (function () {
            const counters = document.querySelectorAll('[data-tech-countdown]');
            const flashSaleDeadlineKey = 'dangduc_flash_sale_deadline_v1';
            const flashSaleDefaultDuration = 24 * 60 * 60 * 1000;

            function readFlashSaleDeadline(counter) {
                const configuredDeadline = counter.getAttribute('data-deadline');

                if (configuredDeadline) {
                    const configuredTime = new Date(configuredDeadline).getTime();

                    if (!Number.isNaN(configuredTime)) {
                        return configuredTime;
                    }
                }

                try {
                    const storedDeadline = Number(localStorage.getItem(flashSaleDeadlineKey));

                    if (storedDeadline > Date.now()) {
                        return storedDeadline;
                    }

                    if (storedDeadline && storedDeadline <= Date.now()) {
                        return storedDeadline;
                    }

                    const nextDeadline = Date.now() + flashSaleDefaultDuration;
                    localStorage.setItem(flashSaleDeadlineKey, String(nextDeadline));

                    return nextDeadline;
                } catch (error) {
                    return Date.now() + flashSaleDefaultDuration;
                }
            }

            counters.forEach((counter) => {
                // Reset demo campaigns by deleting localStorage key dangduc_flash_sale_deadline_v1 or bumping the key version.
                const endTime = readFlashSaleDeadline(counter);
                const boxes = counter.querySelectorAll('[data-countdown-unit]');

                const render = () => {
                    const distance = Math.max(0, endTime - Date.now());
                    const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                    const hours = Math.floor((distance / (1000 * 60 * 60)) % 24);
                    const minutes = Math.floor((distance / (1000 * 60)) % 60);
                    const seconds = Math.floor((distance / 1000) % 60);
                    const values = { days, hours, minutes, seconds };

                    boxes.forEach((box) => {
                        const unit = box.getAttribute('data-countdown-unit');
                        box.textContent = String(values[unit] || 0).padStart(2, '0');
                    });

                    counter.classList.toggle('is-ended', distance <= 0);
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
                    const shell = viewport.parentElement;
                    const dotsContainer = shell ? shell.querySelector('[data-tech-sale-dots]') : null;
                    const dots = dotsContainer ? dotsContainer.querySelectorAll('.tech-sale-dot') : [];

                    if (!track || !prev || !next) {
                        return;
                    }

                    let isDown = false;
                    let startX = 0;
                    let scrollStart = 0;
                    let moved = false;
                    let suppressClick = false;
                    const dragThreshold = 8;
                    const getMaxScroll = () => Math.max(track.scrollWidth - track.clientWidth, 0);
                    const getStep = () => Math.max(track.clientWidth - 80, 260);
                    const scrollToPosition = (left) => {
                        track.scrollTo({
                            left,
                            behavior: 'smooth'
                        });
                    };

                    const updateState = () => {
                        const hasOverflow = track.scrollWidth > track.clientWidth + 4;

                        prev.disabled = !hasOverflow;
                        next.disabled = !hasOverflow;

                        if (dots.length) {
                            const maxScroll = Math.max(getMaxScroll(), 1);
                            const ratio = Math.min(Math.max(track.scrollLeft / maxScroll, 0), 1);
                            const activeIndex = hasOverflow
                                ? Math.min(dots.length - 1, Math.round(ratio * (dots.length - 1)))
                                : 0;
                            dots.forEach((dot, idx) => {
                                dot.classList.toggle('is-active', idx === activeIndex);
                            });
                        }
                    };

                    track.addEventListener('mousedown', (event) => {
                        if (event.button !== 0) return;
                        isDown = true;
                        moved = false;
                        startX = event.pageX - track.offsetLeft;
                        scrollStart = track.scrollLeft;
                        track.classList.add('is-dragging');
                    });

                    window.addEventListener('mousemove', (event) => {
                        if (!isDown) return;

                        const x = event.pageX - track.offsetLeft;
                        const walk = x - startX;

                        if (Math.abs(walk) > dragThreshold) {
                            moved = true;
                        }

                        if (moved) {
                            event.preventDefault();
                            track.scrollLeft = scrollStart - walk;
                        }
                    });

                    const endDrag = () => {
                        if (!isDown) return;
                        isDown = false;
                        suppressClick = moved;
                        track.classList.remove('is-dragging');
                    };

                    window.addEventListener('mouseup', endDrag);
                    track.addEventListener('mouseleave', endDrag);

                    track.addEventListener('click', (event) => {
                        if (!suppressClick) {
                            moved = false;
                            return;
                        }

                        event.preventDefault();
                        event.stopPropagation();
                        suppressClick = false;
                        moved = false;
                    }, true);

                    prev.addEventListener('click', () => {
                        const maxScroll = getMaxScroll();

                        if (track.scrollLeft <= 4) {
                            scrollToPosition(maxScroll);
                            return;
                        }

                        track.scrollBy({
                            left: -getStep(),
                            behavior: 'smooth'
                        });
                    });

                    next.addEventListener('click', () => {
                        const maxScroll = getMaxScroll();

                        if (track.scrollLeft >= maxScroll - 4) {
                            scrollToPosition(0);
                            return;
                        }

                        track.scrollBy({
                            left: getStep(),
                            behavior: 'smooth'
                        });
                    });

                    track.addEventListener('scroll', updateState, { passive: true });
                    window.addEventListener('resize', updateState);
                    updateState();
                });
            }

            document.addEventListener('click', (event) => {
                const card = event.target.closest('[data-tech-sale-track] .tech-product-card[data-product-url]');

                if (!card || event.target.closest('a, button, input, select, textarea, label')) {
                    return;
                }

                const productUrl = card.getAttribute('data-product-url');

                if (productUrl && productUrl !== '#') {
                    window.location.href = productUrl;
                }
            });

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
