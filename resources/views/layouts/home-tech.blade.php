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
            function wishlistItemId(item) {
                if (!item) {
                    return '';
                }

                return String(item.id ?? item.product_id ?? item.productId ?? '').trim();
            }

            function readWishlist() {
                let items = [];

                if (typeof window.readStorefrontStorage === 'function') {
                    items = window.readStorefrontStorage('data');
                } else {
                    try {
                        const parsed = JSON.parse(localStorage.getItem('data') || '[]');
                        items = Array.isArray(parsed) ? parsed : [];
                    } catch (error) {
                        items = [];
                    }
                }

                const seen = new Set();

                return items.filter((item) => {
                    const id = wishlistItemId(item);

                    if (!id || seen.has(id)) {
                        return false;
                    }

                    seen.add(id);
                    item.id = id;

                    return true;
                });
            }

            function writeWishlist(items) {
                if (typeof window.writeStorefrontStorage === 'function') {
                    window.writeStorefrontStorage('data', items);
                    return;
                }

                localStorage.setItem('data', JSON.stringify(items));
            }

            function syncWishlistButtons() {
                const savedIds = new Set(readWishlist().map(wishlistItemId));

                document.querySelectorAll('[data-tech-wishlist]').forEach((button) => {
                    const productId = String(button.getAttribute('data-product-id') || '').trim();
                    const isActive = savedIds.has(productId);
                    const icon = button.querySelector('i');

                    button.classList.toggle('is-active', isActive);
                    button.setAttribute('aria-pressed', isActive ? 'true' : 'false');

                    if (icon) {
                        icon.classList.remove('fa-heart');
                        icon.classList.add('fa-heart-o');
                    }
                });
            }

            function toggleWishlist(button) {
                const productId = String(button.getAttribute('data-product-id') || '').trim();

                if (!productId) {
                    return;
                }

                const product = {
                    id: productId,
                    name: button.getAttribute('data-product-name') || '',
                    price: button.getAttribute('data-product-price') || '',
                    image: button.getAttribute('data-product-image') || '',
                    url: button.getAttribute('data-product-url') || '',
                    category_id: button.getAttribute('data-product-category-id') || '',
                    category: button.getAttribute('data-product-category') || ''
                };
                let items = readWishlist();
                const exists = items.some((item) => wishlistItemId(item) === productId);

                items = exists
                    ? items.filter((item) => wishlistItemId(item) !== productId)
                    : items.concat(product);

                writeWishlist(items);

                if (typeof window.view === 'function') {
                    window.view();
                }

                syncWishlistButtons();
            }

            window.syncStorefrontWishlistButtons = syncWishlistButtons;
            window.addEventListener('storage', (event) => {
                if (event.key === 'data') {
                    syncWishlistButtons();
                }
            });
            window.addEventListener('storefront:wishlist-updated', syncWishlistButtons);

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
                event.stopImmediatePropagation();
                toggleWishlist(wishlistButton);
            }, true);

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
