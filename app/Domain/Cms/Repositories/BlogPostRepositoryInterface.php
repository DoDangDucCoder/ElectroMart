<?php

namespace App\Domain\Cms\Repositories;

use App\Infrastructure\Persistence\Eloquent\Cms\BlogPost;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface BlogPostRepositoryInterface
{
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    public function paginatePublishedByCategorySlug(string $slug, int $perPage = 10): LengthAwarePaginator;

    public function latestPublished(int $limit = 5): Collection;

    public function relatedPosts(BlogPost $post, int $limit = 5): Collection;

    public function findById(int $id): ?BlogPost;

    public function findBySlug(string $slug): ?BlogPost;

    public function findPublishedBySlug(string $slug): ?BlogPost;

    public function incrementViews(BlogPost $post): void;

    public function create(array $data): BlogPost;

    public function update(BlogPost $post, array $data): BlogPost;

    public function delete(BlogPost $post): void;
}
