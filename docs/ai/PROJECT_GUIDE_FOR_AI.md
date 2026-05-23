# PROJECT_GUIDE_FOR_AI

## 1. Tong quan du an

- Ten du an: DangDuc Shop
- Loai du an: Website ban hang thiet bi dien tu
- Framework: Laravel 11
- Hai phan chinh:
  - Storefront / Web khach hang
  - Admin / Quan tri
- Luong chinh:
  - Xem san pham
  - Tim kiem, loc, sap xep, phan trang
  - Gio hang
  - Checkout COD
  - Quan ly don hang admin
  - Quan ly san pham, danh muc, thuong hieu, voucher, blog, lien he

## 2. Cong nghe va kien truc

- Kien truc chinh: Laravel MVC, route -> controller -> service/repository/model -> Blade view.
- View: Blade trong `resources/views`, layout storefront la `resources/views/layouts/home-tech.blade.php`, layout admin chinh la `resources/views/admin_layout.blade.php`.
- Database: MySQL/MariaDB, migration trong `database/migrations`.
- Service layer: co trong `app/Application`, dang dung ro o catalog, checkout, customer, order, cms, reporting, AI/chat.
- Repository layer: co trong `app/Infrastructure/Persistence/Repositories`, phuc vu cac service hien dai.
- Model Eloquent moi: chu yeu trong `app/Infrastructure/Persistence/Eloquent`.
- Model Laravel/legacy: mot phan con trong `app/Models`.
- CacheService: `app/Application/Shared/Services/CacheService.php`, ngoai ra co `EnhancedCacheService.php`, `CacheWarmingService.php`.
- Route files chinh:
  - `routes/web.php`: require frontend/admin/preview.
  - `routes/web/frontend.php`: storefront, customer account, cart, checkout, blog, contact.
  - `routes/web/admin.php`: admin auth, dashboard, CRUD, permissions.
  - `routes/api.php`: REST API `/api/v1`.
- Public assets:
  - Storefront: `public/frontend`.
  - Admin: `public/backend`.
  - Uploads/media: `public/uploads`, `public/assets`.

## 3. Cau truc thu muc chinh

| Thu muc/File | Vai tro | Khi nao can mo |
|---|---|---|
| `routes/web.php` | Entry web routes, require route con | Kiem tra route web tong |
| `routes/web/frontend.php` | Route storefront, cart, checkout, account, blog, contact | Sua trang khach hang |
| `routes/web/admin.php` | Route admin, auth, CRUD, permission middleware | Sua dashboard/admin CRUD |
| `routes/api.php` | API REST `/api/v1` | Sua API public/customer/admin |
| `app/Http/Controllers` | Controller legacy va storefront/admin | Xac dinh handler cua route |
| `app/Http/Controllers/Admin` | Controller admin moi | Sua admin workspace, customer, inventory, returns, reviews |
| `app/Http/Controllers/Api` | Controller API | Sua API mobile/headless |
| `app/Application` | Service/use case layer | Sua logic nghiep vu co service san |
| `app/Infrastructure/Persistence/Eloquent` | Eloquent model theo domain | Sua relation/query model moi |
| `app/Infrastructure/Persistence/Repositories` | Repository cho service layer | Sua truy van dung service/repository |
| `app/Support` | Helper/runtime/auth/admin/storefront support | Sua helper dung chung |
| `resources/views/pages` | Blade storefront theo page/module | Sua UI khach hang |
| `resources/views/admin` | Blade admin page/module | Sua UI quan tri |
| `resources/views/partials` | Partial storefront/admin dung chung | Sua header, footer, scripts, sidebar |
| `resources/views/layouts` | Layout storefront/admin phu | Sua skeleton trang |
| `public/frontend` | CSS/JS/vendor storefront | Sua style/script web khach |
| `public/backend` | CSS/JS/vendor admin, CKEditor | Sua style/script admin |
| `public/uploads` | Anh upload dang duoc DB/view tham chieu | Chi doc/kiem tra, khong xoa neu chua chac |
| `database/migrations` | Cau truc DB hien tai | Map bang/cot, khong reset DB |
| `database/seeders` | Demo data/local seed | Chi doc khi can biet du lieu demo |
| `docs/report` | Tai lieu/so do bao cao | Sua tai lieu bao cao, khong lien quan runtime |

