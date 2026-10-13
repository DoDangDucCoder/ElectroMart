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
        'delete-order/*',
        'update-order-qty',
        'update-qty',
    ]);

    $productProfitActive = $matchesSidebarRoute(['product-profit']);

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

    $salesGroupActive = $orderActive || $deliveryActive || $couponActive;
    $productsGroupActive = $productActive || $productCategoryActive || $brandActive || $attributeActive || $inventoryActive;
    $customersGroupActive = $customersActive || $commentActive;
    $contentGroupActive = $postActive || $postCategoryActive || $sliderActive || $videoActive || $documentActive;
    $reportsGroupActive = $productProfitActive;
    $settingsGroupActive = $informationActive || $contactMessagesActive || $mailPreviewActive || $discoveryActive || $chatInboxActive || $usersActive;
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

                @if ($canManageOrders || $canManageCoupons)
                    <li class="{{ $sidebarItemClass($salesGroupActive, true) }}" data-admin-sidebar-group="sales">
                    <button type="button"
                        class="{{ $sidebarLinkClass($salesGroupActive, true) }}"
                        data-admin-sidebar-trigger="sales"
                        aria-expanded="{{ $salesGroupActive ? 'true' : 'false' }}"
                        aria-controls="admin-sidebar-group-sales">
                        <i class="fa fa-shopping-cart"></i>
                        <span>Bán hàng</span>
                    </button>
                    <ul class="sub" id="admin-sidebar-group-sales">
                        @if ($canManageOrders)
                            <li class="{{ $sidebarItemClass($matchesSidebarRoute(['admin/orders-management', 'manage-order', 'view-order/*', 'print-order/*'])) }}">
                                <a href="{{ URL::to('/admin/orders-management') }}" data-admin-sidebar-key="admin-orders-management" @if ($matchesSidebarRoute(['admin/orders-management', 'manage-order', 'view-order/*', 'print-order/*'])) aria-current="page" @endif>Quản lý đơn hàng</a>
                            </li>
                            <li class="{{ $sidebarItemClass($matchesSidebarRoute(['admin/transactions', 'admin/transactions/*'])) }}">
                                <a href="{{ route('admin.transactions') }}" data-admin-sidebar-key="admin-transactions" @if ($matchesSidebarRoute(['admin/transactions', 'admin/transactions/*'])) aria-current="page" @endif>Thanh toán / giao dịch</a>
                            </li>
                            <li class="{{ $sidebarItemClass($deliveryActive) }}">
                                <a href="{{ route('admin.shipping') }}" data-admin-sidebar-key="delivery" @if ($deliveryActive) aria-current="page" @endif>Vận chuyển</a>
                            </li>
                            <li class="{{ $sidebarItemClass($matchesSidebarRoute(['admin/returns', 'admin/returns/*'])) }}">
                                <a href="{{ route('admin.returns') }}" data-admin-sidebar-key="admin-returns" @if ($matchesSidebarRoute(['admin/returns', 'admin/returns/*'])) aria-current="page" @endif>Trả hàng / hoàn tiền</a>
                            </li>
                        @endif
                        @if ($canManageCoupons)
                            <li class="{{ $sidebarItemClass($matchesSidebarRoute(['list-coupon', 'edit-coupon/*'])) }}">
                                <a href="{{ route('admin.vouchers') }}" data-admin-sidebar-key="list-coupon" @if ($matchesSidebarRoute(['list-coupon', 'edit-coupon/*'])) aria-current="page" @endif>Mã giảm giá</a>
                            </li>

                        @endif
                    </ul>
                    </li>
                @endif

                @if ($canManageProducts)
                    <li class="{{ $sidebarItemClass($productsGroupActive, true) }}" data-admin-sidebar-group="products">
                    <button type="button"
                        class="{{ $sidebarLinkClass($productsGroupActive, true) }}"
                        data-admin-sidebar-trigger="products"
                        aria-expanded="{{ $productsGroupActive ? 'true' : 'false' }}"
                        aria-controls="admin-sidebar-group-products">
                        <i class="fa fa-cube"></i>
                        <span>Sản phẩm</span>
                    </button>
                    <ul class="sub" id="admin-sidebar-group-products">
                        <li class="{{ $sidebarItemClass($matchesSidebarRoute(['admin/products-management', 'all-product', 'edit-product/*'])) }}">
                            <a href="{{ URL::to('/admin/products-management') }}" data-admin-sidebar-key="admin-products-management" @if ($matchesSidebarRoute(['admin/products-management', 'all-product', 'edit-product/*'])) aria-current="page" @endif>Danh sách sản phẩm</a>
                        </li>

                        <li class="{{ $sidebarItemClass($matchesSidebarRoute(['admin/categories-management', 'all-category-product', 'edit-category-product/*', 'add-category-product'])) }}">
                            <a href="{{ URL::to('/admin/categories-management') }}" data-admin-sidebar-key="admin-categories-management" @if ($matchesSidebarRoute(['admin/categories-management', 'all-category-product', 'edit-category-product/*', 'add-category-product'])) aria-current="page" @endif>Danh mục sản phẩm</a>
                        </li>
                        <li class="{{ $sidebarItemClass($brandActive) }}">
                            <a href="{{ route('admin.brands.management') }}" data-admin-sidebar-key="all-brand-product" @if ($brandActive) aria-current="page" @endif>Thương hiệu</a>
                        </li>

                        <li class="{{ $sidebarItemClass($inventoryActive) }}">
                            <a href="{{ route('admin.inventory') }}" data-admin-sidebar-key="admin-inventory" @if ($inventoryActive) aria-current="page" @endif>Tồn kho</a>
                        </li>
                    </ul>
                    </li>
                @endif

                @if ($canManageOrders || $canManageProducts)
                    <li class="{{ $sidebarItemClass($customersGroupActive, true) }}" data-admin-sidebar-group="customers">
                    <button type="button"
                        class="{{ $sidebarLinkClass($customersGroupActive, true) }}"
                        data-admin-sidebar-trigger="customers"
                        aria-expanded="{{ $customersGroupActive ? 'true' : 'false' }}"
                        aria-controls="admin-sidebar-group-customers">
                        <i class="fa fa-users"></i>
                        <span>Khách hàng</span>
                    </button>
                    <ul class="sub" id="admin-sidebar-group-customers">
                        @if ($canManageOrders)
                            <li class="{{ $sidebarItemClass($customersActive) }}">
                                <a href="{{ route('admin.customers') }}" data-admin-sidebar-key="admin-customers" @if ($customersActive) aria-current="page" @endif>Danh sách khách hàng</a>
                            </li>
                        @endif
                        @if ($canManageProducts)
                            <li class="{{ $sidebarItemClass($matchesSidebarRoute(['comment'])) }}">
                                <a href="{{ URL::to('/comment') }}" data-admin-sidebar-key="comment" @if ($matchesSidebarRoute(['comment'])) aria-current="page" @endif>Bình luận</a>
                            </li>
                            <li class="{{ $sidebarItemClass($matchesSidebarRoute(['admin/reviews', 'admin/reviews/*'])) }}">
                                <a href="{{ route('admin.reviews') }}" data-admin-sidebar-key="admin-reviews" @if ($matchesSidebarRoute(['admin/reviews', 'admin/reviews/*'])) aria-current="page" @endif>Đánh giá</a>
                            </li>
                        @endif
                    </ul>
                    </li>
                @endif

                @if ($canManageContent || $canManageDocuments)
                    <li class="{{ $sidebarItemClass($contentGroupActive, true) }}" data-admin-sidebar-group="content">
                    <button type="button"
                        class="{{ $sidebarLinkClass($contentGroupActive, true) }}"
                        data-admin-sidebar-trigger="content"
                        aria-expanded="{{ $contentGroupActive ? 'true' : 'false' }}"
                        aria-controls="admin-sidebar-group-content">
                        <i class="fa fa-file-text"></i>
                        <span>Nội dung</span>
                    </button>
                    <ul class="sub" id="admin-sidebar-group-content">
                        @if ($canManageContent)
                            <li class="{{ $sidebarItemClass($postActive) }}">
                                <a href="{{ route('admin.posts.management') }}" data-admin-sidebar-key="all-post" @if ($postActive) aria-current="page" @endif>Bài viết</a>
                            </li>
                            <li class="{{ $sidebarItemClass($postCategoryActive) }}">
                                <a href="{{ URL::to('/all-category-post') }}" data-admin-sidebar-key="all-category-post" @if ($postCategoryActive) aria-current="page" @endif>Danh mục bài viết</a>
                            </li>

                        @endif
                        @if ($canManageDocuments && false) {{-- Hidden: accessible via direct URL --}}
                            <li class="{{ $sidebarItemClass($documentActive) }}">
                                <a href="{{ URL::to('/read_data') }}" data-admin-sidebar-key="read-data" @if ($documentActive) aria-current="page" @endif>Tài liệu</a>
                            </li>
                        @endif
                    </ul>
                    </li>
                @endif

                @if ($canManageOrders)
                    <li class="{{ $sidebarItemClass($reportsGroupActive, true) }}" data-admin-sidebar-group="reports">
                    <button type="button"
                        class="{{ $sidebarLinkClass($reportsGroupActive, true) }}"
                        data-admin-sidebar-trigger="reports"
                        aria-expanded="{{ $reportsGroupActive ? 'true' : 'false' }}"
                        aria-controls="admin-sidebar-group-reports">
                        <i class="fa fa-bar-chart"></i>
                        <span>Báo cáo</span>
                    </button>
                    <ul class="sub" id="admin-sidebar-group-reports">
                        <li class="{{ $sidebarItemClass($productProfitActive) }}">
                            <a href="{{ URL::to('/product-profit') }}" data-admin-sidebar-key="product-profit" @if ($productProfitActive) aria-current="page" @endif>Lợi nhuận sản phẩm</a>
                        </li>
                    </ul>
                    </li>
                @endif

                @if ($canManageSystem || $canManageUsers || $canManageRoles)
                    <li class="{{ $sidebarItemClass($settingsGroupActive, true) }}" data-admin-sidebar-group="settings">
                    <button type="button"
                        class="{{ $sidebarLinkClass($settingsGroupActive, true) }}"
                        data-admin-sidebar-trigger="settings"
                        aria-expanded="{{ $settingsGroupActive ? 'true' : 'false' }}"
                        aria-controls="admin-sidebar-group-settings">
                        <i class="fa fa-cog"></i>
                        <span>Cấu hình</span>
                    </button>
                    <ul class="sub" id="admin-sidebar-group-settings">
                        @if ($canManageSystem)
                            <li class="{{ $sidebarItemClass($informationActive) }}">
                                <a href="{{ URL::to('/information') }}" data-admin-sidebar-key="information" @if ($informationActive) aria-current="page" @endif>Thông tin website</a>
                            </li>
                            <li class="{{ $sidebarItemClass($contactMessagesActive) }}">
                                <a href="{{ route('admin.contact-messages.index') }}" data-admin-sidebar-key="admin-contact-messages" @if ($contactMessagesActive) aria-current="page" @endif>Liên hệ</a>
                            </li>
                            <li class="{{ $sidebarItemClass($mailPreviewActive) }}">
                                <a href="{{ URL::to('/mail-preview') }}" data-admin-sidebar-key="mail-preview" @if ($mailPreviewActive) aria-current="page" @endif>Email</a>
                            </li>
                            <li class="{{ $sidebarItemClass($discoveryActive) }}">
                                <a href="{{ URL::to('/product-discovery-report') }}" data-admin-sidebar-key="product-discovery-report" @if ($discoveryActive) aria-current="page" @endif>Tìm kiếm và gợi ý</a>
                            </li>
                            <li class="{{ $sidebarItemClass($chatInboxActive) }}">
                                <a href="{{ URL::to('/chat-inbox') }}" data-admin-sidebar-key="chat-inbox" @if ($chatInboxActive) aria-current="page" @endif>
                                    Hộp thư chat
                                    @if (($admin_chat_sidebar_stats['unread_total'] ?? 0) > 0)
                                        <span class="g-admin-sidebar-badge" data-admin-chat-unread-badge>{{ number_format($admin_chat_sidebar_stats['unread_total']) }}</span>
                                    @endif
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
            </ul>
        </div>
    </div>
</aside>
