<?php

namespace App\Http\Controllers;

use App\Application\Cms\Services\BlogCategoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class CategoryPost extends Controller
{
    public function __construct(
        private readonly BlogCategoryService $blogCategoryService,
    ) {
    }

    public function add_category_post()
    {
        return view('admin.category_post.add_category');
    }

    public function save_category_post(Request $request)
    {
        $data = $request->validate([
            'cate_post_name' => ['required', 'string', 'max:255'],
            'cate_post_slug' => ['nullable', 'string', 'max:255'],
            'cate_post_desc' => ['nullable', 'string'],
            'cate_post_status' => ['required'],
        ]);

        $this->blogCategoryService->create([
            'name' => $data['cate_post_name'],
            'slug' => $data['cate_post_slug'] ?: Str::slug($data['cate_post_name']),
            'description' => $data['cate_post_desc'] ?? null,
            'is_active' => ((string) $data['cate_post_status']) === '0',
        ]);

        Session::put('message', 'Thêm danh mục bài viết thành công');

        return Redirect::back();
    }

    public function all_category_post()
    {
        $category_post = $this->blogCategoryService->paginate([], 10);

        return view('admin.category_post.list_category')->with(compact('category_post'));
    }

    public function edit_category_post($category_post_id)
    {
        $category_post = $this->blogCategoryService->findById((int) $category_post_id);

        abort_if(! $category_post, 404);

        return view('admin.category_post.edit_category')->with(compact('category_post'));
    }

    public function update_category_post(Request $request, $cate_id)
    {
        $data = $request->validate([
            'cate_post_name' => ['required', 'string', 'max:255'],
            'cate_post_slug' => ['nullable', 'string', 'max:255'],
            'cate_post_desc' => ['nullable', 'string'],
            'cate_post_status' => ['required'],
        ]);

        $category_post = $this->blogCategoryService->findById((int) $cate_id);

        abort_if(! $category_post, 404);

        $this->blogCategoryService->update($category_post, [
            'name' => $data['cate_post_name'],
            'slug' => $data['cate_post_slug'] ?: Str::slug($data['cate_post_name']),
            'description' => $data['cate_post_desc'] ?? null,
            'is_active' => ((string) $data['cate_post_status']) === '0',
        ]);

        Session::put('message', 'Cập nhật danh mục bài viết thành công');

        return redirect('/all-category-post');
    }

    public function delete_category_post($cate_id)
    {
        $category_post = $this->blogCategoryService->findById((int) $cate_id);

        if ($category_post) {
            $this->blogCategoryService->delete($category_post);
        }

        Session::put('message', 'Xóa danh mục bài viết thành công');

        return redirect()->back();
    }
}
