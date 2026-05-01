<?php

namespace App\Http\Controllers\Concerns;

use App\Models\CatePost;
use App\Models\Slider;
use Illuminate\Support\Facades\Schema;

/**
 * Cung cấp các phương thức truy xuất dữ liệu legacy (tbl_slider, tbl_category_post)
 * dùng chung cho nhiều storefront controller.
 */
trait LegacyStorefrontContext
{
    /**
     * Lấy danh sách danh mục bài viết từ bảng legacy tbl_category_post.
     * Trả về collection rỗng nếu bảng chưa tồn tại.
     */
    protected function legacyCategoryPosts()
    {
        if (! Schema::hasTable('tbl_category_post')) {
            return collect();
        }

        return CatePost::orderBy('cate_post_id', 'DESC')->get();
    }

    /**
     * Lấy các slider đang hoạt động từ bảng legacy tbl_slider.
     * Trả về collection rỗng nếu bảng chưa tồn tại.
     *
     * @param  int  $limit  Số lượng slider tối đa cần lấy
     */
    protected function legacyActiveSliders(int $limit = 4)
    {
        if (! Schema::hasTable('tbl_slider')) {
            return collect();
        }

        return Slider::orderBy('slider_id', 'DESC')
            ->where('slider_status', '1')
            ->take(max(1, $limit))
            ->get();
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