## 4. Route map

Da chay `php artisan route:list`. Tong so route hien tai: 425.

| Nhom route | URL/Prefix | Controller/Handler chinh | Ghi chu |
|---|---|---|---|
| Home | `/`, `/trang-chu`, `/home` | `HomeController@index` | Trang chu storefront |
| Product list/detail | `/san-pham`, `/tim-kiem`, `/chi-tiet/{product_slug}` | `HomeController@products/search`, `ProductController@details_product` | Loc/sap xep/tim kiem/chi tiet |
| Category/brand redirect | `/danh-muc/{slug}`, `/thuong-hieu/{slug}`, `/tag/{tag}` | Closure redirect | Chuyen ve `/san-pham?...` |
| Cart | `/gio-hang`, `/show-cart`, `/save-cart`, `/add-cart-ajax`, `/update-cart*`, `/remove-item` | `CartController` | Gio hang session/AJAX |
| Checkout | `/checkout`, `/payment`, `/confirm-order`, `/calculate-fee`, `/dat-hang-thanh-cong/{order_code}` | `CheckoutController` | COD la luong chinh, co trang bank transfer instruction |
| Account/customer | `/dang-nhap`, `/dang-ky`, `/account/*` | `CheckoutController`, `CustomerAccountController` | Dang nhap khach, profile, orders, wishlist |
| Blog/news | `/bai-viet`, `/bai-viet/{post_slug}` | `PostController` | Listing/detail blog |
| Contact | `/lien-he` | `ContactController@lien_he/submitContact` | Contact form co throttle |
| Support | `/cham-soc-khach-hang/*` | Route view closures | FAQ, bao hanh, doi tra, chinh sach |
| Admin auth | `/admin`, `/admin/register`, `/admin/forgot-password`, `/auth-login` | `AuthController` | Middleware `guest:web` |
| Admin dashboard | `/dashboard`, `/admin-dashboard` | `AdminController@show_dashboard` | Trong middleware `auth:web`, `admin.access` |
| Admin products | `/admin/products-management`, `/add-product`, `/edit-product/{id}`, `/save-product`, `/update-product/{id}` | `AdminWorkspaceController`, `ProductController` | Co permission `MANAGE_PRODUCTS` |
| Admin categories | `/admin/categories-management`, `/catalog/categories/*`, `/add-category-product` | `AdminWorkspaceController`, `Admin\Catalog\CategoryController`, `CategoryProductAdminController` | Co API JSON va legacy Blade |
| Admin brands | `/catalog/brands/*`, `/all-brand-product`, `/add-brand-product` | `Admin\Catalog\BrandController`, `BrandProduct` | Co API JSON va legacy Blade |
| Admin orders | `/admin/orders-management`, `/view-order/{code}`, `/track-order/{code}`, `/update-order-qty` | `AdminWorkspaceController`, `OrderController` | Quan ly don va cap nhat so luong/trang thai |
| Admin customers | `/admin/customers`, `/admin/customers/{customer}` | `Admin\CustomerController` | Permission dang gan `MANAGE_ORDERS` |
| Admin coupons | `/list-coupon`, `/insert-coupon`, `/edit-coupon/{id}` | `CouponController` | Permission `MANAGE_COUPONS` |
| Admin posts | `/all-post`, `/add-post`, `/edit-post/{id}`, category post routes | `PostController`, `CategoryPost` | Permission `MANAGE_CONTENT` |
| Admin contacts | `/admin/contact-messages`, `/information`, `/list-nut`, `/list-doitac` | `Admin\ContactMessageController`, `ContactController` | Contact inbox va thong tin site |
| Admin shipping | `/delivery`, `/admin/shipping` | `DeliveryController`, redirect | Quan ly shipping fee |
| Admin settings/media | `/admin/settings`, `/admin/site-settings`, `/manage-slider`, gallery/document/video routes | `AdminWorkspaceController`, `SliderController`, `GalleryController`, `DocumentController`, `VideoController` | Media/upload/content |
| Admin users/roles | `/users`, `/add-users`, `/index-spatie`, `/create-spatie/{id}` | `UserController`, `UserRolesController` | spatie/laravel-permission |
| API public/customer | `/api/v1/products`, `/api/v1/cart`, `/api/v1/orders`, `/api/v1/blog`, `/api/v1/auth` | `App\Http\Controllers\Api\*` | Sanctum/API use cases |
| API admin | `/api/v1/admin/*` | `App\Http\Controllers\Api\Admin\*` | Admin API product/category/brand/order/statistics |

