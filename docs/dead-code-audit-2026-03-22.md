# Dead Code Audit

Ngày audit: `2026-03-22`

## Phạm vi

Audit này tập trung vào `frontend/admin surface` và các view/template phụ vừa được dọn trong các vòng trước.

Checklist dùng để phân loại:

- `Route active`: có xuất hiện trong `php artisan route:list --json`
- `Controller reachable`: method còn được route hoặc controller khác gọi tới
- `View referenced`: có `return view(...)`, `response()->view(...)`, `view(...)` hoặc include thực tế
- `Test coverage`: có ít nhất một feature/direct render test đang chạm tới

## Snapshot hiện tại

- Tổng route hiện có: `295`
- Tổng Blade view dưới `resources/views`: `160`

## Đã xóa trong phase 1

- `resources/views/pages/modern-home.blade.php`
- `resources/views/layouts/modern.blade.php`

Lý do:

- Không có route active
- Không có controller reference dùng thật
- Không có test coverage
- Thuộc nhánh prototype `modern` không còn nằm trên storefront live

## Đã xóa trong phase 2

- `app/Http/Controllers/ProductController::local_storage()`
- `app/Http/Controllers/AuthController::register_auth()`
- `app/Http/Controllers/AuthController::register_auth_dashboard()`

Lý do:

- Không có route active
- Không có call site trong controller, route hay test
- Chỉ còn là dead method hoặc `abort(404)` placeholder

## Đã xóa trong phase 3

- `app/Http/Controllers/MailController::mail_order()`
- pattern `AuthLogin()` no-op trong các controller legacy:
  - `AdminController`
  - `CategoryPost`
  - `CouponController`
  - `DocumentController`
  - `GalleryController`
  - `ProductController`
  - `SliderController`
  - `UserRolesController`
  - `VideoController`

Lý do:

- `mail_order()` không có route active và không có call site còn sống
- `AuthLogin()` đều là stub rỗng hoặc chỉ còn comment lịch sử
- Route hiện đã được bảo vệ bằng middleware `auth:web`, `admin.access`, `admin.permission:*`
- Các call site `\$this->AuthLogin();` chỉ tạo noise, không còn tác dụng runtime

## Đã xóa trong phase 4

- `resources/views/admin_login.blade.php`
- `resources/views/pages/send_mail.blade.php`
- `resources/views/admin/roles/edit.blade.php`
- `resources/views/pages/local_storage/storage.blade.php`

Lý do:

- Không có route active trỏ trực tiếp
- Không có controller runtime còn dùng thật
- Consumer còn lại chỉ là direct render test hoặc wrapper preview đã được loại bỏ
- Thuộc nhóm alias/preview có thể xóa mạnh tay sau khi đã audit xong

## Đã xóa trong phase 5

- `app/Http/Controllers/AdminController::login_google()`
- `app/Http/Controllers/AdminController::callback_google()`
- `app/Http/Controllers/AdminController::findOrCreateUser()`
- `app/Http/Controllers/AdminController::login_facebook()`
- `app/Http/Controllers/AdminController::callback_facebook()`
- `app/Http/Controllers/AdminController::dashboard()`
- `app/Http/Controllers/AdminController::logout()`

Lý do:

- Không có route active trỏ tới
- Không có call site còn sống trong controller, route hay test
- Toàn bộ đều chỉ là `abort(404)` placeholder lịch sử
- Luồng admin thật hiện dùng `show_dashboard()` và `AuthController::logout_auth()`

## Đã xóa trong phase 6

- `app/Http/Controllers/AdminController::index()`
- `app/Http/Controllers/ProductAttributeController::index()`
- `app/Http/Controllers/ProductAttributeController::create()`
- `app/Http/Controllers/ProductAttributeController::edit()`
- `app/Http/Controllers/ProductAttributeController::update()`
- `app/Http/Controllers/ProductAttributeController::destroy()`

Lý do:

