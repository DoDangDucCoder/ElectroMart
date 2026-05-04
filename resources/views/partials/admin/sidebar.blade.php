@php
    use App\Support\AdminPermissions;

    $adminSidebarUser = auth('web')->user();

    $matchesSidebarRoute = static function (array $patterns): bool {
        foreach ($patterns as $pattern) {
            $normalized = trim($pattern, '/');

            if ($normalized !== '' && request()->is($normalized)) {
                return true;
            }
        }

        return false;
    };

    $sidebarItemClass = static function (bool $active, bool $hasChildren = false): string {
        $classes = ['g-admin-sidebar-item'];

        if ($hasChildren) {
            $classes[] = 'sub-menu';
            $classes[] = 'g-admin-sidebar-group';
        }

        if ($active) {
            $classes[] = 'active';
            $classes[] = 'is-active';
        }

        if ($hasChildren && $active) {
            $classes[] = 'dcjq-current-parent';
            $classes[] = 'is-open';
        }

        return implode(' ', $classes);
    };

    $sidebarLinkClass = static function (bool $active, bool $isParent = false): string {
        $classes = ['g-admin-sidebar-link'];

        if ($isParent) {
            $classes[] = 'g-admin-sidebar-parent-link';
        }

        if ($active) {
            $classes[] = 'active';
            $classes[] = 'is-active';
        }

        return implode(' ', $classes);
    };

    $dashboardActive = $matchesSidebarRoute(['dashboard']);
    $informationActive = $matchesSidebarRoute(['information']);
    $contactMessagesActive = $matchesSidebarRoute(['admin/contact-messages', 'admin/contact-messages/*']);
    $mailPreviewActive = $matchesSidebarRoute(['mail-preview', 'mail-preview/*']);
    $discoveryActive = $matchesSidebarRoute(['product-discovery-report']);
    $chatInboxActive = $matchesSidebarRoute(['chat-inbox', 'chat-inbox/*']);
    $customersActive = $matchesSidebarRoute(['admin/customers', 'admin/customers/*']);
    $inventoryActive = $matchesSidebarRoute(['admin/inventory']);

    $couponActive = $matchesSidebarRoute([
        'insert-coupon',
        'edit-coupon/*',
        'update-coupon/*',
        'list-coupon',
        'delete-coupon/*',
        'insert-coupon-code',
        'send-coupon/*',
        'send-coupon-vip/*',
    ]);

    $deliveryActive = $matchesSidebarRoute([
        'delivery',
        'select-delivery',
        'insert-delivery',
        'select-feeship',
        'update-delivery',
    ]);

    $productCategoryActive = $matchesSidebarRoute([
        'admin/categories-management',
        'add-category-product',
        'all-category-product',
        'edit-category-product/*',
    ]);

    $brandActive = $matchesSidebarRoute([
        'add-brand-product',
        'all-brand-product',
        'edit-brand-product/*',
    ]);

    $attributeActive = $matchesSidebarRoute([
        'attribute',
        'attribute/*',
        'product-attribute/*',
    ]);

    $postCategoryActive = $matchesSidebarRoute([
        'add-category-post',
        'all-category-post',
        'edit-category-post/*',
    ]);

    $sliderActive = $matchesSidebarRoute([
        'manage-slider',
        'add-slider',
        'edit-slider/*',
    ]);

    $orderActive = $matchesSidebarRoute([
        'admin/orders-management',
        'admin/returns',
        'admin/returns/*',
        'admin/transactions',
        'admin/transactions/*',
        'manage-order',
        'view-order/*',
        'print-order/*',
        'product-profit',
        'delete-order/*',
        'update-order-qty',
        'update-qty',
    ]);

    $commentActive = $matchesSidebarRoute([
        'admin/reviews',
        'admin/reviews/*',
        'comment',
        'allow-comment',
        'reply-comment',
    ]);

    $productActive = $matchesSidebarRoute([
        'admin/products-management',
        'add-product',
        'all-product',
        'edit-product/*',
    ]);

    $postActive = $matchesSidebarRoute([
        'add-post',
        'all-post',
        'edit-post/*',
        'delete-post/*',
    ]);

    $videoActive = $matchesSidebarRoute([
        'video',
        'select-video',
        'insert-video',
        'update-video',
        'delete-video',
        'update-video-image',
    ]);

    $usersActive = $matchesSidebarRoute([
        'add-users',
        'users',
        'index-spatie',
        'create-spatie/*',
        'create-permission/*',
        'assign-role/*',
        'assign-permission/*',
        'store-users',
        'assign-roles',
        'delete-user-roles/*',
    ]);

    $documentActive = $matchesSidebarRoute([
        'read_data',
        'download_document/*',
        'delete_document/*',
    ]);

    $canManageSystem = $adminSidebarUser?->can(AdminPermissions::MANAGE_SYSTEM) ?? false;
    $canManageCoupons = $adminSidebarUser?->can(AdminPermissions::MANAGE_COUPONS) ?? false;
    $canManageOrders = $adminSidebarUser?->can(AdminPermissions::MANAGE_ORDERS) ?? false;
    $canManageProducts = $adminSidebarUser?->can(AdminPermissions::MANAGE_PRODUCTS) ?? false;
    $canManageContent = $adminSidebarUser?->can(AdminPermissions::MANAGE_CONTENT) ?? false;
    $canManageUsers = $adminSidebarUser?->can(AdminPermissions::MANAGE_USERS) ?? false;
    $canManageRoles = $adminSidebarUser?->can(AdminPermissions::MANAGE_ROLES) ?? false;
    $canManageDocuments = $adminSidebarUser?->can(AdminPermissions::MANAGE_DOCUMENTS) ?? false;
