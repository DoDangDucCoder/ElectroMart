<?php

namespace App\Http\Controllers\Concerns;

use App\Infrastructure\Persistence\Eloquent\Cms\Banner;
use App\Infrastructure\Persistence\Eloquent\Cms\BlogCategory;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

/**
 * Cung cấp context dùng chung cho nhiều storefront controller.
 *
 * Tên method cũ được giữ lại để tránh phải thay đổi đồng loạt các controller/view.
 */
trait LegacyStorefrontContext
{
    /**
     * Lấy danh sách danh mục bài viết từ schema hiện tại.
     */
    protected function legacyCategoryPosts()
    {
        return Cache::remember('storefront.legacy_category_posts.active', 300, function () {
            $table = (new BlogCategory())->getTable();

            if (! Schema::hasTable($table)) {
                return collect();
            }

            return BlogCategory::query()
                ->where('is_active', true)
                ->orderBy('display_order')
                ->orderByDesc('id')
                ->get();
        });
    }

    /**
     * Lấy các banner đang hoạt động từ schema hiện tại.
     *
     * @param  int  $limit  Số lượng slider tối đa cần lấy
     */
    protected function legacyActiveSliders(int $limit = 4)
    {
        $limit = max(1, $limit);

        return Cache::remember("storefront.legacy_active_sliders.{$limit}", 300, function () use ($limit) {
            $table = (new Banner())->getTable();

            if (! Schema::hasTable($table)) {
                return collect();
            }

            return Banner::query()
                ->where('is_active', true)
                ->where('placement', 'homepage')
                ->where(function ($query) {
                    $query->whereNull('starts_at')
                        ->orWhere('starts_at', '<=', now());
                })
                ->where(function ($query) {
                    $query->whereNull('ends_at')
                        ->orWhere('ends_at', '>=', now());
                })
                ->orderBy('display_order')
                ->orderByDesc('id')
                ->take($limit)
                ->get()
                ->map(fn (Banner $banner) => $this->mapBannerToSliderShape($banner));
        });
    }

    /**
     * Map Banner sang shape cũ để các Blade hiện tại chưa phải đổi cùng lúc.
     */
    protected function mapBannerToSliderShape(Banner $banner): object
    {
        $meta = is_array($banner->meta) ? $banner->meta : [];
        $imagePath = trim((string) $banner->image_path);

        return (object) [
            'slider_id' => $banner->id,
            'slider_name' => $banner->title,
            'slider_alt' => $meta['slider_alt'] ?? $banner->title,
            'slider_image' => basename($imagePath),
            'slider_status' => $banner->is_active ? '1' : '0',
            'slider_desc' => $meta['legacy_description']
                ?? $meta['slider_desc']
                ?? $meta['description']
                ?? null,
            'slider_tone' => $meta['slider_tone'] ?? null,
        ];
    }

    /**
     * Xây dựng các khoảng giá (buckets) cho một danh mục dựa trên giá min/max.
     */
    protected function buildPriceBuckets(float $min, float $max, string $slug): array
    {
        if ($min <= 0 || $max <= 0) {
            return [];
        }

        if ($min > $max) {
            [$min, $max] = [$max, $min];
        }

        if (abs($max - $min) < 1) {
            return [[
                'label' => $this->formatPriceMillions($min),
                'url' => url('/danh-muc/'.$slug).'?start_price='.(int) $min,
            ]];
        }

        $roundTo = 1000000;
        $start = floor($min / $roundTo) * $roundTo;
        $end = ceil($max / $roundTo) * $roundTo;
        if ($end <= $start) {
            $end = $start + $roundTo;
        }

        $segments = 4;
        $step = (int) ceil(($end - $start) / $segments / $roundTo) * $roundTo;
        if ($step <= 0) {
            $step = $roundTo;
        }

        $buckets = [];
        $current = $start;

        for ($i = 0; $i < $segments; $i++) {
            $next = $i === $segments - 1 ? $end : min($end, $current + $step);

            if ($i === 0) {
                $label = 'Duoi '.$this->formatPriceMillions($next);
                $minVal = null;
                $maxVal = $next;
            } elseif ($i === $segments - 1) {
                $label = 'Tren '.$this->formatPriceMillions($current);
                $minVal = $current;
                $maxVal = null;
            } else {
                $label = 'Tu '.$this->formatPriceMillions($current).' den '.$this->formatPriceMillions($next);
                $minVal = $current;
                $maxVal = $next;
            }

            $query = [];
            if ($minVal !== null) {
                $query['start_price'] = (int) $minVal;
            }
            if ($maxVal !== null) {
                $query['end_price'] = (int) $maxVal;
            }

            $buckets[] = [
                'label' => $label,
                'url' => url('/danh-muc/'.$slug).($query ? ('?'.http_build_query($query)) : ''),
            ];

            $current = $next;
            if ($current >= $end) {
                break;
            }
        }

        return $buckets;
    }

    /**
     * Định dạng giá tiền theo đơn vị triệu đồng.
     */
    protected function formatPriceMillions(float $price): string
    {
        if ($price <= 0) {
            return '0 trieu';
        }

        $millions = (int) round($price / 1000000);

        return $millions.' trieu';
    }
}
