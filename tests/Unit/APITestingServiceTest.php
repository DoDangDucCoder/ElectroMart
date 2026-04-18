<?php

namespace Tests\Unit;

use App\Application\Shared\Services\APITestingService;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class APITestingServiceTest extends TestCase
{
    public function test_api_testing_service_normalizes_v1_endpoints_and_extracts_nested_tokens(): void
    {
        config([
            'app.url' => 'https://shop.test',
            'admin-auth.seed_defaults.primary_email' => 'admin@shop.com',
            'admin-auth.seed_defaults.primary_password' => 'admin123',
        ]);

        app()->forgetInstance(APITestingService::class);

        Http::preventStrayRequests();
        Http::fake(function (Request $request) {
            $url = $request->url();
            $auth = $request->header('Authorization')[0] ?? null;
            $payload = $request->data();

            if ($request->method() === 'POST' && $url === 'https://shop.test/api/v1/auth/login') {
                return Http::response([
                    'success' => true,
                    'data' => [
                        'token' => 'customer-token',
                        'user' => ['id' => 1, 'name' => 'Customer', 'email' => 'test@example.com'],
                    ],
                ], 200);
            }

            if ($request->method() === 'POST' && $url === 'https://shop.test/api/v1/admin/auth/login') {
                return Http::response([
                    'success' => true,
                    'data' => [
                        'token' => 'admin-token',
                        'user' => ['id' => 99, 'name' => 'Admin', 'email' => 'admin@shop.com'],
                    ],
                ], 200);
            }

            if ($request->method() === 'GET' && $url === 'https://shop.test/api/v1/products') {
                return Http::response([
                    'success' => true,
                    'data' => [['id' => 1, 'name' => 'Product A', 'price' => 100000]],
                ], 200);
            }

            if ($request->method() === 'GET' && str_starts_with($url, 'https://shop.test/api/v1/products/search')) {
                return Http::response([
                    'success' => true,
                    'data' => [],
                ], 200);
            }

            if ($request->method() === 'GET' && $url === 'https://shop.test/api/v1/categories') {
                return Http::response([
                    'success' => true,
                    'data' => [['id' => 10, 'name' => 'Category A', 'slug' => 'category-a']],
                ], 200);
            }

            if ($request->method() === 'GET' && $url === 'https://shop.test/api/v1/brands') {
                return Http::response([
                    'success' => true,
                    'data' => [['id' => 20, 'name' => 'Brand A']],
                ], 200);
            }

            if ($request->method() === 'GET' && $url === 'https://shop.test/api/v1/user') {
                if ($auth === 'Bearer customer-token' || $auth === 'Bearer admin-token') {
                    return Http::response([
                        'success' => true,
                        'data' => ['id' => 1, 'name' => 'Customer', 'email' => 'test@example.com'],
                    ], 200);
                }

                return Http::response(['message' => 'Unauthenticated'], 401);
            }

            if ($request->method() === 'POST' && $url === 'https://shop.test/api/v1/orders') {
                if ($auth === 'Bearer customer-token') {
                    return Http::response([
                        'success' => true,
                        'data' => ['id' => 500, 'status' => 'pending', 'total' => 200000],
                    ], 201);
                }

                return Http::response(['message' => 'Unauthenticated'], 401);
            }

            if ($request->method() === 'GET' && $url === 'https://shop.test/api/v1/admin/auth/me') {
                if ($auth === 'Bearer admin-token') {
                    return Http::response([
                        'success' => true,
                        'data' => ['id' => 99, 'name' => 'Admin', 'email' => 'admin@shop.com'],
                    ], 200);
                }

                if ($auth === 'Bearer customer-token') {
                    return Http::response(['message' => 'Forbidden'], 403);
                }

                return Http::response(['message' => 'Unauthenticated'], 401);
            }

            if ($request->method() === 'POST' && $url === 'https://shop.test/api/v1/admin/products') {
                if (array_key_exists('price', $payload) && $payload['price'] === 'not_a_number') {
                    return Http::response(['message' => 'Validation failed'], 422);
                }

                if ($auth === 'Bearer admin-token') {
                    return Http::response([
                        'success' => true,
                        'data' => ['id' => 700, 'name' => 'Test Product', 'price' => 99990],
                    ], 201);
                }

                return Http::response(['message' => 'Forbidden'], 403);
            }

            return Http::response(['message' => 'Unhandled ' . $request->method() . ' ' . $url], 418);
        });

        $service = app(APITestingService::class);
        $results = $service->runAPITests();

        $this->assertTrue($results['public_endpoints']['GET /api/v1/products']['success']);
        $this->assertTrue($results['protected_endpoints']['GET /api/v1/user']['success']);
        $this->assertTrue($results['admin_endpoints']['GET /api/v1/admin/auth/me']['success']);
        $this->assertSame(200, $results['protected_endpoints']['POST /api/v1/auth/login']['status']);

        Http::assertSent(fn (Request $request) => $request->method() === 'GET'
            && $request->url() === 'https://shop.test/api/v1/products');

        Http::assertSent(fn (Request $request) => $request->method() === 'POST'
            && $request->url() === 'https://shop.test/api/v1/admin/auth/login');
    }
}