## 5. Module map chi tiet

| Module | Route | Controller | Service/Repository | Model | View Blade | CSS/JS | DB tables | Test nhanh |
|---|---|---|---|---|---|---|---|---|
| Storefront Home | `/`, `/trang-chu`, `/load-more-product` | `HomeController` | `ProductService`, `ProductRecommendationService`, `BlogPostService` neu duoc inject/dung | `Product`, `ProductCategory`, `Brand`, `Banner`, `BlogPost` | `resources/views/home.blade.php`, `resources/views/partials/home/*`, `resources/views/pages/home/partials/*` | `partials/storefront/head.blade.php`, `home-tech.min.css`, `owl.carousel.js`, `partials/storefront/scripts/*` | `products`, `product_categories`, `brands`, `banners`, `blog_posts`, `product_views` | Mo `/`, load more, banner/slider, category, san pham noi bat/khuyen mai, tin tuc |
| Product Storefront | `/san-pham`, `/tim-kiem`, `/chi-tiet/{product_slug}`, `/quickview`, `/load-comment`, `/send-comment`, `/insert-rating` | `HomeController`, `ProductController`, `CategoryProduct`, `BrandProduct` | `ProductService`, `ProductPricingService`, `ProductSearchService`, `ProductReviewService`, `ProductViewTrackingService`, `ProductRecommendationService` | `Product`, `ProductMedia`, `ProductCategory`, `Brand`, `ProductReview`, `ProductView`, `ProductVariant` | `pages/sanpham/listing.blade.php`, `pages/sanpham/show_details.blade.php`, `pages/sanpham/partials/*`, `partials/storefront/product-price.blade.php` | `category-redesign.css`, `search-redesign.css`, `price-range.css/js`, `lightslider`, `lightgallery`, `wishlist-state.css` | `products`, `product_media`, `product_categories`, `product_category_product`, `brands`, `product_reviews`, `product_views`, `product_search_queries` | `/san-pham`, `/chi-tiet/iphone-16-pro-max-256gb`, search/filter/sort/page, gallery, review, related |
| Cart | `/gio-hang`, `/show-cart`, `/save-cart`, `/add-cart-ajax`, `/update-cart*`, `/delete-to-cart/{rowId}`, `/remove-item`, `/cart-session`, `/check-coupon` | `CartController`, `CouponController` | `CartService`, `CustomerCartService`, `CouponService` | `Cart`, `CartItem`, `Product`, `Coupon` | `pages/cart/cart_ajax.blade.php`, `pages/cart/partials/*` | Storefront shared JS, inline AJAX in related Blade, SweetAlert | `carts`, `cart_items`, `products`, `coupons`, `coupon_redemptions` | Add cart, update qty, delete item, clear cart, coupon, quick cart; quick checkout dang sap ra mat neu UI co note |
| Checkout / Order | `/checkout`, `/payment`, `/confirm-order`, `/calculate-fee`, `/select-delivery-home`, `/dat-hang-thanh-cong/{order_code}`, `/dat-hang-that-bai/{order_code}`, `/account/orders` | `CheckoutController`, `OrderController`, `CustomerAccountController` | `CheckoutService`, `CreateOrderUseCase`, `ShippingService`, `CouponService`, `LegacyOrderWorkflowService`, `LegacyOrderHistoryService`, `LegacyOrderPresentationService` | `Order`, `OrderItem`, `Payment`, `OrderShipping`, `ShippingFee`, `Customer`, `Coupon`, `Cart` | `pages/checkout/show_checkout.blade.php`, `payment.blade.php`, `order_success.blade.php`, `order_failed.blade.php`, `pages/history/*`, `pages/account/order-management.blade.php` | Select2 loaded for `checkout-quick-page`, PayPal script loaded but payment methods are not primary; shared storefront JS | `orders`, `order_items`, `payments`, `payment_transactions`, `order_shipping`, `shipping_fees`, `customers`, `customer_addresses`, `coupons` | `/gio-hang`, `/checkout`, COD order, coupon, shipping fee, idempotency key, success page, admin order, customer history |
| Customer Account | `/dang-nhap`, `/dang-ky`, `/dang-xuat-khach-hang`, `/quen-mat-khau`, `/account/*` | `CheckoutController`, `CustomerAccountController`, `MailController`, `AdminController` social callbacks | `CustomerService`, `StorefrontCustomerAuth`, `StorefrontCustomerPassword` | `Customer`, `Address`, `Wishlist`, `Order`, `ProductReview`, `CustomerPaymentMethod`, `VatInvoice`, `OrderReturn` | `pages/auth/*`, `pages/account/*`, `pages/account/partials/*` | `account-shared.css`, `wishlist-state.css`, storefront JS | `customers`, `customer_addresses`, `wishlists`, `orders`, `product_reviews`, `customer_payment_methods`, `vat_invoices`, `order_returns` | Login/register/logout, profile, address, orders, wishlist sync/toggle, reset password |
| Admin Product | `/admin/products-management`, `/add-product`, `/edit-product/{id}`, `/save-product`, `/update-product/{id}`, `/active-product/{id}`, `/unactive-product/{id}`, `/delete-product/{id}` | `Admin\AdminWorkspaceController`, `ProductController` | `ProductService`, `ProductPricingService`, `PublicUploadService`, catalog repositories | `Product`, `ProductMedia`, `ProductVariant`, `ProductCategory`, `Brand`, `Attribute` | `admin/product/admin_index.blade.php`, `admin/add_product.blade.php`, `admin/edit_product.blade.php`, `admin/product/partials/*` | `backend/css/app.css`, `backend/css/admin-*.css`, CKEditor, DataTables, admin partial scripts | `products`, `product_media`, `product_variants`, `product_category_product`, `brands`, `product_categories`, `attributes` | List/create/update/status/delete neu lien quan, upload anh, gallery, gia, ton kho, icon/console |
| Admin Category | `/admin/categories-management`, `/catalog/categories/*`, legacy `/add-category-product`, `/edit-category-product/{id}` | `AdminWorkspaceController`, `Admin\Catalog\CategoryController`, `CategoryProductAdminController` | `CategoryService`, `EloquentCategoryRepository` | `ProductCategory` | `admin/category/admin_index.blade.php`, `admin/add_category_product.blade.php`, `admin/edit_category_product.blade.php` | Admin shared CSS/JS | `product_categories`, `product_category_product` | List/create/update/status/delete/reorder |
| Admin Brand | `/catalog/brands/*`, `/all-brand-product`, `/add-brand-product`, `/edit-brand-product/{id}` | `Admin\Catalog\BrandController`, `BrandProduct` | `BrandService`, `EloquentBrandRepository` | `Brand`, `Product` | `admin/brand/index.blade.php`, `admin/brand/create.blade.php`, `admin/brand/edit.blade.php` | Admin shared CSS/JS | `brands`, `products` | List/create/update/status/delete, product relation |
| Admin Order | `/admin/orders-management`, `/view-order/{code}`, `/track-order/{code}`, `/print-order/{code}`, `/update-order-qty`, `/update-qty` | `AdminWorkspaceController`, `OrderController`, `Admin\OrderReturnController`, `Admin\PaymentController` | `LegacyOrderWorkflowService`, `LegacyOrderPresentationService`, `ProductProfitReportService` | `Order`, `OrderItem`, `Payment`, `OrderShipping`, `OrderReturn`, `PaymentTransaction` | `admin/order/index.blade.php`, `admin/view_order.blade.php`, `admin/order/track.blade.php`, `admin/order/print.blade.php`, `admin/returns/index.blade.php`, `admin/transactions/index.blade.php` | Admin order-management script, DataTables, print view | `orders`, `order_items`, `payments`, `payment_transactions`, `order_shipping`, `order_returns` | List/detail/status/qty/print/returns/transactions |
| Admin Customer | `/admin/customers`, `/admin/customers/{customer}`, `/admin/customers/{customer}/toggle-status` | `Admin\CustomerController` | `CustomerService` neu can logic customer | `Customer`, `Address`, `Order` | `admin/customer/index.blade.php`, `admin/customer/show.blade.php` | Admin shared CSS/JS | `customers`, `customer_addresses`, `orders`, `wishlists` | List/detail/toggle status |
| Admin Coupon | `/list-coupon`, `/insert-coupon`, `/edit-coupon/{id}`, `/insert-coupon-code`, `/update-coupon/{id}`, `/delete-coupon/{id}` | `CouponController`, `MailController` coupon mail routes | `CouponService` | `Coupon`, `CouponRedemption` | `admin/coupon/list_coupon.blade.php`, `insert_coupon.blade.php`, `edit_coupon.blade.php` | Admin coupon-datepicker script | `coupons`, `coupon_redemptions` | Create/update/delete, validate coupon in cart/checkout |
| Admin Shipping | `/delivery`, `/select-delivery`, `/insert-delivery`, `/select-feeship`, `/update-delivery`, `/admin/shipping` redirect | `DeliveryController` | `ShippingService`, `EloquentShippingFeeRepository` | `ShippingFee`, `Location`, `VietnamProvince`, `VietnamWard` | `admin/delivery/add_delivery.blade.php`, `admin/delivery/partials/*` | `partials/admin/scripts/delivery/*`, jQuery UI | `shipping_fees`, `locations`, `vietnam_provinces`, `vietnam_wards` | Load province/ward, add/update fee, checkout calculate fee |
| Admin Blog/Post | `/all-post`, `/add-post`, `/edit-post/{id}`, `/delete-post/{id}`, category post routes | `PostController`, `CategoryPost` | `BlogPostService`, `BlogCategoryService`, CMS repositories | `BlogPost`, `BlogCategory`, legacy post/category tables if controller uses query builder | `admin/post/*`, `admin/category_post/*`, storefront `pages/blog/*`, `pages/baiviet/*` | CKEditor, admin scripts | `blog_posts`, `blog_categories`, `news` neu lien quan | Admin CRUD, `/bai-viet`, detail post |
| Admin Contact | `/admin/contact-messages`, `/information`, `/list-nut`, `/list-doitac`, `/save-info`, `/update-info/{id}` | `Admin\ContactMessageController`, `ContactController` | CMS/contact service neu them moi | `ContactMessage`, `SiteContact`, `SiteLink` | `admin/contact_messages/index.blade.php`, `admin/information/add_information.blade.php`, `admin/information/partials/*`, `pages/lienhe/contact.blade.php` | `contact-page.css`, admin media/site-info script | `contact_messages`, `site_contacts`, `site_links` | `/lien-he` desktop/mobile, form, social icons, map, FAQ, admin inbox |
| Admin User/Role/Permission | `/users`, `/add-users`, `/store-users`, `/assign-roles`, `/index-spatie`, `/create-spatie/{id}`, `/create-permission/{id}` | `UserController`, `UserRolesController` | `AdminAccess`, `AdminPermissions`, spatie permission | `User`, spatie role/permission models | `admin/users/*`, `admin/roles/*` | Admin shared CSS/JS | `users`, `roles`, `permissions`, `model_has_roles`, `model_has_permissions`, `role_has_permissions` | User list/create/assign role/permission |
| Settings/Media | `/admin/settings`, `/admin/profile`, `/manage-slider`, `/add-slider`, gallery, document, video, CKEditor upload | `AdminWorkspaceController`, `SliderController`, `GalleryController`, `DocumentController`, `VideoController`, `ProductController@ckeditor_image` | `PublicUploadService`, `CacheService` if settings cached | `Setting`, `Banner`, `Document`, `Video`, `ProductMedia` | `admin/tools/settings.blade.php`, `admin/slider/*`, `admin/gallery/*`, `admin/document/read.blade.php`, `admin/video/*`, `admin/images/file_browser.blade.php` | `public/backend/ckeditor`, admin media scripts, lightbox/dropzone if page loads | `settings`, `banners`, `documents`, `videos`, `product_media`, legacy `tbl_slider` if used | Settings page, slider CRUD, gallery upload/update/delete, document/video UI |

