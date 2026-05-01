# Testing And Debugging

## Cách Chạy Test

Project dùng PHPUnit 11. Cấu hình nằm trong `phpunit.xml`.

Chạy tất cả:

```bash
php artisan test
```

Chạy theo class/keyword:

```bash
php artisan test --filter=CartFlow
php artisan test --filter=CheckoutFlow
php artisan test --filter=AdminProductCrudTest
php artisan test --filter=ProductDetailPageTest
```

Chạy PHPUnit trực tiếp nếu cần:

```bash
vendor/bin/phpunit
```

Format PHP:

```bash
vendor/bin/pint --dirty
```

Build frontend:

```bash
npm run build
```

Kiểm tra whitespace:

```bash
git diff --check
```

## Test Environment

`phpunit.xml` đang set:

- `APP_ENV=testing`
- `DB_CONNECTION=sqlite`
- `DB_DATABASE=:memory:`
- `CACHE_STORE=array`
- `SESSION_DRIVER=array`
- `QUEUE_CONNECTION=sync`
- `MAIL_MAILER=array`
- `APP_CONFIG_CACHE=bootstrap/cache/config.testing.php`

Vì test dùng SQLite memory, một số query quá phụ thuộc MySQL có thể fail. Nếu gặp trường hợp đó, ưu tiên sửa query portable hoặc tách unit test cho logic, không đổi mặc định test sang MySQL khi chưa thống nhất.

## Test Nên Chạy Theo Loại Thay Đổi

| Thay đổi | Test nên chạy |
| --- | --- |
| Cart | `CartFlowTest` |
| Checkout/payment/bank transfer | `CheckoutFlowTest` |
| Product detail | `ProductDetailPageTest`, test product service nếu có |
| Search/catalog | `ProductSearchAndRecommendationTest`, `CategoryPageTest` |
| Admin product | `AdminProductCrudTest`, `AdminCatalogCacheConsistencyTest` |
| Admin auth/permission | `AdminAuthFlowTest`, `AdminPermissionHardeningTest`, `AdminSecurityTest` |
| API | `HealthAndDocsApiTest`, `ProductApiCacheConsistencyTest`, `CouponApiTest`, `BlogApiTest` |
| Mail legacy | `LegacyMailFlowTest`, `LegacyMailSnapshotTest`, `LegacyMailTemplateRenderTest` |
| Import/data audit | `LoadRealDataCommandTest`, `CatalogDataAuditCommandTest`, `LegacyDataMigratorTest` |
| Frontend asset | `npm run build`, feature test liên quan view |

## Cách Đọc Log

Log Laravel:

```bash
tail -f storage/logs/laravel.log
```

PowerShell:

```powershell
Get-Content storage\logs\laravel.log -Wait
```

Nếu lỗi 500 mà log không rõ:

- Kiểm tra `APP_DEBUG=true` ở local.
- Chạy `php artisan optimize:clear`.
- Kiểm tra permission `storage` và `bootstrap/cache`.
- Nếu chạy qua Nginx/PHP-FPM, đọc thêm log của Nginx và PHP-FPM.

## Debug Route

Xem route:

```bash
php artisan route:list
php artisan route:list --path=gio-hang
php artisan route:list --path=checkout
php artisan route:list --path=api/v1
```

Tìm route/controller bằng ripgrep:

```bash
rg "add-cart-ajax|confirm-order|chi-tiet|manage-order" routes app
```

Nhớ cấu trúc route:

- `routes/web/frontend.php`: storefront.
- `routes/web/admin.php`: admin.
- `routes/api.php`: API.

## Debug Controller Và Service

Quy trình thực dụng:

1. Tìm route.
2. Mở controller action.
3. Xem request class nếu action type-hint Form Request.
4. Xem service được inject trong constructor.
5. Xem model/repository service gọi.
6. Xem view hoặc JSON response.
7. Tìm test đang cover flow đó.

Ví dụ cart:

```bash
rg "add_cart_ajax|CartService|AddCartAjaxRequest" app tests
```

Ví dụ checkout:

```bash
rg "confirm_order|confirmOrder|ConfirmOrderRequest" app tests
```

## Debug View Blade

Tìm view được render:

```bash
rg "return view\\(" app\\Http\\Controllers
rg "pages.checkout.show_checkout|pages.cart.cart_ajax|pages.sanpham.show_details" app resources
```

Nếu view dùng biến không tồn tại:

- Kiểm tra controller có truyền biến đó không.
- Kiểm tra `AppServiceProvider` vì có global view composer share footer/contact/icon/stat data cho mọi view.
- Kiểm tra view partial được include từ layout.

## Debug Frontend Build

Chạy:

```bash
npm run build
```

Nếu lỗi build:

- Mở stack trace xem lỗi ở `resources/css`, `resources/js`, hay Vue component.
- Kiểm tra import path trong `resources/js/app.js`.
- Kiểm tra Vite input trong `vite.config.js`.
- Nếu asset không load sau build, kiểm tra `public/build/manifest.json`.
- Nếu đang dev, đảm bảo `npm run dev` đang chạy.

## Lỗi Thường Gặp Trong Repo Này

### Model mới và legacy bị nhầm

Triệu chứng:

- Query không thấy field.
- Primary key sai.
- View gọi `product_name` nhưng model chỉ có `name`.

Cách xử lý:

- Kiểm tra namespace model.
- `app/Models/Product` dùng `tbl_product`, primary key `product_id`.
- `Infrastructure\Eloquent\Catalog\Product` dùng `products`, primary key `id`, có accessor legacy.

### Cart session stale

Triệu chứng:

- Quick cart không thấy item.
- Update quantity báo không tìm thấy item.

Cách xử lý:

- Kiểm tra session key `cart`.
- Xem `CartService::items()`.
- Chạy `CartFlowTest`.

### Checkout lỗi giỏ hàng trống

Triệu chứng:

- `/checkout` redirect `/gio-hang`.
- `/confirm-order` trả JSON 422 `Giỏ hàng đang trống.`

Cách xử lý:

- Đảm bảo đã thêm item qua `CartService` hoặc `/add-cart-ajax`.
- Kiểm tra session driver.
- Kiểm tra product tồn kho.

### Coupon bị stale

Triệu chứng:

- Coupon session còn nhưng không được áp vào order.

Cách xử lý:

- `CheckoutService` gọi `CouponService::validatedCurrent()` trước khi tạo order.
- Nếu coupon không còn đạt điều kiện min total/hạn dùng, service sẽ bỏ coupon.

### Admin không có quyền

Triệu chứng:

- Login được nhưng vào module bị chặn.

Cách xử lý:

- Kiểm tra role/permission Spatie.
- Xem `AdminPermissions`.
- Chạy `php artisan admin:normalize-auth --dry-run`.

### Cache config/view gây sai behavior

Xử lý:

```bash
php artisan optimize:clear
```

## Debug Database

Migration status:

```bash
php artisan migrate:status
```

Import data thật:

```bash
php artisan data:load-real --dump=shopbanhang.sql --force
```

Audit catalog:

```bash
php artisan catalog:audit-data
```

Khi lỗi DB, kiểm tra:

- `.env` đúng chưa.
- MySQL đang chạy chưa.
- Bảng mới hay legacy có tồn tại không.
- Code có `Schema::hasTable()` fallback không.
