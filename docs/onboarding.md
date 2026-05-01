# Onboarding

Doc nay nen doc dau tien khi nhan codebase `Laravel11_Shop_Website_Full`.

## Muc tieu du an

Day la website ban hang Laravel 11 cho storefront, admin, cart, checkout, order, coupon, CMS noi dung, chatbot va API JSON. Codebase dang o trang thai hybrid:

- Storefront/admin chinh van la Blade va controller legacy.
- Mot so module moi da tach sang `app/Application`, `app/Domain`, `app/Infrastructure`.
- Database co schema moi trong `database/migrations`, dong thoi con nhieu model legacy trong `app/Models`.

Khi sua code, dung bat dau tu service truoc. Hay lan theo request that:

1. Route trong `routes/web/frontend.php`, `routes/web/admin.php`, hoac `routes/api.php`.
2. Controller trong `app/Http/Controllers`.
3. Form Request trong `app/Http/Requests` neu co.
4. Service/repository/model dang duoc controller goi.
5. Blade view hoac JSON resource tra ve.
6. Migration/model lien quan den bang du lieu.

## File nen biet truoc

| File/thu muc | Vai tro |
| --- | --- |
| `routes/web.php` | Include `routes/web/frontend.php` va `routes/web/admin.php`. |
| `routes/web/frontend.php` | Storefront: home, catalog, product detail, cart, checkout, account, blog, chatbot. |
| `routes/web/admin.php` | Admin Blade va JSON admin catalog, bao ve boi `auth:web`, `admin.access`, `admin.permission`. |
| `routes/api.php` | API `/api/v1`, protected Sanctum API, admin API. |
| `app/Http/Controllers` | Controller legacy va controller moi cho admin/API. |
| `app/Http/Requests` | Validation. Da co request rieng cho cart, checkout, catalog, admin legacy. |
| `app/Application` | Service use-case moi: Catalog, Checkout, Order, CMS, Marketing, Shared. |
| `app/Domain` | Interface repository, entity/value object thuoc domain moi. |
| `app/Infrastructure/Persistence` | Eloquent model/repository theo schema moi. |
| `app/Models` | Model legacy, nhieu controller cu van dung truc tiep. |
| `resources/views` | Blade storefront/admin. |
| `database/migrations` | Schema moi. Khong sua migration cu neu khong duoc yeu cau. |
| `shopbanhang.sql` | Dump du lieu that/legacy de nap local. |

## Cac module chinh

| Module | Route chinh | Controller/service nen doc |
| --- | --- | --- |
| Home/search | `/`, `/trang-chu`, `/tim-kiem`, `/san-pham` | `HomeController`, `ProductSearchService`, `ProductService` |
| Catalog | `/danh-muc/{slug}`, `/thuong-hieu/{slug}`, `/chi-tiet/{slug}` | `CategoryProduct`, `BrandProduct`, `ProductController`, `Application/Catalog` |
| Cart | `/gio-hang`, `/add-cart-ajax`, `/update-cart`, `/remove-item` | `CartController`, `Application/Checkout/Services/CartService` |
| Checkout | `/dang-nhap`, `/checkout`, `/payment`, `/confirm-order` | `CheckoutController`, `CheckoutService`, `ShippingService`, `CouponService` |
| Customer account | `/account`, `/history`, `/view-history-order/{code}` | `CustomerAccountController`, `OrderController` |
| Admin auth/dashboard | `/admin`, `/auth-login`, `/dashboard` | `AuthController`, `AdminController`, `AdminAuthenticator` |
| Admin products | `/all-product`, `/add-product`, `/catalog/categories`, `/catalog/brands` | `ProductController`, `Admin\Catalog\CategoryController`, `Admin\Catalog\BrandController` |
| Admin orders | `/manage-order`, `/view-order/{code}`, `/delivery` | `OrderController`, `DeliveryController`, `ProductProfitController` |
| CMS/content | `/bai-viet/{slug}`, `/add-post`, `/manage-slider`, `/video-shop` | `PostController`, `SliderController`, `VideoController`, `Application/Cms` |
| API | `/api/v1/*` | `app/Http/Controllers/Api`, `app/Http/Resources` |

## Luong doc code nhanh

Neu sua storefront:

1. Mo route trong `routes/web/frontend.php`.
2. Mo controller/action tuong ung.
3. Tim view bang `return view(...)`.
4. Neu controller inject service, doc service truoc khi sua query.
5. Kiem tra request validation trong `app/Http/Requests/Storefront`.

Neu sua admin:

1. Mo `routes/web/admin.php`.
2. Xac dinh middleware permission nao dang bao ve route.
3. Doc controller/action.
4. Kiem tra view trong `resources/views/admin` hoac response JSON.
5. Neu dung Spatie permission, doc `app/Support/AdminPermissions.php`.

Neu sua API:

1. Mo `routes/api.php`.
2. Xac dinh public, `auth:sanctum`, hay admin API.
3. Doc controller trong `app/Http/Controllers/Api`.
4. Doc `Http\Resources` va request validation.
5. Test bang feature test neu co.

## Lenh hay dung

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan data:load-real --dump=shopbanhang.sql --force
php artisan serve
npm run dev
```

Kiem tra:

```bash
php artisan test
vendor/bin/pint --dirty
npm run build
git diff --check
```

## Diem can canh giac

- Web cart chu yeu dung session; API cart dung authenticated flow.
- Admin guard hien dung `auth:web` voi `admin.access`, khong phai guard `admin` rieng.
- Co song song bang/model moi va legacy. Dung doi ten cot/bang neu chua kiem tra controller dang dung model nao.
- Mot so route legacy van dung `GET` cho thao tac xoa de tuong thich UI cu.
- Payment that chua nen mac dinh la hoan chinh. VNPay/MoMo route dang comment, PayPal route co san, VNPay mock co controller rieng.
- UI header/brand co quy tac trong `AGENTS.md`; khong tu doi logo, search, cart, auth behavior neu task khong yeu cau.

## Tai lieu tiep theo

Doc theo thu tu:

1. `docs/setup.md`
2. `docs/architecture.md`
3. `docs/request-flows.md`
4. `docs/data-model.md`
5. `docs/common-tasks.md`
6. `docs/testing-debugging.md`
7. `docs/deployment-vps.md`