@endphp

{{-- Admin sidebar navigation extracted from admin_layout.blade.php. --}}
<!--sidebar start-->
<aside>
    <div id="sidebar" class="nav-collapse" data-admin-sidebar>
        <div class="leftside-navigation">
            <ul class="sidebar-menu" id="nav-accordion" data-admin-sidebar-nav>
                <li class="{{ $sidebarItemClass($dashboardActive) }}">
                    <a class="{{ $sidebarLinkClass($dashboardActive) }}"
                        href="{{ URL::to('/dashboard') }}"
                        data-admin-sidebar-key="dashboard"
                        @if ($dashboardActive) aria-current="page" @endif>
                        <i class="fa fa-dashboard"></i>
                        <span>Tổng quan</span>
                    </a>
                </li>

                @if ($canManageSystem)
                    <li class="{{ $sidebarItemClass($informationActive) }}">
                        <a class="{{ $sidebarLinkClass($informationActive) }}"
                            href="{{ URL::to('/information') }}"
                            data-admin-sidebar-key="information"
                            @if ($informationActive) aria-current="page" @endif>
                            <i class="fa fa-info-circle"></i>
                            <span>Thông tin website</span>
                        </a>
                    </li>

                    <li class="{{ $sidebarItemClass($contactMessagesActive) }}">
                        <a class="{{ $sidebarLinkClass($contactMessagesActive) }}"
                            href="{{ route('admin.contact-messages.index') }}"
                            data-admin-sidebar-key="admin-contact-messages"
                            @if ($contactMessagesActive) aria-current="page" @endif>
                            <i class="fa fa-inbox"></i>
                            <span>Liên hệ khách hàng</span>
                        </a>
                    </li>

                    <li class="{{ $sidebarItemClass($mailPreviewActive) }}">
                        <a class="{{ $sidebarLinkClass($mailPreviewActive) }}"
                            href="{{ URL::to('/mail-preview') }}"
                            data-admin-sidebar-key="mail-preview"
                            @if ($mailPreviewActive) aria-current="page" @endif>
                            <i class="fa fa-envelope-o"></i>
                            <span>Xem trước email</span>
                        </a>
                    </li>

                    <li class="{{ $sidebarItemClass($discoveryActive) }}">
                        <a class="{{ $sidebarLinkClass($discoveryActive) }}"
                            href="{{ URL::to('/product-discovery-report') }}"
                            data-admin-sidebar-key="product-discovery-report"
                            @if ($discoveryActive) aria-current="page" @endif>
                            <i class="fa fa-line-chart"></i>
                            <span>Tìm kiếm và gợi ý</span>
                        </a>
                    </li>

                    <li class="{{ $sidebarItemClass($chatInboxActive) }}">
                        <a class="{{ $sidebarLinkClass($chatInboxActive) }}"
                            href="{{ URL::to('/chat-inbox') }}"
                            data-admin-sidebar-key="chat-inbox"
                            @if ($chatInboxActive) aria-current="page" @endif>
                            <i class="fa fa-comments-o"></i>
                            <span>Hộp thư chat</span>
                            @if (($admin_chat_sidebar_stats['unread_total'] ?? 0) > 0)
                                <span class="g-admin-sidebar-badge" data-admin-chat-unread-badge>
                                    {{ number_format($admin_chat_sidebar_stats['unread_total']) }}
                                </span>
                            @endif
                        </a>
                    </li>
                @endif

                @if ($canManageOrders)
                    <li class="{{ $sidebarItemClass($customersActive) }}">
                        <a class="{{ $sidebarLinkClass($customersActive) }}"
                            href="{{ route('admin.customers') }}"
                            data-admin-sidebar-key="admin-customers"
                            @if ($customersActive) aria-current="page" @endif>
                            <i class="fa fa-address-book-o"></i>
                            <span>Khách hàng</span>
                        </a>
                    </li>
                @endif

                @if ($canManageCoupons)
                    <li class="{{ $sidebarItemClass($couponActive, true) }}" data-admin-sidebar-group="coupons">
                    <button type="button"
                        class="{{ $sidebarLinkClass($couponActive, true) }}"
                        data-admin-sidebar-trigger="coupons"
                        aria-expanded="{{ $couponActive ? 'true' : 'false' }}"
                        aria-controls="admin-sidebar-group-coupons">
                        <i class="fa fa-tags"></i>
                        <span>Mã giảm giá</span>
                    </button>
                    <ul class="sub" id="admin-sidebar-group-coupons">
                        <li class="{{ $sidebarItemClass($matchesSidebarRoute(['insert-coupon'])) }}">
                            <a href="{{ URL::to('/insert-coupon') }}"
                                data-admin-sidebar-key="insert-coupon"
                                @if ($matchesSidebarRoute(['insert-coupon'])) aria-current="page" @endif>
                                Thêm mã giảm giá
                            </a>
                        </li>
                        <li class="{{ $sidebarItemClass($matchesSidebarRoute(['list-coupon', 'edit-coupon/*'])) }}">
                            <a href="{{ URL::to('/list-coupon') }}"
                                data-admin-sidebar-key="list-coupon"
                                @if ($matchesSidebarRoute(['list-coupon', 'edit-coupon/*'])) aria-current="page" @endif>
                                Liệt kê mã giảm giá
                            </a>
                        </li>
                    </ul>
                    </li>
                @endif

                @if ($canManageOrders)
                    <li class="{{ $sidebarItemClass($deliveryActive, true) }}" data-admin-sidebar-group="delivery">
                    <button type="button"
                        class="{{ $sidebarLinkClass($deliveryActive, true) }}"
                        data-admin-sidebar-trigger="delivery"
                        aria-expanded="{{ $deliveryActive ? 'true' : 'false' }}"
                        aria-controls="admin-sidebar-group-delivery">
                        <i class="fa fa-truck"></i>
                        <span>Vận chuyển</span>
                    </button>
                    <ul class="sub" id="admin-sidebar-group-delivery">
                        <li class="{{ $sidebarItemClass($matchesSidebarRoute(['delivery'])) }}">
                            <a href="{{ URL::to('/delivery') }}"
                                data-admin-sidebar-key="delivery"
                                @if ($matchesSidebarRoute(['delivery'])) aria-current="page" @endif>
                                Quản lý vận chuyển
                            </a>
                        </li>
                    </ul>
                    </li>
                @endif

                @if ($canManageProducts)
                    <li class="{{ $sidebarItemClass($inventoryActive) }}">
                        <a class="{{ $sidebarLinkClass($inventoryActive) }}"
                            href="{{ route('admin.inventory') }}"
                            data-admin-sidebar-key="admin-inventory"
                            @if ($inventoryActive) aria-current="page" @endif>
                            <i class="fa fa-archive"></i>
                            <span>Tồn kho</span>
                        </a>
                    </li>

                    <li class="{{ $sidebarItemClass($productCategoryActive, true) }}" data-admin-sidebar-group="product-categories">
                    <button type="button"
                        class="{{ $sidebarLinkClass($productCategoryActive, true) }}"
                        data-admin-sidebar-trigger="product-categories"
                        aria-expanded="{{ $productCategoryActive ? 'true' : 'false' }}"
                        aria-controls="admin-sidebar-group-product-categories">
                        <i class="fa fa-list"></i>
                        <span>Danh mục sản phẩm</span>
                    </button>
                    <ul class="sub" id="admin-sidebar-group-product-categories">
                        <li class="{{ $sidebarItemClass($matchesSidebarRoute(['add-category-product'])) }}">
                            <a href="{{ URL::to('/add-category-product') }}"
                                data-admin-sidebar-key="add-category-product"
                                @if ($matchesSidebarRoute(['add-category-product'])) aria-current="page" @endif>
                                Thêm danh mục sản phẩm
                            </a>
                        </li>
                        <li class="{{ $sidebarItemClass($matchesSidebarRoute(['admin/categories-management', 'all-category-product', 'edit-category-product/*'])) }}">
                            <a href="{{ URL::to('/admin/categories-management') }}"
                                data-admin-sidebar-key="admin-categories-management"
                                @if ($matchesSidebarRoute(['admin/categories-management', 'all-category-product', 'edit-category-product/*'])) aria-current="page" @endif>
                                Liệt kê danh mục sản phẩm
                            </a>
                        </li>
                    </ul>
                    </li>

                    <li class="{{ $sidebarItemClass($brandActive, true) }}" data-admin-sidebar-group="brands">
                    <button type="button"
                        class="{{ $sidebarLinkClass($brandActive, true) }}"
                        data-admin-sidebar-trigger="brands"
                        aria-expanded="{{ $brandActive ? 'true' : 'false' }}"
                        aria-controls="admin-sidebar-group-brands">
                        <i class="fa fa-industry"></i>
                        <span>Thương hiệu</span>
                    </button>
                    <ul class="sub" id="admin-sidebar-group-brands">
                        <li class="{{ $sidebarItemClass($matchesSidebarRoute(['add-brand-product'])) }}">
                            <a href="{{ URL::to('/add-brand-product') }}"
                                data-admin-sidebar-key="add-brand-product"
                                @if ($matchesSidebarRoute(['add-brand-product'])) aria-current="page" @endif>
                                Thêm thương hiệu
                            </a>
                        </li>
                        <li class="{{ $sidebarItemClass($matchesSidebarRoute(['all-brand-product', 'edit-brand-product/*'])) }}">
                            <a href="{{ URL::to('/all-brand-product') }}"
                                data-admin-sidebar-key="all-brand-product"
                                @if ($matchesSidebarRoute(['all-brand-product', 'edit-brand-product/*'])) aria-current="page" @endif>
                                Liệt kê thương hiệu
                            </a>
                        </li>
                    </ul>
                    </li>

                    <li class="{{ $sidebarItemClass($attributeActive, true) }}" data-admin-sidebar-group="attributes">
                    <button type="button"
                        class="{{ $sidebarLinkClass($attributeActive, true) }}"
                        data-admin-sidebar-trigger="attributes"
                        aria-expanded="{{ $attributeActive ? 'true' : 'false' }}"
                        aria-controls="admin-sidebar-group-attributes">
                        <i class="fa fa-cogs"></i>
                        <span>Thuộc tính</span>
                    </button>
                    <ul class="sub" id="admin-sidebar-group-attributes">
                        <li class="{{ $sidebarItemClass($matchesSidebarRoute(['attribute/create'])) }}">
                            <a href="{{ route('attribute.create') }}"
                                data-admin-sidebar-key="attribute-create"
                                @if ($matchesSidebarRoute(['attribute/create'])) aria-current="page" @endif>
                                Thêm thuộc tính
                            </a>
                        </li>
                        <li class="{{ $sidebarItemClass($matchesSidebarRoute(['attribute', 'attribute/*', 'product-attribute/*']) && ! $matchesSidebarRoute(['attribute/create'])) }}">
                            <a href="{{ route('attribute.index') }}"
                                data-admin-sidebar-key="attribute-index"
                                @if ($matchesSidebarRoute(['attribute', 'attribute/*', 'product-attribute/*']) && ! $matchesSidebarRoute(['attribute/create'])) aria-current="page" @endif>
                                Liệt kê thuộc tính
                            </a>
                        </li>
                    </ul>
                    </li>
                @endif

                @if ($canManageContent)
                    <li class="{{ $sidebarItemClass($postCategoryActive, true) }}" data-admin-sidebar-group="blog-categories">
                    <button type="button"
                        class="{{ $sidebarLinkClass($postCategoryActive, true) }}"
                        data-admin-sidebar-trigger="blog-categories"
                        aria-expanded="{{ $postCategoryActive ? 'true' : 'false' }}"
                        aria-controls="admin-sidebar-group-blog-categories">
                        <i class="fa fa-edit"></i>
                        <span>Danh mục bài viết</span>
                    </button>
                    <ul class="sub" id="admin-sidebar-group-blog-categories">
                        <li class="{{ $sidebarItemClass($matchesSidebarRoute(['add-category-post'])) }}">
                            <a href="{{ URL::to('/add-category-post') }}"
                                data-admin-sidebar-key="add-category-post"
                                @if ($matchesSidebarRoute(['add-category-post'])) aria-current="page" @endif>
                                Thêm danh mục bài viết
                            </a>
                        </li>
                        <li class="{{ $sidebarItemClass($matchesSidebarRoute(['all-category-post', 'edit-category-post/*'])) }}">
                            <a href="{{ URL::to('/all-category-post') }}"
                                data-admin-sidebar-key="all-category-post"
                                @if ($matchesSidebarRoute(['all-category-post', 'edit-category-post/*'])) aria-current="page" @endif>
                                Liệt kê danh mục bài viết
                            </a>
                        </li>
                    </ul>
                    </li>

                    <li class="{{ $sidebarItemClass($sliderActive, true) }}" data-admin-sidebar-group="sliders">
                    <button type="button"
                        class="{{ $sidebarLinkClass($sliderActive, true) }}"
                        data-admin-sidebar-trigger="sliders"
                        aria-expanded="{{ $sliderActive ? 'true' : 'false' }}"
                        aria-controls="admin-sidebar-group-sliders">
                        <i class="fa fa-sliders"></i>
                        <span>Trình chiếu</span>
                    </button>
                    <ul class="sub" id="admin-sidebar-group-sliders">
                        <li class="{{ $sidebarItemClass($matchesSidebarRoute(['manage-slider'])) }}">
                            <a href="{{ URL::to('/manage-slider') }}"
                                data-admin-sidebar-key="manage-slider"
                                @if ($matchesSidebarRoute(['manage-slider'])) aria-current="page" @endif>
                                Danh sách trình chiếu
                            </a>
                        </li>
                        <li class="{{ $sidebarItemClass($matchesSidebarRoute(['add-slider', 'edit-slider/*'])) }}">
                            <a href="{{ URL::to('/add-slider') }}"
                                data-admin-sidebar-key="add-slider"
                                @if ($matchesSidebarRoute(['add-slider', 'edit-slider/*'])) aria-current="page" @endif>
                                Thêm trình chiếu
                            </a>
                        </li>
                    </ul>
                    </li>
                @endif

                @if ($canManageOrders)
                    <li class="{{ $sidebarItemClass($orderActive, true) }}" data-admin-sidebar-group="orders">
                    <button type="button"
                        class="{{ $sidebarLinkClass($orderActive, true) }}"
                        data-admin-sidebar-trigger="orders"
                        aria-expanded="{{ $orderActive ? 'true' : 'false' }}"
                        aria-controls="admin-sidebar-group-orders">
                        <i class="fa fa-shopping-cart"></i>
                        <span>Đơn hàng</span>
                    </button>
                    <ul class="sub" id="admin-sidebar-group-orders">
                        <li class="{{ $sidebarItemClass($matchesSidebarRoute(['admin/orders-management', 'manage-order', 'view-order/*', 'print-order/*'])) }}">
                            <a href="{{ URL::to('/admin/orders-management') }}"
                                data-admin-sidebar-key="admin-orders-management"
                                @if ($matchesSidebarRoute(['admin/orders-management', 'manage-order', 'view-order/*', 'print-order/*'])) aria-current="page" @endif>
                                Quản lý đơn hàng
                            </a>
                        </li>
                        <li class="{{ $sidebarItemClass($matchesSidebarRoute(['product-profit'])) }}">
                            <a href="{{ URL::to('/product-profit') }}"
                                data-admin-sidebar-key="product-profit"
                                @if ($matchesSidebarRoute(['product-profit'])) aria-current="page" @endif>
                                Lợi nhuận sản phẩm
                            </a>
                        </li>
                        <li class="{{ $sidebarItemClass($matchesSidebarRoute(['admin/returns', 'admin/returns/*'])) }}">
                            <a href="{{ route('admin.returns') }}"
                                data-admin-sidebar-key="admin-returns"
                                @if ($matchesSidebarRoute(['admin/returns', 'admin/returns/*'])) aria-current="page" @endif>
                                Trả hàng / hoàn tiền
                            </a>
                        </li>
                        <li class="{{ $sidebarItemClass($matchesSidebarRoute(['admin/transactions', 'admin/transactions/*'])) }}">
                            <a href="{{ route('admin.transactions') }}"
                                data-admin-sidebar-key="admin-transactions"
                                @if ($matchesSidebarRoute(['admin/transactions', 'admin/transactions/*'])) aria-current="page" @endif>
                                Thanh toán / giao dịch
                            </a>
                        </li>
                    </ul>
                    </li>
                @endif

                @if ($canManageProducts)
                    <li class="{{ $sidebarItemClass($commentActive, true) }}" data-admin-sidebar-group="comments">
                    <button type="button"
                        class="{{ $sidebarLinkClass($commentActive, true) }}"
                        data-admin-sidebar-trigger="comments"
                        aria-expanded="{{ $commentActive ? 'true' : 'false' }}"
                        aria-controls="admin-sidebar-group-comments">
                        <i class="fa fa-comments"></i>
                        <span>Bình luận</span>
                    </button>
                    <ul class="sub" id="admin-sidebar-group-comments">
                        <li class="{{ $sidebarItemClass($matchesSidebarRoute(['comment'])) }}">
                            <a href="{{ URL::to('/comment') }}"
                                data-admin-sidebar-key="comment"
                                @if ($matchesSidebarRoute(['comment'])) aria-current="page" @endif>
                                Liệt kê bình luận
                            </a>
                        </li>
                        <li class="{{ $sidebarItemClass($matchesSidebarRoute(['admin/reviews', 'admin/reviews/*'])) }}">
                            <a href="{{ route('admin.reviews') }}"
                                data-admin-sidebar-key="admin-reviews"
                                @if ($matchesSidebarRoute(['admin/reviews', 'admin/reviews/*'])) aria-current="page" @endif>
                                Duyệt đánh giá
                            </a>
                        </li>
                    </ul>
                    </li>

                    <li class="{{ $sidebarItemClass($productActive, true) }}" data-admin-sidebar-group="products">
                    <button type="button"
                        class="{{ $sidebarLinkClass($productActive, true) }}"
                        data-admin-sidebar-trigger="products"
                        aria-expanded="{{ $productActive ? 'true' : 'false' }}"
                        aria-controls="admin-sidebar-group-products">
                        <i class="fa fa-cube"></i>
                        <span>Sản phẩm</span>
                    </button>
                    <ul class="sub" id="admin-sidebar-group-products">
                        <li class="{{ $sidebarItemClass($matchesSidebarRoute(['add-product'])) }}">
                            <a href="{{ URL::to('/add-product') }}"
                                data-admin-sidebar-key="add-product"
                                @if ($matchesSidebarRoute(['add-product'])) aria-current="page" @endif>
                                Thêm sản phẩm
                            </a>
                        </li>
                        <li class="{{ $sidebarItemClass($matchesSidebarRoute(['admin/products-management', 'all-product', 'edit-product/*'])) }}">
                            <a href="{{ URL::to('/admin/products-management') }}"
                                data-admin-sidebar-key="admin-products-management"
                                @if ($matchesSidebarRoute(['admin/products-management', 'all-product', 'edit-product/*'])) aria-current="page" @endif>
                                Liệt kê sản phẩm
                            </a>
                        </li>
                    </ul>
                    </li>
                @endif

                @if ($canManageContent)
                    <li class="{{ $sidebarItemClass($postActive, true) }}" data-admin-sidebar-group="posts">
                    <button type="button"
                        class="{{ $sidebarLinkClass($postActive, true) }}"
                        data-admin-sidebar-trigger="posts"
                        aria-expanded="{{ $postActive ? 'true' : 'false' }}"
                        aria-controls="admin-sidebar-group-posts">
                        <i class="fa fa-file-text"></i>
                        <span>Bài viết</span>
                    </button>
                    <ul class="sub" id="admin-sidebar-group-posts">
                        <li class="{{ $sidebarItemClass($matchesSidebarRoute(['add-post'])) }}">
                            <a href="{{ URL::to('/add-post') }}"
                                data-admin-sidebar-key="add-post"
                                @if ($matchesSidebarRoute(['add-post'])) aria-current="page" @endif>
                                Thêm bài viết
                            </a>
                        </li>
                        <li class="{{ $sidebarItemClass($matchesSidebarRoute(['all-post', 'edit-post/*'])) }}">
                            <a href="{{ URL::to('/all-post') }}"
                                data-admin-sidebar-key="all-post"
                                @if ($matchesSidebarRoute(['all-post', 'edit-post/*'])) aria-current="page" @endif>
                                Liệt kê bài viết
                            </a>
                        </li>
                    </ul>
                    </li>

                    <li class="{{ $sidebarItemClass($videoActive, true) }}" data-admin-sidebar-group="videos">
                    <button type="button"
                        class="{{ $sidebarLinkClass($videoActive, true) }}"
                        data-admin-sidebar-trigger="videos"
                        aria-expanded="{{ $videoActive ? 'true' : 'false' }}"
                        aria-controls="admin-sidebar-group-videos">
                        <i class="fa fa-video-camera"></i>
                        <span>Video</span>
                    </button>
                    <ul class="sub" id="admin-sidebar-group-videos">
                        <li class="{{ $sidebarItemClass($matchesSidebarRoute(['video'])) }}">
                            <a href="{{ URL::to('video') }}"
                                data-admin-sidebar-key="video"
                                @if ($matchesSidebarRoute(['video'])) aria-current="page" @endif>
                                Thêm video
                            </a>
                        </li>
                    </ul>
                    </li>
                @endif

                @if ($canManageUsers || $canManageRoles)
                    <li class="{{ $sidebarItemClass($usersActive, true) }}" data-admin-sidebar-group="users">
                    <button type="button"
                        class="{{ $sidebarLinkClass($usersActive, true) }}"
                        data-admin-sidebar-trigger="users"
                        aria-expanded="{{ $usersActive ? 'true' : 'false' }}"
                        aria-controls="admin-sidebar-group-users">
                        <i class="fa fa-users"></i>
                        <span>Người dùng</span>
                    </button>
                    <ul class="sub" id="admin-sidebar-group-users">
                        @if ($canManageUsers)
                            <li class="{{ $sidebarItemClass($matchesSidebarRoute(['add-users'])) }}">
                                <a href="{{ URL::to('/add-users') }}"
                                    data-admin-sidebar-key="add-users"
                                    @if ($matchesSidebarRoute(['add-users'])) aria-current="page" @endif>
                                    Thêm người dùng
                                </a>
                            </li>
                        @endif
                        @if ($canManageUsers)
                            <li class="{{ $sidebarItemClass($matchesSidebarRoute(['users'])) }}">
                                <a href="{{ URL::to('/users') }}"
                                    data-admin-sidebar-key="users"
                                    @if ($matchesSidebarRoute(['users'])) aria-current="page" @endif>
                                    Danh sách người dùng
                                </a>
                            </li>
                        @endif
                        @if ($canManageRoles)
                            <li class="{{ $sidebarItemClass($matchesSidebarRoute(['index-spatie', 'create-spatie/*', 'create-permission/*'])) }}">
                                <a href="{{ URL::to('/index-spatie') }}"
                                    data-admin-sidebar-key="index-spatie"
                                    @if ($matchesSidebarRoute(['index-spatie', 'create-spatie/*', 'create-permission/*'])) aria-current="page" @endif>
                                    Vai trò và quyền
                                </a>
                            </li>
                        @endif
                    </ul>
                    </li>
                @endif

                @if ($canManageDocuments)
                    <li class="{{ $sidebarItemClass($documentActive, true) }}" data-admin-sidebar-group="documents">
                    <button type="button"
                        class="{{ $sidebarLinkClass($documentActive, true) }}"
                        data-admin-sidebar-trigger="documents"
                        aria-expanded="{{ $documentActive ? 'true' : 'false' }}"
                        aria-controls="admin-sidebar-group-documents">
                        <i class="fa fa-folder-open"></i>
                        <span>Tài liệu</span>
                    </button>
                    <ul class="sub" id="admin-sidebar-group-documents">
                        <li class="{{ $sidebarItemClass($matchesSidebarRoute(['read_data'])) }}">
                            <a href="{{ URL::to('/read_data') }}"
                                data-admin-sidebar-key="read-data"
                                @if ($matchesSidebarRoute(['read_data'])) aria-current="page" @endif>
                                Kho tài liệu
                            </a>
                        </li>
                    </ul>
                    </li>
                @endif
            </ul>
        </div>
    </div>
</aside>