## 6. Database map

| Bang | Vai tro | Module dung | Cot quan trong |
|---|---|---|---|
| `products` | San pham chinh | Product storefront, admin product, cart, order | `id`, `product_name`, `product_slug`, `brand_id`, `price`, `sale_price`, `stock`, `is_active`, `is_hot` |
| `product_media` | Anh/gallery/media san pham | Product detail, admin gallery/media | `id`, `product_id`, `path/url`, `type`, `sort_order`, `is_primary` |
| `product_categories` | Danh muc san pham | Home/category/admin category | `id`, `name`, `slug`, `parent_id`, `is_active`, `sort_order` |
| `product_category_product` | Pivot san pham-danh muc | Product filter/admin relation | `product_id`, `product_category_id` |
| `brands` | Thuong hieu | Product filter/admin brand | `id`, `name`, `slug`, `is_active`, `logo` |
| `carts` | Gio hang DB neu customer/cart service dung | Cart/customer cart | `id`, `customer_id`, `session_id`, `status` |
| `cart_items` | Dong san pham trong gio | Cart/checkout | `cart_id`, `product_id`, `quantity`, `price` |
| `orders` | Don hang | Checkout, admin order, customer history | `id`, `order_code`, `customer_id`, `status`, `total`, `payment_method`, `created_at` |
| `order_items` | Chi tiet san pham trong don | Checkout, admin order, report | `order_id`, `product_id`, `product_name`, `quantity`, `price`, `subtotal` |
| `payments` | Thanh toan don hang | Checkout/order/admin transactions | `order_id`, `method`, `status`, `amount` |
| `payment_transactions` | Giao dich cong thanh toan | Payment/admin transactions | `payment_id`, `provider`, `transaction_id`, `status`, `payload` |
| `order_shipping` | Thong tin giao hang cua don | Checkout/admin order | `order_id`, `name`, `phone`, `address`, `shipping_fee` |
| `customers` | Tai khoan khach hang | Account, checkout, admin customer | `id`, `name`, `email`, `phone`, `password`, `status`, loyalty fields |
| `users` | Tai khoan admin/web guard | Admin auth, roles | `id`, `name`, `email`, `password`, `status` |
| `customer_addresses` | Dia chi khach hang | Account/checkout | `customer_id`, `name`, `phone`, `address`, `is_default` |
| `coupons` | Ma giam gia | Cart/checkout/admin coupon | `code`, `type/condition`, `value/number`, `quantity`, `starts_at`, `ends_at`, `is_active` |
| `coupon_redemptions` | Lich su dung coupon | Checkout/coupon | `coupon_id`, `customer_id`, `order_id`, `used_at` |
| `shipping_fees` | Phi van chuyen | Checkout/admin shipping | `province`, `district/ward`, `fee`, `province_code`, `ward_code` |
| `wishlists` | San pham yeu thich | Account/wishlist | `customer_id`, `product_id` |
| `product_reviews` | Danh gia san pham | Product detail/account/admin reviews/API | `product_id`, `customer_id`, `rating`, `content`, `status` |
| `blog_posts` | Bai viet/blog | Blog storefront/admin post | `id`, `title`, `slug`, `content`, `category_id`, `status`, `published_at` |
| `blog_categories` | Danh muc bai viet | Blog/admin category post | `id`, `name`, `slug`, `status` |
| `contact_messages` | Tin nhan form lien he | Contact/admin contact inbox | `name`, `email`, `phone`, `message`, `status` |
| `site_contacts` | Thong tin lien he site | Contact/admin information | `id`, contact/social/map fields |
| `site_links` | Link/icon doi tac/nut | Contact/footer/admin information | `id`, `type`, `title`, `url`, `icon`, `sort_order` |
| `settings` | Cau hinh he thong | Admin settings/shared config | `key`, `value`, `group`, `type` |
| `banners` | Banner/slider moi | Home/admin slider/media | `title`, `image`, `link`, `status`, `sort_order` |
| `documents` | Tai lieu upload | Admin document/product attachment | `path`, `name`, `mime_type`, `size` |
| `videos` | Video content | Admin video/storefront video | `title`, `slug`, `image`, `url`, `status` |
| `order_returns` | Yeu cau doi tra | Account/admin returns | `order_id`, `customer_id`, `reason`, `status` |
| `customer_payment_methods` | Phuong thuc thanh toan luu cua customer | Account payment methods | `customer_id`, `type`, `provider`, `last_four`, `is_default` |
| `vat_invoices` | Hoa don VAT | Account invoices/order | `customer_id`, `order_id`, invoice fields |

