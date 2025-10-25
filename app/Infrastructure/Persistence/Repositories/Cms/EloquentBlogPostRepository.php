<?php

namespace App\Infrastructure\Persistence\Repositories\Cms;

use App\Domain\Cms\Repositories\BlogPostRepositoryInterface;
use App\Infrastructure\Persistence\Eloquent\Cms\BlogPost;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class EloquentBlogPostRepository implements BlogPostRepositoryInterface
{
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->applyFilters($this->baseQuery(), $filters)
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->paginate($perPage);
    }

    public function paginatePublishedByCategorySlug(string $slug, int $perPage = 10): LengthAwarePaginator
    {
        return $this->baseQuery()
            ->where('status', 'published')
            ->whereHas('category', fn (Builder $query) => $query->where('slug', $slug)->where('is_active', true))
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->paginate($perPage);
    }

    public function latestPublished(int $limit = 5): Collection
    {
        return $this->baseQuery()
            ->where('status', 'published')
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->limit($limit)
            ->get();
    }

    public function relatedPosts(BlogPost $post, int $limit = 5): Collection
    {
        return $this->baseQuery()
            ->where('status', 'published')
            ->where('id', '!=', $post->id)
            ->when($post->blog_category_id, fn (Builder $query, $categoryId) => $query->where('blog_category_id', $categoryId))
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->limit($limit)
            ->get();
    }

    public function findById(int $id): ?BlogPost
    {
        return $this->baseQuery()->find($id);
    }

    public function findBySlug(string $slug): ?BlogPost
    {
        return $this->baseQuery()->where('slug', $slug)->first();
    }

    public function findPublishedBySlug(string $slug): ?BlogPost
    {
        return $this->baseQuery()
            ->where('slug', $slug)
            ->where('status', 'published')
            ->first();
    }

    public function incrementViews(BlogPost $post): void
    {
        $post->increment('views_count');
    }

    public function create(array $data): BlogPost
    {
        return BlogPost::create($data);
    }

    public function update(BlogPost $post, array $data): BlogPost
    {
        $post->fill($data);
        $post->save();

        return $post;
    }

    public function delete(BlogPost $post): void
    {
        $post->delete();
    }

    protected function baseQuery(): Builder
    {
        return BlogPost::with('category');
    }

    protected function applyFilters(Builder $query, array $filters): Builder
    {
        if (! empty($filters['status'])) {
            $statuses = (array) $filters['status'];
            $query->whereIn('status', $statuses);
        }

        if (! empty($filters['category_id'])) {
            $query->where('blog_category_id', (int) $filters['category_id']);
        }

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function (Builder $builder) use ($search) {
                $builder->where('title', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%")
                    ->orWhere('meta->keywords', 'like', "%{$search}%");
            });
        }

        if (isset($filters['is_featured'])) {
            $query->where('is_featured', (bool) $filters['is_featured']);
        }

        return $query;
    }
}
