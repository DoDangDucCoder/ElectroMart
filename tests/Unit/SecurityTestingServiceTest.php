<?php

namespace Tests\Unit;

use App\Application\Shared\Services\SecurityTestingService;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class SecurityTestingServiceTest extends TestCase
{
    public function test_security_testing_service_supports_opaque_tokens_and_real_v1_routes(): void
    {
        config([
            'app.url' => 'https://shop.test',
            'admin-auth.seed_defaults.primary_email' => 'admin@shop.com',
            'admin-auth.seed_defaults.primary_password' => 'admin123',
        ]);

        app()->forgetInstance(SecurityTestingService::class);

        Http::preventStrayRequests();
        Http::fake(function (Request $request) {
            $url = $request->url();
            $auth = $request->header('Authorization')[0] ?? null;

            if ($request->method() === 'GET' && $url === 'https://shop.test/sanctum/csrf-cookie') {
                return Http::response([], 204, ['Set-Cookie' => 'XSRF-TOKEN=fake-cookie']);
            }

            if ($request->method() === 'POST' && $url === 'https://shop.test/api/v1/auth/login') {
                return Http::response([
                    'success' => true,
                    'data' => [
                        'token' => '1|customerOpaqueTokenValue1234567890',
                        'user' => ['id' => 1, 'name' => 'Customer', 'email' => 'test@example.com'],
                    ],
                ], 200, ['Set-Cookie' => 'laravel_session=session-a']);
            }

            if ($request->method() === 'POST' && $url === 'https://shop.test/api/v1/admin/auth/login') {
                return Http::response([
                    'success' => true,
                    'data' => [
                        'token' => '2|adminOpaqueTokenValue1234567890',
                        'user' => ['id' => 99, 'name' => 'Admin', 'email' => 'admin@shop.com'],
                    ],
                ], 200, ['Set-Cookie' => 'laravel_session=session-admin']);
            }

            if ($request->method() === 'GET' && str_starts_with($url, 'https://shop.test/api/v1/products/search')) {
                return Http::response(['success' => true, 'data' => []], 200);
            }

            if ($request->method() === 'GET' && $url === 'https://shop.test/api/v1/products') {
                return Http::response(['success' => true, 'data' => []], 200);
            }

            if ($request->method() === 'GET' && $url === 'https://shop.test/api/v1/user') {
                if ($auth === 'Bearer 1|customerOpaqueTokenValue1234567890'
                    || $auth === 'Bearer 2|adminOpaqueTokenValue1234567890') {
                    return Http::response([
                        'success' => true,
                        'data' => ['id' => 1, 'name' => 'Customer', 'email' => 'test@example.com'],
                    ], 200);
                }

                return Http::response(['message' => 'Unauthenticated'], 401);
            }

            if ($request->method() === 'GET' && $url === 'https://shop.test/api/v1/orders') {
                return Http::response(['success' => true, 'data' => []], 200);
            }

            if ($request->method() === 'GET' && $url === 'https://shop.test/api/v1/orders/999') {
                return Http::response(['message' => 'Not found'], 404);
            }

            if ($request->method() === 'POST' && $url === 'https://shop.test/api/v1/orders') {
                return $auth === 'Bearer 1|customerOpaqueTokenValue1234567890'
                    ? Http::response(['success' => true, 'data' => ['id' => 501]], 201)
                    : Http::response(['message' => 'Unauthenticated'], 401);
            }

            if ($url === 'https://shop.test/api/v1/admin/auth/me') {
                if ($auth === 'Bearer 2|adminOpaqueTokenValue1234567890') {
                    return Http::response([
                        'success' => true,
                        'data' => ['id' => 99, 'name' => 'Admin', 'email' => 'admin@shop.com'],
                    ], 200);
                }

                if ($auth === 'Bearer 1|customerOpaqueTokenValue1234567890') {
                    return Http::response(['message' => 'Forbidden'], 403);
                }

                return Http::response(['message' => 'Unauthenticated'], 401);
            }

            if (str_starts_with($url, 'https://shop.test/api/v1/admin/products')) {
                if ($request->method() === 'POST') {
                    return Http::response(['message' => 'Validation failed'], 422);
                }

                return $auth === 'Bearer 2|adminOpaqueTokenValue1234567890'
                    ? Http::response(['success' => true], 200)
                    : Http::response(['message' => 'Forbidden'], 403);
            }

            if ($request->method() === 'GET' && $url === 'https://shop.test/') {
                return Http::response([], 200, [
                    'X-Content-Type-Options' => 'nosniff',
                    'X-Frame-Options' => 'SAMEORIGIN',
                ]);
            }

            return Http::response(['message' => 'Unhandled ' . $request->method() . ' ' . $url], 418);
        });

        $service = app(SecurityTestingService::class);
        $results = $service->runSecurityTests();

        $jwt = $results['authentication_tests']['token_security']['jwt_security'];
        $this->assertTrue($jwt['token_received']);
        $this->assertSame('Opaque token', $jwt['token_format']);

        $rbac = $results['authorization_tests']['role_based_access'];
        $this->assertSame(200, $rbac['admin']['GET /api/v1/admin/auth/me']['actual_status']);
        $this->assertSame(403, $rbac['customer']['GET /api/v1/admin/auth/me']['actual_status']);

        Http::assertSent(fn (Request $request) => $request->method() === 'POST'
            && $request->url() === 'https://shop.test/api/v1/admin/auth/login');
    }
}