- Không có route active trỏ tới
- Không có call site trong controller, route hay test
- Đây đều là resource stub/empty wrapper còn sót sau khi flow thật đã chuyển sang `show()`, `store()` và `deleteProductAttribute()`

## Đã xóa trong phase 7

- Route `/mail-example` và `app/Http/Controllers/MailController::mail_example()`
- Route/method document utility không có UI consumer:
  - `upload_file`
  - `upload_image`
  - `upload_video`
  - `create_document`
  - `create_sub_dir`
  - `create_folder`
  - `rename_folder`
  - `delete_folder`
  - `list_document`

Lý do:

- Không có link, form, JS caller hoặc view consumer trong bề mặt admin hiện tại
- `read_data` chỉ còn dùng `download_document` và `delete_document`, nên nhóm utility còn lại là route sống nhưng không có UI thực tế
- `mail-example` chỉ còn route preview + test render, không còn entrypoint từ UI hay flow vận hành

Giữ lại:

- `read_data`
- `download_document`
- `delete_document`

Vì:

- Trang kho tài liệu ở admin vẫn dùng thật
- `read.blade.php` còn render nút tải xuống và form xóa tài liệu

## Nhóm 1: Active và nên giữ

Đây là phần đang có route active hoặc là dependency trực tiếp của luồng đang dùng.

### Storefront / customer-facing

- `resources/views/layout.blade.php`
- `resources/views/partials/storefront/*`
- `resources/views/home.blade.php`
- `resources/views/pages/category/show_category.blade.php`
- `resources/views/pages/sanpham/show_details.blade.php`
- `resources/views/pages/cart/cart_ajax.blade.php`
- `resources/views/pages/checkout/login_checkout.blade.php`
- `resources/views/pages/checkout/show_checkout.blade.php`
- `resources/views/pages/history/history.blade.php`
- `resources/views/pages/history/view_history_order.blade.php`
- `resources/views/pages/sanpham/search.blade.php`
- `resources/views/pages/compare/index.blade.php`

Lý do giữ:

- Có route frontend active trong `routes/web/frontend.php`
- Có controller/view reference thật
- Đã có coverage ở `HomepageTest`, `CategoryPageTest`, `ProductDetailPageTest`, `CartFlowTest`, `CheckoutFlowTest`, `StorefrontComparePageTest`, `StorefrontLayoutShellTest`

### Admin active surface

- `resources/views/admin_layout.blade.php`
- `resources/views/partials/admin/*`
- `resources/views/admin/dashboard.blade.php`
- `resources/views/admin/manage_order.blade.php`
- `resources/views/admin/view_order.blade.php`
- `resources/views/admin/order_date.blade.php`
- `resources/views/admin/post/*`
- `resources/views/admin/brand/*`
- `resources/views/admin/category_post/*`
- `resources/views/admin/slider/*`
- `resources/views/admin/video/list_video.blade.php`
- `resources/views/admin/users/*`
- `resources/views/admin/coupon/*`
- `resources/views/admin/document/read.blade.php`
- `resources/views/admin/gallery/add_gallery.blade.php`
- `resources/views/admin/product_attribute/create.blade.php`
- `resources/views/admin/attribute/*`
- `resources/views/admin/information/add_information.blade.php`
- `resources/views/admin/comment/list_comment.blade.php`
- `resources/views/admin/images/file_browser.blade.php`
- `resources/views/admin/custom_auth/login_auth.blade.php`

Lý do giữ:

- Có route admin active trong `routes/web/admin.php`
- Đã có coverage ở `AdminPanelTest`, `AdminSecurityTest`, `AdminApiSecurityTest`, `AdminPermissionHardeningTest`, `AdminProductCrudTest`

### Mail/template đang còn consumer

- `resources/views/mail/legacy/order-confirmation.blade.php`
- `resources/views/mail/legacy/coupon-campaign.blade.php`
- `resources/views/mail/legacy/password-recovery.blade.php`
- `resources/views/vendor/mail/html/themes/laravel-shop.css`

Lý do giữ:

