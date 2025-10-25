<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class ApiDocumentationController extends Controller
{
    public function index(): JsonResponse
    {
        $documentation = [
            'title' => 'Laravel Shop API',
            'version' => '1.0.0',
            'description' => 'RESTful API for Laravel Shop e-commerce platform',
            'base_url' => url('/api/v1'),
            'authentication' => [
                'type' => 'Bearer Token',
                'description' => 'Use Laravel Sanctum for API authentication',
                'header' => 'Authorization: Bearer {token}',
            ],
            'rate_limiting' => [
                'description' => 'API requests are rate limited to prevent abuse',
                'limit' => '60 requests per minute per IP/user',
                'headers' => [
                    'X-RateLimit-Limit' => 'Maximum requests allowed',
                    'X-RateLimit-Remaining' => 'Remaining requests in current window',
                    'X-RateLimit-Reset' => 'Unix timestamp when the rate limit resets',
                ],
            ],
            'endpoints' => [
                'products' => [
                    'GET /api/v1/products' => [
                        'description' => 'Get paginated list of products',
                        'parameters' => [
                            'page' => 'Page number (default: 1)',
                            'per_page' => 'Items per page (max: 50, default: 12)',
                            'category' => 'Filter by category slug',
                            'brand' => 'Filter by brand slug',
                            'min_price' => 'Minimum price filter',
                            'max_price' => 'Maximum price filter',
                            'search' => 'Search keyword',
                            'sort' => 'Sort by: price_asc, price_desc, name_asc, name_desc, created_at_desc',
                        ],
                        'response' => [
                            'success' => 'boolean',
                            'data' => 'array of ProductResource',
                            'pagination' => 'pagination object',
                            'filters' => 'applied filters',
                        ],
                    ],
                    'GET /api/v1/products/{slug}' => [
                        'description' => 'Get product by slug',
                        'response' => [
                            'success' => 'boolean',
                            'data' => 'ProductResource',
                            'related_products' => 'array of ProductResource',
                        ],
                    ],
                    'GET /api/v1/products/latest' => [
                        'description' => 'Get latest products',
                        'response' => [
                            'success' => 'boolean',
                            'data' => 'array of ProductResource',
                        ],
                    ],
                    'GET /api/v1/products/featured' => [
                        'description' => 'Get featured products',
                        'response' => [
                            'success' => 'boolean',
                            'data' => 'array of ProductResource',
                        ],
                    ],
                    'GET /api/v1/products/search' => [
                        'description' => 'Search products',
                        'parameters' => [
                            'q' => 'Search keyword (required)',
                            'limit' => 'Maximum results (max: 50, default: 20)',
                        ],
                        'response' => [
                            'success' => 'boolean',
                            'data' => 'array of ProductResource',
                            'keyword' => 'search keyword',
                            'total' => 'total results count',
                        ],
                    ],
                ],
                'categories' => [
                    'GET /api/v1/categories' => [
                        'description' => 'Get all active categories',
                        'response' => [
                            'success' => 'boolean',
                            'data' => 'array of CategoryResource',
                            'total' => 'total count',
                        ],
                    ],
                    'GET /api/v1/categories/{slug}' => [
                        'description' => 'Get category by slug',
                        'response' => [
                            'success' => 'boolean',
                            'data' => 'CategoryResource',
                        ],
                    ],
                    'GET /api/v1/categories/tree' => [
                        'description' => 'Get category tree structure',
                        'response' => [
                            'success' => 'boolean',
                            'data' => 'hierarchical category tree',
                        ],
                    ],
                ],
                'brands' => [
                    'GET /api/v1/brands' => [
                        'description' => 'Get all active brands',
                        'response' => [
                            'success' => 'boolean',
                            'data' => 'array of BrandResource',
                            'total' => 'total count',
                        ],
                    ],
                    'GET /api/v1/brands/{slug}' => [
                        'description' => 'Get brand by slug',
                        'response' => [
                            'success' => 'boolean',
                            'data' => 'BrandResource',
                        ],
                    ],
                ],
                'authentication' => [
                    'POST /api/v1/auth/login' => [
                        'description' => 'Login user',
                        'body' => [
                            'email' => 'string (required)',
                            'password' => 'string (required)',
                        ],
                        'response' => [
                            'success' => 'boolean',
                            'message' => 'string',
                            'data' => [
                                'user' => 'user object',
                                'token' => 'string',
                                'token_type' => 'Bearer',
                            ],
                        ],
                    ],
                    'POST /api/v1/auth/register' => [
                        'description' => 'Register new user',
                        'body' => [
                            'name' => 'string (required)',
                            'email' => 'string (required, unique)',
                            'phone' => 'string (required, unique)',
                            'password' => 'string (required, min:8)',
                            'password_confirmation' => 'string (required)',
                        ],
                        'response' => [
                            'success' => 'boolean',
                            'message' => 'string',
                            'data' => [
                                'user' => 'user object',
                                'token' => 'string',
                                'token_type' => 'Bearer',
                            ],
                        ],
                    ],
                    'POST /api/v1/auth/logout' => [
                        'description' => 'Logout user (requires authentication)',
                        'headers' => ['Authorization: Bearer {token}'],
                        'response' => [
                            'success' => 'boolean',
                            'message' => 'string',
                        ],
                    ],
                ],
                'cart' => [
                    'GET /api/v1/cart' => [
                        'description' => 'Get user cart (requires authentication)',
                        'headers' => ['Authorization: Bearer {token}'],
                        'response' => [
                            'success' => 'boolean',
                            'data' => 'CartResource',
                        ],
                    ],
                    'POST /api/v1/cart/add' => [
                        'description' => 'Add item to cart (requires authentication)',
                        'headers' => ['Authorization: Bearer {token}'],
                        'body' => [
                            'product_id' => 'integer (required)',
                            'quantity' => 'integer (required, min:1, max:10)',
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
                        'description' => 'Get user orders (requires authentication)',
                        'headers' => ['Authorization: Bearer {token}'],
                        'response' => [
                            'success' => 'boolean',
                            'data' => 'array of OrderResource',
                        ],
                    ],
                    'POST /api/v1/orders' => [
                        'description' => 'Create new order (requires authentication)',
                        'headers' => ['Authorization: Bearer {token}'],
                        'body' => [
                            'shipping_address_id' => 'integer (required)',
                            'payment_method' => 'string (required, in:cash,cod,card,paypal)',
                            'notes' => 'string (optional, max:500)',
                            'coupon_code' => 'string (optional)',
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
                        'description' => 'API health check',
                        'response' => [
                            'status' => 'healthy|degraded|unhealthy',
                            'timestamp' => 'ISO 8601 timestamp',
                            'version' => 'API version',
                            'environment' => 'environment name',
                            'database' => 'connection status',
                            'cache' => 'cache status',
                            'storage' => 'storage status',
                        ],
                    ],
                ],
            ],
            'error_responses' => [
                '400' => 'Bad Request - Invalid parameters',
                '401' => 'Unauthorized - Invalid or missing token',
                '403' => 'Forbidden - Insufficient permissions',
                '404' => 'Not Found - Resource not found',
                '422' => 'Unprocessable Entity - Validation errors',
                '429' => 'Too Many Requests - Rate limit exceeded',
                '500' => 'Internal Server Error - Server error',
            ],
            'response_format' => [
                'success' => 'boolean - Indicates if request was successful',
                'message' => 'string - Human readable message',
                'data' => 'mixed - Response data',
                'errors' => 'object - Validation errors (when applicable)',
                'pagination' => 'object - Pagination info (when applicable)',
            ],
        ];

        return response()->json($documentation);
    }
}
