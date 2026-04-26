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
}
