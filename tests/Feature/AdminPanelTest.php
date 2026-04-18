<?php

namespace Tests\Feature;

use App\Application\Order\Services\LegacyOrderPresentationService;
use App\Application\Catalog\Services\ProductService;
use App\Infrastructure\Persistence\Eloquent\Catalog\Brand;
use App\Infrastructure\Persistence\Eloquent\Catalog\ProductCategory;
use App\Infrastructure\Persistence\Eloquent\Catalog\ProductReview;
use App\Infrastructure\Persistence\Eloquent\Cms\BlogCategory;
use App\Infrastructure\Persistence\Eloquent\Cms\BlogPost;
use App\Models\User;
use App\Models\Attribute;
use App\Models\City;
use App\Models\Coupon;
use App\Models\Contact;
use App\Models\Customer;
use App\Models\Feeship;
use App\Models\Gallery;
use App\Models\Icons;
use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\Province;
use App\Models\Shipping;
use App\Models\Slider;
use App\Models\Statistic;
use App\Models\Visitors;
use App\Models\Wards;
use App\Support\AdminAccess;
use App\Support\AdminPermissions;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminPanelTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_admin_login_from_dashboard(): void
    {
        $this->get('/dashboard')
            ->assertRedirect('/admin');
    }

    public function test_admin_post_page_renders_admin_shell_stack(): void
    {
        $admin = $this->createAdminUser();

        $this->actingAs($admin, 'web')
            ->get('/all-post')
            ->assertOk()
            ->assertSee('<meta name="csrf-token"', false)
            ->assertSee('CMS SHOP')
            ->assertSee('class="g-admin-shell"', false)
            ->assertSee('id="sidebar"', false)
            ->assertSee('id="main-content"', false)
            ->assertSee('g-admin-shell-leading', false)
            ->assertSee('g-admin-shell-brand', false)
            ->assertSee('g-admin-shell-logo', false)
            ->assertSee('g-admin-shell-toggle', false)
            ->assertSee('data-admin-sidebar-toggle', false)
            ->assertSee('aria-expanded="true"', false)
            ->assertSee('aria-controls="sidebar"', false)
            ->assertSee('g-admin-post-list')
            ->assertSee('Liệt kê bài viết')
            ->assertSee('Your Company Name')
            ->assertSee('backend/js/jquery2.0.3.min.js')
            ->assertSee('backend/js/bootstrap.js')
            ->assertSee('backend/js/jquery.dcjqaccordion.2.7.js')
            ->assertSee('filebrowserImageBrowseUrl')
            ->assertSee('list_doitac();')
            ->assertSee('chart365daysorder();')
            ->assertSee('/select-delivery');
    }

    public function test_admin_mail_preview_tooling_renders_inside_admin_shell(): void
    {
        $admin = $this->createAdminUser();

        $this->actingAs($admin, 'web')
            ->get('/mail-preview')
            ->assertOk()
            ->assertSee('Xem trước email hệ thống')
            ->assertSee('Coupon khách thường')
            ->assertSee('Khôi phục mật khẩu')
            ->assertSee('id="mail-preview-frame"', false);

        $this->actingAs($admin, 'web')
            ->get('/mail-preview?preview=order-atm')
            ->assertOk()
            ->assertSee('Xác nhận đơn chuyển khoản')
            ->assertSee('order-confirmation.atm');

        $this->actingAs($admin, 'web')
            ->get('/mail-preview/render?preview=order-atm')
            ->assertOk()
            ->assertSee('Xác nhận đơn hàng')
            ->assertSee('ORD-TEST-001', false)
            ->assertSee('Chuyển khoản ATM')
            ->assertSee(config('services.storefront_brand.name', 'ShopW'));

        $this->actingAs($admin, 'web')
            ->get('/mail-preview/render?preview=missing-template')
            ->assertNotFound();
    }

    public function test_admin_product_discovery_report_renders_inside_admin_shell(): void
    {
        $admin = $this->createAdminUser();

        $this->actingAs($admin, 'web')
            ->get('/product-discovery-report')
            ->assertOk()
            ->assertSee('Tìm kiếm và gợi ý sản phẩm')
            ->assertSee('Lượt tìm kiếm đã ghi nhận')
            ->assertSee('Sản phẩm được gợi ý nhiều');
    }

    public function test_admin_product_discovery_report_falls_back_cleanly_when_analytics_tables_are_missing(): void
    {
        $admin = $this->createAdminUser();

        Schema::dropIfExists('product_recommendation_impressions');
        Schema::dropIfExists('product_search_queries');

        $this->actingAs($admin, 'web')
            ->get('/product-discovery-report')
            ->assertOk()
            ->assertSee('Tìm kiếm và gợi ý sản phẩm')
            ->assertSee('Một phần dữ liệu analytics chưa sẵn sàng')
            ->assertSee('Chưa có dữ liệu từ khóa tìm kiếm để hiển thị.')
            ->assertSee('Chưa có dữ liệu impression recommendation.')
            ->assertSee('Chưa có dữ liệu sản phẩm được gợi ý trong bộ lọc hiện tại.');
    }

    public function test_admin_post_create_and_edit_pages_render_refreshed_shell(): void
    {
        $admin = $this->createAdminUser();
        $category = BlogCategory::create([
            'name' => 'Tin tức admin render',
            'slug' => 'tin-tuc-admin-render',
            'description' => 'Danh mục bài viết cho admin render',
            'is_active' => true,
            'display_order' => 0,
        ]);

        $post = BlogPost::create([
            'blog_category_id' => $category->id,
            'title' => 'Bài viết render admin',
            'slug' => 'bai-viet-render-admin',
            'excerpt' => 'Tóm tắt bài viết render admin',
            'content' => 'Nội dung bài viết render admin',
            'featured_image_path' => 'uploads/post/no-image.svg',
            'status' => 'published',
            'published_at' => now(),
            'meta_title' => 'Meta title bài viết render admin',
            'meta_description' => 'Meta description bài viết render admin',
            'meta' => ['keywords' => 'render,admin'],
        ]);

        $this->actingAs($admin, 'web')
            ->get('/add-post')
            ->assertOk()
            ->assertSee('g-admin-post-create')
            ->assertSee('Thêm bài viết')
            ->assertSee('Thông tin bài viết')
            ->assertSee('Tìm kiếm nhanh')
            ->assertSee('Hồ sơ')
            ->assertSee('Cài đặt');

        $this->actingAs($admin, 'web')
            ->get('/edit-post/' . $post->post_id)
            ->assertOk()
            ->assertSee('g-admin-post-edit')
            ->assertSee($post->post_title)
            ->assertSee('Thông tin hiện tại')
            ->assertSee('name="remove_image"', false)
            ->assertSee('uploads/product/no-image.svg');
    }

    public function test_admin_post_edit_can_remove_current_image_and_delete_missing_post_gracefully(): void
    {
        $admin = $this->createAdminUser();
        $category = BlogCategory::create([
            'name' => 'Danh mục xóa ảnh bài viết',
            'slug' => 'danh-muc-xoa-anh-bai-viet',
            'description' => 'Danh mục dùng cho test remove image',
            'is_active' => true,
            'display_order' => 0,
        ]);

        File::ensureDirectoryExists(public_path('uploads/post'));
        File::put(public_path('uploads/post/remove-image-post.jpg'), 'fake-image');

        $post = BlogPost::create([
            'blog_category_id' => $category->id,
            'title' => 'Bài viết có ảnh cần gỡ',
            'slug' => 'bai-viet-co-anh-can-go',
            'excerpt' => 'Tóm tắt bài viết có ảnh',
            'content' => 'Nội dung bài viết có ảnh',
            'featured_image_path' => 'uploads/post/remove-image-post.jpg',
            'status' => 'published',
            'published_at' => now(),
            'meta_title' => 'Meta title remove image',
            'meta_description' => 'Meta description remove image',
            'meta' => ['keywords' => 'remove,image'],
        ]);

        $this->actingAs($admin, 'web')
            ->from('/edit-post/' . $post->post_id)
            ->post('/update-post/' . $post->post_id, [
                'post_title' => $post->post_title,
                'post_slug' => $post->post_slug,
                'post_desc' => $post->post_desc,
                'post_content' => $post->post_content,
                'post_meta_desc' => $post->post_meta_desc,
                'post_meta_keywords' => $post->post_meta_keywords,
                'cate_post_id' => $category->id,
                'post_status' => '0',
                'remove_image' => '1',
            ])
            ->assertRedirect('/all-post')
            ->assertSessionHas('message', 'Cập nhật bài viết thành công');

        $post->refresh();
        $this->assertNull($post->featured_image_path);
        $this->assertFalse(File::exists(public_path('uploads/post/remove-image-post.jpg')));

        $this->actingAs($admin, 'web')
            ->from('/all-post')
            ->delete('/delete-post/999999')
            ->assertRedirect('/all-post')
            ->assertSessionHas('error', 'Không tìm thấy bài viết cần xóa.');

        $this->actingAs($admin, 'web')
            ->get('/edit-post/999999')
            ->assertRedirect('/all-post')
            ->assertSessionHas('error', 'Không tìm thấy bài viết cần chỉnh sửa.');

        $this->actingAs($admin, 'web')
            ->post('/update-post/999999', [
                'post_title' => 'Bai viet khong ton tai',
                'post_slug' => 'bai-viet-khong-ton-tai',
                'post_desc' => 'Tom tat',
                'post_content' => 'Noi dung',
                'post_meta_desc' => 'Meta mo ta',
                'post_meta_keywords' => 'meta,keywords',
                'cate_post_id' => $category->id,
                'post_status' => '0',
            ])
            ->assertRedirect('/all-post')
            ->assertSessionHas('error', 'Không tìm thấy bài viết cần cập nhật.');
    }

    public function test_admin_product_page_reuses_the_same_shell(): void
    {
        $admin = $this->createAdminUser();
        $category = ProductCategory::create([
            'name' => 'Danh mục admin product render',
            'slug' => 'danh-muc-admin-product-render',
            'is_active' => true,
            'display_order' => 0,
        ]);
        $brand = Brand::create([
            'name' => 'Thương hiệu admin product render',
            'slug' => 'thuong-hieu-admin-product-render',
            'is_active' => true,
            'display_order' => 0,
        ]);
        $product = $this->app->make(ProductService::class)->createProduct([
            'name' => 'Sản phẩm render admin',
            'slug' => 'san-pham-render-admin',
            'brand_id' => $brand->id,
            'primary_category_id' => $category->id,
            'price' => 1800000,
            'cost_price' => 1500000,
            'stock_quantity' => 10,
            'description' => 'Mô tả render admin',
            'content' => 'Nội dung render admin',
            'category_ids' => [$category->id],
            'tags' => 'admin,render',
            'is_active' => true,
            'thumbnail_path' => 'uploads/product/no-image.svg',
        ]);

        $this->actingAs($admin, 'web')
            ->get('/add-product')
            ->assertOk()
            ->assertSee('g-admin-product-create')
            ->assertSee('Thêm sản phẩm');

        $response = $this->actingAs($admin, 'web')->get('/all-product');

        $response
            ->assertOk()
            ->assertSee('g-admin-product-list')
            ->assertSee('Liệt kê sản phẩm')
            ->assertSee('CMS SHOP')
            ->assertSee('id="nav-accordion"', false)
            ->assertSee('id="main-content"', false)
            ->assertSee('backend/js/jquery2.0.3.min.js')
            ->assertSee('backend/js/bootstrap.js')
            ->assertSee($product->product_name)
            ->assertDontSee('Thông báo')
            ->assertDontSee('alert alert-danger');

        $this->assertMatchesRegularExpression(
            '/<button[^>]*data-admin-sidebar-trigger="products"[^>]*aria-expanded="true"/',
            $response->getContent()
        );
        $this->assertMatchesRegularExpression(
            '/<a[^>]*data-admin-sidebar-key="all-product"[^>]*aria-current="page"/',
            $response->getContent()
        );

        $this->actingAs($admin, 'web')
            ->get('/edit-product/' . $product->product_id)
            ->assertOk()
            ->assertSee('g-admin-product-edit')
            ->assertSee($product->product_name)
            ->assertSee('id="document_notice"', false)
            ->assertSee('window.renderDocumentNotice', false)
            ->assertSee('data-document-card', false)
            ->assertSee('data-document-empty-state', false)
            ->assertSee("\$button.closest('[data-document-card]')", false)
            ->assertSee("\$('[data-document-empty-state]').removeAttr('hidden');", false)
            ->assertDontSee("alert('Xóa file thành công')", false);
    }

    public function test_admin_product_actions_handle_missing_records_gracefully(): void
    {
        $admin = $this->createAdminUser();

        $this->actingAs($admin, 'web')
            ->get('/edit-product/999999')
            ->assertRedirect('/all-product')
            ->assertSessionHas('error', 'Không tìm thấy sản phẩm cần chỉnh sửa.');

        $this->actingAs($admin, 'web')
            ->patch('/active-product/999999')
            ->assertRedirect('/all-product')
            ->assertSessionHas('error', 'Không tìm thấy sản phẩm cần cập nhật trạng thái.');

        $this->actingAs($admin, 'web')
            ->patch('/unactive-product/999999')
            ->assertRedirect('/all-product')
            ->assertSessionHas('error', 'Không tìm thấy sản phẩm cần cập nhật trạng thái.');

        $this->actingAs($admin, 'web')
            ->delete('/delete-product/999999')
            ->assertRedirect('/all-product')
            ->assertSessionHas('error', 'Không tìm thấy sản phẩm cần xóa.');
    }

    public function test_admin_brand_pages_render_refreshed_shell(): void
    {
        $admin = $this->createAdminUser();
        $brand = Brand::create([
            'name' => 'Thương hiệu render admin',
            'slug' => 'thuong-hieu-render-admin',
            'description' => 'Mô tả thương hiệu render admin',
            'is_active' => true,
            'display_order' => 0,
        ]);

        $this->actingAs($admin, 'web')
            ->get('/add-brand-product')
            ->assertOk()
            ->assertSee('g-admin-brand-create')
            ->assertSee('Thêm thương hiệu sản phẩm');

        $this->actingAs($admin, 'web')
            ->get('/all-brand-product')
            ->assertOk()
            ->assertSee('g-admin-brand-list')
            ->assertSee($brand->name);

        $this->actingAs($admin, 'web')
            ->get('/edit-brand-product/' . $brand->id)
            ->assertOk()
            ->assertSee('g-admin-brand-edit')
            ->assertSee($brand->name);
    }

    public function test_admin_brand_actions_handle_missing_records_gracefully(): void
    {
        $admin = $this->createAdminUser();

        $this->actingAs($admin, 'web')
            ->get('/edit-brand-product/999999')
            ->assertRedirect('/all-brand-product')
            ->assertSessionHas('error', 'Không tìm thấy thương hiệu cần chỉnh sửa');

        $this->actingAs($admin, 'web')
            ->patch('/active-brand-product/999999')
            ->assertRedirect('/all-brand-product')
            ->assertSessionHas('error', 'Không tìm thấy thương hiệu cần cập nhật trạng thái');

        $this->actingAs($admin, 'web')
            ->patch('/unactive-brand-product/999999')
            ->assertRedirect('/all-brand-product')
            ->assertSessionHas('error', 'Không tìm thấy thương hiệu cần cập nhật trạng thái');

        $this->actingAs($admin, 'web')
            ->delete('/delete-brand-product/999999')
            ->assertRedirect('/all-brand-product')
            ->assertSessionHas('error', 'Không tìm thấy thương hiệu cần xóa');
    }

    public function test_live_category_product_routes_render_legacy_views_in_safe_mode(): void
    {
        $admin = $this->createAdminUser();

        $rootCategory = ProductCategory::create([
            'name' => 'Laptop',
            'slug' => 'laptop',
            'description' => 'Danh mục laptop',
            'is_active' => true,
            'display_order' => 1,
        ]);

        $childCategory = ProductCategory::create([
            'name' => 'Laptop gaming',
            'slug' => 'laptop-gaming',
            'description' => 'Danh mục laptop gaming',
            'parent_id' => $rootCategory->id,
            'is_active' => true,
            'display_order' => 2,
            'meta' => ['meta_keywords' => 'laptop,gaming'],
        ]);

        $this->actingAs($admin, 'web')
            ->get('/add-category-product')
            ->assertOk()
            ->assertSee('g-admin-category-product-create')
            ->assertSee('Laptop')
            ->assertSee('chỉ dùng để tham chiếu cấu trúc dữ liệu cũ');

        $listResponse = $this->actingAs($admin, 'web')
            ->get('/all-category-product');

        $listResponse
            ->assertOk()
            ->assertSee('g-admin-category-product-list')
            ->assertSee('Laptop')
            ->assertSee('Laptop gaming')
            ->assertSee('chế độ chỉ xem')
            ->assertDontSee('/delete-category-product/');

        $this->assertMatchesRegularExpression(
            '/<button[^>]*data-admin-sidebar-trigger="product-categories"[^>]*aria-expanded="true"/',
            $listResponse->getContent()
        );
        $this->assertMatchesRegularExpression(
            '/<a[^>]*data-admin-sidebar-key="all-category-product"[^>]*aria-current="page"/',
            $listResponse->getContent()
        );

        $editResponse = $this->actingAs($admin, 'web')
            ->get('/edit-category-product/' . $childCategory->id);

        $editResponse
            ->assertOk()
            ->assertSee('g-admin-category-product-edit')
            ->assertSee('Laptop gaming')
            ->assertSee('đang ở chế độ chỉ xem');

        $this->actingAs($admin, 'web')
            ->get('/edit-category-product/999999')
            ->assertOk()
            ->assertSee('g-admin-category-product-edit')
            ->assertSee('Không tìm thấy danh mục cần cập nhật.');

        $this->assertMatchesRegularExpression(
            '/<option[^>]*(?:selected[^>]*value="' . $rootCategory->id . '"|value="' . $rootCategory->id . '"[^>]*selected)[^>]*>Laptop<\/option>/',
            $editResponse->getContent()
        );
    }

    public function test_orphaned_category_product_views_still_render_with_admin_shell(): void
    {
        $admin = $this->createAdminUser();

        $rootCategory = (object) [
            'category_id' => 10,
            'category_name' => 'Laptop',
            'category_parent' => 0,
            'slug_category_product' => 'laptop',
            'category_desc' => 'Danh mục laptop',
            'meta_keywords' => 'laptop',
            'category_order' => 1,
            'category_status' => 0,
        ];

        $childCategory = (object) [
            'category_id' => 11,
            'category_name' => 'Laptop gaming',
            'category_parent' => 10,
            'slug_category_product' => 'laptop-gaming',
            'category_desc' => 'Danh mục laptop gaming',
            'meta_keywords' => 'laptop,gaming',
            'category_order' => 2,
            'category_status' => 0,
        ];

        $categories = collect([$rootCategory, $childCategory]);
        $paginator = new LengthAwarePaginator([$rootCategory, $childCategory], 2, 15, 1);

        $this->actingAs($admin, 'web');

        $this->view('admin.add_category_product', [
            'category' => collect([$rootCategory]),
        ])
            ->assertSee('g-admin-category-product-create')
            ->assertSee('Thêm danh mục sản phẩm')
            ->assertSee('chỉ dùng để tham chiếu cấu trúc dữ liệu cũ')
            ->assertSee('<fieldset disabled>', false);

        $this->view('admin.edit_category_product', [
            'edit_category_product' => collect([$childCategory]),
            'category' => $categories,
        ])
            ->assertSee('g-admin-category-product-edit')
            ->assertSee('Laptop gaming')
            ->assertSee('đang ở chế độ chỉ xem')
            ->assertSee('<fieldset disabled>', false);

        $this->view('admin.all_category_product', [
            'all_category_product' => $paginator,
            'category_product' => $categories,
        ])
            ->assertSee('g-admin-category-product-list')
            ->assertSee('Laptop')
            ->assertSee('Laptop gaming')
            ->assertSee('id="category_order"', false)
            ->assertSee('id="category_sort_notice"', false)
            ->assertSee('data-category-sort-enabled="false"', false)
            ->assertSee('chế độ chỉ xem')
            ->assertSee('kéo thả sắp xếp cũng đang được tắt')
            ->assertDontSee('/delete-category-product/');
    }

    public function test_admin_file_browser_popup_renders_uploaded_images_for_ckeditor(): void
    {
        $admin = $this->createAdminUser();
        $directory = public_path('uploads/ckeditor');

        File::ensureDirectoryExists($directory);

        $filePath = $directory . DIRECTORY_SEPARATOR . 'browser-test-image.jpg';
        File::put($filePath, 'fake-image-content');

        try {
            $this->actingAs($admin, 'web')
                ->get('/file-browser?CKEditorFuncNum=7')
                ->assertOk()
                ->assertSee('Kho ảnh CKEditor')
                ->assertSee('data-file-browser', false)
                ->assertSee('data-func-num="7"', false)
                ->assertSee('browser-test-image.jpg')
                ->assertSee('uploads/ckeditor/browser-test-image.jpg');
        } finally {
            File::delete($filePath);
        }
    }

    public function test_admin_can_upload_ckeditor_image_and_receive_callback_script(): void
    {
        $admin = $this->createAdminUser();
        $directory = public_path('uploads/ckeditor');

        File::ensureDirectoryExists($directory);

        $before = collect(File::files($directory))->map(fn ($file) => $file->getFilename())->all();

        $tempFile = tempnam(sys_get_temp_dir(), 'ckeditor-upload-');
        file_put_contents(
            $tempFile,
            base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAusB9WnRk6sAAAAASUVORK5CYII=')
        );

        try {
            $upload = new UploadedFile(
                $tempFile,
                'ckeditor-upload.png',
                'image/png',
                null,
                true
            );

            $response = $this->actingAs($admin, 'web')->post('/uploads-ckeditor', [
                'CKEditorFuncNum' => 3,
                'upload' => $upload,
            ]);

            $response->assertOk()
                ->assertSee('CKEDITOR.tools.callFunction(3', false)
                ->assertSee('Tải ảnh thành công.');

            $after = collect(File::files($directory))->map(fn ($file) => $file->getFilename())->all();
            $newFiles = array_values(array_diff($after, $before));

            $this->assertCount(1, $newFiles);

            foreach ($newFiles as $fileName) {
                File::delete($directory . DIRECTORY_SEPARATOR . $fileName);
            }
        } finally {
            @unlink($tempFile);
        }
    }

    public function test_admin_information_and_video_pages_render_refreshed_legacy_panels(): void
    {
        $admin = $this->createAdminUser();

        $response = $this->actingAs($admin, 'web')->get('/information');

        $response
            ->assertOk()
            ->assertSee('g-admin-site-info')
            ->assertSee('Thông tin website')
            ->assertSee('backend/css/admin-refresh.css')
            ->assertSee('id="site_info_notice"', false)
            ->assertSee('window.renderSiteInfoNotice', false)
            ->assertDontSee("alert('Thêm nút thành công')", false)
            ->assertDontSee("alert('Thêm đối tác thành công')", false);

        $this->assertMatchesRegularExpression(
            '/<a[^>]*data-admin-sidebar-key="information"[^>]*aria-current="page"/',
            $response->getContent()
        );
        $this->assertDoesNotMatchRegularExpression(
            '/<a[^>]*data-admin-sidebar-key="dashboard"[^>]*aria-current="page"/',
            $response->getContent()
        );
        $this->assertDoesNotMatchRegularExpression(
            '/<button[^>]*data-admin-sidebar-trigger="sliders"[^>]*aria-expanded="true"/',
            $response->getContent()
        );

        $this->actingAs($admin, 'web')
            ->get('/video')
            ->assertOk()
            ->assertSee('g-admin-video-page')
            ->assertSee('Quản lý video')
            ->assertSee('id="video_load"', false)
            ->assertSee('window.renderVideoNotice', false)
            ->assertSee("renderVideoNotice('error', resolveVideoError(xhr, 'Không thể tải danh sách video trên môi trường này.'));", false)
            ->assertSee('if (fileInput && fileInput.files && fileInput.files[0])', false)
            ->assertSee("renderVideoNotice('error', resolveVideoError(xhr, 'Không thể thêm video trên môi trường này.'));", false);
    }

    public function test_admin_can_insert_video_without_optional_thumbnail(): void
    {
        $admin = $this->createAdminUser();

        $this->actingAs($admin, 'web')
            ->withHeaders([
                'X-Requested-With' => 'XMLHttpRequest',
                'Accept' => 'application/json',
            ])
            ->post('/insert-video', [
                'video_title' => 'Video không cần thumbnail',
                'video_slug' => 'video-khong-can-thumbnail',
                'video_desc' => 'Video insert không đính kèm ảnh đại diện.',
                'video_link' => 'https://youtu.be/dQw4w9WgXcQ',
            ])
            ->assertNoContent();

        $this->assertDatabaseHas('videos', [
            'title' => 'Video không cần thumbnail',
            'slug' => 'video-khong-can-thumbnail',
            'thumbnail_path' => null,
        ]);
    }

    public function test_admin_category_post_and_slider_pages_render_refreshed_legacy_shell(): void
    {
        $admin = $this->createAdminUser();
        $category = BlogCategory::create([
            'name' => 'Tin khuyen mai',
            'slug' => 'tin-khuyen-mai',
            'description' => 'Danh muc blog promotion',
            'is_active' => true,
            'display_order' => 0,
        ]);

        $this->ensureSliderTableExists();
        Slider::create([
            'slider_name' => 'Hero slider',
            'slider_alt' => 'Điện thoại mới lên kệ trong tuần',
            'slider_image' => 'missing-slider.jpg',
            'slider_status' => 1,
            'slider_desc' => 'Banner hero storefront cho chiến dịch điện thoại',
            'slider_tone' => 'mobile',
        ]);

        $this->actingAs($admin, 'web')
            ->get('/add-category-post')
            ->assertOk()
            ->assertSee('g-admin-category-post-create')
            ->assertSee('Thêm danh mục bài viết');

        $this->actingAs($admin, 'web')
            ->get('/all-category-post')
            ->assertOk()
            ->assertSee('g-admin-category-post-list')
            ->assertSee($category->name);

        $this->actingAs($admin, 'web')
            ->get('/edit-category-post/' . $category->id)
            ->assertOk()
            ->assertSee('g-admin-category-post-edit')
            ->assertSee($category->name);

        $this->actingAs($admin, 'web')
            ->get('/add-slider')
            ->assertOk()
            ->assertSee('g-admin-slider-create')
            ->assertSee('Thêm trình chiếu')
            ->assertSee('Văn bản thay thế / tiêu đề nổi bật')
            ->assertSee('Phong cách hiển thị')
            ->assertSee('value="1" selected', false)
            ->assertSee('Hiển thị ngay trên giao diện cửa hàng');

        $this->actingAs($admin, 'web')
            ->get('/manage-slider')
            ->assertOk()
            ->assertSee('g-admin-slider-list')
            ->assertSee('Hero slider')
            ->assertSee('Điện thoại mới lên kệ trong tuần')
            ->assertSee('Điện thoại');
    }

    public function test_admin_deleting_missing_blog_category_returns_clear_message(): void
    {
        $admin = $this->createAdminUser();

        $this->actingAs($admin, 'web')
            ->from('/all-category-post')
            ->delete('/delete-category-post/999999')
            ->assertRedirect('/all-category-post')
            ->assertSessionHas('error', 'Không tìm thấy danh mục bài viết cần xóa.');

        $this->actingAs($admin, 'web')
            ->get('/edit-category-post/999999')
            ->assertRedirect('/all-category-post')
            ->assertSessionHas('error', 'Không tìm thấy danh mục bài viết cần chỉnh sửa.');

        $this->actingAs($admin, 'web')
            ->post('/update-category-post/999999', [
                'cate_post_name' => 'Danh muc khong ton tai',
                'cate_post_slug' => 'danh-muc-khong-ton-tai',
                'cate_post_desc' => 'Mo ta',
                'cate_post_status' => '0',
            ])
            ->assertRedirect('/all-category-post')
            ->assertSessionHas('error', 'Không tìm thấy danh mục bài viết cần cập nhật.');
    }

    public function test_admin_slider_status_actions_use_storefront_consistent_visibility_states(): void
    {
        $admin = $this->createAdminUser();

        $this->ensureSliderTableExists();

        $slider = Slider::create([
            'slider_name' => 'Slider cần bật tắt',
            'slider_alt' => 'Banner test toggle',
            'slider_image' => 'missing-slider.jpg',
            'slider_status' => 0,
            'slider_desc' => 'Banner đang ẩn để test bật hiển thị',
            'slider_tone' => 'mobile',
        ]);

        $this->actingAs($admin, 'web')
            ->from('/manage-slider')
            ->patch('/active-slide/' . $slider->slider_id)
            ->assertRedirect('manage-slider')
            ->assertSessionHas('message', 'Đã bật hiển thị trình chiếu thành công');

        $this->assertDatabaseHas('tbl_slider', [
            'slider_id' => $slider->slider_id,
            'slider_status' => 1,
        ]);

        $this->actingAs($admin, 'web')
            ->from('/manage-slider')
            ->patch('/unactive-slide/' . $slider->slider_id)
            ->assertRedirect('manage-slider')
            ->assertSessionHas('message', 'Đã ẩn trình chiếu thành công');

        $this->assertDatabaseHas('tbl_slider', [
            'slider_id' => $slider->slider_id,
            'slider_status' => 0,
        ]);

        $this->actingAs($admin, 'web')
            ->from('/manage-slider')
            ->patch('/active-slide/999999')
            ->assertRedirect('manage-slider')
            ->assertSessionHas('error', 'Không tìm thấy mục trình chiếu cần cập nhật.');

        $this->actingAs($admin, 'web')
            ->from('/manage-slider')
            ->delete('/delete-slide/999999')
            ->assertRedirect('/manage-slider')
            ->assertSessionHas('error', 'Không tìm thấy mục trình chiếu cần xóa.');

        Schema::dropIfExists('tbl_slider');

        $this->actingAs($admin, 'web')
            ->from('/manage-slider')
            ->patch('/active-slide/1')
            ->assertRedirect('manage-slider')
            ->assertSessionHas('error', 'Bảng trình chiếu chưa được cấu hình.');

        $this->actingAs($admin, 'web')
            ->from('/add-slider')
            ->post('/insert-slider', [
                'slider_name' => 'Hero slider',
                'slider_alt' => 'Banner test',
                'slider_status' => 1,
                'slider_desc' => 'Mo ta slider',
                'slider_image' => $this->makeTinyPngUpload('hero-slider.png'),
            ])
            ->assertRedirect('add-slider')
            ->assertSessionHas('error', 'Bảng trình chiếu chưa được cấu hình.');
    }

    public function test_admin_attribute_gallery_and_comment_pages_render_refreshed_legacy_shell(): void
    {
        $admin = $this->createAdminUser();

        $this->ensureLegacyAttributeProductTablesExist();

        $attribute = Attribute::create([
            'name' => 'Màu sắc',
            'description' => 'Thuộc tính màu nền',
            'status' => 0,
        ]);

        Product::create([
            'product_id' => 1,
            'product_name' => 'Legacy Product One',
            'product_slug' => 'legacy-product-one',
            'category_id' => null,
            'brand_id' => null,
            'product_desc' => 'Legacy description',
            'product_content' => 'Legacy content',
            'product_price' => 1000000,
            'product_image' => 'no-image.svg',
            'product_status' => 0,
            'product_tags' => 'legacy',
            'product_views' => 0,
            'price_cost' => 800000,
        ]);

        ProductAttribute::create([
            'product_id' => 1,
            'attribute_id' => $attribute->id,
            'name' => 'Màu đen',
            'price' => 1250000,
            'quantity' => 5,
            'image' => 'no-image.svg',
            'description' => 'Biến thể màu đen',
        ]);

        Gallery::create([
            'gallery_name' => 'gallery-image-1',
            'gallery_image' => 'missing-gallery.jpg',
            'product_id' => 1,
        ]);

        $modernProduct = $this->createModernCommentProduct();
        $rootComment = ProductReview::create([
            'product_id' => $modernProduct->id,
            'author_name' => 'Nguyen Van A',
            'content' => 'Bình luận test admin moderation',
            'rating' => 0,
            'status' => 'pending',
            'is_public' => true,
            'meta' => ['source' => 'test'],
        ]);
        ProductReview::create([
            'product_id' => $modernProduct->id,
            'parent_id' => $rootComment->id,
            'author_name' => 'Admin',
            'content' => 'Phản hồi test admin moderation',
            'rating' => 0,
            'status' => 'approved',
            'is_public' => true,
            'approved_at' => now(),
            'meta' => ['source' => 'admin_reply'],
        ]);

        $this->actingAs($admin, 'web')
            ->get('/attribute')
            ->assertOk()
            ->assertSee('g-admin-attribute-index')
            ->assertSee('Màu sắc');

        $this->actingAs($admin, 'web')
            ->get('/attribute/create')
            ->assertOk()
            ->assertSee('g-admin-attribute-create')
            ->assertSee('Thêm thuộc tính');

        $this->actingAs($admin, 'web')
            ->get('/attribute/' . $attribute->id . '/edit')
            ->assertOk()
            ->assertSee('g-admin-attribute-edit')
            ->assertSee('Màu sắc');

        $this->actingAs($admin, 'web')
            ->get('/product-attribute/1')
            ->assertOk()
            ->assertSee('g-admin-product-attribute-page')
            ->assertSee('Legacy Product One')
            ->assertSee('Màu đen');

        $this->actingAs($admin, 'web')
            ->get('/add-gallery/1')
            ->assertOk()
            ->assertSee('g-admin-gallery-page')
            ->assertSee('id="gallery_load"', false)
            ->assertSee('class="pro_id"', false)
            ->assertSee('id="gallery_notify"', false)
            ->assertSee('window.renderGalleryNotice', false)
            ->assertSee('if (!fileInput || !fileInput.files || !fileInput.files[0])', false);

        $this->actingAs($admin, 'web')
            ->get('/comment')
            ->assertOk()
            ->assertSee('g-admin-comment-list')
            ->assertSee('Nguyen Van A')
            ->assertSee('Bình luận test admin moderation')
            ->assertSee('window.renderCommentNotice', false)
            ->assertSee('window.persistCommentNotice', false)
            ->assertDontSee('text-alert', false);
    }

    public function test_admin_attribute_pages_fall_back_cleanly_when_attribute_table_is_missing(): void
    {
        $admin = $this->createAdminUser();

        $this->ensureLegacyAttributeProductTablesExist();

        Product::create([
            'product_id' => 1,
            'product_name' => 'Legacy Product Missing Attribute Table',
            'product_slug' => 'legacy-product-missing-attribute-table',
            'category_id' => null,
            'brand_id' => null,
            'product_desc' => 'Legacy product for missing attribute table fallback.',
            'product_content' => 'Legacy product content for missing attribute table fallback.',
            'product_price' => 1450000,
            'product_image' => 'no-image.svg',
            'product_status' => 0,
            'product_tags' => 'legacy,attribute,missing',
            'product_views' => 0,
            'product_quantity' => 12,
            'product_sold' => 0,
            'price_cost' => 980000,
        ]);

        ProductAttribute::create([
            'product_id' => 1,
            'attribute_id' => 999,
            'price' => 1500000,
            'quantity' => 3,
            'image' => 'no-image.svg',
            'name' => 'Biến thể fallback',
            'description' => 'Biến thể vẫn cần hiển thị khi thiếu bảng thuộc tính nền.',
        ]);

        Schema::dropIfExists('tbl_attribute');

        $this->actingAs($admin, 'web')
            ->get('/attribute')
            ->assertOk()
            ->assertSee('Danh sách thuộc tính')
            ->assertSee('Bảng thuộc tính nền chưa sẵn sàng');

        $this->actingAs($admin, 'web')
            ->get('/attribute/create')
            ->assertOk()
            ->assertSee('Thêm thuộc tính')
            ->assertSee('Bảng thuộc tính nền chưa sẵn sàng')
            ->assertSee('disabled', false);

        $this->actingAs($admin, 'web')
            ->from('/attribute/create')
            ->post('/attribute', [
                'name' => 'Màu sắc',
                'description' => 'Mô tả fallback',
            ])
            ->assertRedirect(route('attribute.index'))
            ->assertSessionHas('error', 'Bảng thuộc tính chưa sẵn sàng trên môi trường này.');

        $this->actingAs($admin, 'web')
            ->get('/product-attribute/1')
            ->assertOk()
            ->assertSee('Legacy Product Missing Attribute Table')
            ->assertSee('Bảng thuộc tính nền chưa sẵn sàng')
            ->assertSee('Thuộc tính nền chưa sẵn sàng');

        $this->actingAs($admin, 'web')
            ->from('/product-attribute/1')
            ->post(route('product-attribute.store'), [
                'product_id' => 1,
                'name' => 'Biến thể mới',
                'price' => 1550000,
                'quantity' => 4,
                'image' => $this->makeTinyPngUpload('missing-attribute-table.png'),
                'description' => 'Biến thể mới khi thiếu bảng thuộc tính nền.',
                'attribute_id' => 1,
            ])
            ->assertRedirect()
            ->assertSessionHas('message', 'Bảng thuộc tính nền chưa sẵn sàng trên môi trường này.');
    }

    public function test_admin_dashboard_order_and_delivery_pages_render_refreshed_legacy_shell(): void
    {
        $admin = $this->createAdminUser();

        $this->ensureLegacyDashboardTablesExist();
        $legacyOrder = $this->createLegacyOrderFixture();
        $this->createLegacyDeliveryFixture();

        Visitors::create([
            'ip_address' => '127.0.0.100',
            'date_visitor' => now()->subDays(3)->toDateString(),
        ]);

        Statistic::create([
            'order_date' => now()->toDateString(),
            'sales' => 1250000,
            'profit' => 240000,
            'quantity' => 3,
            'total_order' => 1,
        ]);

        $this->actingAs($admin, 'web')
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('g-admin-dashboard')
            ->assertSee('Tổng quan vận hành')
            ->assertSee('Chọn khoảng thời gian hoặc bộ lọc nhanh để xem doanh số')
            ->assertSee('id="dashboard_chart_notice"', false)
            ->assertSee('id="chart"', false)
            ->assertSee('window.renderDashboardChartNotice', false)
            ->assertSee('value="365ngayqua" selected', false)
            ->assertSee('label: "Sản phẩm"', false)
            ->assertSee('label: "Bài viết"', false)
            ->assertSee('label: "Đơn hàng"', false)
            ->assertSee('label: "Khách hàng"', false)
            ->assertSee('id="donut"', false);

        $this->actingAs($admin, 'web')
            ->get('/manage-order')
            ->assertOk()
            ->assertSee('g-admin-order-list')
            ->assertSee($legacyOrder->order_code)
            ->assertSee('Tổng đơn đang quản lý')
            ->assertSee('Quản lý đơn hàng');

        $this->actingAs($admin, 'web')
            ->get('/view-order/' . $legacyOrder->order_code)
            ->assertOk()
            ->assertSee('g-admin-order-view')
            ->assertSee($legacyOrder->order_code)
            ->assertSee('Le Thi Test')
            ->assertSee('Thông tin khách hàng đi kèm với đơn hàng.')
            ->assertSee('update_quantity_order')
            ->assertSee('id="order_management_notice"', false)
            ->assertSee('window.renderOrderManagementNotice', false)
            ->assertDontSee("alert('Cập nhật số lượng thành công')", false)
            ->assertDontSee("alert('Thay đổi tình trạng đơn hàng thành công')", false);

        $this->actingAs($admin, 'web')
            ->get('/order-date?date=' . $legacyOrder->order_date)
            ->assertOk()
            ->assertSee('g-admin-order-date-list')
            ->assertSee('Danh sách đơn hàng được lọc theo khoảng thời gian đã chọn từ trang tổng quan.')
            ->assertSee($legacyOrder->order_code);

        $this->actingAs($admin, 'web')
            ->get('/delivery')
            ->assertOk()
            ->assertSee('g-admin-delivery-page')
            ->assertSee('Cấu hình phí vận chuyển')
            ->assertSee('Hà Nội')
            ->assertSee('id="load_delivery"', false)
            ->assertSee('id="delivery_notify"', false)
            ->assertSee('window.renderDeliveryNotice', false)
            ->assertSee("renderDeliveryNotice('success', 'Đã lưu phí vận chuyển cho khu vực đã chọn.');", false);
    }

    public function test_admin_view_order_renders_cancelled_legacy_order_as_read_only(): void
    {
        $admin = $this->createAdminUser();
        $order = $this->createLegacyOrderFixture([
            'order_code' => 'ADMINLEGACYCANCELLED',
            'order_status' => 3,
            'order_destroy' => 'Khách đổi ý trước khi giao',
        ]);

        $this->actingAs($admin, 'web')
            ->get('/view-order/' . $order->order_code)
            ->assertOk()
            ->assertSee('Đã hủy')
            ->assertSee('Đơn hàng đã bị hủy.')
            ->assertSee('Khách đổi ý trước khi giao')
            ->assertDontSee('name="update_quantity_order"', false)
            ->assertSee('select class="form-control order_details" disabled', false);
    }

    public function test_admin_dashboard_filter_defaults_safely_and_supports_day_month_year_inputs(): void
    {
        $admin = $this->createAdminUser();

        $this->ensureLegacyDashboardTablesExist();

        Statistic::create([
            'order_date' => '2026-03-15',
            'sales' => 1250000,
            'profit' => 240000,
            'quantity' => 3,
            'total_order' => 1,
        ]);

        $this->actingAs($admin, 'web')
            ->postJson('/dashboard-filter', [])
            ->assertOk()
            ->assertJsonFragment([
                'period' => '2026-03-15',
            ]);

        $this->actingAs($admin, 'web')
            ->postJson('/filter-by-date', [
                'from_date' => '14/03/2026',
                'to_date' => '16/03/2026',
            ])
            ->assertOk()
            ->assertJsonFragment([
                'period' => '2026-03-15',
            ]);

        $this->actingAs($admin, 'web')
            ->postJson('/filter-by-date', [
                'from_date' => 'invalid-date',
                'to_date' => 'still-invalid',
            ])
            ->assertOk()
            ->assertExactJson([]);
    }

    public function test_admin_dashboard_records_current_day_visit_for_returning_ip_without_creating_duplicates(): void
    {
        $admin = $this->createAdminUser();
        $today = Carbon::now('Asia/Ho_Chi_Minh')->toDateString();

        $this->ensureLegacyDashboardTablesExist();

        Visitors::create([
            'ip_address' => '203.0.113.10',
            'date_visitor' => now()->subDays(2)->toDateString(),
        ]);

        $this->actingAs($admin, 'web')
            ->withServerVariables([
                'REMOTE_ADDR' => '203.0.113.10',
            ])
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('Tổng quan vận hành')
            ->assertSee('Khách đang được ghi nhận từ IP hiện tại.');

        $this->assertDatabaseHas('tbl_visitors', [
            'ip_address' => '203.0.113.10',
            'date_visitor' => $today,
        ]);
        $this->assertSame(2, Visitors::where('ip_address', '203.0.113.10')->count());
        $this->assertSame(
            1,
            Visitors::where('ip_address', '203.0.113.10')
                ->where('date_visitor', $today)
                ->count()
        );

        $this->actingAs($admin, 'web')
            ->withServerVariables([
                'REMOTE_ADDR' => '203.0.113.10',
            ])
            ->get('/dashboard')
            ->assertOk();

        $this->assertSame(2, Visitors::where('ip_address', '203.0.113.10')->count());
    }

    public function test_admin_print_order_route_returns_not_found_for_unknown_legacy_order(): void
    {
        $admin = $this->createAdminUser();

        $this->actingAs($admin, 'web')
            ->get('/print-order/UNKNOWN-PRINT-ORDER')
            ->assertNotFound();
    }

    public function test_admin_legacy_order_actions_handle_missing_records_and_failure_paths(): void
    {
        $admin = $this->createAdminUser();
        $this->ensureLegacyAttributeProductTablesExist();
        $this->ensureLegacyOrderManagementTablesExist();

        $this->actingAs($admin, 'web')
            ->get('/view-order/UNKNOWN-ORDER-CODE')
            ->assertRedirect('/manage-order')
            ->assertSessionHas('message', 'Không tìm thấy đơn hàng cần xem.');

        $this->actingAs($admin, 'web')
            ->from('/manage-order')
            ->delete('/delete-order/UNKNOWN-ORDER-CODE')
            ->assertRedirect('/manage-order')
            ->assertSessionHas('error', 'Không tìm thấy đơn hàng cần xóa.');

        $this->actingAs($admin, 'web')
            ->postJson('/update-qty', [
                'order_product_id' => 999,
                'order_code' => 'UNKNOWN-ORDER-CODE',
                'order_qty' => 3,
            ])
            ->assertNotFound()
            ->assertJson([
                'message' => 'Không tìm thấy dòng sản phẩm cần cập nhật.',
            ]);

        $this->actingAs($admin, 'web')
            ->postJson('/update-order-qty', [
                'order_id' => 999,
                'order_status' => 2,
                'quantity' => [2],
                'order_product_id' => [11],
            ])
            ->assertNotFound()
            ->assertJson([
                'message' => 'Không tìm thấy đơn hàng cần cập nhật.',
            ]);
    }

    public function test_admin_update_qty_falls_back_cleanly_when_legacy_order_tables_are_missing(): void
    {
        $admin = $this->createAdminUser();

        Schema::dropIfExists('tbl_order_details');
        Schema::dropIfExists('tbl_order');
        Schema::dropIfExists('tbl_product');

        $this->actingAs($admin, 'web')
            ->postJson('/update-qty', [
                'order_product_id' => 11,
                'order_code' => 'ADMINLEGACYMISSING',
                'order_qty' => 3,
            ])
            ->assertNotFound()
            ->assertJson([
                'message' => 'Dữ liệu đơn hàng legacy hiện chưa sẵn sàng trên môi trường này.',
            ]);
    }

    public function test_admin_deleting_legacy_order_also_removes_its_line_items(): void
    {
        $admin = $this->createAdminUser();
        $order = $this->createLegacyOrderFixture([
            'order_code' => 'ADMINLEGACYDELETE',
        ]);

        $this->assertDatabaseHas('tbl_order', [
            'order_code' => 'ADMINLEGACYDELETE',
        ]);
        $this->assertDatabaseHas('tbl_order_details', [
            'order_code' => 'ADMINLEGACYDELETE',
        ]);

        $this->actingAs($admin, 'web')
            ->from('/manage-order')
            ->delete('/delete-order/' . $order->order_code)
            ->assertRedirect('/manage-order')
            ->assertSessionHas('message', 'Xóa đơn hàng thành công');

        $this->assertDatabaseMissing('tbl_order', [
            'order_code' => 'ADMINLEGACYDELETE',
        ]);
        $this->assertDatabaseMissing('tbl_order_details', [
            'order_code' => 'ADMINLEGACYDELETE',
        ]);
    }

    public function test_admin_cannot_finalize_legacy_order_when_stock_is_insufficient_without_partial_mutation(): void
    {
        $admin = $this->createAdminUser();
        $this->ensureLegacyDashboardTablesExist();
        $order = $this->createLegacyOrderFixture([
            'order_code' => 'ADMINLEGACYLOWSTOCK',
        ], [
            'product_quantity' => 1,
            'product_sold' => 4,
        ], [
            'product_sales_quantity' => 1,
        ]);
        $productId = (int) OrderDetails::where('order_code', $order->order_code)->value('product_id');

        $response = $this->actingAs($admin, 'web')
            ->postJson('/update-order-qty', [
                'order_id' => $order->order_id,
                'order_status' => 2,
                'quantity' => [2],
                'order_product_id' => [$productId],
            ]);

        $response->assertStatus(422)
            ->assertJson([
                'message' => 'Số lượng bán trong kho không đủ.',
            ]);

        $order->refresh();
        $detail = OrderDetails::where('order_code', $order->order_code)->where('product_id', $productId)->firstOrFail();
        $product = Product::findOrFail($productId);

        $this->assertSame(1, (int) $order->order_status);
        $this->assertSame(1, (int) $detail->product_sales_quantity);
        $this->assertSame(1, (int) $product->product_quantity);
        $this->assertSame(4, (int) $product->product_sold);
        $this->assertDatabaseMissing('tbl_statistical', [
            'order_date' => $order->order_date,
        ]);
    }

    public function test_admin_finalizing_legacy_order_updates_inventory_statistics_and_is_idempotent(): void
    {
        $admin = $this->createAdminUser();
        $this->ensureLegacyDashboardTablesExist();
        $order = $this->createLegacyOrderFixture([
            'order_code' => 'ADMINLEGACYFINALIZE',
        ], [
            'product_quantity' => 20,
            'product_sold' => 2,
            'product_price' => 1250000,
            'price_cost' => 980000,
        ], [
            'product_sales_quantity' => 1,
        ]);
        $productId = (int) OrderDetails::where('order_code', $order->order_code)->value('product_id');

        $payload = [
            'order_id' => $order->order_id,
            'order_status' => 2,
            'quantity' => [3],
            'order_product_id' => [$productId],
        ];

        $this->actingAs($admin, 'web')
            ->postJson('/update-order-qty', $payload)
            ->assertOk()
            ->assertJson([
                'message' => 'Thay đổi tình trạng đơn hàng thành công.',
            ]);

        $order->refresh();
        $detail = OrderDetails::where('order_code', $order->order_code)->where('product_id', $productId)->firstOrFail();
        $product = Product::findOrFail($productId);
        $statistic = Statistic::where('order_date', $order->order_date)->firstOrFail();

        $this->assertSame(2, (int) $order->order_status);
        $this->assertSame(3, (int) $detail->product_sales_quantity);
        $this->assertSame(17, (int) $product->product_quantity);
        $this->assertSame(5, (int) $product->product_sold);
        $this->assertSame(3750000.0, (float) $statistic->sales);
        $this->assertSame(810000.0, (float) $statistic->profit);
        $this->assertSame(3, (int) $statistic->quantity);
        $this->assertSame(1, (int) $statistic->total_order);

        $this->actingAs($admin, 'web')
            ->postJson('/update-order-qty', $payload)
            ->assertOk()
            ->assertJson([
                'message' => 'Thay đổi tình trạng đơn hàng thành công.',
            ]);

        $product->refresh();
        $statistic->refresh();
        $detail->refresh();
        $order->refresh();

        $this->assertSame(2, (int) $order->order_status);
        $this->assertSame(3, (int) $detail->product_sales_quantity);
        $this->assertSame(17, (int) $product->product_quantity);
        $this->assertSame(5, (int) $product->product_sold);
        $this->assertSame(3750000.0, (float) $statistic->sales);
        $this->assertSame(810000.0, (float) $statistic->profit);
        $this->assertSame(3, (int) $statistic->quantity);
        $this->assertSame(1, (int) $statistic->total_order);
    }

    public function test_admin_multi_product_legacy_order_finalization_aggregates_inventory_and_statistics_once(): void
    {
        $admin = $this->createAdminUser();
        $this->ensureLegacyDashboardTablesExist();
        $order = $this->createLegacyOrderFixture([
            'order_code' => 'ADMINLEGACYMULTI',
        ], [
            'product_quantity' => 20,
            'product_sold' => 2,
            'product_price' => 1250000,
            'price_cost' => 980000,
        ], [
            'product_sales_quantity' => 1,
        ]);

        $secondProductId = $this->appendLegacyOrderLineItem($order, [
            'product_id' => 12,
            'product_name' => 'Legacy Dashboard Product Two',
            'product_slug' => 'legacy-dashboard-product-two',
            'product_price' => 900000,
            'product_quantity' => 15,
            'product_sold' => 1,
            'price_cost' => 650000,
            'product_image' => 'no-image.svg',
            'product_status' => 0,
            'product_tags' => 'legacy,multi',
            'product_views' => 0,
        ], [
            'product_name' => 'Legacy Dashboard Product Two',
            'product_attribute' => 'Màu bạc',
            'product_price' => 900000,
            'product_sales_quantity' => 2,
            'product_feeship' => 30000,
        ]);

        $payload = [
            'order_id' => $order->order_id,
            'order_status' => 2,
            'quantity' => [3, 2],
            'order_product_id' => [11, $secondProductId],
        ];

        $this->actingAs($admin, 'web')
            ->postJson('/update-order-qty', $payload)
            ->assertOk()
            ->assertJson([
                'message' => 'Thay đổi tình trạng đơn hàng thành công.',
            ]);

        $order->refresh();
        $firstDetail = OrderDetails::where('order_code', $order->order_code)->where('product_id', 11)->firstOrFail();
        $secondDetail = OrderDetails::where('order_code', $order->order_code)->where('product_id', $secondProductId)->firstOrFail();
        $firstProduct = Product::findOrFail(11);
        $secondProduct = Product::findOrFail($secondProductId);
        $statistic = Statistic::where('order_date', $order->order_date)->firstOrFail();

        $this->assertSame(2, (int) $order->order_status);
        $this->assertSame(3, (int) $firstDetail->product_sales_quantity);
        $this->assertSame(2, (int) $secondDetail->product_sales_quantity);
        $this->assertSame(17, (int) $firstProduct->product_quantity);
        $this->assertSame(5, (int) $firstProduct->product_sold);
        $this->assertSame(13, (int) $secondProduct->product_quantity);
        $this->assertSame(3, (int) $secondProduct->product_sold);
        $this->assertSame(5550000.0, (float) $statistic->sales);
        $this->assertSame(1310000.0, (float) $statistic->profit);
        $this->assertSame(5, (int) $statistic->quantity);
        $this->assertSame(1, (int) $statistic->total_order);

        $this->actingAs($admin, 'web')
            ->postJson('/update-order-qty', $payload)
            ->assertOk();

        $firstProduct->refresh();
        $secondProduct->refresh();
        $statistic->refresh();

        $this->assertSame(17, (int) $firstProduct->product_quantity);
        $this->assertSame(5, (int) $firstProduct->product_sold);
        $this->assertSame(13, (int) $secondProduct->product_quantity);
        $this->assertSame(3, (int) $secondProduct->product_sold);
        $this->assertSame(5550000.0, (float) $statistic->sales);
        $this->assertSame(1310000.0, (float) $statistic->profit);
        $this->assertSame(5, (int) $statistic->quantity);
        $this->assertSame(1, (int) $statistic->total_order);
    }

    public function test_admin_multi_product_legacy_order_finalization_rejects_on_second_line_without_mutating_first(): void
    {
        $admin = $this->createAdminUser();
        $this->ensureLegacyDashboardTablesExist();
        $order = $this->createLegacyOrderFixture([
            'order_code' => 'ADMINLEGACYMULTIFAIL',
        ], [
            'product_quantity' => 20,
            'product_sold' => 2,
        ], [
            'product_sales_quantity' => 1,
        ]);

        $secondProductId = $this->appendLegacyOrderLineItem($order, [
            'product_id' => 12,
            'product_name' => 'Legacy Dashboard Product Fail',
            'product_slug' => 'legacy-dashboard-product-fail',
            'product_price' => 900000,
            'product_quantity' => 1,
            'product_sold' => 6,
            'price_cost' => 650000,
            'product_image' => 'no-image.svg',
            'product_status' => 0,
            'product_tags' => 'legacy,multi,fail',
            'product_views' => 0,
        ], [
            'product_name' => 'Legacy Dashboard Product Fail',
            'product_attribute' => 'Màu đỏ',
            'product_price' => 900000,
            'product_sales_quantity' => 1,
            'product_feeship' => 30000,
        ]);

        $this->actingAs($admin, 'web')
            ->postJson('/update-order-qty', [
                'order_id' => $order->order_id,
                'order_status' => 2,
                'quantity' => [2, 2],
                'order_product_id' => [11, $secondProductId],
            ])
            ->assertStatus(422)
            ->assertJson([
                'message' => 'Số lượng bán trong kho không đủ.',
            ]);

        $order->refresh();
        $firstDetail = OrderDetails::where('order_code', $order->order_code)->where('product_id', 11)->firstOrFail();
        $secondDetail = OrderDetails::where('order_code', $order->order_code)->where('product_id', $secondProductId)->firstOrFail();
        $firstProduct = Product::findOrFail(11);
        $secondProduct = Product::findOrFail($secondProductId);

        $this->assertSame(1, (int) $order->order_status);
        $this->assertSame(1, (int) $firstDetail->product_sales_quantity);
        $this->assertSame(1, (int) $secondDetail->product_sales_quantity);
        $this->assertSame(20, (int) $firstProduct->product_quantity);
        $this->assertSame(2, (int) $firstProduct->product_sold);
        $this->assertSame(1, (int) $secondProduct->product_quantity);
        $this->assertSame(6, (int) $secondProduct->product_sold);
        $this->assertDatabaseMissing('tbl_statistical', [
            'order_date' => $order->order_date,
        ]);
    }

    public function test_admin_legacy_printable_html_aggregates_coupon_and_shared_shipping_for_multi_product_order(): void
    {
        $this->ensureLegacyDashboardTablesExist();
        $this->ensureLegacyCouponTableExists();

        Coupon::create([
            'coupon_name' => 'Giảm 10%',
            'coupon_code' => 'VIP10',
            'coupon_time' => 20,
            'coupon_number' => 10,
            'coupon_condition' => 1,
            'coupon_date_start' => '01/03/2026',
            'coupon_date_end' => '31/12/2026',
            'coupon_status' => 1,
            'coupon_used' => '',
        ]);

        $order = $this->createLegacyOrderFixture([
            'order_code' => 'ADMINLEGACYPRINT',
        ], [
            'product_quantity' => 20,
            'product_sold' => 0,
            'product_price' => 1250000,
            'price_cost' => 980000,
        ], [
            'product_sales_quantity' => 2,
            'product_coupon' => 'VIP10',
            'product_feeship' => 45000,
        ]);

        $secondProductId = $this->appendLegacyOrderLineItem($order, [
            'product_id' => 12,
            'product_name' => 'Legacy Printable Product Two',
            'product_slug' => 'legacy-printable-product-two',
            'product_price' => 900000,
            'product_quantity' => 15,
            'product_sold' => 1,
            'price_cost' => 650000,
            'product_image' => 'no-image.svg',
            'product_status' => 0,
            'product_tags' => 'legacy,print',
            'product_views' => 0,
        ], [
            'product_name' => 'Legacy Printable Product Two',
            'product_attribute' => 'Màu bạc',
            'product_price' => 900000,
            'product_sales_quantity' => 1,
            'product_coupon' => 'VIP10',
            'product_feeship' => 45000,
        ]);

        $html = app(LegacyOrderPresentationService::class)->renderPrintableHtml($order->order_code);

        $this->assertNotNull($html);
        $this->assertStringContainsString('Phiếu đơn hàng #' . $order->order_code, $html);
        $this->assertStringContainsString('Legacy Dashboard Product', $html);
        $this->assertStringContainsString('Legacy Printable Product Two', $html);
        $this->assertStringContainsString('VIP10', $html);
        $this->assertStringContainsString('45.000đ', $html);
        $this->assertStringContainsString('3.105.000đ', $html);
        $this->assertStringContainsString('10%', $html);

        $this->assertNotNull(Product::find($secondProductId));
    }

    public function test_admin_coupon_users_and_roles_pages_render_refreshed_shell_and_keep_assignment_working(): void
    {
        $admin = $this->createAdminUser();

        $this->ensureLegacyCouponTableExists();

        Coupon::create([
            'coupon_name' => 'Flash Sale VIP',
            'coupon_code' => 'FLASHVIP',
            'coupon_time' => 50,
            'coupon_number' => 15,
            'coupon_condition' => 1,
            'coupon_date_start' => '01/03/2026',
            'coupon_date_end' => '31/12/2026',
            'coupon_status' => 1,
            'coupon_used' => '',
        ]);

        $managerRole = Role::firstOrCreate([
            'name' => 'Manager',
            'guard_name' => AdminAccess::guardName(),
        ]);
        $managerRole->syncPermissions(AdminPermissions::defaultsForRole('Manager'));

        $staffRole = Role::firstOrCreate([
            'name' => 'Staff',
            'guard_name' => AdminAccess::guardName(),
        ]);
        $staffRole->syncPermissions(AdminPermissions::defaultsForRole('Staff'));

        $managedUser = User::factory()->create([
            'name' => 'Content Operator',
            'email' => 'operator@example.com',
        ]);
        $managedUser->assignRole($managerRole);
        Cache::put('admin-panel-role-page-smoke', 'still-here', now()->addMinutes(5));

        $this->actingAs($admin, 'web')
            ->get('/insert-coupon')
            ->assertOk()
            ->assertSee('g-admin-coupon-create')
            ->assertSee('Thêm mã giảm giá');

        $this->actingAs($admin, 'web')
            ->get('/list-coupon')
            ->assertOk()
            ->assertSee('g-admin-coupon-list')
            ->assertSee('Flash Sale VIP')
            ->assertSee('Gửi khách VIP');

        $this->actingAs($admin, 'web')
            ->get('/add-users')
            ->assertOk()
            ->assertSee('g-admin-user-create')
            ->assertSee('Thêm người dùng quản trị');

        $this->actingAs($admin, 'web')
            ->get('/users')
            ->assertOk()
            ->assertSee('g-admin-user-list')
            ->assertSee('Content Operator')
            ->assertSee('operator@example.com')
            ->assertSee('name="roles[]"', false)
            ->assertSee('-- Chưa gán vai trò --');

        $this->actingAs($admin, 'web')
            ->get('/index-spatie')
            ->assertOk()
            ->assertSee('g-admin-role-index')
            ->assertSee('Content Operator')
            ->assertSee('manage orders');

        $this->assertSame('still-here', Cache::get('admin-panel-role-page-smoke'));

        $this->actingAs($admin, 'web')
            ->get('/create-spatie/' . $managedUser->id)
            ->assertOk()
            ->assertSee('g-admin-role-create')
            ->assertSee('value="Manager"', false)
            ->assertSee('Cấp vai trò cho Content Operator');

        $this->actingAs($admin, 'web')
            ->get('/create-permission/' . $managedUser->id)
            ->assertOk()
            ->assertSee('g-admin-permission-create')
            ->assertSee('value="manage orders"', false)
            ->assertSee('Cập nhật quyền cho Content Operator')
            ->assertSee('không gán quyền trực tiếp riêng lẻ lên user');

        $this->actingAs($admin, 'web')
            ->post('/assign-role/' . $managedUser->id, [
                'role' => ['Staff'],
            ])
            ->assertRedirect()
            ->assertSessionHas('message', 'Đã cập nhật vai trò cho người dùng');

        $managedUser->refresh();
        $this->assertTrue($managedUser->hasRole('Staff'));

        $this->actingAs($admin, 'web')
            ->post('/assign-permission/' . $managedUser->id, [
                'permission' => ['manage products', 'manage content'],
            ])
            ->assertRedirect()
            ->assertSessionHas('message', 'Đã cập nhật quyền cho vai trò hiện tại');

        $staffRole->refresh();
        $this->assertTrue($staffRole->hasPermissionTo('manage products'));
        $this->assertTrue($staffRole->hasPermissionTo('manage content'));
    }

    public function test_admin_users_inline_role_sync_rejects_multiple_roles_and_permission_page_warns_without_role(): void
    {
        $admin = $this->createAdminUser();

        $managerRole = Role::firstOrCreate([
            'name' => 'Manager',
            'guard_name' => AdminAccess::guardName(),
        ]);
        $managerRole->syncPermissions(AdminPermissions::defaultsForRole('Manager'));

        $staffRole = Role::firstOrCreate([
            'name' => 'Staff',
            'guard_name' => AdminAccess::guardName(),
        ]);
        $staffRole->syncPermissions(AdminPermissions::defaultsForRole('Staff'));

        $rolelessUser = User::factory()->create([
            'name' => 'Roleless Admin',
            'email' => 'roleless-admin@example.com',
        ]);

        $this->actingAs($admin, 'web')
            ->from('/users')
            ->post('/assign-roles', [
                'admin_id' => $rolelessUser->id,
                'admin_email' => $rolelessUser->email,
                'roles' => ['Manager', 'Staff'],
            ])
            ->assertRedirect('/users')
            ->assertSessionHasErrors(['roles']);

        $this->actingAs($admin, 'web')
            ->get('/create-permission/' . $rolelessUser->id)
            ->assertOk()
            ->assertSee('Tài khoản này chưa có vai trò')
            ->assertSee('disabled', false);

        $this->actingAs($admin, 'web')
            ->from('/create-permission/' . $rolelessUser->id)
            ->post('/assign-permission/' . $rolelessUser->id, [
                'permission' => ['manage products'],
            ])
            ->assertRedirect('/create-permission/' . $rolelessUser->id)
            ->assertSessionHas('message', 'Tài khoản này chưa có vai trò để cập nhật quyền');
    }

    public function test_admin_role_and_permission_pages_reject_self_updates(): void
    {
        $admin = $this->createAdminUser([
            'name' => 'Self Protected Admin',
            'email' => 'self-protected-admin@example.com',
        ]);

        $staffRole = Role::firstOrCreate([
            'name' => 'Staff',
            'guard_name' => AdminAccess::guardName(),
        ]);
        $staffRole->syncPermissions(AdminPermissions::defaultsForRole('Staff'));

        $this->actingAs($admin, 'web')
            ->from('/create-spatie/' . $admin->id)
            ->post('/assign-role/' . $admin->id, [
                'role' => ['Staff'],
            ])
            ->assertRedirect('/create-spatie/' . $admin->id)
            ->assertSessionHas('message', 'Bạn không được cập nhật vai trò của chính mình');

        $this->actingAs($admin, 'web')
            ->from('/create-permission/' . $admin->id)
            ->post('/assign-permission/' . $admin->id, [
                'permission' => ['manage products'],
            ])
            ->assertRedirect('/create-permission/' . $admin->id)
            ->assertSessionHas('message', 'Bạn không được cập nhật quyền của chính mình');

        $admin->refresh();
        $this->assertTrue($admin->hasRole('Admin'));
    }

    public function test_admin_coupon_page_marks_expired_coupons_correctly_for_day_month_year_dates(): void
    {
        $admin = $this->createAdminUser();

        $this->ensureLegacyCouponTableExists();

        Coupon::create([
            'coupon_name' => 'Expired Coupon',
            'coupon_code' => 'EXPIRED10',
            'coupon_time' => 10,
            'coupon_number' => 10,
            'coupon_condition' => 1,
            'coupon_date_start' => '01/01/2025',
            'coupon_date_end' => '02/01/2025',
            'coupon_status' => 1,
            'coupon_used' => '',
        ]);

        Coupon::create([
            'coupon_name' => 'Future Coupon',
            'coupon_code' => 'FUTURE10',
            'coupon_time' => 10,
            'coupon_number' => 10,
            'coupon_condition' => 1,
            'coupon_date_start' => '01/01/2027',
            'coupon_date_end' => '31/12/2027',
            'coupon_status' => 1,
            'coupon_used' => '',
        ]);

        $this->actingAs($admin, 'web')
            ->get('/insert-coupon')
            ->assertOk()
            ->assertSee('placeholder="DD/MM/YYYY"', false);

        $this->actingAs($admin, 'web')
            ->get('/list-coupon')
            ->assertOk()
            ->assertSee('Expired Coupon')
            ->assertSee('Future Coupon')
            ->assertSee('>1<', false)
            ->assertSee('Đã hết hạn')
            ->assertSee('Còn hạn');
    }

    public function test_admin_coupon_page_disables_campaign_actions_for_locked_or_expired_coupons(): void
    {
        $admin = $this->createAdminUser();

        $this->ensureLegacyCouponTableExists();

        Coupon::create([
            'coupon_name' => 'Locked Coupon',
            'coupon_code' => 'LOCKADMIN',
            'coupon_time' => 10,
            'coupon_number' => 15,
            'coupon_condition' => 1,
            'coupon_date_start' => '01/03/2026',
            'coupon_date_end' => '31/12/2026',
            'coupon_status' => 0,
            'coupon_used' => '',
        ]);

        Coupon::create([
            'coupon_name' => 'Expired Coupon Admin',
            'coupon_code' => 'EXPIREDADMIN',
            'coupon_time' => 10,
            'coupon_number' => 15,
            'coupon_condition' => 1,
            'coupon_date_start' => '01/01/2025',
            'coupon_date_end' => '02/01/2025',
            'coupon_status' => 1,
            'coupon_used' => '',
        ]);

        $this->actingAs($admin, 'web')
            ->get('/list-coupon')
            ->assertOk()
            ->assertSee('Locked Coupon')
            ->assertSee('Expired Coupon Admin')
            ->assertSee('Coupon đang bị khóa')
            ->assertSee('Coupon đã hết hạn')
            ->assertSee('button type="button" class="btn btn-primary btn-sm" disabled', false);
    }

    public function test_admin_deleting_missing_coupon_returns_clear_message(): void
    {
        $admin = $this->createAdminUser();

        $this->ensureLegacyCouponTableExists();

        $this->actingAs($admin, 'web')
            ->from('/list-coupon')
            ->delete('/delete-coupon/999999')
            ->assertRedirect('list-coupon')
            ->assertSessionHas('error', 'Không tìm thấy mã giảm giá cần xóa.');
    }

    public function test_admin_coupon_list_page_surfaces_error_feedback_after_failed_actions(): void
    {
        $admin = $this->createAdminUser();

        $this->ensureLegacyCouponTableExists();

        $this->actingAs($admin, 'web')
            ->followingRedirects()
            ->from('/list-coupon')
            ->delete('/delete-coupon/999999')
            ->assertOk()
            ->assertSee('Liệt kê mã giảm giá')
            ->assertSee('Không tìm thấy mã giảm giá cần xóa.');
    }

    public function test_admin_coupon_create_page_preserves_input_and_renders_validation_errors(): void
    {
        $admin = $this->createAdminUser();

        $this->ensureLegacyCouponTableExists();

        $response = $this->actingAs($admin, 'web')
            ->followingRedirects()
            ->from('/insert-coupon')
            ->post('/insert-coupon-code', [
                'coupon_name' => 'Invalid coupon',
                'coupon_date_start' => '31/12/2026',
                'coupon_date_end' => '01/12/2026',
                'coupon_code' => 'INVALID',
                'coupon_time' => 5,
                'coupon_condition' => 1,
                'coupon_number' => 10,
            ])
            ->assertOk()
            ->assertSee('Thêm mã giảm giá')
            ->assertSee('Ngày kết thúc phải lớn hơn hoặc bằng ngày bắt đầu.')
            ->assertSee('value="Invalid coupon"', false)
            ->assertSee('value="INVALID"', false)
            ->assertSee('value="31/12/2026"', false)
            ->assertSee('value="01/12/2026"', false)
            ->assertSee('value="5"', false)
            ->assertSee('value="10"', false);
    }

    public function test_admin_order_date_page_surfaces_delete_errors_in_the_page_shell(): void
    {
        $admin = $this->createAdminUser();
        $this->ensureLegacyDashboardTablesExist();
        $order = $this->createLegacyOrderFixture([
            'order_code' => 'ADMINORDERDATEVIEW',
            'order_date' => '2026-03-15',
        ]);

        $this->actingAs($admin, 'web')
            ->followingRedirects()
            ->from('/order-date?date=' . $order->order_date)
            ->delete('/delete-order/UNKNOWN-ORDER-FROM-DATE')
            ->assertOk()
            ->assertSee('Đơn hàng theo ngày')
            ->assertSee('Không tìm thấy đơn hàng cần xóa.');
    }

    public function test_admin_document_manager_page_renders_refreshed_shell_with_public_disk_fallback(): void
    {
        $admin = $this->createAdminUser();

        Storage::fake('public');
        Storage::disk('public')->put('documents/manual.pdf', 'fake-pdf-content');
        Storage::disk('public')->put('documents/banner.txt', 'banner copy');
        Storage::disk('public')->put('misc/private.txt', 'private-content');

        $this->actingAs($admin, 'web')
            ->get('/read_data')
            ->assertOk()
            ->assertSee('g-admin-document-page')
            ->assertSee('Kho tài liệu')
            ->assertSee('PUBLIC')
            ->assertSee('manual.pdf')
            ->assertSee('documents/manual.pdf')
            ->assertDontSee('misc/private.txt')
            ->assertSee('Tải xuống');
    }

    public function test_legacy_admin_gg_document_route_maps_to_document_manager(): void
    {
        $admin = $this->createAdminUser();

        Storage::fake('public');
        Storage::disk('public')->put('documents/manual.pdf', 'fake-pdf-content');

        $this->actingAs($admin, 'web')
            ->get('/gg-document')
            ->assertOk()
            ->assertSee('Kho tài liệu')
            ->assertSee('manual.pdf');
    }

    public function test_admin_document_routes_handle_download_delete_and_failure_paths(): void
    {
        $admin = $this->createAdminUser();

        Storage::fake('public');
        Storage::disk('public')->put('documents/manual.pdf', 'fake-pdf-content');
        Storage::disk('public')->put('misc/private.txt', 'private-content');

        $this->actingAs($admin, 'web')
            ->get('/download_document/documents/manual.pdf/manual.pdf')
            ->assertOk()
            ->assertHeader('Content-Disposition', 'attachment; filename="manual.pdf"');

        $this->actingAs($admin, 'web')
            ->get('/download_document/%2E%2E/%2Eenv/.env')
            ->assertNotFound();

        $this->actingAs($admin, 'web')
            ->get('/download_document/misc/private.txt/private.txt')
            ->assertNotFound();

        $this->actingAs($admin, 'web')
            ->from('/read_data')
            ->delete('/delete_document/documents/missing.pdf')
            ->assertRedirect('/read_data')
            ->assertSessionHas('error', 'Không tìm thấy tài liệu cần xóa.');

        $this->actingAs($admin, 'web')
            ->from('/read_data')
            ->delete('/delete_document/documents/manual.pdf')
            ->assertRedirect('/read_data')
            ->assertSessionHas('message', 'Xóa tài liệu thành công');

        Storage::disk('public')->assertMissing('documents/manual.pdf');
        Storage::disk('public')->assertExists('misc/private.txt');
    }

    public function test_admin_document_manager_surfaces_delete_errors_in_the_page_shell(): void
    {
        $admin = $this->createAdminUser();

        Storage::fake('public');

        $this->actingAs($admin, 'web')
            ->followingRedirects()
            ->from('/read_data')
            ->delete('/delete_document/documents/missing.pdf')
            ->assertOk()
            ->assertSee('Không tìm thấy tài liệu cần xóa.');
    }

    public function test_admin_can_save_initial_site_information_with_slogan_logo(): void
    {
        $admin = $this->createAdminUser();

        if (! Schema::hasTable('tbl_infomation')) {
            Schema::create('tbl_infomation', function (Blueprint $table) {
                $table->increments('info_id');
                $table->text('info_contact');
                $table->text('info_map')->nullable();
                $table->string('info_logo')->nullable();
                $table->string('slogan_logo')->nullable();
                $table->text('info_fanpage')->nullable();
            });
        }

        $this->actingAs($admin, 'web')
            ->from('/information')
            ->post('/save-info', [
                'info_contact' => 'Thong tin website demo',
                'slogan_logo' => 'Mua sắm công nghệ gọn hơn',
                'info_map' => '<iframe src="https://example.com/map"></iframe>',
                'info_fanpage' => '<iframe src="https://example.com/fanpage"></iframe>',
            ])
            ->assertRedirect('/information')
            ->assertSessionHas('message', 'Cập nhật thông tin website thành công');

        $this->assertDatabaseHas('tbl_infomation', [
            'info_contact' => 'Thong tin website demo',
            'slogan_logo' => 'Mua sắm công nghệ gọn hơn',
        ]);

        $this->assertSame('Mua sắm công nghệ gọn hơn', Contact::query()->value('slogan_logo'));
    }

    public function test_admin_save_info_upserts_canonical_information_row(): void
    {
        $admin = $this->createAdminUser();

        if (! Schema::hasTable('tbl_infomation')) {
            Schema::create('tbl_infomation', function (Blueprint $table) {
                $table->increments('info_id');
                $table->text('info_contact');
                $table->text('info_map')->nullable();
                $table->string('info_logo')->nullable();
                $table->string('slogan_logo')->nullable();
                $table->text('info_fanpage')->nullable();
            });
        }

        Contact::query()->insert([
            'info_id' => 2,
            'info_contact' => 'Legacy row',
            'info_map' => null,
            'info_logo' => null,
            'slogan_logo' => 'Old slogan',
            'info_fanpage' => null,
        ]);

        $this->actingAs($admin, 'web')
            ->from('/information')
            ->post('/save-info', [
                'info_contact' => 'Canonical website info',
                'slogan_logo' => 'Canonical slogan',
                'info_map' => '<iframe src="https://example.com/map-2"></iframe>',
                'info_fanpage' => '<iframe src="https://example.com/fanpage-2"></iframe>',
            ])
            ->assertRedirect('/information')
            ->assertSessionHas('message', 'Cập nhật thông tin website thành công');

        $this->assertDatabaseHas('tbl_infomation', [
            'info_id' => 1,
            'info_contact' => 'Canonical website info',
            'slogan_logo' => 'Canonical slogan',
        ]);
        $this->assertDatabaseCount('tbl_infomation', 2);
    }

    public function test_admin_legacy_actions_validate_input_before_persisting_changes(): void
    {
        $admin = $this->createAdminUser();

        $this->ensureLegacyCouponTableExists();
        $this->ensureSliderTableExists();
        $this->createLegacyDeliveryFixture();
        $this->ensureLegacyIconsTableExists();

        $managedUser = User::factory()->create([
            'name' => 'Needs Validation',
            'email' => 'needs-validation@example.com',
        ]);

        Role::firstOrCreate([
            'name' => 'Staff',
            'guard_name' => AdminAccess::guardName(),
        ]);

        $this->actingAs($admin, 'web')
            ->from('/insert-coupon')
            ->post('/insert-coupon-code', [
                'coupon_name' => 'Invalid coupon',
                'coupon_date_start' => '31/12/2026',
                'coupon_date_end' => '01/12/2026',
                'coupon_code' => 'INVALID',
                'coupon_time' => 5,
                'coupon_condition' => 1,
                'coupon_number' => 10,
            ])
            ->assertRedirect('/insert-coupon')
            ->assertSessionHasErrors(['coupon_date_end']);

        $this->assertDatabaseCount('tbl_coupon', 0);

        $this->actingAs($admin, 'web')
            ->from('/add-users')
            ->post('/store-users', [
                'admin_name' => 'Bad User',
                'admin_email' => 'not-an-email',
                'admin_phone' => '0123',
                'admin_password' => '123',
            ])
            ->assertRedirect('/add-users')
            ->assertSessionHasErrors(['admin_email', 'admin_password']);

        $this->assertDatabaseMissing('users', ['email' => 'not-an-email']);

        $this->actingAs($admin, 'web')
            ->from('/create-spatie/' . $managedUser->id)
            ->post('/assign-role/' . $managedUser->id, [
                'role' => ['Unknown Role'],
            ])
            ->assertRedirect('/create-spatie/' . $managedUser->id)
            ->assertSessionHasErrors(['role.0']);

        $this->actingAs($admin, 'web')
            ->withHeaders([
                'X-Requested-With' => 'XMLHttpRequest',
                'Accept' => 'application/json',
            ])
            ->post('/insert-delivery', [
                'city' => 999,
                'province' => 10,
                'wards' => 100,
                'fee_ship' => 'invalid-fee',
            ])
            ->assertStatus(422);

        $this->assertDatabaseCount('tbl_feeship', 1);

        $this->actingAs($admin, 'web')
            ->withHeaders([
                'Accept' => 'application/json',
            ])
            ->post('/insert-slider', [
                'slider_name' => 'Slider invalid',
                'slider_status' => 1,
                'slider_desc' => 'Invalid image upload',
                'slider_image' => UploadedFile::fake()->create('not-image.pdf', 10, 'application/pdf'),
            ])
            ->assertStatus(422);

        $this->actingAs($admin, 'web')
            ->withHeaders([
                'X-Requested-With' => 'XMLHttpRequest',
                'Accept' => 'application/json',
            ])
            ->post('/insert-video', [
                'video_title' => '',
                'video_slug' => '',
                'video_desc' => 'Missing title',
                'video_link' => '',
                'file' => UploadedFile::fake()->create('bad-video.pdf', 10, 'application/pdf'),
            ])
            ->assertStatus(422);

        $this->assertDatabaseCount('videos', 0);

        $this->actingAs($admin, 'web')
            ->withHeaders([
                'X-Requested-With' => 'XMLHttpRequest',
                'Accept' => 'application/json',
            ])
            ->post('/add-nut', [
                'name' => '',
                'link' => str_repeat('a', 600),
            ])
            ->assertStatus(422);
    }

    public function test_admin_created_user_receives_safe_default_role(): void
    {
        $admin = $this->createAdminUser();

        config()->set('admin-auth.provisioning.default_role', 'Staff');

        $this->actingAs($admin, 'web')
            ->from('/add-users')
            ->post('/store-users', [
                'admin_name' => 'Fresh Staff',
                'admin_email' => 'fresh-staff@example.com',
                'admin_phone' => '0909123123',
                'admin_password' => 'secret123',
            ])
            ->assertRedirect('/users')
            ->assertSessionHas('message', 'Thêm users thành công và đã gán vai trò Staff');

        $createdUser = User::where('email', 'fresh-staff@example.com')->first();

        $this->assertNotNull($createdUser);
        $this->assertTrue($createdUser->hasRole('Staff'));
        $this->assertTrue(AdminAccess::canAccess($createdUser));
    }

    public function test_users_page_keeps_role_sync_action_visible_for_user_without_role(): void
    {
        $admin = $this->createAdminUser();
        $rolelessUser = User::factory()->create([
            'name' => 'No Role Yet',
            'email' => 'roleless-admin@example.com',
        ]);

        Role::firstOrCreate([
            'name' => 'Staff',
            'guard_name' => AdminAccess::guardName(),
        ]);

        $this->actingAs($admin, 'web')
            ->get('/users')
            ->assertOk()
            ->assertSee('roleless-admin@example.com')
            ->assertSee('Chưa có vai trò')
            ->assertSee('Cập nhật vai trò')
            ->assertSee('value="' . $rolelessUser->id . '"', false);
    }

    public function test_users_page_surfaces_role_sync_failures_with_clear_feedback(): void
    {
        $admin = $this->createAdminUser();

        $this->actingAs($admin, 'web')
            ->followingRedirects()
            ->from('/users')
            ->post('/assign-roles', [
                'admin_id' => 999999,
                'roles' => ['Admin'],
            ])
            ->assertOk()
            ->assertSee('Không tìm thấy người dùng cần cập nhật vai trò.');

        $targetUser = User::factory()->create([
            'name' => 'Temp Admin',
            'email' => 'temp-admin@example.com',
        ]);

        $this->actingAs($admin, 'web')
            ->delete('/delete-user-roles/' . $targetUser->id)
            ->assertRedirect()
            ->assertSessionHas('message', 'Xóa user thành công');

        $this->actingAs($admin, 'web')
            ->followingRedirects()
            ->from('/users')
            ->delete('/delete-user-roles/' . $targetUser->id)
            ->assertOk()
            ->assertSee('Không tìm thấy người dùng cần xóa');
    }

    public function test_admin_can_store_product_attribute_with_shared_upload_service_and_persist_description(): void
    {
        $admin = $this->createAdminUser();

        $this->ensureLegacyAttributeProductTablesExist();

        $attribute = Attribute::create([
            'name' => 'Dung lượng',
            'description' => 'Thuộc tính dung lượng',
            'status' => 0,
        ]);

        $product = Product::create([
            'product_name' => 'Legacy Attribute Product',
            'product_slug' => 'legacy-attribute-product',
            'category_id' => null,
            'brand_id' => null,
            'product_desc' => 'Legacy attribute product description',
            'product_content' => 'Legacy attribute product content',
            'product_price' => 3500000,
            'product_image' => 'no-image.svg',
            'product_status' => 0,
            'product_tags' => 'legacy,attribute',
            'product_views' => 0,
            'price_cost' => 2900000,
        ]);

        $tinyPngPath = storage_path('framework/testing/tiny-product-attribute.png');
        if (! is_dir(dirname($tinyPngPath))) {
            mkdir(dirname($tinyPngPath), 0777, true);
        }
        file_put_contents(
            $tinyPngPath,
            base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAusB9pG2bN0AAAAASUVORK5CYII=')
        );

        $this->actingAs($admin, 'web')
            ->post(route('product-attribute.store'), [
                'product_id' => $product->product_id,
                'name' => '256GB',
                'price' => 3990000,
                'quantity' => 8,
                'image' => new UploadedFile($tinyPngPath, 'attribute.png', 'image/png', null, true),
                'description' => 'Biến thể dung lượng 256GB',
                'attribute_id' => $attribute->id,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('tbl_product_attribute', [
            'product_id' => $product->product_id,
            'name' => '256GB',
            'description' => 'Biến thể dung lượng 256GB',
            'attribute_id' => $attribute->id,
        ]);
    }

    public function test_admin_deleting_product_attribute_also_removes_uploaded_image(): void
    {
        $admin = $this->createAdminUser();

        $this->ensureLegacyAttributeProductTablesExist();

        File::ensureDirectoryExists(public_path('uploads/attribute'));
        File::put(public_path('uploads/attribute/test-delete-attribute.png'), 'attribute-image');

        $productAttribute = ProductAttribute::create([
            'product_id' => 1,
            'attribute_id' => 1,
            'price' => 150000,
            'quantity' => 5,
            'image' => 'test-delete-attribute.png',
            'name' => 'Xóa ảnh thuộc tính',
            'description' => 'Thuộc tính dùng để test xóa ảnh',
        ]);

        $this->actingAs($admin, 'web')
            ->delete(route('product-attribute.delete', $productAttribute->id))
            ->assertRedirect();

        $this->assertDatabaseMissing('tbl_product_attribute', [
            'id' => $productAttribute->id,
        ]);
        $this->assertFalse(File::exists(public_path('uploads/attribute/test-delete-attribute.png')));
    }

    public function test_admin_comment_moderation_endpoints_update_status_and_create_reply(): void
    {
        $admin = $this->createAdminUser([
            'name' => 'Admin Moderator',
        ]);

        $modernProduct = $this->createModernCommentProduct();
        $rootComment = ProductReview::create([
            'product_id' => $modernProduct->id,
            'author_name' => 'Khach hang',
            'content' => 'Bình luận cần duyệt',
            'rating' => 0,
            'status' => 'pending',
            'is_public' => true,
            'meta' => ['source' => 'test'],
        ]);

        $this->actingAs($admin, 'web')
            ->post('/allow-comment', [
                'comment_status' => 0,
                'comment_id' => $rootComment->id,
                'comment_product_id' => $modernProduct->id,
            ])
            ->assertOk()
            ->assertJson([
                'message' => 'ok',
                'status' => 0,
            ]);

        $this->assertDatabaseHas('product_reviews', [
            'id' => $rootComment->id,
            'status' => 'approved',
        ]);

        $this->actingAs($admin, 'web')
            ->post('/reply-comment', [
                'comment' => 'Admin da tra loi',
                'comment_id' => $rootComment->id,
                'comment_product_id' => $modernProduct->id,
            ])
            ->assertOk()
            ->assertJson([
                'message' => 'ok',
            ]);

        $this->assertDatabaseHas('product_reviews', [
            'product_id' => $modernProduct->id,
            'parent_id' => $rootComment->id,
            'author_name' => 'Admin Moderator',
            'content' => 'Admin da tra loi',
            'status' => 'approved',
        ]);
    }

    public function test_admin_can_store_and_update_attribute_via_form_requests(): void
    {
        $admin = $this->createAdminUser();

        $this->ensureLegacyAttributeProductTablesExist();

        $this->actingAs($admin, 'web')
            ->post(route('attribute.store'), [
                'name' => 'Màu sắc',
                'description' => 'Thuộc tính màu sắc',
            ])
            ->assertRedirect(route('attribute.index'));

        $attribute = Attribute::query()->firstOrFail();

        $this->assertDatabaseHas('tbl_attribute', [
            'id' => $attribute->id,
            'name' => 'Màu sắc',
            'description' => 'Thuộc tính màu sắc',
        ]);

        $this->actingAs($admin, 'web')
            ->put(route('attribute.update', $attribute->id), [
                'name' => 'Màu hoàn thiện',
                'description' => 'Thuộc tính màu sau khi cập nhật',
            ])
            ->assertRedirect(route('attribute.index'));

        $this->assertDatabaseHas('tbl_attribute', [
            'id' => $attribute->id,
            'name' => 'Màu hoàn thiện',
            'description' => 'Thuộc tính màu sau khi cập nhật',
        ]);
    }

    public function test_admin_attribute_actions_handle_missing_records_gracefully(): void
    {
        $admin = $this->createAdminUser();

        $this->ensureLegacyAttributeProductTablesExist();

        $this->actingAs($admin, 'web')
            ->get(route('attribute.edit', 999999))
            ->assertRedirect(route('attribute.index'))
            ->assertSessionHas('error', 'Không tìm thấy thuộc tính cần cập nhật.');

        $this->actingAs($admin, 'web')
            ->put(route('attribute.update', 999999), [
                'name' => 'Màu sắc',
                'description' => 'Thuộc tính màu sắc',
            ])
            ->assertRedirect(route('attribute.index'))
            ->assertSessionHas('error', 'Không tìm thấy thuộc tính cần cập nhật.');

        $this->actingAs($admin, 'web')
            ->delete(route('attribute.destroy', 999999))
            ->assertRedirect(route('attribute.index'))
            ->assertSessionHas('error', 'Không tìm thấy thuộc tính cần xóa.');
    }

    public function test_admin_can_manage_gallery_with_form_requests_and_shared_upload_service(): void
    {
        $admin = $this->createAdminUser();

        $this->ensureLegacyAttributeProductTablesExist();

        $product = Product::create([
            'product_name' => 'Legacy Gallery Product',
            'product_slug' => 'legacy-gallery-product',
            'category_id' => null,
            'brand_id' => null,
            'product_desc' => 'Legacy gallery description',
            'product_content' => 'Legacy gallery content',
            'product_price' => 2750000,
            'product_image' => 'no-image.svg',
            'product_status' => 0,
            'product_tags' => 'legacy,gallery',
            'product_views' => 0,
            'price_cost' => 2200000,
        ]);

        $this->actingAs($admin, 'web')
            ->post('/insert-gallery/' . $product->product_id, [
                'file' => [$this->makeTinyPngUpload('gallery-original.png')],
            ])
            ->assertRedirect();

        $gallery = Gallery::query()->where('product_id', $product->product_id)->firstOrFail();
        $originalImage = $gallery->gallery_image;

        $this->assertDatabaseHas('tbl_gallery', [
            'gallery_id' => $gallery->gallery_id,
            'product_id' => $product->product_id,
        ]);
        $this->assertTrue(file_exists(public_path('uploads/gallery/' . $originalImage)));

        $this->actingAs($admin, 'web')
            ->post('/update-gallery-name', [
                'gal_id' => $gallery->gallery_id,
                'gal_text' => 'gallery-updated-name',
            ])
            ->assertNoContent();

        $this->assertDatabaseHas('tbl_gallery', [
            'gallery_id' => $gallery->gallery_id,
            'gallery_name' => 'gallery-updated-name',
        ]);

        $this->actingAs($admin, 'web')
            ->post('/update-gallery', [
                'gal_id' => $gallery->gallery_id,
                'file' => $this->makeTinyPngUpload('gallery-replaced.png'),
            ])
            ->assertNoContent();

        $gallery->refresh();
        $this->assertNotSame($originalImage, $gallery->gallery_image);
        $this->assertTrue(file_exists(public_path('uploads/gallery/' . $gallery->gallery_image)));

        $updatedImage = $gallery->gallery_image;

        $this->actingAs($admin, 'web')
            ->post('/delete-gallery', [
                'gal_id' => $gallery->gallery_id,
            ])
            ->assertNoContent();

        $this->assertDatabaseMissing('tbl_gallery', [
            'gallery_id' => $gallery->gallery_id,
        ]);
        $this->assertFalse(file_exists(public_path('uploads/gallery/' . $updatedImage)));
    }

    public function test_admin_gallery_ajax_falls_back_cleanly_when_gallery_table_is_missing(): void
    {
        $admin = $this->createAdminUser();
        $this->ensureLegacyAttributeProductTablesExist();

        $product = Product::create([
            'product_name' => 'Legacy Gallery Missing Table Product',
            'product_slug' => 'legacy-gallery-missing-table-product',
            'category_id' => null,
            'brand_id' => null,
            'product_desc' => 'Legacy gallery missing table description',
            'product_content' => 'Legacy gallery missing table content',
            'product_price' => 1000000,
            'product_image' => 'no-image.svg',
            'product_status' => 0,
            'product_tags' => 'legacy,gallery,missing',
            'product_views' => 0,
            'product_quantity' => 10,
            'product_sold' => 0,
            'price_cost' => 700000,
        ]);

        Schema::dropIfExists('tbl_gallery');

        $this->actingAs($admin, 'web')
            ->post('/select-gallery', [
                'pro_id' => $product->product_id,
            ])
            ->assertOk()
            ->assertSee('Chưa có bảng thư viện ảnh');

        $this->actingAs($admin, 'web')
            ->post('/update-gallery-name', [
                'gal_id' => 1,
                'gal_text' => 'renamed',
            ])
            ->assertStatus(422)
            ->assertJson([
                'message' => 'Bảng thư viện ảnh chưa sẵn sàng trên môi trường này.',
            ]);

        $this->actingAs($admin, 'web')
            ->post('/delete-gallery', [
                'gal_id' => 1,
            ])
            ->assertStatus(422)
            ->assertJson([
                'message' => 'Bảng thư viện ảnh chưa sẵn sàng trên môi trường này.',
            ]);

        $this->actingAs($admin, 'web')
            ->post('/update-gallery', [
                'gal_id' => 1,
                'file' => $this->makeTinyPngUpload('gallery-missing-table.png'),
            ])
            ->assertStatus(422)
            ->assertJson([
                'message' => 'Bảng thư viện ảnh chưa sẵn sàng trên môi trường này.',
            ]);
    }

    public function test_admin_gallery_ajax_reports_missing_records_with_clear_error_statuses(): void
    {
        $admin = $this->createAdminUser();

        $this->ensureLegacyAttributeProductTablesExist();

        $this->actingAs($admin, 'web')
            ->withHeaders([
                'X-Requested-With' => 'XMLHttpRequest',
                'Accept' => 'application/json',
            ])
            ->post('/update-gallery-name', [
                'gal_id' => 999999,
                'gal_text' => 'renamed',
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['gal_id'])
            ->assertJsonPath('errors.gal_id.0', 'Không tìm thấy ảnh trong thư viện cần cập nhật.');

        $this->actingAs($admin, 'web')
            ->withHeaders([
                'X-Requested-With' => 'XMLHttpRequest',
                'Accept' => 'application/json',
            ])
            ->post('/update-gallery', [
                'gal_id' => 999999,
                'file' => $this->makeTinyPngUpload('gallery-missing-record.png'),
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['gal_id'])
            ->assertJsonPath('errors.gal_id.0', 'Không tìm thấy ảnh trong thư viện cần cập nhật.');

        $this->actingAs($admin, 'web')
            ->withHeaders([
                'X-Requested-With' => 'XMLHttpRequest',
                'Accept' => 'application/json',
            ])
            ->post('/delete-gallery', [
                'gal_id' => 999999,
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['gal_id'])
            ->assertJsonPath('errors.gal_id.0', 'Không tìm thấy ảnh trong thư viện cần xóa.');
    }

    public function test_admin_legacy_ajax_renderers_keep_expected_hooks_after_blade_partial_refactor(): void
    {
        $admin = $this->createAdminUser();

        $this->createLegacyDeliveryFixture();
        $this->ensureLegacyIconsTableExists();
        $this->ensureLegacyAttributeProductTablesExist();

        $galleryProduct = Product::create([
            'product_name' => 'Legacy Gallery Ajax Product',
            'product_slug' => 'legacy-gallery-ajax-product',
            'category_id' => null,
            'brand_id' => null,
            'product_desc' => 'Legacy ajax gallery description',
            'product_content' => 'Legacy ajax gallery content',
            'product_price' => 1950000,
            'product_image' => 'no-image.svg',
            'product_status' => 0,
            'product_tags' => 'legacy,ajax,gallery',
            'product_views' => 0,
            'price_cost' => 1500000,
        ]);

        $hotlineButton = new Icons();
        $hotlineButton->name = 'Hotline Button';
        $hotlineButton->image = 'hotline.png';
        $hotlineButton->link = 'https://example.com/hotline';
        $hotlineButton->category = 'icons';
        $hotlineButton->save();

        $partnerIcon = new Icons();
        $partnerIcon->name = 'Tech Partner';
        $partnerIcon->image = 'partner.png';
        $partnerIcon->link = 'https://example.com/partner';
        $partnerIcon->category = 'doitac';
        $partnerIcon->save();

        DB::table('videos')->insert([
            'title' => 'Admin ajax video',
            'slug' => 'admin-ajax-video',
            'description' => 'Video for ajax markup test',
            'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'thumbnail_path' => null,
            'is_active' => true,
            'display_order' => 1,
            'views_count' => 0,
            'meta' => null,
            'created_at' => now(),
            'updated_at' => now(),
            'deleted_at' => null,
        ]);

        Gallery::create([
            'gallery_name' => 'gallery-markup-test',
            'gallery_image' => 'gallery-markup.png',
            'product_id' => $galleryProduct->product_id,
        ]);

        $this->actingAs($admin, 'web')
            ->post('/select-feeship')
            ->assertOk()
            ->assertSee('g-admin-data-table')
            ->assertSee('fee_feeship_edit')
            ->assertSee('30.000');

        $this->actingAs($admin, 'web')
            ->post('/select-delivery', [
                'action' => 'city',
                'ma_id' => 1,
            ])
            ->assertOk()
            ->assertSee('---Chọn quận huyện---')
            ->assertSee('value="10"', false)
            ->assertSee('Ba Đình');

        $this->actingAs($admin, 'web')
            ->post('/select-delivery', [
                'action' => 'province',
                'ma_id' => 10,
            ])
            ->assertOk()
            ->assertSee('---Chọn xã phường---')
            ->assertSee('value="100"', false)
            ->assertSee('Phúc Xá');

        $this->actingAs($admin, 'web')
            ->post('/select-video')
            ->assertOk()
            ->assertSee('video_edit')
            ->assertSee('btn-delete-video')
            ->assertSee('file_img_video')
            ->assertSee('Admin ajax video');

        $this->actingAs($admin, 'web')
            ->post('/select-gallery', [
                'pro_id' => $galleryProduct->product_id,
            ])
            ->assertOk()
            ->assertSee('edit_gal_name')
            ->assertSee('delete-gallery')
            ->assertSee('file_image')
            ->assertSee('gallery-markup-test');

        $this->actingAs($admin, 'web')
            ->get('/list-nut')
            ->assertOk()
            ->assertSee('Hotline Button')
            ->assertSee('delete_icons(this.id)', false)
            ->assertSee('btn btn-danger', false);

        $this->actingAs($admin, 'web')
            ->get('/list-doitac')
            ->assertOk()
            ->assertSee('Tên đối tác')
            ->assertSee('delete_icons(this.id)', false)
            ->assertSee('btn btn-warning', false);
    }

    public function test_admin_information_video_and_delivery_pages_fall_back_cleanly_when_legacy_tables_are_missing(): void
    {
        $admin = $this->createAdminUser();

        Schema::dropIfExists('tbl_icons');
        Schema::dropIfExists('videos');
        Schema::dropIfExists('tbl_videos');
        Schema::dropIfExists('tbl_feeship');
        Schema::dropIfExists('tbl_province');
        Schema::dropIfExists('tbl_wards');

        $this->actingAs($admin, 'web')
            ->get('/information')
            ->assertOk()
            ->assertSee('Thông tin website')
            ->assertSee('method: "DELETE"', false);

        $this->actingAs($admin, 'web')
            ->get('/list-nut')
            ->assertOk()
            ->assertSee('Chưa có bảng đối tác/icons');

        $this->actingAs($admin, 'web')
            ->get('/list-doitac')
            ->assertOk()
            ->assertSee('Chưa có bảng đối tác/icons');

        $this->actingAs($admin, 'web')
            ->delete('/delete-icons', ['id' => 999])
            ->assertNoContent();

        $this->actingAs($admin, 'web')
            ->post('/select-video')
            ->assertOk()
            ->assertSee('Chưa có video nào hết');

        $this->actingAs($admin, 'web')
            ->post('/select-feeship')
            ->assertOk()
            ->assertSee('Chưa có cấu hình phí vận chuyển nào');

        $this->actingAs($admin, 'web')
            ->post('/select-delivery', [
                'action' => 'city',
                'ma_id' => 1,
            ])
            ->assertOk()
            ->assertSee('---Không có dữ liệu quận huyện---');

        $this->actingAs($admin, 'web')
            ->post('/select-delivery', [
                'action' => 'province',
                'ma_id' => 10,
            ])
            ->assertOk()
            ->assertSee('---Không có dữ liệu xã phường---');
    }

    public function test_admin_delete_icons_also_removes_uploaded_icon_file(): void
    {
        $admin = $this->createAdminUser();
        $this->ensureLegacyIconsTableExists();

        $iconDirectory = public_path('uploads/icons');
        File::ensureDirectoryExists($iconDirectory);
        File::put($iconDirectory . '/legacy-delete-icon.png', 'icon');

        $icon = new Icons();
        $icon->name = 'Delete Me';
        $icon->image = 'legacy-delete-icon.png';
        $icon->link = 'https://example.com';
        $icon->category = 'icons';
        $icon->save();

        $this->actingAs($admin, 'web')
            ->delete('/delete-icons', ['id' => $icon->id_icons])
            ->assertNoContent();

        $this->assertDatabaseMissing('tbl_icons', [
            'id_icons' => $icon->id_icons,
        ]);
        $this->assertFileDoesNotExist($iconDirectory . '/legacy-delete-icon.png');
    }

    public function test_admin_video_actions_return_clear_errors_for_missing_storage_and_missing_records(): void
    {
        $admin = $this->createAdminUser();

        Schema::dropIfExists('videos');
        Schema::dropIfExists('tbl_videos');

        $this->actingAs($admin, 'web')
            ->postJson('/insert-video', [
                'video_title' => 'Video lỗi',
                'video_slug' => 'video-loi',
                'video_desc' => 'Mo ta',
                'video_link' => 'https://youtu.be/dQw4w9WgXcQ',
            ])
            ->assertStatus(422)
            ->assertJson([
                'message' => 'Chưa có nơi lưu video trên môi trường này.',
            ]);

        Schema::create('videos', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('video_url')->nullable();
            $table->string('thumbnail_path')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('display_order')->default(0);
            $table->unsignedBigInteger('views_count')->default(0);
            $table->json('meta')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        DB::table('videos')->insert([
            'title' => 'Video ton tai',
            'slug' => 'video-ton-tai',
            'description' => 'Test',
            'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'thumbnail_path' => null,
            'is_active' => true,
            'display_order' => 1,
            'views_count' => 0,
            'meta' => null,
            'created_at' => now(),
            'updated_at' => now(),
            'deleted_at' => null,
        ]);

        $this->actingAs($admin, 'web')
            ->postJson('/delete-video', [
                'video_id' => 999999,
            ])
            ->assertNotFound()
            ->assertJson([
                'message' => 'Không tìm thấy video cần xóa.',
            ]);

        $this->actingAs($admin, 'web')
            ->postJson('/update-video', [
                'video_id' => 999999,
                'video_check' => 'video_title',
                'video_edit' => 'Cap nhat that bai',
            ])
            ->assertNotFound()
            ->assertJson([
                'message' => 'Không tìm thấy video cần cập nhật.',
            ]);

        $this->actingAs($admin, 'web')
            ->postJson('/insert-video', [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['video_title', 'video_link'])
            ->assertJsonPath('errors.video_title.0', 'Vui lòng nhập tên video.')
            ->assertJsonPath('errors.video_link.0', 'Vui lòng nhập link video.');
    }

    public function test_admin_can_insert_video_into_legacy_tbl_video_schema(): void
    {
        $admin = $this->createAdminUser();

        Schema::dropIfExists('videos');
        Schema::dropIfExists('tbl_videos');
        Schema::dropIfExists('tbl_video');

        Schema::create('tbl_video', function (Blueprint $table) {
            $table->increments('video_id');
            $table->string('video_title');
            $table->string('video_slug');
            $table->string('video_link');
            $table->string('video_image')->nullable();
            $table->text('video_desc')->nullable();
            $table->integer('video_status')->default(1);
        });

        $this->actingAs($admin, 'web')
            ->post('/insert-video', [
                'video_title' => 'Legacy admin insert',
                'video_slug' => 'legacy-admin-insert',
                'video_desc' => 'Mo ta video legacy',
                'video_link' => 'https://youtu.be/dQw4w9WgXcQ',
            ])
            ->assertNoContent();

        $this->assertDatabaseHas('tbl_video', [
            'video_title' => 'Legacy admin insert',
            'video_slug' => 'legacy-admin-insert',
            'video_link' => 'dQw4w9WgXcQ',
            'video_status' => 1,
        ]);
    }

    public function test_admin_video_list_renders_safe_preview_fallback_for_invalid_video_links(): void
    {
        $admin = $this->createAdminUser();

        Schema::dropIfExists('videos');
        Schema::dropIfExists('tbl_videos');

        Schema::create('videos', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('video_url')->nullable();
            $table->string('thumbnail_path')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('display_order')->default(0);
            $table->unsignedBigInteger('views_count')->default(0);
            $table->json('meta')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        DB::table('videos')->insert([
            'title' => 'Video chưa có preview',
            'slug' => 'video-chua-co-preview',
            'description' => 'Test preview fallback',
            'video_url' => '',
            'thumbnail_path' => null,
            'is_active' => true,
            'display_order' => 1,
            'views_count' => 0,
            'meta' => null,
            'created_at' => now(),
            'updated_at' => now(),
            'deleted_at' => null,
        ]);

        $this->actingAs($admin, 'web')
            ->post('/select-video')
            ->assertOk()
            ->assertSee('Video chưa có preview')
            ->assertSee('Chưa có link xem trước hợp lệ.');
    }

    public function test_admin_insert_delivery_updates_existing_location_instead_of_creating_duplicate_rows(): void
    {
        $admin = $this->createAdminUser();
        $this->createLegacyDeliveryFixture();

        $this->assertDatabaseCount('tbl_feeship', 1);

        $this->actingAs($admin, 'web')
            ->post('/insert-delivery', [
                'city' => 1,
                'province' => 10,
                'wards' => 100,
                'fee_ship' => '45.000',
            ])
            ->assertNoContent();

        $this->assertDatabaseCount('tbl_feeship', 1);
        $this->assertDatabaseHas('tbl_feeship', [
            'fee_matp' => 1,
            'fee_maqh' => 10,
            'fee_xaid' => 100,
            'fee_feeship' => 45000,
        ]);
    }

    public function test_admin_select_feeship_survives_missing_location_tables(): void
    {
        $admin = $this->createAdminUser();
        $this->createLegacyDeliveryFixture();

        Schema::dropIfExists('tbl_xaphuongthitran');
        Schema::dropIfExists('tbl_quanhuyen');
        Schema::dropIfExists('tbl_tinhthanhpho');

        $this->actingAs($admin, 'web')
            ->post('/select-feeship')
            ->assertOk()
            ->assertSee('g-admin-data-table')
            ->assertSee('Chưa cấu hình')
            ->assertSee('30.000');
    }

    protected function createAdminUser(array $attributes = []): User
    {
        foreach (AdminPermissions::all() as $permissionName) {
            Permission::firstOrCreate([
                'name' => $permissionName,
                'guard_name' => AdminAccess::guardName(),
            ]);
        }

        $adminRole = Role::firstOrCreate([
            'name' => 'Admin',
            'guard_name' => AdminAccess::guardName(),
        ]);
        $adminRole->syncPermissions(AdminPermissions::defaultsForRole('Admin'));

        $user = User::factory()->create($attributes);
        $user->assignRole($adminRole);

        return $user;
    }

    protected function makeTinyPngUpload(string $filename): UploadedFile
    {
        $filePath = storage_path('framework/testing/' . Str::uuid() . '-' . $filename);

        if (! is_dir(dirname($filePath))) {
            mkdir(dirname($filePath), 0777, true);
        }

        file_put_contents(
            $filePath,
            base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAusB9pG2bN0AAAAASUVORK5CYII=')
        );

        return new UploadedFile($filePath, $filename, 'image/png', null, true);
    }

    protected function ensureSliderTableExists(): void
    {
        if (Schema::hasTable('tbl_slider')) {
            return;
        }

        Schema::create('tbl_slider', function (Blueprint $table) {
            $table->increments('slider_id');
            $table->string('slider_name')->nullable();
            $table->string('slider_alt')->nullable();
            $table->string('slider_image')->nullable();
            $table->integer('slider_status')->default(0);
            $table->text('slider_desc')->nullable();
            $table->string('slider_tone', 30)->nullable();
        });
    }

    protected function ensureLegacyAttributeProductTablesExist(): void
    {
        if (! Schema::hasTable('tbl_attribute')) {
            Schema::create('tbl_attribute', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name');
                $table->text('description')->nullable();
                $table->integer('status')->default(0);
            });
        }

        if (! Schema::hasTable('tbl_product')) {
            Schema::create('tbl_product', function (Blueprint $table) {
                $table->increments('product_id');
                $table->string('product_name');
                $table->string('product_slug')->nullable();
                $table->integer('category_id')->nullable();
                $table->integer('brand_id')->nullable();
                $table->text('product_desc')->nullable();
                $table->longText('product_content')->nullable();
                $table->bigInteger('product_price')->default(0);
                $table->string('product_image')->nullable();
                $table->integer('product_status')->default(0);
                $table->string('product_tags')->nullable();
                $table->integer('product_views')->default(0);
                $table->integer('product_quantity')->default(0);
                $table->integer('product_sold')->default(0);
                $table->bigInteger('price_cost')->default(0);
            });
        }

        if (! Schema::hasColumn('tbl_product', 'product_quantity')) {
            Schema::table('tbl_product', function (Blueprint $table) {
                $table->integer('product_quantity')->default(0);
            });
        }

        if (! Schema::hasColumn('tbl_product', 'product_sold')) {
            Schema::table('tbl_product', function (Blueprint $table) {
                $table->integer('product_sold')->default(0);
            });
        }

        if (! Schema::hasTable('tbl_product_attribute')) {
            Schema::create('tbl_product_attribute', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('product_id');
                $table->integer('attribute_id');
                $table->string('name');
                $table->bigInteger('price')->default(0);
                $table->integer('quantity')->default(0);
                $table->string('image')->nullable();
                $table->text('description')->nullable();
            });
        }

        if (! Schema::hasTable('tbl_gallery')) {
            Schema::create('tbl_gallery', function (Blueprint $table) {
                $table->increments('gallery_id');
                $table->string('gallery_name')->nullable();
                $table->string('gallery_image')->nullable();
                $table->integer('product_id');
            });
        }
    }

    protected function ensureLegacyDashboardTablesExist(): void
    {
        if (! Schema::hasTable('tbl_visitors')) {
            Schema::create('tbl_visitors', function (Blueprint $table) {
                $table->increments('id_visitors');
                $table->string('ip_address')->nullable();
                $table->date('date_visitor')->nullable();
            });
        }

        if (! Schema::hasTable('tbl_statistical')) {
            Schema::create('tbl_statistical', function (Blueprint $table) {
                $table->increments('id_statistical');
                $table->date('order_date')->nullable();
                $table->bigInteger('sales')->default(0);
                $table->bigInteger('profit')->default(0);
                $table->integer('quantity')->default(0);
                $table->integer('total_order')->default(0);
            });
        }
    }

    protected function ensureLegacyCouponTableExists(): void
    {
        if (Schema::hasTable('tbl_coupon')) {
            return;
        }

        Schema::create('tbl_coupon', function (Blueprint $table) {
            $table->increments('coupon_id');
            $table->string('coupon_name')->nullable();
            $table->string('coupon_code')->nullable();
            $table->integer('coupon_time')->default(0);
            $table->bigInteger('coupon_number')->default(0);
            $table->integer('coupon_condition')->default(1);
            $table->string('coupon_date_start')->nullable();
            $table->string('coupon_date_end')->nullable();
            $table->integer('coupon_status')->default(1);
            $table->text('coupon_used')->nullable();
        });
    }

    protected function ensureLegacyIconsTableExists(): void
    {
        if (Schema::hasTable('tbl_icons')) {
            return;
        }

        Schema::create('tbl_icons', function (Blueprint $table) {
            $table->increments('id_icons');
            $table->string('name')->nullable();
            $table->string('image')->nullable();
            $table->string('link', 500)->nullable();
            $table->string('category')->nullable();
        });
    }

    protected function ensureLegacyOrderManagementTablesExist(): void
    {
        if (! Schema::hasTable('tbl_customers')) {
            Schema::create('tbl_customers', function (Blueprint $table) {
                $table->increments('customer_id');
                $table->string('customer_name')->nullable();
                $table->string('customer_email')->nullable();
                $table->string('customer_password')->nullable();
                $table->string('customer_phone')->nullable();
                $table->integer('customer_vip')->default(0);
                $table->string('customer_picture')->nullable();
            });
        }

        if (! Schema::hasTable('tbl_shipping')) {
            Schema::create('tbl_shipping', function (Blueprint $table) {
                $table->increments('shipping_id');
                $table->string('shipping_name')->nullable();
                $table->string('shipping_address')->nullable();
                $table->string('shipping_phone')->nullable();
                $table->string('shipping_email')->nullable();
                $table->text('shipping_notes')->nullable();
                $table->integer('shipping_method')->default(1);
            });
        }

        if (! Schema::hasTable('tbl_order')) {
            Schema::create('tbl_order', function (Blueprint $table) {
                $table->increments('order_id');
                $table->integer('customer_id');
                $table->integer('shipping_id');
                $table->integer('order_status')->default(1);
                $table->string('order_code')->unique();
                $table->string('order_destroy')->nullable();
                $table->date('order_date')->nullable();
                $table->timestamp('created_at')->nullable();
            });
        }

        if (! Schema::hasTable('tbl_order_details')) {
            Schema::create('tbl_order_details', function (Blueprint $table) {
                $table->increments('order_details_id');
                $table->string('order_code');
                $table->integer('product_id');
                $table->string('product_name');
                $table->string('product_attribute')->nullable();
                $table->bigInteger('product_price')->default(0);
                $table->integer('product_sales_quantity')->default(0);
                $table->string('product_coupon')->default('no');
                $table->bigInteger('product_feeship')->default(0);
            });
        }
    }

    protected function ensureLegacyDeliveryTablesExist(): void
    {
        if (! Schema::hasTable('tbl_tinhthanhpho')) {
            Schema::create('tbl_tinhthanhpho', function (Blueprint $table) {
                $table->increments('matp');
                $table->string('name_city')->nullable();
                $table->string('type')->nullable();
            });
        }

        if (! Schema::hasTable('tbl_quanhuyen')) {
            Schema::create('tbl_quanhuyen', function (Blueprint $table) {
                $table->increments('maqh');
                $table->integer('matp');
                $table->string('name_quanhuyen')->nullable();
                $table->string('type')->nullable();
            });
        }

        if (! Schema::hasTable('tbl_xaphuongthitran')) {
            Schema::create('tbl_xaphuongthitran', function (Blueprint $table) {
                $table->increments('xaid');
                $table->integer('maqh');
                $table->string('name_xaphuong')->nullable();
                $table->string('type')->nullable();
            });
        }

        if (! Schema::hasTable('tbl_feeship')) {
            Schema::create('tbl_feeship', function (Blueprint $table) {
                $table->increments('fee_id');
                $table->integer('fee_matp');
                $table->integer('fee_maqh');
                $table->integer('fee_xaid');
                $table->bigInteger('fee_feeship')->default(0);
            });
        }
    }

    protected function createLegacyOrderFixture(array $orderOverrides = [], array $productOverrides = [], array $detailOverrides = []): Order
    {
        $this->ensureLegacyAttributeProductTablesExist();
        $this->ensureLegacyOrderManagementTablesExist();

        if (! Product::where('product_id', 11)->exists()) {
            $product = new Product();
            $product->forceFill(array_merge([
                'product_id' => 11,
                'product_name' => 'Legacy Dashboard Product',
                'product_slug' => 'legacy-dashboard-product',
                'category_id' => null,
                'brand_id' => null,
                'product_desc' => 'Legacy dashboard description',
                'product_content' => 'Legacy dashboard content',
                'product_price' => 1250000,
                'product_image' => 'no-image.svg',
                'product_status' => 0,
                'product_tags' => 'legacy,dashboard',
                'product_views' => 42,
                'product_quantity' => 20,
                'product_sold' => 0,
                'price_cost' => 980000,
            ], $productOverrides));
            $product->save();
        } elseif ($productOverrides !== []) {
            $product = Product::findOrFail(11);
            $product->forceFill($productOverrides);
            $product->save();
        }

        $customer = Customer::create([
            'customer_name' => 'Le Thi Test',
            'customer_email' => 'legacy-order@example.com',
            'customer_password' => '',
            'customer_phone' => '0909000001',
        ]);

        $shipping = Shipping::create([
            'shipping_name' => 'Le Thi Test',
            'shipping_address' => '123 Le Loi, Quan 1',
            'shipping_phone' => '0909000001',
            'shipping_email' => 'legacy-order@example.com',
            'shipping_notes' => 'Giao trong giờ hành chính',
            'shipping_method' => 1,
        ]);

        $order = Order::create(array_merge([
            'customer_id' => $customer->customer_id,
            'shipping_id' => $shipping->shipping_id,
            'order_status' => 1,
            'order_code' => 'ADMINLEGACY001',
            'created_at' => now(),
            'order_date' => now()->toDateString(),
        ], $orderOverrides));

        $orderDetails = new OrderDetails();
        $orderDetails->forceFill(array_merge([
            'order_code' => $order->order_code,
            'product_id' => 11,
            'product_name' => 'Legacy Dashboard Product',
            'product_attribute' => 'Màu đen',
            'product_price' => 1250000,
            'product_sales_quantity' => 1,
            'product_coupon' => 'no',
            'product_feeship' => 30000,
        ], $detailOverrides));
        $orderDetails->save();

        return $order;
    }

    protected function appendLegacyOrderLineItem(Order $order, array $productOverrides = [], array $detailOverrides = []): int
    {
        $this->ensureLegacyAttributeProductTablesExist();
        $productId = (int) ($productOverrides['product_id'] ?? ((int) Product::max('product_id') + 1));

        $product = new Product();
        $product->forceFill(array_merge([
            'product_id' => $productId,
            'product_name' => 'Legacy Extra Product ' . $productId,
            'product_slug' => 'legacy-extra-product-' . $productId,
            'category_id' => null,
            'brand_id' => null,
            'product_desc' => 'Legacy extra product description',
            'product_content' => 'Legacy extra product content',
            'product_price' => 1000000,
            'product_image' => 'no-image.svg',
            'product_status' => 0,
            'product_tags' => 'legacy,extra',
            'product_views' => 0,
            'product_quantity' => 10,
            'product_sold' => 0,
            'price_cost' => 700000,
        ], $productOverrides));
        $product->save();

        $detail = new OrderDetails();
        $detail->forceFill(array_merge([
            'order_code' => $order->order_code,
            'product_id' => $productId,
            'product_name' => $product->product_name,
            'product_attribute' => 'Mặc định',
            'product_price' => $product->product_price,
            'product_sales_quantity' => 1,
            'product_coupon' => 'no',
            'product_feeship' => 30000,
        ], $detailOverrides));
        $detail->save();

        return $productId;
    }

    protected function createLegacyDeliveryFixture(): void
    {
        $this->ensureLegacyDeliveryTablesExist();

        DB::table('tbl_tinhthanhpho')->insertOrIgnore([
            'matp' => 1,
            'name_city' => 'Hà Nội',
            'type' => 'Thành phố',
        ]);

        DB::table('tbl_quanhuyen')->insertOrIgnore([
            'maqh' => 10,
            'matp' => 1,
            'name_quanhuyen' => 'Ba Đình',
            'type' => 'Quận',
        ]);

        DB::table('tbl_xaphuongthitran')->insertOrIgnore([
            'xaid' => 100,
            'maqh' => 10,
            'name_xaphuong' => 'Phúc Xá',
            'type' => 'Phường',
        ]);

        Feeship::create([
            'fee_matp' => 1,
            'fee_maqh' => 10,
            'fee_xaid' => 100,
            'fee_feeship' => 30000,
        ]);
    }

    protected function createModernCommentProduct()
    {
        $category = ProductCategory::create([
            'name' => 'Admin comment category',
            'slug' => 'admin-comment-category',
            'is_active' => true,
            'display_order' => 0,
        ]);

        $brand = Brand::create([
            'name' => 'Admin comment brand',
            'slug' => 'admin-comment-brand',
            'is_active' => true,
            'display_order' => 0,
        ]);

        return $this->app->make(ProductService::class)->createProduct([
            'name' => 'Modern Comment Product',
            'slug' => 'modern-comment-product-' . Str::lower(Str::random(6)),
            'brand_id' => $brand->id,
            'primary_category_id' => $category->id,
            'price' => 2300000,
            'cost_price' => 1800000,
            'stock_quantity' => 12,
            'description' => 'Product for admin comment moderation tests',
            'content' => 'Content for admin comment moderation tests',
            'category_ids' => [$category->id],
            'tags' => 'comment,admin',
            'is_active' => true,
            'thumbnail_path' => 'uploads/product/no-image.svg',
        ]);
    }
}
