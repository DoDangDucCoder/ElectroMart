<?php

namespace App\Infrastructure\Persistence\Repositories\Catalog;

use App\Domain\Catalog\Repositories\BrandRepositoryInterface;
use App\Infrastructure\Persistence\Eloquent\Catalog\Brand;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class EloquentBrandRepository implements BrandRepositoryInterface
{
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->applyFilters(Brand::query(), $filters)
            ->orderBy('display_order')
            ->orderByDesc('id')
            ->paginate($perPage);
    }

    public function all(array $filters = []): Collection
    {
        return $this->applyFilters(Brand::query(), $filters)
            ->orderBy('display_order')
            ->orderByDesc('id')
            ->get();
    }

    public function findById(int $id): ?Brand
    {
        return Brand::find($id);
    }

    public function findBySlug(string $slug, bool $onlyActive = false): ?Brand
    {
        return Brand::query()
            ->when($onlyActive, fn ($query) => $query->where('is_active', true))
            ->where('slug', $slug)
            ->first();
    }

    public function create(array $data): Brand
    {
        return Brand::create($data);
    }

    public function update(Brand $brand, array $data): Brand
    {
        $brand->fill($data);
        $brand->save();

        return $brand;
    }

    public function delete(Brand $brand): void
    {
        $brand->delete();
    }

    public function setStatus(Brand $brand, bool $isActive): Brand
    {
        $brand->is_active = $isActive;
        $brand->save();

        return $brand;
    }

    protected function applyFilters($query, array $filters)
    {
        if (! empty($filters['search'])) {
            $query->where(function ($builder) use ($filters) {
                $builder->where('name', 'like', '%'.$filters['search'].'%')
                    ->orWhere('slug', 'like', '%'.$filters['search'].'%')
                    ->orWhere('code', 'like', '%'.$filters['search'].'%');
            });
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', (bool) $filters['is_active']);
        }

        return $query;
    }
}