- `MailController@send_coupon`, `MailController@send_coupon_vip`, `recover_pass` còn dùng thật qua markdown mailable
- mail render giờ đi qua DTO + markdown theme chung thay vì wrapper HTML cũ
- Đã có coverage ở `LegacyMailTemplateRenderTest` và `LegacyMailSnapshotTest`

## Nhóm 2: Reachable nhưng ngoài luồng chính

Hiện tại chưa còn view alias/preview cấp cao nào sau phase 4.

Ghi chú:

- Một số service/test heuristic vẫn có key khái niệm như `admin_login` hoặc `local_storage`, nhưng đó là tên scenario/pattern, không còn là dependency tới Blade view đã xóa
- Nếu muốn đi tiếp, vòng sau nên tập trung vào controller/method `abort(404)` hoặc tooling phụ thay vì view preview

## Nhóm 3: Candidate xóa thật sau xác nhận

Hiện tại không còn candidate cấp cao nào trong nhóm này.

Lý do:

- `app/Http/Controllers/MailController::mail_order()` đã bị xóa trong phase 3
- Pattern `AuthLogin()` no-op trong controller legacy đã bị xóa trong phase 3
- Dead `abort(404)` placeholder trong `AdminController` đã bị xóa trong phase 5
- Resource stub/empty wrapper trong `ProductAttributeController` đã bị xóa trong phase 6
- Preview route `mail-example` và document utility routes không có UI consumer đã bị xóa trong phase 7
- Các phần còn lại không còn là dead code runtime rõ ràng

## Nhóm 4: Redundant pattern cần refactor nếu muốn mạnh tay

Đây chưa phải dead code thuần, nhưng là phần dư lịch sử.

### `AuthLogin()` no-op methods trong controller legacy

Ví dụ:

- `app/Http/Controllers/ProductController.php`
- `app/Http/Controllers/CategoryPost.php`
- `app/Http/Controllers/CouponController.php`
- `app/Http/Controllers/SliderController.php`
- `app/Http/Controllers/VideoController.php`
- `app/Http/Controllers/GalleryController.php`
- `app/Http/Controllers/DocumentController.php`
- `app/Http/Controllers/UserRolesController.php`
- `app/Http/Controllers/AdminController.php`

Trạng thái:

- Đã xóa trong phase 3 cho nhóm controller còn gọi stub rỗng
- Không còn match `AuthLogin(` trong `app/Http/Controllers`

## Test coverage liên quan trực tiếp tới audit


## Đề xuất xóa theo thứ tự an toàn

1. Không cần mở thêm phase riêng cho `mail_order()` hoặc `AuthLogin()` nữa, vì hai nhóm này đã được xử lý xong trong phase 3.
2. Nhóm alias/preview view chính đã được xử lý xong trong phase 4.
3. Sau phase 5, nhóm `abort(404)` còn lại chủ yếu là runtime guard hợp lệ cho not-found state, không nên xóa theo cơ học.
4. Sau phase 6, các resource stub/controller wrapper rõ ràng cũng đã được dọn khá sâu; nếu đi tiếp nên audit tooling/editor phụ và endpoint ít dùng thay vì quét tiếp resource method trống.
5. Sau phase 7, cụm preview/tooling phụ rõ ràng nhất đã được cắt; nếu tiếp tục nữa thì chủ yếu là hardening hoặc refactor low-level, không còn candidate xóa rõ.

## Kết luận

Ở trạng thái hiện tại:

- Bề mặt storefront/admin chính đã sạch và có test khóa khá tốt
- Dead code rõ nhất không còn nhiều
- Nhóm có thể xóa thật chủ yếu không còn nhiều; `abort(404)` còn lại hiện đa số là runtime guard hợp lệ, còn tooling phụ rõ ràng nhất đã được cắt ở phase 7

Nếu đi tiếp theo hướng mạnh tay, nên mở một PR riêng chỉ cho `delete candidates` và bám đúng thứ tự ở trên.