## 7. Asset map

| Khu vuc | CSS/JS chinh | Ghi chu |
|---|---|---|
| Storefront head CSS | `resources/views/partials/storefront/head.blade.php` load `frontend/css/vendor-core.min.css`, `storefront-tokens.min.css`, `storefront-components.min.css`, `storefront-theme.min.css`, `storefront-layout.min.css`, `shop-pagination.min.css`, `wishlist-state.css`, `font-override.css` | CSS shared moi cua storefront |
| Storefront scripts | `resources/views/partials/storefront/scripts-stack.blade.php` load `jquery.js`, `bootstrap.min.js`, `jquery.scrollUp.min.js`, `sweetalert.min.js`, partial scripts countdown/interactions | Script shared storefront |
| Home carousel | `owl.carousel.min.css`, `owl.theme.default.min.css`, `owl.carousel.js` | Auto-load khi body class co `g-home` |
| Product detail CSS/JS | `lightslider.css`, `lightgallery.min.css`, `lightslider.min.js`, `lightgallery-all.min.js` | Auto-load khi body class co `g-product-detail` |
| Search/category filter | `price-range.css`, `price-range.js`, `frontend/vendor/jquery-ui/*`, `category-redesign.css`, `search-redesign.css` | Auto-load mot phan theo `g-search`/`g-category` |
| Cart/checkout CSS/JS | Storefront shared, `frontend/vendor/select2/*`, PayPal checkout script | Select2/PayPal auto-load khi body class co `checkout-quick-page`; COD van la luong chinh |
| Contact page | `public/frontend/css/contact-page.css` | Dung cho `pages/lienhe/contact.blade.php` |
| FontAwesome/Glyphicons | `frontend/css/vendor-core.min.css`, `frontend/css/font-awesome.min.css`, `backend/css/font-awesome.css`, fonts trong `public/backend/fonts` | Test icon sau khi sua font/CSS |
| `font-override.css` | `public/frontend/css/font-override.css` | Phu font hardcode; sua can test icon/header/mobile |
| Admin CSS | `resources/views/partials/admin/head.blade.php`, `public/backend/css/app.css`, `admin-modern.css`, `admin-pages.css`, `admin-dashboard.css`, `dangduc-admin.css` | Admin layout cu dung `admin_layout.blade.php`; layout moi co `layouts/dangduc_admin.blade.php` |
| Admin JS | `backend/js/app.js`, `jquery.dataTables.min.js`, `morris.js`, `partials/admin/scripts-stack.blade.php` | Scripts chia theo core/editor/media/chart/delivery |
| Admin editor/media | `public/backend/ckeditor`, `partials/admin/scripts/editor/*`, `partials/admin/scripts/media/*` | Post/product/content upload/editor |

