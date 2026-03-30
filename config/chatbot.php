<?php

$storefrontBrandName = env('STOREFRONT_BRAND_NAME', 'ShopW');
$storefrontHotline = env('STOREFRONT_CONTACT_PHONE_DISPLAY', '1900.0000');
$contactSupportLabel = env('STOREFRONT_LABEL_CONTACT_SUPPORT', 'Liên hệ hỗ trợ');
$contactPageLabel = env('STOREFRONT_LABEL_CONTACT_PAGE', 'Trang liên hệ');

return [
    'welcome_message' => 'Xin chào, tôi là trợ lý bán hàng của ' . $storefrontBrandName . '. Tôi có thể giúp bạn tìm sản phẩm, gợi ý mặt hàng phù hợp hoặc trả lời các câu hỏi thường gặp.',

    'fallback_message' => 'Tôi chưa hiểu rõ yêu cầu này. Bạn có thể hỏi theo các cách như: "tìm iPhone dưới 20 triệu", "gợi ý cho tôi laptop gaming", hoặc "chính sách bảo hành thế nào?".',

    'welcome_actions' => [
        ['label' => 'Tìm sản phẩm', 'path' => '/tim-kiem'],
        ['label' => 'Sản phẩm gợi ý', 'path' => '/goi-y-san-pham'],
        ['label' => 'Liên hệ cửa hàng', 'path' => '/lien-he'],
    ],

    'faq' => [
        [
            'intent' => 'shipping',
            'keywords' => ['giao hang', 'ship', 'van chuyen', 'bao lau nhan duoc', 'nhan hang'],
            'message' => 'Shop hỗ trợ giao hàng toàn quốc. Bạn có thể thêm sản phẩm vào giỏ, nhập địa chỉ nhận hàng và hệ thống sẽ tính phí giao hàng trước khi xác nhận đơn.',
            'actions' => [
                ['label' => 'Vào giỏ hàng', 'path' => '/gio-hang'],
                ['label' => $contactSupportLabel, 'path' => '/lien-he'],
            ],
        ],
        [
            'intent' => 'warranty',
            'keywords' => ['bao hanh', 'hau mai', 'bao tri'],
            'message' => 'Chính sách bảo hành phụ thuộc từng sản phẩm và thương hiệu. Bạn có thể xem chi tiết trong trang sản phẩm hoặc liên hệ hotline ' . $storefrontHotline . ' để được kiểm tra nhanh tình trạng bảo hành.',
            'actions' => [
                ['label' => $contactSupportLabel, 'path' => '/lien-he'],
                ['label' => 'Tra cứu đơn hàng', 'path' => '/history'],
            ],
        ],
        [
            'intent' => 'payment',
            'keywords' => ['thanh toan', 'momo', 'vnpay', 'cod', 'tra gop'],
            'message' => 'Hệ thống hiện hỗ trợ thanh toán khi nhận hàng, chuyển khoản và một số cổng thanh toán trực tuyến như VNPAY hoặc MoMo tùy luồng đặt hàng.',
            'actions' => [
                ['label' => 'Đăng nhập để thanh toán', 'path' => '/dang-nhap'],
                ['label' => 'Xem giỏ hàng', 'path' => '/gio-hang'],
            ],
        ],
        [
            'intent' => 'support',
            'keywords' => ['hotline', 'lien he', 'ho tro', 'cua hang', 'showroom'],
            'message' => 'Bạn có thể liên hệ hotline ' . $storefrontHotline . ' hoặc mở trang Liên hệ để xem thông tin cửa hàng, địa chỉ và khung giờ hỗ trợ.',
            'actions' => [
                ['label' => $contactPageLabel, 'path' => '/lien-he'],
            ],
        ],
    ],

    'ai' => [
        'enabled' => env('CHATBOT_AI_ENABLED', false),
        'system_prompt' => <<<PROMPT
Bạn là trợ lý bán hàng cho website {$storefrontBrandName}.
- Trả lời bằng tiếng Việt rõ ràng, ngắn gọn, thân thiện.
- Ưu tiên hỗ trợ tìm kiếm sản phẩm, giải thích sale/giá, bảo hành, giao hàng, thanh toán.
- Không bịa đặt tồn kho, giá hoặc chính sách nếu không có dữ liệu chắc chắn.
- Nếu không chắc, hướng khách tới trang liên hệ hoặc chức năng tìm kiếm sản phẩm.
PROMPT,
    ],
];
