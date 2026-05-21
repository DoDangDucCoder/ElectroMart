<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

final class AdminWorkspace
{
    private const DESTINATIONS = [
        [
            'key' => 'dashboard',
            'title' => 'Tổng quan vận hành',
            'description' => 'Theo dõi doanh thu, đơn hàng, biểu đồ và tình trạng vận hành chung.',
            'section' => 'Trung tâm',
            'path' => '/dashboard',
            'icon' => 'fa-dashboard',
            'keywords' => ['dashboard', 'tong quan', 'home', 'van hanh'],
        ],
        [
            'key' => 'admin-profile',
            'title' => 'Hồ sơ quản trị',
            'description' => 'Xem và cập nhật thông tin đăng nhập của tài khoản đang sử dụng.',
            'section' => 'Tài khoản',
            'path' => '/admin/profile',
            'icon' => 'fa-user',
            'keywords' => ['ho so', 'profile', 'tai khoan', 'mat khau', 'email'],
        ],
        [
            'key' => 'admin-settings',
            'title' => 'Cài đặt quản trị',
            'description' => 'Đi tới các màn hình cấu hình và tác vụ quản trị bạn đang có quyền truy cập.',
            'section' => 'Tài khoản',
            'path' => '/admin/settings',
            'icon' => 'fa-cog',
            'keywords' => ['cai dat', 'settings', 'thiet lap', 'admin'],
        ],
        [
            'key' => 'information',
            'title' => 'Thông tin website',
            'description' => 'Cập nhật nhận diện, liên hệ, đối tác và thông tin hiển thị trên giao diện cửa hàng.',
            'section' => 'Hệ thống',
            'path' => '/information',
            'icon' => 'fa-info-circle',
            'permission' => AdminPermissions::MANAGE_SYSTEM,
            'keywords' => ['website', 'thong tin', 'branding', 'lien he', 'doi tac'],
        ],
        [
            'key' => 'mail-preview',
            'title' => 'Xem trước email',
            'description' => 'Kiểm tra các mẫu email hệ thống và chiến dịch hiện có.',
            'section' => 'Hệ thống',
            'path' => '/mail-preview',
            'icon' => 'fa-envelope-o',
            'permission' => AdminPermissions::MANAGE_SYSTEM,
            'keywords' => ['mail', 'email', 'preview', 'template'],
        ],
        [
            'key' => 'product-discovery-report',
            'title' => 'Tìm kiếm và gợi ý',
            'description' => 'Báo cáo truy vấn tìm kiếm và hiệu quả gợi ý trên giao diện cửa hàng.',
            'section' => 'Hệ thống',
            'path' => '/product-discovery-report',
            'icon' => 'fa-line-chart',
            'permission' => AdminPermissions::MANAGE_SYSTEM,
            'keywords' => ['search', 'goi y', 'discovery', 'analytics'],
        ],
        [
            'key' => 'chat-inbox',
            'title' => 'Hộp thư chat',
            'description' => 'Theo dõi hội thoại khách hàng và phản hồi từ khu admin.',
            'section' => 'Hệ thống',
            'path' => '/chat-inbox',
            'icon' => 'fa-comments-o',
            'permission' => AdminPermissions::MANAGE_SYSTEM,
            'keywords' => ['chat', 'inbox', 'hoi thoai', 'khach hang'],
        ],
        [
            'key' => 'admin-products-management',
            'title' => 'Quản lý sản phẩm',
            'description' => 'Xem danh sách sản phẩm, trạng thái hiển thị và tồn kho.',
            'section' => 'Sản phẩm',
            'path' => '/admin/products-management',
            'icon' => 'fa-cube',
            'permission' => AdminPermissions::MANAGE_PRODUCTS,
            'keywords' => ['san pham', 'product', 'danh sach', 'ton kho'],
        ],
        [
            'key' => 'add-product',
            'title' => 'Thêm sản phẩm',
            'description' => 'Tạo sản phẩm mới, giá bán, hình ảnh và danh mục liên quan.',
            'section' => 'Sản phẩm',
            'path' => '/add-product',
            'icon' => 'fa-plus-square',
            'permission' => AdminPermissions::MANAGE_PRODUCTS,
            'keywords' => ['them san pham', 'tao san pham', 'new product'],
        ],
        [
            'key' => 'admin-categories-management',
            'title' => 'Danh mục sản phẩm',
            'description' => 'Theo dõi cây danh mục sản phẩm, trạng thái hiển thị và số sản phẩm liên quan.',
            'section' => 'Sản phẩm',
            'path' => '/admin/categories-management',
            'icon' => 'fa-list',
            'permission' => AdminPermissions::MANAGE_PRODUCTS,
            'keywords' => ['danh muc san pham', 'category product', 'cate'],
        ],
        [
            'key' => 'all-brand-product',
            'title' => 'Thương hiệu sản phẩm',
            'description' => 'Quản lý thương hiệu, thứ tự hiển thị và trạng thái hoạt động.',
            'section' => 'Sản phẩm',
            'path' => '/all-brand-product',
            'icon' => 'fa-industry',
            'permission' => AdminPermissions::MANAGE_PRODUCTS,
            'keywords' => ['brand', 'thuong hieu', 'hang'],
        ],
        [
            'key' => 'attribute-index',
            'title' => 'Thuộc tính sản phẩm',
            'description' => 'Quản lý thuộc tính gốc và cấu hình biến thể cho sản phẩm.',
            'section' => 'Sản phẩm',
            'path' => '/attribute',
            'icon' => 'fa-tags',
            'permission' => AdminPermissions::MANAGE_PRODUCTS,
            'keywords' => ['attribute', 'thuoc tinh', 'variant'],
        ],
        [
            'key' => 'comment',
            'title' => 'Bình luận sản phẩm',
            'description' => 'Duyệt bình luận, phản hồi khách hàng và kiểm soát review.',
            'section' => 'Sản phẩm',
            'path' => '/comment',
            'icon' => 'fa-commenting-o',
            'permission' => AdminPermissions::MANAGE_PRODUCTS,
            'keywords' => ['comment', 'binh luan', 'review'],
        ],
        [
            'key' => 'all-post',
            'title' => 'Liệt kê bài viết',
            'description' => 'Quản lý bài viết, trạng thái xuất bản và nội dung biên tập.',
            'section' => 'Nội dung',
            'path' => '/all-post',
            'icon' => 'fa-file-text-o',
            'permission' => AdminPermissions::MANAGE_CONTENT,
            'keywords' => ['bai viet', 'post', 'content', 'tin tuc'],
        ],
        [
            'key' => 'add-post',
            'title' => 'Thêm bài viết',
            'description' => 'Tạo bài viết mới cho blog, tin tức và nội dung chiến dịch.',
            'section' => 'Nội dung',
            'path' => '/add-post',
            'icon' => 'fa-pencil-square-o',
            'permission' => AdminPermissions::MANAGE_CONTENT,
            'keywords' => ['them bai viet', 'viet bai', 'new post'],
        ],
        [
            'key' => 'all-category-post',
            'title' => 'Danh mục bài viết',
            'description' => 'Quản lý nhóm nội dung và phân loại bài viết trên blog.',
            'section' => 'Nội dung',
            'path' => '/all-category-post',
            'icon' => 'fa-folder-open-o',
            'permission' => AdminPermissions::MANAGE_CONTENT,
            'keywords' => ['danh muc bai viet', 'category post', 'blog'],
        ],
        [
            'key' => 'manage-slider',
            'title' => 'Trình chiếu trang chủ',
            'description' => 'Quản lý ảnh trình chiếu, thứ tự hiển thị và nội dung nổi bật trên trang chủ.',
            'section' => 'Nội dung',
            'path' => '/manage-slider',
            'icon' => 'fa-picture-o',
            'permission' => AdminPermissions::MANAGE_CONTENT,
            'keywords' => ['slider', 'banner', 'hero'],
        ],
        [
            'key' => 'video',
            'title' => 'Kho video',
            'description' => 'Thêm, sửa và quản lý video giới thiệu hoặc nội dung truyền thông.',
            'section' => 'Nội dung',
            'path' => '/video',
            'icon' => 'fa-play-circle-o',
            'permission' => AdminPermissions::MANAGE_CONTENT,
            'keywords' => ['video', 'media', 'youtube'],
        ],
        [
            'key' => 'admin-orders-management',
            'title' => 'Quản lý đơn hàng',
            'description' => 'Theo dõi đơn, cập nhật trạng thái và kiểm tra lịch sử mua hàng.',
            'section' => 'Đơn hàng',
            'path' => '/admin/orders-management',
            'icon' => 'fa-shopping-bag',
            'permission' => AdminPermissions::MANAGE_ORDERS,
            'keywords' => ['don hang', 'order', 'fulfillment'],
        ],
        [
            'key' => 'product-profit',
            'title' => 'Lợi nhuận sản phẩm',
            'description' => 'Báo cáo doanh thu, giá vốn và lợi nhuận theo từng sản phẩm.',
            'section' => 'Đơn hàng',
            'path' => '/product-profit',
            'icon' => 'fa-money',
            'permission' => AdminPermissions::MANAGE_ORDERS,
            'keywords' => ['profit', 'loi nhuan', 'doanh thu', 'bao cao'],
        ],
        [
            'key' => 'delivery',
            'title' => 'Cấu hình vận chuyển',
            'description' => 'Thiết lập phí ship và cấu hình địa giới vận chuyển.',
            'section' => 'Đơn hàng',
            'path' => '/delivery',
            'icon' => 'fa-truck',
            'permission' => AdminPermissions::MANAGE_ORDERS,
            'keywords' => ['van chuyen', 'delivery', 'shipping', 'phi ship'],
        ],
        [
            'key' => 'list-coupon',
            'title' => 'Liệt kê mã giảm giá',
            'description' => 'Kiểm tra tình trạng mã giảm giá, chiến dịch gửi và lịch hết hạn.',
            'section' => 'Khuyến mãi',
            'path' => '/list-coupon',
            'icon' => 'fa-ticket',
            'permission' => AdminPermissions::MANAGE_COUPONS,
            'keywords' => ['coupon', 'giam gia', 'ma giam gia', 'khuyen mai'],
        ],
        [
            'key' => 'insert-coupon',
            'title' => 'Tạo mã giảm giá',
            'description' => 'Khởi tạo mã giảm giá mới và lên lịch chiến dịch gửi khách hàng.',
            'section' => 'Khuyến mãi',
            'path' => '/insert-coupon',
            'icon' => 'fa-plus-circle',
            'permission' => AdminPermissions::MANAGE_COUPONS,
            'keywords' => ['tao coupon', 'them coupon', 'campaign'],
        ],
        [
            'key' => 'users',
            'title' => 'Tài khoản quản trị',
            'description' => 'Xem danh sách admin, vai trò hiện tại và đồng bộ quyền truy cập.',
            'section' => 'Phân quyền',
            'path' => '/users',
            'icon' => 'fa-users',
            'permission' => AdminPermissions::MANAGE_USERS,
            'keywords' => ['user', 'nguoi dung', 'tai khoan quan tri', 'admin user'],
        ],
        [
            'key' => 'index-spatie',
            'title' => 'Vai trò và quyền',
            'description' => 'Quản lý role, permission và mapping truy cập trong khu admin.',
            'section' => 'Phân quyền',
            'path' => '/index-spatie',
            'icon' => 'fa-shield',
            'permission' => AdminPermissions::MANAGE_ROLES,
            'keywords' => ['role', 'permission', 'phan quyen', 'spatie'],
        ],
        [
            'key' => 'read_data',
            'title' => 'Kho tài liệu',
            'description' => 'Duyệt tài liệu nội bộ trong phạm vi documents đã được cấp quyền.',
            'section' => 'Tài liệu',
            'path' => '/read_data',
            'icon' => 'fa-folder-open',
            'permission' => AdminPermissions::MANAGE_DOCUMENTS,
            'keywords' => ['document', 'tai lieu', 'download'],
        ],
    ];