## 8. Nhung file/rui ro can chu y

- Khong xoa migrations.
- Khong xoa anh trong `public/uploads` neu DB co the tham chieu.
- Khong commit `.env`.
- Khong ghi mat khau, secret, token, key tu `.env` vao tai lieu/code.
- Khong xoa `public/assets/news` neu bai viet dang dung.
- Khong sua manh `public/frontend/css/font-override.css` neu chua test icon.
- Khong doi payment method neu he thong chi ho tro COD trong demo.
- Khong cache du lieu checkout/order ca nhan bua bai.
- Khong xoa view/archive/service neu chua co bang chung khong con dung.
- Can can trong voi route legacy redirect va route admin co middleware permission.
- Can doc controller truoc khi doi view vi mot so controller legacy tra ve `admin_layout` kem view con.
- Can test AJAX/CSRF khi sua cart, checkout, delivery, gallery, video, coupon.

## 9. Quy tac AI khi sua code

- Truoc khi sua module nao, doc phan module map tuong ung trong file nay.
- Sau do doc route, controller, service/repository, model, view, asset lien quan thuc te.
- Sua toi thieu, dung pattern hien co.
- Khong tao file test tam tru khi duoc yeu cau.
- Neu tao file tam thi xoa sau khi xong.
- Khong reset DB.
- Khong migrate fresh.
- Khong seed.
- Khong sua/xoa du lieu that.
- Voi CRUD test, dung tien to `QA_TEST_`.
- Sau khi sua route/controller, chay `php artisan route:list` hoac loc route lien quan.
- Sau khi sua Blade/cache/view, chay `php artisan view:clear` hoac `php artisan optimize:clear` neu phu hop.
- Sau khi sua UI, test bang browser that va xem console neu co the.
- Commit rieng tung nhom thay doi.
- Neu workspace co thay doi khong phai cua minh, khong revert; chi ignore neu khong lien quan.

