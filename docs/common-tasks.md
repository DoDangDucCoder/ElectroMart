# Common Tasks

Tài liệu này là sổ tay thao tác cho những việc developer mới thường gặp trong repo.

## Thêm Route + Controller + View Mới

### Storefront

1. Thêm route vào `routes/web/frontend.php`.
2. Chọn controller gần module nhất trong `app/Http/Controllers`.
3. Nếu input cần validate, tạo Form Request trong `app/Http/Requests/Storefront/<Module>`.
4. Tạo Blade trong `resources/views/pages/<module>` hoặc partial trong `resources/views/partials/storefront`.
5. Trả view với context cần thiết: SEO, category, brand, slider nếu page nằm trong layout storefront.
6. Thêm feature test nếu route có logic hoặc form.

Ví dụ khung route:

```php
Route::get('/duong-dan-moi', [SomeController::class, 'index']);
```

Ví dụ controller:

```php
public function index(Request $request)
{
    return view('pages.some.index', [
        'meta_title' => 'Tiêu đề',
        'meta_desc' => 'Mô tả',
        'meta_keywords' => 'keyword',
        'url_canonical' => $request->url(),
    ]);
}
```

### Admin

1. Thêm route vào đúng group permission trong `routes/web/admin.php`.
2. Dùng middleware có sẵn: `auth:web`, `admin.access`, `admin.permission`.
3. Nếu là module mới, ưu tiên controller namespace `App\Http\Controllers\Admin`.
4. View admin đặt trong `resources/views/admin`.
5. Nếu thêm quyền mới, cập nhật `app/Support/AdminPermissions.php` và logic seed/normalize quyền.

Không thêm route admin ngoài group bảo vệ quyền.

## Thêm Field Vào Sản Phẩm

Luồng admin product hiện nhận field legacy từ form, sau đó map sang schema mới.

File cần kiểm tra:

- `database/migrations/*_create_products_table.php`
- `app/Infrastructure/Persistence/Eloquent/Catalog/Product.php`
- `app/Http/Controllers/ProductController.php`
- `app/Application/Catalog/Services/ProductService.php`
- `resources/views/admin/add_product.blade.php`
- `resources/views/admin/edit_product.blade.php`
- `resources/views/admin/all_product.blade.php`
- `resources/views/pages/sanpham/show_details.blade.php` nếu field hiển thị storefront
- `tests/Feature/AdminProductCrudTest.php` hoặc test liên quan

Các bước:

1. Tạo migration mới để thêm cột.
2. Nếu cần cast, thêm vào model `Catalog\Product`.
3. Nếu Blade cũ cần tên legacy, thêm accessor/appends hợp lý trong model mới.
4. Thêm validation trong `ProductController::validateProductRequest()`.
5. Thêm mapping trong `ProductController::mapProductData()`.
6. Cập nhật `ProductService::createProduct()` và `updateProduct()` nếu field được lưu qua service.
7. Cập nhật form add/edit.
8. Cập nhật test/factory/seeder nếu field bắt buộc.

Chạy:

```bash
php artisan test --filter=AdminProductCrudTest
php artisan test --filter=Product
```

## Sửa Giao Diện Storefront

Trước khi sửa:

1. Xác định route và view thật bằng `rg`.
2. Đọc `AGENTS.md` phần UI/header/brand nếu đụng layout, header, footer, logo, search, cart.
3. Sửa nhỏ trong view/partial đúng component.
4. Không đổi route form, name input, class kỹ thuật hoặc data attribute nếu JS/test đang dùng.

File hay gặp:

- Header/layout: `resources/views/layout.blade.php`, `resources/views/layouts/home-tech.blade.php`, `resources/views/partials/storefront/*`
- Home: `resources/views/home.blade.php`, `resources/views/pages/home/partials/*`
- Product: `resources/views/pages/sanpham/*`
- Cart: `resources/views/pages/cart/*`
- Checkout: `resources/views/pages/checkout/*`

Build frontend:

```bash
npm run build
```

Nếu chỉ sửa Blade/CSS nhỏ vẫn nên chạy `git diff --check`.

