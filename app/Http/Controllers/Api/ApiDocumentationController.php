<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class ApiDocumentationController extends Controller
{
    public function index(): JsonResponse
    {
        $documentation = [
            'title' => 'ShopW API',
            'version' => '1.0.0',
            'description' => 'API RESTful cho nền tảng thương mại điện tử ShopW',
            'base_url' => url('/api/v1'),
            'authentication' => [
                'type' => 'Mã truy cập Bearer',
                'description' => 'Sử dụng Laravel Sanctum để xác thực API',
                'header' => 'Authorization: Bearer {token}',
            ],
            'rate_limiting' => [
                'description' => 'Yêu cầu API được giới hạn tần suất để tránh lạm dụng',
                'limit' => '60 yêu cầu mỗi phút cho mỗi IP/người dùng',
                'headers' => [
                    'X-RateLimit-Limit' => 'Số yêu cầu tối đa được phép',
                    'X-RateLimit-Remaining' => 'Số yêu cầu còn lại trong cửa sổ hiện tại',
                    'X-RateLimit-Reset' => 'Unix timestamp khi giới hạn được đặt lại',
                ],
            ],
            'endpoints' => [
                'products' => [
                    'GET /api/v1/products' => [
                        'description' => 'Lấy danh sách sản phẩm có phân trang',
                        'parameters' => [
                            'page' => 'Số trang (mặc định: 1)',
                            'per_page' => 'Số mục mỗi trang (tối đa: 50, mặc định: 12)',
                            'category' => 'Lọc theo slug danh mục',
                            'brand' => 'Lọc theo slug thương hiệu',
                            'min_price' => 'Giá tối thiểu',
                            'max_price' => 'Giá tối đa',
                            'search' => 'Từ khóa tìm kiếm',
                            'sort' => 'Sắp xếp theo: price_asc, price_desc, name_asc, name_desc, created_at_desc',
                        ],
                        'response' => [
                            'success' => 'boolean',
                            'data' => 'mảng ProductResource',
                            'pagination' => 'đối tượng phân trang',
                            'filters' => 'bộ lọc đã áp dụng',
                        ],
                    ],
                    'GET /api/v1/products/{slug}' => [
                        'description' => 'Lấy sản phẩm theo slug',
                        'response' => [
                            'success' => 'boolean',
                            'data' => 'ProductResource',
                            'related_products' => 'mảng ProductResource liên quan',
                            'recommended_products' => 'mảng ProductResource gợi ý',
                        ],
                    ],
                    'GET /api/v1/products/category/{categorySlug}' => [
                        'description' => 'Lấy sản phẩm có phân trang theo slug danh mục đang hoạt động',
                        'parameters' => [
                            'page' => 'Số trang (mặc định: 1)',
                            'per_page' => 'Số mục mỗi trang (tối đa: 50, mặc định: 12)',
                            'brand' => 'Lọc theo slug thương hiệu đang hoạt động',
                            'min_price' => 'Giá tối thiểu',
                            'max_price' => 'Giá tối đa',
                            'sort' => 'Sắp xếp theo: price_asc, price_desc, name_asc, name_desc',
                        ],
                    ],
                    'GET /api/v1/products/brand/{brandSlug}' => [
                        'description' => 'Lấy sản phẩm có phân trang theo slug thương hiệu đang hoạt động',
                        'parameters' => [
                            'page' => 'Số trang (mặc định: 1)',
                            'per_page' => 'Số mục mỗi trang (tối đa: 50, mặc định: 12)',
                            'category' => 'Lọc theo slug danh mục đang hoạt động',
                            'min_price' => 'Giá tối thiểu',
                            'max_price' => 'Giá tối đa',
                            'sort' => 'Sắp xếp theo: price_asc, price_desc, name_asc, name_desc',
                        ],
                    ],
                    'GET /api/v1/products/latest' => [
                        'description' => 'Lấy sản phẩm mới nhất',
                        'response' => [
                            'success' => 'boolean',
                            'data' => 'mảng ProductResource',
                        ],
                    ],
                    'GET /api/v1/products/featured' => [
                        'description' => 'Lấy sản phẩm nổi bật',
                        'response' => [
                            'success' => 'boolean',
                            'data' => 'mảng ProductResource',
                        ],
                    ],
                    'GET /api/v1/products/search' => [
                        'description' => 'Tìm kiếm sản phẩm',
                        'parameters' => [
                            'q' => 'Từ khóa tìm kiếm (bắt buộc)',
                            'limit' => 'Số kết quả tối đa (tối đa: 50, mặc định: 20)',
                        ],
                        'response' => [
                            'success' => 'boolean',
                            'data' => 'mảng ProductResource',
                            'keyword' => 'từ khóa tìm kiếm',
                            'total' => 'tổng số kết quả',
                        ],
                    ],
                ],
                'categories' => [
                    'GET /api/v1/categories' => [
                        'description' => 'Lấy tất cả danh mục đang hoạt động',
                        'response' => [
                            'success' => 'boolean',
                            'data' => 'mảng CategoryResource',
                            'total' => 'tổng số lượng',
                        ],
                    ],
                    'GET /api/v1/categories/{slug}' => [
                        'description' => 'Lấy danh mục theo slug',
                        'response' => [
                            'success' => 'boolean',
                            'data' => 'CategoryResource',
                        ],
                    ],
                    'GET /api/v1/categories/tree' => [
                        'description' => 'Lấy cấu trúc cây danh mục',
                        'response' => [
                            'success' => 'boolean',
                            'data' => 'cây danh mục phân cấp',
                        ],
                    ],
                ],
                'brands' => [
                    'GET /api/v1/brands' => [
                        'description' => 'Lấy tất cả thương hiệu đang hoạt động',
                        'response' => [
                            'success' => 'boolean',
                            'data' => 'mảng BrandResource',
                            'total' => 'tổng số lượng',
                        ],
                    ],
                    'GET /api/v1/brands/{slug}' => [
                        'description' => 'Lấy thương hiệu theo slug',
                        'response' => [
                            'success' => 'boolean',
                            'data' => 'BrandResource',
                        ],
                    ],
                ],
                'authentication' => [
                    'POST /api/v1/auth/login' => [
                        'description' => 'Đăng nhập người dùng',
                        'body' => [
                            'email' => 'chuỗi (bắt buộc)',
                            'password' => 'chuỗi (bắt buộc)',
                        ],
                        'response' => [
                            'success' => 'boolean',
                            'message' => 'string',
                            'data' => [
                                'user' => 'đối tượng người dùng',
                                'token' => 'string',
                                'token_type' => 'Bearer',
                            ],
                        ],
                    ],
                    'POST /api/v1/auth/register' => [
                        'description' => 'Đăng ký người dùng mới',
                        'body' => [
                            'name' => 'chuỗi (bắt buộc)',
                            'email' => 'chuỗi (bắt buộc, duy nhất)',
                            'phone' => 'chuỗi (bắt buộc, duy nhất)',
                            'password' => 'chuỗi (bắt buộc, tối thiểu: 8)',
                            'password_confirmation' => 'chuỗi (bắt buộc)',
                        ],
                        'response' => [
                            'success' => 'boolean',
                            'message' => 'string',
                            'data' => [
                                'user' => 'đối tượng người dùng',
                                'token' => 'string',
                                'token_type' => 'Bearer',
                            ],
                        ],
                    ],
                    'POST /api/v1/auth/logout' => [
                        'description' => 'Đăng xuất người dùng (cần xác thực)',
                        'headers' => ['Authorization: Bearer {token}'],
                        'response' => [
                            'success' => 'boolean',
                            'message' => 'string',
                        ],
                    ],
                ],
                'cart' => [
                    'GET /api/v1/cart' => [
                        'description' => 'Lấy giỏ hàng của người dùng (cần xác thực)',
                        'headers' => ['Authorization: Bearer {token}'],
                        'response' => [
                            'success' => 'boolean',
                            'data' => 'CartResource',
                        ],
                    ],
                    'POST /api/v1/cart/add' => [
                        'description' => 'Thêm sản phẩm vào giỏ hàng (cần xác thực)',
                        'headers' => ['Authorization: Bearer {token}'],
                        'body' => [
                            'product_id' => 'số nguyên (bắt buộc)',
                            'quantity' => 'số nguyên (bắt buộc, tối thiểu: 1, tối đa: 10)',
                        ],
                        'response' => [
                            'success' => 'boolean',
                            'message' => 'string',
                            'data' => 'CartResource',
                        ],
                    ],
                ],
                'orders' => [
                    'GET /api/v1/orders' => [
                        'description' => 'Lấy danh sách đơn hàng của người dùng (cần xác thực)',
                        'headers' => ['Authorization: Bearer {token}'],
                        'response' => [
                            'success' => 'boolean',
                            'data' => 'mảng OrderResource',
                        ],
                    ],
                    'POST /api/v1/orders' => [
                        'description' => 'Tạo đơn hàng mới (cần xác thực)',
                        'headers' => ['Authorization: Bearer {token}'],
                        'body' => [
                            'shipping_address_id' => 'số nguyên (bắt buộc)',
                            'payment_method' => 'chuỗi (bắt buộc, thuộc: cash,cod,card,paypal)',
                            'notes' => 'chuỗi (không bắt buộc, tối đa: 500)',
                            'coupon_code' => 'chuỗi (không bắt buộc)',
                        ],
                        'response' => [
                            'success' => 'boolean',
                            'message' => 'string',
                            'data' => 'OrderResource',
                        ],
                    ],
                ],
                'health' => [
                    'GET /api/v1/health' => [
                        'description' => 'Kiểm tra tình trạng API',
                        'response' => [
                            'status' => 'healthy|degraded|unhealthy',
                            'timestamp' => 'thời gian ISO 8601',
                            'version' => 'phiên bản API',
                            'environment' => 'tên môi trường',
                            'database' => 'trạng thái kết nối',
                            'cache' => 'trạng thái cache',
                            'storage' => 'trạng thái lưu trữ',
                        ],
                    ],
                    'GET /api/v1/health/cache' => [
                        'description' => 'Tình trạng cache và thống kê driver',
                    ],
                    'GET /api/v1/health/performance' => [
                        'description' => 'Ảnh chụp nhanh hiệu năng runtime',
                    ],
                ],
                'settings' => [
                    'GET /api/v1/settings' => [
                        'description' => 'Lấy các cặp khóa/giá trị thiết lập công khai',
                    ],
                    'GET /api/v1/settings/contact' => [
                        'description' => 'Lấy thiết lập liên hệ công khai',
                    ],
                    'GET /api/v1/settings/seo' => [
                        'description' => 'Lấy thiết lập SEO công khai',
                    ],
                ],
            ],
            'error_responses' => [
                '400' => 'Yêu cầu không hợp lệ - Tham số không hợp lệ',
                '401' => 'Chưa xác thực - Token không hợp lệ hoặc bị thiếu',
                '403' => 'Bị từ chối - Không đủ quyền',
                '404' => 'Không tìm thấy - Không tìm thấy tài nguyên',
                '422' => 'Không thể xử lý - Lỗi xác thực dữ liệu',
                '429' => 'Quá nhiều yêu cầu - Vượt giới hạn tần suất',
                '500' => 'Lỗi máy chủ nội bộ - Lỗi phía máy chủ',
            ],
            'response_format' => [
                'success' => 'boolean - Cho biết yêu cầu có thành công hay không',
                'message' => 'string - Thông báo dễ đọc',
                'data' => 'mixed - Dữ liệu phản hồi',
                'errors' => 'object - Lỗi xác thực dữ liệu (khi có)',
                'pagination' => 'object - Thông tin phân trang (khi có)',
            ],
        ];

        return response()->json($documentation);
    }
}
