<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Support\AdminAccess;
use App\Support\AdminAuthenticator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'device_name' => ['nullable', 'string', 'max:255'],
        ]);

        $user = AdminAuthenticator::resolveUser($credentials['email'], $credentials['password']);

        if (! $user || ! AdminAccess::canAccess($user)) {
            return response()->json([
                'success' => false,
                'message' => 'Thông tin đăng nhập không chính xác hoặc tài khoản không có quyền quản trị.',
            ], 401);
        }

        $token = $user->createToken($credentials['device_name'] ?? 'admin-api')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Đăng nhập quản trị thành công.',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'roles' => $user->getRoleNames()->values(),
                ],
                'token' => $token,
                'token_type' => 'Bearer',
            ],
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'roles' => $user->getRoleNames()->values(),
            ],
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()?->currentAccessToken()?->delete();

        return response()->json([
            'success' => true,
            'message' => 'Đăng xuất quản trị thành công.',
        ]);
    }
}