## Thêm Validation Form

Repo đã có nhiều Form Request. Ưu tiên dùng request class thay vì validate rải trong controller nếu module đã theo pattern đó.

Vị trí:

- Storefront cart: `app/Http/Requests/Storefront/Cart`
- Storefront checkout: `app/Http/Requests/Storefront/Checkout`
- Storefront product/home: `app/Http/Requests/Storefront/Product`, `Home`
- Admin legacy: `app/Http/Requests/Admin/Legacy`
- Admin catalog mới: `app/Http/Requests/Catalog`
- API admin product: `app/Http/Requests/Api/Admin`

Các bước:

1. Tạo request class đúng namespace.
2. Viết `rules()` và message nếu cần tiếng Việt.
3. Inject request vào controller action.
4. Dùng `$request->validated()` thay vì `$request->all()`.
5. Thêm test cho payload thiếu/sai.

Ví dụ:

```php
public function store(StoreSomethingRequest $request)
{
    $data = $request->validated();
}
```

## Debug Lỗi Checkout/Cart

### Cart

Kiểm tra theo thứ tự:

1. Route trong `routes/web/frontend.php`.
2. Request validation trong `app/Http/Requests/Storefront/Cart`.
3. `CartController`.
4. `CartService`.
5. Product có tồn tại, active và có `stock_quantity` không.
6. Session key `cart`.
7. Coupon/session fee có bị stale không.

Lệnh/test:

```bash
php artisan test --filter=CartFlow
```

Lỗi thường gặp:

- Thêm quá tồn kho trả JSON 422.
- `id_attribute` được gửi nhưng bảng `tbl_product_attribute` không sẵn sàng.
- Cart session bị null hoặc stale, service sẽ trả collection rỗng.
- Cart rỗng sẽ clear `coupon` và `fee`.

### Checkout

Kiểm tra theo thứ tự:

1. Cart có item chưa.
2. `CheckoutController@checkout` có redirect về `/gio-hang` không.
3. Request `ConfirmOrderRequest` có reject payload không.
4. `ShippingService` có tính được phí không.
5. `CouponService` có validate lại coupon không.
6. `CheckoutService::confirmOrder()` có tạo đủ `orders`, `order_items`, `order_shipping`, `payments` không.
7. Session `guest_order_code` nếu là guest checkout.

Lệnh/test:

```bash
php artisan test --filter=CheckoutFlow
```

## Thêm Test

Project dùng PHPUnit qua `php artisan test`.

Khi thêm test:

- Feature test đặt trong `tests/Feature`.
- Unit test đặt trong `tests/Unit`.
- Dùng `RefreshDatabase` nếu test cần DB.
- Dùng factory nếu đã có factory phù hợp.
- Với cart/checkout, có thể xem `CartFlowTest` và `CheckoutFlowTest` làm mẫu.
- Nếu endpoint có CSRF, test hiện thường dùng `$this->withoutMiddleware(VerifyCsrfToken::class)` cho flow AJAX/form.

Ví dụ chạy test mới:

```bash
php artisan test --filter=TenTest
```

## Thêm API Endpoint

1. Thêm route vào `routes/api.php`.
2. Chọn đúng group: public `/api/v1`, protected `auth:sanctum`, hoặc admin `/api/v1/admin`.
3. Tạo controller trong `app/Http/Controllers/Api` hoặc `Api/Admin`.
4. Dùng Form Request nếu payload có validation đáng kể.
5. Dùng `app/Http/Resources` nếu trả model/list.
6. Thêm feature test cho auth, validation và response shape.

Kiểm tra:

```bash
php artisan route:list --path=api/v1
php artisan test --filter=Api
```

## Review Trước Khi Kết Thúc Task

- Diff có đúng phạm vi không?
- Có đụng file unrelated không?
- Route có middleware/permission đúng không?
- Form có validation không?
- Model mới hay legacy đã chọn đúng chưa?
- Test liên quan đã chạy chưa?
- `npm run build` có cần chạy không?
- `git diff --check` sạch chưa?