## 10. Checklist test theo module

### Khi sua Product

Test:
- `/san-pham`
- `/chi-tiet/iphone-16-pro-max-256gb`
- Search/filter/sort/pagination
- Quickview neu lien quan
- Review/comment/rating neu lien quan
- Admin product list/create/update/status/delete neu lien quan
- Anh/gallery
- Icon
- Console

### Khi sua Checkout

Test:
- `/gio-hang`
- `/checkout`
- COD order
- Coupon
- Shipping fee
- Idempotency key
- Success/failed page
- Admin order
- Customer order history

### Khi sua Contact

Test:
- `/lien-he` desktop/mobile
- Contact form
- Social icons
- Map
- FAQ/support links neu lien quan
- Admin contact messages
- Console

### Khi sua Icon/Font

Test:
- Header
- Search overlay/autocomplete
- Product detail
- Cart/checkout
- Contact
- Admin sidebar
- Mobile menu

### Khi sua Admin Layout

Test:
- `/dashboard`
- `/admin/products-management`
- `/admin/orders-management`
- `/admin/customers`
- Sidebar/header/content overflow
- DataTables/buttons/icons
- Mobile width neu co yeu cau

### Khi sua Blog/Post

Test:
- `/bai-viet`
- `/bai-viet/{post_slug}` voi slug co san
- `/all-post`
- Add/edit post neu lien quan
- CKEditor/upload anh neu lien quan

### Khi sua Coupon

Test:
- `/list-coupon`
- Create/update coupon neu lien quan
- `/gio-hang` check/unset coupon
- `/checkout` ap dung tong tien

### Khi sua Shipping

Test:
- `/delivery`
- Select province/ward
- Insert/update fee
- `/checkout` calculate fee

## 11. Lenh kiem tra nhanh

```bash
php artisan optimize:clear
php artisan view:clear
php artisan route:list
php artisan migrate:status
git status --short
git log --oneline -5
```

## 12. Ket luan

- Day la file dinh huong cho AI, khong thay the viec doc code thuc te.
- Neu code thay doi lon, can cap nhat file nay.
- Khi nghi ngo, uu tien kiem tra `route:list`, controller, view va asset thuc te thay vi doan.
