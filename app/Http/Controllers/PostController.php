<?php

namespace App\Http\Controllers;

use App\Application\Catalog\Services\BrandService;
use App\Application\Catalog\Services\CategoryService;
use App\Application\Cms\Services\BlogCategoryService;
use App\Application\Cms\Services\BlogPostService;
use App\Http\Requests\Cms\StoreBlogPostRequest;
use App\Http\Requests\Cms\UpdateBlogPostRequest;
use App\Models\Slider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PostController extends Controller
{
    public function __construct(
        private readonly BlogCategoryService $blogCategoryService,
        private readonly BlogPostService $blogPostService,
        private readonly CategoryService $categoryService,
        private readonly BrandService $brandService,
    ) {
    }

    public function add_post(): View
    {
        $categories = $this->blogCategoryService->listAll(['is_active' => true]);

        return view('admin.post.add_post', [
            'cate_post' => $categories,
        ]);
    }

    public function save_post(StoreBlogPostRequest $request): RedirectResponse
    {
        $this->blogPostService->create($request->validated(), $request->file('post_image'));

        return redirect()->to('/all-post')->with('message', 'Thêm bài viết thành công');
    }

    public function all_post(Request $request): View
    {
        $filters = array_filter([
            'search' => $request->input('search'),
            'status' => $request->input('status'),
            'category_id' => $request->input('category_id'),
        ]);

        $perPage = (int) $request->input('per_page', 15);

        $posts = $this->blogPostService->paginateForAdmin($filters, $perPage);
        $categories = $this->blogCategoryService->listAll();

        return view('admin.post.list_post', [
            'all_post' => $posts,
            'categories' => $categories,
            'filters' => $filters,
        ]);
    }

    public function delete_post(int $postId): RedirectResponse
    {
        $post = $this->blogPostService->findById($postId);

        abort_if(! $post, 404);

        $this->blogPostService->delete($post);

        return redirect()->to('/all-post')->with('message', 'Xóa bài viết thành công');
    }

    public function edit_post(int $postId): View
    {
        $post = $this->blogPostService->findById($postId);

        abort_if(! $post, 404);

        $categories = $this->blogCategoryService->listAll();

        return view('admin.post.edit_post', [
            'post' => $post,
            'cate_post' => $categories,
        ]);
    }

    public function update_post(UpdateBlogPostRequest $request, int $postId): RedirectResponse
    {
        $post = $this->blogPostService->findById($postId);

        abort_if(! $post, 404);

        $this->blogPostService->update($post, $request->validated(), $request->file('post_image'));

        return redirect()->to('/all-post')->with('message', 'Cập nhật bài viết thành công');
    }

    public function danh_muc_bai_viet(Request $request, string $postSlug): View
    {
        $category = $this->blogCategoryService->findActiveBySlug($postSlug);

        abort_if(! $category, 404);

        $slider = Slider::orderBy('slider_id', 'DESC')
            ->where('slider_status', '1')
            ->take(4)
            ->get();

        $categoryPosts = $this->blogCategoryService->listAll(['is_active' => true]);
        $productCategories = $this->categoryService->listAll(['is_active' => true]);
        $brands = $this->brandService->listActive();

        $posts = $this->blogPostService->getPublishedByCategorySlug($postSlug, 6);
        $posts->appends($request->query());

        return view('pages.baiviet.danhmucbaiviet', [
            'category' => $productCategories,
            'brand' => $brands,
            'category_post' => $categoryPosts,
            'post_cate' => $posts,
            'selected_category' => $category,
            'meta_desc' => $category->cate_post_desc,
            'meta_keywords' => $category->cate_post_slug,
            'meta_title' => $category->cate_post_name,
            'url_canonical' => $request->url(),
            'slider' => $slider,
            'share_image' => url('frontend/images/share_news.png'),
        ]);
    }

    public function bai_viet(Request $request, string $postSlug): View
    {
        $post = $this->blogPostService->findPublishedBySlug($postSlug);

        abort_if(! $post, 404);

        $this->blogPostService->incrementViews($post);

        $slider = Slider::orderBy('slider_id', 'DESC')
            ->where('slider_status', '1')
            ->take(4)
            ->get();

        $categoryPosts = $this->blogCategoryService->listAll(['is_active' => true]);
        $productCategories = $this->categoryService->listAll(['is_active' => true]);
        $brands = $this->brandService->listActive();
        $related = $this->blogPostService->getRelatedPosts($post, 5);

        return view('pages.baiviet.baiviet', [
            'category' => $productCategories,
            'brand' => $brands,
            'category_post' => $categoryPosts,
            'slider' => $slider,
            'post' => $post,
            'related' => $related,
            'meta_desc' => $post->post_meta_desc,
            'meta_keywords' => $post->post_meta_keywords,
            'meta_title' => $post->post_title,
            'url_canonical' => $request->url(),
            'share_image' => $post->featured_image_path
                ? url($post->featured_image_path)
                : url('frontend/images/share_news.png'),
        ]);
    }
}
