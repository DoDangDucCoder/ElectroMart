<?php

return [
    'flash_sale' => [
        'enabled' => filter_var(env('STOREFRONT_FLASH_SALE_ENABLED', true), FILTER_VALIDATE_BOOL),
        'kicker' => env('STOREFRONT_FLASH_SALE_KICKER', ''),
        'title' => env('STOREFRONT_FLASH_SALE_TITLE', 'FLASH SALE'),
        'cta_label' => env('STOREFRONT_FLASH_SALE_CTA_LABEL', 'Xem tất cả'),
        'cta_url' => env('STOREFRONT_FLASH_SALE_CTA_URL', '/tim-kiem'),
        'countdown_ends_at' => env('STOREFRONT_FLASH_SALE_ENDS_AT', '2026-12-31 23:59:59'),
        'target_count' => (int) env('STOREFRONT_FLASH_SALE_TARGET_COUNT', 6),
        'use_demo_products' => in_array(env('APP_ENV', 'production'), ['local', 'testing'], true)
            && filter_var(env('STOREFRONT_FLASH_SALE_DEMO_PRODUCTS', false), FILTER_VALIDATE_BOOL),
        'product_slugs' => array_values(array_filter(array_map(
            static fn ($slug) => trim((string) $slug),
            explode(',', (string) env('STOREFRONT_FLASH_SALE_PRODUCT_SLUGS', ''))
        ))),
    ],
    'hero_slider' => [
        'best_selling_path' => env('STOREFRONT_HERO_BEST_SELLING_PATH', '/san-pham-ban-chay'),
        'max_slides' => (int) env('STOREFRONT_HERO_MAX_SLIDES', 4),
        'product_hints' => [
        ],
    ],
    'search_suggest' => [
        'max_products' => (int) env('STOREFRONT_SEARCH_SUGGEST_MAX_PRODUCTS', 6),
        'trending_keywords' => array_values(array_filter(array_map(
            static fn ($keyword) => trim((string) $keyword),
            explode(',', (string) env(
                'STOREFRONT_SEARCH_SUGGEST_TRENDING',
                'Laptop gaming,Iphone,RTX 4060,Màn hình 2K,Bàn phím cơ'
            ))
        ))),
        'shortcut_category_slugs' => array_values(array_filter(array_map(
            static fn ($slug) => trim((string) $slug),
            explode(',', (string) env(
                'STOREFRONT_SEARCH_SUGGEST_SHORTCUT_CATEGORIES',
                'laptop-gaming,laptop,dien-thoai,phu-kien'
            ))
        ))),
    ],
    'labels' => [
        'utility_hotline' => env('STOREFRONT_LABEL_UTILITY_HOTLINE', 'Hotline'),
        'utility_store' => env('STOREFRONT_LABEL_UTILITY_STORE', 'Cửa hàng'),
        'topbar_contact_fast' => env('STOREFRONT_LABEL_TOPBAR_CONTACT_FAST', 'Liên hệ nhanh'),
        'chat_assistant' => env('STOREFRONT_LABEL_CHAT_ASSISTANT', 'Trợ lý bán hàng'),
        'chat_panel_title' => env('STOREFRONT_LABEL_CHAT_PANEL_TITLE', 'Chat hỗ trợ sản phẩm'),
        'support_quick' => env('STOREFRONT_LABEL_SUPPORT_QUICK', 'Hỗ trợ nhanh'),
        'support_connect_title' => env('STOREFRONT_LABEL_SUPPORT_CONNECT_TITLE', 'Kết nối với cửa hàng'),
        'contact_support' => env('STOREFRONT_LABEL_CONTACT_SUPPORT', 'Liên hệ hỗ trợ'),
        'contact_page' => env('STOREFRONT_LABEL_CONTACT_PAGE', 'Trang liên hệ'),
        'compare_products' => env('STOREFRONT_LABEL_COMPARE_PRODUCTS', 'So sánh sản phẩm'),
        'recommend_for_you' => env('STOREFRONT_LABEL_RECOMMEND_FOR_YOU', 'Gợi ý cho bạn'),
        'order_tracking' => env('STOREFRONT_LABEL_ORDER_TRACKING', 'Theo dõi đơn hàng'),
        'buy_more_products' => env('STOREFRONT_LABEL_BUY_MORE_PRODUCTS', 'Mua thêm sản phẩm'),
        'hotline_consulting' => env('STOREFRONT_LABEL_HOTLINE_CONSULTING', 'Hotline tư vấn'),
        'hotline_ordering' => env('STOREFRONT_LABEL_HOTLINE_ORDERING', 'Hotline đặt hàng'),
        'hotline_support' => env('STOREFRONT_LABEL_HOTLINE_SUPPORT', 'Hotline hỗ trợ'),
        'hotline_after_sales' => env('STOREFRONT_LABEL_HOTLINE_AFTER_SALES', 'Hotline hậu mãi'),
        'contact_fast' => env('STOREFRONT_LABEL_CONTACT_FAST', 'Liên hệ nhanh'),
        'view_previous_orders' => env('STOREFRONT_LABEL_VIEW_PREVIOUS_ORDERS', 'Xem đơn cũ'),
    ],
    'copy' => [
        'detail_shipping_in_stock' => env('STOREFRONT_COPY_DETAIL_SHIPPING_IN_STOCK', 'Sản phẩm đang có sẵn để xử lý đơn và giao nhanh trong khung giờ hỗ trợ.'),
        'detail_shipping_out_stock' => env('STOREFRONT_COPY_DETAIL_SHIPPING_OUT_STOCK', 'Đăng ký nhận tư vấn để được cập nhật ngay khi hàng về kho.'),
        'detail_prepurchase_help' => env('STOREFRONT_COPY_DETAIL_PREPURCHASE_HELP', 'So sánh sản phẩm, liên hệ nhanh hoặc xem thêm gợi ý phù hợp với nhu cầu hiện tại.'),
        'category_compare_value' => env('STOREFRONT_COPY_CATEGORY_COMPARE_VALUE', 'Mở bảng so sánh nhanh'),
        'cart_chat_help' => env('STOREFRONT_COPY_CART_CHAT_HELP', 'Mở khung chat để được hỗ trợ chọn cấu hình nhanh.'),
        'cart_order_tracking_help' => env('STOREFRONT_COPY_CART_ORDER_TRACKING_HELP', 'Xem lịch sử mua sắm và trạng thái giao hàng hiện tại.'),
        'checkout_delivery_help' => env('STOREFRONT_COPY_CHECKOUT_DELIVERY_HELP', 'Điền chính xác khu vực giao hàng để hệ thống tính phí ship ngay trên form.'),
        'checkout_security_help' => env('STOREFRONT_COPY_CHECKOUT_SECURITY_HELP', 'Dữ liệu đơn hàng được dùng trực tiếp để tạo đơn và đối soát thanh toán.'),
        'checkout_contact_fast_value' => env('STOREFRONT_COPY_CHECKOUT_CONTACT_FAST_VALUE', 'Mở trang liên hệ để được hỗ trợ nhanh'),
        'checkout_previous_orders_value' => env('STOREFRONT_COPY_CHECKOUT_PREVIOUS_ORDERS_VALUE', 'Kiểm tra lịch sử mua sắm'),
        'login_continue_help' => env('STOREFRONT_COPY_LOGIN_CONTINUE_HELP', 'Dùng tài khoản cũ để tiếp tục thanh toán, theo dõi lịch sử mua và lưu thông tin nhận hàng.'),
        'login_register_help' => env('STOREFRONT_COPY_LOGIN_REGISTER_HELP', 'Nếu chưa có tài khoản, bạn có thể đăng ký ngay và tiếp tục đơn hàng trong cùng một bước.'),
        'history_buy_more_value' => env('STOREFRONT_COPY_HISTORY_BUY_MORE_VALUE', 'Quay lại trang chủ để tiếp tục chọn hàng'),
        'history_recommend_value' => env('STOREFRONT_COPY_HISTORY_RECOMMEND_VALUE', 'Khám phá thêm từ lịch sử mua và xem'),
        'video_blog_value' => env('STOREFRONT_COPY_VIDEO_BLOG_VALUE', 'Theo dõi thêm các bài đánh giá, tư vấn và hướng dẫn mới'),
        'video_recommend_value' => env('STOREFRONT_COPY_VIDEO_RECOMMEND_VALUE', 'Khám phá thêm sản phẩm phù hợp'),
        'contact_chat_value' => env('STOREFRONT_COPY_CONTACT_CHAT_VALUE', 'Nhận tư vấn nhanh trước khi chốt đơn hoặc cần hỗ trợ sau mua'),
        'contact_history_value' => env('STOREFRONT_COPY_CONTACT_HISTORY_VALUE', 'Theo dõi trạng thái đơn đã đặt'),
    ],
];