    public static function destinationsFor(?Authenticatable $user): Collection
    {
        if (! $user instanceof User || ! AdminAccess::canAccess($user)) {
            return collect();
        }

        return collect(self::DESTINATIONS)
            ->filter(fn (array $entry) => self::isAccessibleTo($user, $entry))
            ->map(fn (array $entry) => [
                'key' => $entry['key'],
                'title' => $entry['title'],
                'description' => $entry['description'],
                'section' => $entry['section'],
                'path' => $entry['path'],
                'url' => url($entry['path']),
                'icon' => $entry['icon'] ?? 'fa-circle-o',
                'keywords' => $entry['keywords'] ?? [],
            ])
            ->values();
    }

    public static function search(?Authenticatable $user, ?string $query): Collection
    {
        $destinations = self::destinationsFor($user);
        $rawQuery = trim((string) $query);
        $normalizedQuery = self::normalize($rawQuery);

        if ($rawQuery === '') {
            return $destinations;
        }

        if ($normalizedQuery === '') {
            return collect();
        }

        $terms = collect(explode(' ', $normalizedQuery))
            ->filter()
            ->values();

        return $destinations
            ->filter(fn (array $entry) => self::matchesTerms($entry, $terms))
            ->values();
    }

    public static function topSuggestions(?Authenticatable $user, int $limit = 8): Collection
    {
        $destinations = self::destinationsFor($user)->keyBy('key');
        $priorityKeys = [
            'dashboard',
            'admin-products-management',
            'admin-orders-management',
            'list-coupon',
            'information',
            'users',
            'chat-inbox',
            'admin-profile',
            'admin-settings',
        ];

        $ordered = collect($priorityKeys)
            ->map(fn (string $key) => $destinations->get($key))
            ->filter()
            ->values();

        return $ordered
            ->concat($destinations->except($priorityKeys)->values())
            ->unique('key')
            ->take($limit)
            ->values();
    }

    protected static function isAccessibleTo(User $user, array $entry): bool
    {
        $permission = $entry['permission'] ?? null;

        return $permission === null || $user->can($permission);
    }

    protected static function matchesTerms(array $entry, Collection $terms): bool
    {
        $haystack = self::normalize(implode(' ', array_filter([
            $entry['title'] ?? '',
            $entry['description'] ?? '',
            $entry['section'] ?? '',
            implode(' ', $entry['keywords'] ?? []),
            trim((string) ($entry['path'] ?? ''), '/'),
        ])));

        return $terms->every(
            fn (string $term) => str_contains($haystack, $term)
        );
    }

    protected static function normalize(string $value): string
    {
        return (string) Str::of($value)
            ->ascii()
            ->lower()
            ->replaceMatches('/[^a-z0-9\/\s-]+/', ' ')
            ->squish();
    }
}
