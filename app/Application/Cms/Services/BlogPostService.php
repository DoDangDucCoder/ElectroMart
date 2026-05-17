<?php

namespace App\Application\Cms\Services;

use App\Domain\Cms\Repositories\BlogCategoryRepositoryInterface;
use App\Domain\Cms\Repositories\BlogPostRepositoryInterface;
use App\Infrastructure\Persistence\Eloquent\Cms\BlogPost;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class BlogPostService
{
    public function __construct(
        private readonly BlogPostRepositoryInterface $posts,
        private readonly BlogCategoryRepositoryInterface $categories,
    ) {
    }

    public function paginateForAdmin(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->posts->paginate($filters, $perPage);
    }

    public function paginatePosts(array $filters = [], int $perPage = 12): LengthAwarePaginator
    {
        $queryFilters = [
            'status' => 'published',
        ];

        if (! empty($filters['category'])) {
            $category = $this->categories->findBySlug($filters['category'], true);
            if ($category) {
                $queryFilters['category_id'] = $category->id;
            }
        } elseif (! empty($filters['category_id'])) {
            $queryFilters['category_id'] = (int) $filters['category_id'];
        }

        if (array_key_exists('featured', $filters)) {
            $queryFilters['is_featured'] = (bool) $filters['featured'];
        }

        if (! empty($filters['search'])) {
            $queryFilters['search'] = $filters['search'];
        }

        if (! empty($filters['sort'])) {
            $queryFilters['sort'] = $filters['sort'];
        }

        $page = \Illuminate\Pagination\Paginator::resolveCurrentPage('page');
        $cacheKey = 'storefront.blog.posts.'.md5(json_encode([
            'version' => $this->cacheVersion(),
            'filters' => $queryFilters,
            'per_page' => $perPage,
            'page' => $page,
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

        return Cache::remember($cacheKey, 300, fn () => $this->posts->paginate($queryFilters, $perPage));
    }

    public function getPublishedByCategorySlug(string $slug, int $perPage = 10): LengthAwarePaginator
    {
        return $this->posts->paginatePublishedByCategorySlug($slug, $perPage);
    }

    public function latestPublished(int $limit = 5): Collection
    {
        return $this->posts->latestPublished($limit);
    }

    public function getFeaturedPosts(int $limit = 6): Collection
    {
        return Cache::remember(
            'storefront.blog.featured.'.$limit.'.'.$this->cacheVersion(),
            300,
            function () use ($limit) {
                $paginator = $this->posts->paginate([
                    'status' => 'published',
                    'is_featured' => true,
                ], $limit);

                return collect($paginator->items());
            }
        );
    }

    public function getLatestPosts(int $limit = 8): Collection
    {
        return Cache::remember(
            'storefront.blog.latest.'.$limit.'.'.$this->cacheVersion(),
            300,
            fn () => $this->posts->latestPublished($limit)
        );
    }

    public function searchPosts(string $keyword, ?int $limit = null): Collection
    {
        $perPage = $limit ?? 20;
        $paginator = $this->posts->paginate([
            'status' => 'published',
            'search' => $keyword,
        ], $perPage);

        return collect($paginator->items());
    }

    public function getRelatedPosts(BlogPost $post, int $limit = 5): Collection
    {
        return $this->posts->relatedPosts($post, $limit);
    }

    public function findById(int $id): ?BlogPost
    {
        return $this->posts->findById($id);
    }

    public function findBySlug(string $slug): ?BlogPost
    {
        return $this->posts->findBySlug($slug);
    }

    public function findPublishedBySlug(string $slug): ?BlogPost
    {
        return $this->posts->findPublishedBySlug($slug);
    }

    public function create(array $data, ?UploadedFile $image = null): BlogPost
    {
        $payload = $this->preparePayload($data);

        if ($image) {
            $payload['featured_image_path'] = $this->storeUploadedImage($image);
        }

        $post = $this->posts->create($payload);
        $this->clearStorefrontCache();

        return $post->fresh(['category']);
    }

    public function update(BlogPost $post, array $data, ?UploadedFile $image = null): BlogPost
    {
        $payload = $this->preparePayload($data, $post);

        if ($image) {
            $this->deleteImageIfExists($post->featured_image_path);
            $payload['featured_image_path'] = $this->storeUploadedImage($image);
        } elseif (! empty($data['remove_image'])) {
            $this->deleteImageIfExists($post->featured_image_path);
            $payload['featured_image_path'] = null;
        }

        $updated = $this->posts->update($post, $payload);
        $this->clearStorefrontCache();

        return $updated->fresh(['category']);
    }

    public function delete(BlogPost $post): void
    {
        $this->deleteImageIfExists($post->featured_image_path);
        $this->posts->delete($post);
        $this->clearStorefrontCache();
    }

    public function incrementViews(BlogPost $post): void
    {
        $this->posts->incrementViews($post);
    }

    protected function preparePayload(array $data, ?BlogPost $post = null): array
    {
        $title = $data['post_title'] ?? $post?->title ?? '';
        $slug = $data['post_slug'] ?? $post?->slug ?? Str::slug($title);
        $status = $this->normalizeStatus($data['post_status'] ?? $data['status'] ?? $post?->status ?? null);

        $payload = [
            'blog_category_id' => $this->resolveCategoryId($data['cate_post_id'] ?? $data['blog_category_id'] ?? null),
            'title' => $title,
            'slug' => $slug ?: Str::slug(Str::random(8)),
            'excerpt' => $data['post_desc'] ?? $post?->excerpt,
            'content' => $data['post_content'] ?? $post?->content,
            'status' => $status,
            'is_featured' => $data['is_featured'] ?? $post?->is_featured ?? false,
            'meta_title' => $data['meta_title'] ?? $post?->meta_title ?? $title,
            'meta_description' => $data['post_meta_desc'] ?? $data['meta_description'] ?? $post?->meta_description,
            'views_count' => $data['post_views'] ?? $post?->views_count ?? 0,
        ];

        $payload['published_at'] = $this->determinePublishedAt($status, $post?->published_at ?? null);
        $payload['meta'] = $this->buildMetaPayload(
            $data['post_meta_keywords'] ?? $data['meta_keywords'] ?? null,
            $post?->meta ?? []
        );

        return $payload;
    }

    protected function normalizeStatus(mixed $status): string
    {
        if (is_numeric($status)) {
            return ((int) $status) === 0 ? 'published' : 'draft';
        }

        $status = (string) $status;

        return match (Str::lower($status)) {
            'published', 'public', 'active' => 'published',
            'scheduled' => 'scheduled',
            'archived' => 'archived',
            default => 'draft',
        };
    }

    protected function determinePublishedAt(string $status, ?string $current = null): ?string
    {
        return match ($status) {
            'published' => $current ?? now()->toDateTimeString(),
            'scheduled' => $current,
            default => null,
        };
    }

    protected function buildMetaPayload(?string $keywords, array $existing = []): array
    {
        $meta = $existing;

        if ($keywords !== null) {
            $meta['keywords'] = $keywords;
        }

        return array_filter($meta, static fn ($value) => $value !== null && $value !== '');
    }

    protected function resolveCategoryId(mixed $categoryId): ?int
    {
        if (! $categoryId) {
            return null;
        }

        $category = $this->categories->findById((int) $categoryId);

        return $category?->id;
    }

    protected function storeUploadedImage(UploadedFile $file): string
    {
        $directory = public_path('uploads/post');

        if (! File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        $filename = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
        $extension = $file->getClientOriginalExtension();
        $uniqueName = $filename.'-'.Str::random(6).'.'.$extension;

        $file->move($directory, $uniqueName);

        return 'uploads/post/'.$uniqueName;
    }

    protected function deleteImageIfExists(?string $path): void
    {
        if (! $path) {
            return;
        }

        $fullPath = public_path($path);

        if (File::exists($fullPath)) {
            File::delete($fullPath);
        }
    }

    protected function cacheVersion(): int
    {
        return (int) Cache::get('storefront.blog_cache_version', 1);
    }

    protected function clearStorefrontCache(): void
    {
        Cache::forever('storefront.blog_cache_version', $this->cacheVersion() + 1);
        Cache::forget('storefront.blog_categories.active');
        Cache::forget('storefront.blog.category_counts');
    }
}
