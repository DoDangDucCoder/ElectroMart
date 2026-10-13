<?php

namespace App\Http\Controllers;

use App\Application\Catalog\Services\BrandService;
use App\Application\Catalog\Services\CategoryService;
use App\Application\Catalog\Services\ProductService;
use App\Application\Cms\Services\BlogCategoryService;
use App\Application\Cms\Services\BlogPostService;
use App\Http\Controllers\Concerns\LegacyStorefrontContext;
use App\Http\Requests\Cms\StoreBlogPostRequest;
use App\Http\Requests\Cms\UpdateBlogPostRequest;
use App\Infrastructure\Persistence\Eloquent\Cms\BlogPost;
use App\Support\AssetUrl;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class PostController extends Controller
{
    use LegacyStorefrontContext;
    public function __construct(
        private readonly BlogCategoryService $blogCategoryService,
        private readonly BlogPostService $blogPostService,
        private readonly CategoryService $categoryService,
        private readonly BrandService $brandService,
        private readonly ProductService $productService,
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
            'sort' => $request->input('sort', 'newest'),
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

        if (! $post) {
            return redirect()->to('/all-post')->with('error', 'Không tìm thấy bài viết cần xóa.');
        }

        $this->blogPostService->delete($post);

        return redirect()->to('/all-post')->with('message', 'Xóa bài viết thành công');
    }

    public function edit_post(int $postId): View|RedirectResponse
    {
        $post = $this->blogPostService->findById($postId);

        if (! $post) {
            return redirect()->to('/all-post')->with('error', 'Không tìm thấy bài viết cần chỉnh sửa.');
        }

        $categories = $this->blogCategoryService->listAll();

        return view('admin.post.edit_post', [
            'post' => $post,
            'cate_post' => $categories,
        ]);
    }

    public function update_post(UpdateBlogPostRequest $request, int $postId): RedirectResponse
    {
        $post = $this->blogPostService->findById($postId);

        if (! $post) {
            return redirect()->to('/all-post')->with('error', 'Không tìm thấy bài viết cần cập nhật.');
        }

        $this->blogPostService->update($post, $request->validated(), $request->file('post_image'));

        return redirect()->to('/all-post')->with('message', 'Cập nhật bài viết thành công');
    }

    public function index(Request $request): View
    {
        $selectedCategory = null;
        $categorySlug = trim((string) $request->query('category', ''));

        if ($categorySlug !== '') {
            $selectedCategory = $this->blogCategoryService->findActiveBySlug($categorySlug);
            abort_if(! $selectedCategory, 404);
        }

        return $this->renderArchive($request, $selectedCategory);
    }

    public function danh_muc_bai_viet(Request $request, string $postSlug): View
    {
        $category = $this->blogCategoryService->findActiveBySlug($postSlug);

        abort_if(! $category, 404);

        return $this->renderArchive($request, $category);
    }

    protected function renderArchive(Request $request, $selectedCategory): View
    {
        $sortMap = [
            'moi-nhat' => 'newest',
            'cu-nhat' => 'oldest',
            'xem-nhieu' => 'most_viewed',
            'noi-bat' => 'featured',
        ];
        $sortParam = (string) $request->input('sort', 'moi-nhat');
        $sortKey = $sortMap[$sortParam] ?? 'newest';

        $blogCategories = $this->blogCategoryService->listAll(['is_active' => true]);
        $productCategories = $this->categoryService->listAll(['is_active' => true]);
        $brands = $this->brandService->listActive();

        $filters = [
            'status' => 'published',
            'sort' => $sortKey,
        ];
        if ($selectedCategory) {
            $filters['category_id'] = $selectedCategory->id;
        }
        if ($search = trim((string) $request->input('search'))) {
            $filters['search'] = $search;
        }

        $posts = $this->blogPostService->paginatePosts(
            array_merge($filters, $selectedCategory ? ['category' => $selectedCategory->cate_post_slug] : []),
            6
        );
        $posts->appends($request->query());

        $categoryCounts = Cache::remember('storefront.blog.category_counts', 300, fn () => \App\Infrastructure\Persistence\Eloquent\Cms\BlogPost::query()
            ->where('status', 'published')
            ->selectRaw('blog_category_id, COUNT(*) as total')
            ->groupBy('blog_category_id')
            ->pluck('total', 'blog_category_id'));
        $totalPublished = (int) $categoryCounts->sum();

        $featuredPosts = $this->blogPostService->getFeaturedPosts(4);
        if ($featuredPosts->isEmpty()) {
            $featuredPosts = $this->blogPostService->getLatestPosts(4);
        }

        $hotKeywords = $this->buildHotKeywords($blogCategories, $brands);

        $heroTitle = $selectedCategory
            ? $selectedCategory->cate_post_name
            : 'Bài viết & Tin tức';
        $heroDesc = $selectedCategory
            ? ($selectedCategory->cate_post_desc ?: 'Tổng hợp bài viết, tin nhanh và hướng dẫn liên quan đến nhóm nội dung này.')
            : 'Cập nhật xu hướng công nghệ, hướng dẫn sử dụng và mẹo chọn mua sản phẩm phù hợp.';

        return view('pages.blog.listing', [
            'category' => $productCategories,
            'brand' => $brands,
            'category_post' => $blogCategories,
            'blog_categories' => $blogCategories,
            'post_cate' => $posts,
            'selected_category' => $selectedCategory,
            'category_counts' => $categoryCounts,
            'total_published' => $totalPublished,
            'featured_posts' => $featuredPosts,
            'hot_keywords' => $hotKeywords,
            'sort_current' => $sortParam,
            'search_keyword' => $search ?? '',
            'meta_desc' => $selectedCategory?->cate_post_desc ?: $heroDesc,
            'meta_keywords' => $selectedCategory?->cate_post_slug ?: 'bai viet, tin tuc, cong nghe',
            'meta_title' => $heroTitle,
            'hero_title' => $heroTitle,
            'hero_desc' => $heroDesc,
            'url_canonical' => $request->url(),
            'slider' => $this->legacyActiveSliders(),
            'share_image' => url('frontend/images/share_news.png'),
        ]);
    }

    protected function buildHotKeywords($blogCategories, $brands): array
    {
        $keywords = [];
        foreach ($blogCategories as $cat) {
            if (! empty($cat->cate_post_name)) {
                $keywords[] = [
                    'label' => $cat->cate_post_name,
                    'url' => url('/bai-viet?' . http_build_query(['category' => $cat->cate_post_slug])),
                ];
            }
        }
        foreach (collect($brands)->take(4) as $brand) {
            $name = $brand->brand_name ?? $brand->name ?? null;
            if ($name) {
                $keywords[] = [
                    'label' => $name,
                    'url' => url('/bai-viet?' . http_build_query(['search' => $name])),
                ];
            }
        }
        if (count($keywords) < 6) {
            $defaults = ['Laptop', 'PC Gaming', 'SSD', 'Màn hình', 'Linh kiện', 'Hướng dẫn', 'Đánh giá', 'Công nghệ'];
            foreach ($defaults as $d) {
                if (count($keywords) >= 8) {
                    break;
                }
                $keywords[] = [
                    'label' => $d,
                    'url' => url('/bai-viet?search=' . urlencode($d)),
                ];
            }
        }

        return array_slice($keywords, 0, 8);
    }

    public function bai_viet(Request $request, string $postSlug): View
    {
        $post = $this->blogPostService->findPublishedBySlug($postSlug);

        abort_if(! $post, 404);

        $this->blogPostService->incrementViews($post);

        $categoryPosts = $this->blogCategoryService->listAll(['is_active' => true]);
        $productCategories = $this->categoryService->listAll(['is_active' => true]);
        $brands = $this->brandService->listActive();
        $related = $this->blogPostService->getRelatedPosts($post, 3);

        $blogCategoryCounts = BlogPost::query()
            ->where('status', 'published')
            ->selectRaw('blog_category_id, COUNT(*) as total')
            ->groupBy('blog_category_id')
            ->pluck('total', 'blog_category_id');

        $totalPublishedPosts = (int) $blogCategoryCounts->sum();

        $featuredPosts = $this->blogPostService->getFeaturedPosts(4);
        if ($featuredPosts->isEmpty()) {
            $featuredPosts = $this->blogPostService->getLatestPosts(4);
        }
        $featuredPosts = $featuredPosts->reject(fn ($item) => (int) $item->id === (int) $post->id)->take(4);

        $suggestedProducts = $this->productService->getLatestProducts(8)
            ->filter(fn ($product) => AssetUrl::resolve($product->product_image) !== null)
            ->take(3)
            ->values();

        return view('pages.baiviet.baiviet', [
            'category' => $productCategories,
            'brand' => $brands,
            'category_post' => $categoryPosts,
            'slider' => $this->legacyActiveSliders(),
            'post' => $post,
            'related' => $related,
            'blog_category_counts' => $blogCategoryCounts,
            'total_published_posts' => $totalPublishedPosts,
            'featured_posts' => $featuredPosts,
            'suggested_products' => $suggestedProducts,
            'meta_desc' => $post->post_meta_desc,
            'meta_keywords' => $post->post_meta_keywords,
            'meta_title' => trim((string) ($post->meta_title ?? '')) ?: $post->post_title,
            'url_canonical' => $request->url(),
            'share_image' => $post->featured_image_path
                ? url($post->featured_image_path)
                : url('frontend/images/share_news.png'),
        ]);
    }

}
