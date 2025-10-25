<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class MobileNotificationController extends Controller
{
    public function index(): JsonResponse
    {
        // TODO: Implement mobile API endpoint
        return response()->json([
            'status' => 'success',
            'data' => [],
            'message' => 'Mobile API endpoint ready',
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        // TODO: Implement mobile API store
        return response()->json([
            'status' => 'success',
            'message' => 'Data stored successfully',
        ]);
    }

    public function show(int $id): JsonResponse
    {
        // TODO: Implement mobile API show
        return response()->json([
            'status' => 'success',
            'data' => [],
        ]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        // TODO: Implement mobile API update
        return response()->json([
            'status' => 'success',
            'message' => 'Data updated successfully',
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        // TODO: Implement mobile API destroy
        return response()->json([
            'status' => 'success',
            'message' => 'Data deleted successfully',
        ]);
    }
}
