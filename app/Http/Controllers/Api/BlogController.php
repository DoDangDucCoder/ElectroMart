<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Application\Cms\Services\BlogPostService;
use App\Http\Resources\BlogPostResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class BlogController extends Controller
{
    public function __construct(
        private readonly BlogPostService $blogService
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['category', 'featured', 'search']);
        $perPage = min($request->get('per_page', 12), 50);

        $posts = $this->blogService->paginatePosts($filters, $perPage);

        return response()->json([
            'success' => true,
            'data' => BlogPostResource::collection($posts->items()),
            'pagination' => [
                'current_page' => $posts->currentPage(),
                'last_page' => $posts->lastPage(),
                'per_page' => $posts->perPage(),
                'total' => $posts->total(),
            ],
        ]);
    }

    public function show(string $slug): JsonResponse
    {
        $post = $this->blogService->findBySlug($slug);

        if (!$post) {
            return response()->json([
                'success' => false,
                'message' => 'Blog post not found',
            ], 404);
        }

        // Increment views
        $this->blogService->incrementViews($post);

        return response()->json([
            'success' => true,
            'data' => new BlogPostResource($post),
            'related_posts' => BlogPostResource::collection(
                $this->blogService->getRelatedPosts($post, 4)
            ),
        ]);
    }

    public function featured(): JsonResponse
    {
        $posts = $this->blogService->getFeaturedPosts(6);

        return response()->json([
            'success' => true,
            'data' => BlogPostResource::collection($posts),
        ]);
    }

    public function latest(): JsonResponse
    {
        $posts = $this->blogService->getLatestPosts(8);

        return response()->json([
            'success' => true,
            'data' => BlogPostResource::collection($posts),
        ]);
    }

    public function search(Request $request): JsonResponse
    {
        $keyword = $request->get('q');
        $limit = min($request->get('limit', 20), 50);

        if (empty($keyword)) {
            return response()->json([
                'success' => false,
                'message' => 'Search keyword is required',
            ], 400);
        }

        $posts = $this->blogService->searchPosts($keyword, $limit);

        return response()->json([
            'success' => true,
            'data' => BlogPostResource::collection($posts),
            'keyword' => $keyword,
            'total' => $posts->count(),
        ]);
    }
}
