# Chuẩn Hóa Dữ Liệu & Kế Hoạch Migration

## 1. Mục tiêu
- Bỏ phụ thuộc `shopbanhang.sql` vì đây chỉ là archive legacy cũ; dữ liệu hiện hành phải đi qua migration/import chuẩn của Laravel.
- Chuẩn hóa naming (snake_case, không tiền tố `tbl_`), khóa chính auto-increment `id`, khóa ngoại đầy đủ.
- Áp dụng timestamp, soft delete (khi phù hợp), enum trạng thái rõ ràng.
- Chuẩn bị script di trú dữ liệu từ DB cũ sang schema mới, đảm bảo không mất dữ liệu.

## 2. Mapping bảng cũ → bảng mới

| Bảng cũ (`tbl_*`)            | Bảng mới (đề xuất)      | Ghi chú chính                                                                 |
|------------------------------|-------------------------|--------------------------------------------------------------------------------|
| `tbl_category_product`       | `product_categories`    | Trường: `slug`, `parent_id`, `order`, `is_active`. Thêm index `parent_id`.     |
| `tbl_brand`                  | `brands`                | Thêm `slug`, `is_active`.                                                     |
| `tbl_product`                | `products`              | Chuẩn hóa cột: `sku`, `price`, `sale_price`, `stock`, `brand_id`, `category_id`. |
| `tbl_product_attribute`      | `product_variants`      | Quan hệ `product_id`, `attribute_id`, `price`, `quantity`, `image_path`.       |
| `tbl_attribute` (nếu có)     | `attributes`            | Bổ sung `slug`, `type`.                                                       |
| `tbl_gallery`                | `product_media`         | Polymorphic cho sản phẩm (sẵn sàng mở rộng).                                  |
| `tbl_category_post`          | `blog_categories`       | Giữ slug, mô tả.                                                              |
| `tbl_post`                   | `blog_posts`            | Thêm soft delete, `published_at`, `status`.                                   |
| `tbl_coupon`                 | `coupons`               | Trường: `code`, `type`, `value`, `usage_limit`, `expires_at`, `is_active`.     |
| `tbl_customers`              | `customers`             | Dùng guard chuẩn Laravel; mật khẩu `password` hashed.                         |
| `tbl_customer_address` (?)   | `customer_addresses`    | Nếu chưa có, tách từ `tbl_shipping`.                                          |
| `tbl_order`                  | `orders`                | Trường: `customer_id`, `status`, `total`, `subtotal`, `discount_total`.       |
| `tbl_order_details`          | `order_items`           | Trường: `order_id`, `product_id`, `variant_id`, `price`, `quantity`, `total`. |
| `tbl_shipping`               | `order_shipping`        | Gắn `order_id`, `name`, `email`, `address`, `phone`, `method`, `notes`.       |
| `tbl_payment`                | `payments`              | `order_id`, `channel`, `status`, `paid_at`, `reference`.                      |
| `tbl_feeship`                | `shipping_fees`         | `city_id`, `province_id`, `ward_id`, `amount`.                                 |
| `tbl_city`, `tbl_province`, `tbl_wards` | `locations`    | Chuẩn hóa thành `locations` (type level) hoặc giữ 3 bảng nhưng có FK rõ ràng. |
| `tbl_slider`                 | `banners`               | `title`, `image_path`, `is_active`, `ordering`.                                |
| `tbl_icons`                  | `site_links`            | `name`, `category`, `url`, `image_path`.                                       |
| `tbl_contact`                | `site_contacts`         | Thông tin chung (fanpage, map, slogan).                                       |
| `tbl_comment`                | `product_comments`      | Soft delete + status, quan hệ `product_id`, `customer_id`.                    |
| `tbl_rating`                 | `product_ratings`       | `customer_id`, `product_id`, `score`.                                         |
| `tbl_visitors`               | `analytics_visitors`    | Lưu IP, user agent, ngày.                                                     |
| `tbl_statistic`              | `analytics_daily`       | `date`, `orders_count`, `sales`, `profit`, `quantity`.                        |
| `tbl_document`               | `documents`             | Nếu dùng.                                                                     |
| `tbl_video`                  | `videos`                | `title`, `description`, `url`, `thumbnail`.                                   |
| `tbl_admin`, `tbl_roles`...  | Sử dụng mặc định `users`, `roles`, `role_user` của Spatie | Gỡ bỏ bảng tùy chỉnh nếu trùng chức năng.                                     |

> Ghi chú: các bảng đã có migration mặc định (users, password_resets, jobs, permissions/roles của Spatie) giữ nguyên.

## 3. Chi tiết cột quan trọng (ví dụ)

### products
- `id`, `name`, `slug`, `sku`, `description`, `content`, `price`, `sale_price`, `stock_quantity`, `brand_id`, `category_id`, `status`, `views_count`, timestamps, soft deletes.
- Index: `slug`, `brand_id`, `category_id`.

### orders
- `id`, `code` (unique), `customer_id`, `status` (enum: pending/confirmed/shipped/cancelled/completed), `subtotal`, `discount_total`, `shipping_total`, `total`, `payment_method`, `payment_status`, `notes`, timestamps.
- Index: `code`, `customer_id`, `status`.

### order_items
- `id`, `order_id`, `product_id`, `product_name_snapshot`, `variant_id`, `price`, `quantity`, `subtotal`, `discount_amount`.
- FK: `order_id` → orders, `product_id` → products (nullable nếu sản phẩm bị xóa).

### coupons
- `id`, `code`, `type` (`percent`/`fixed`/`shipping`), `value`, `max_discount`, `min_order_total`, `usage_limit`, `usage_per_customer`, `used_count`, `starts_at`, `expires_at`, `status`.

### shipping_fees
- `id`, `city_id`, `province_id`, `ward_id`, `amount`, `currency` (`VND`), timestamps.

## 4. Migration Strategy
### 4.1 Pending migrations đã review

Các migration sau đang `Pending` trên DB `shopbanhang` hiện tại và **không được chạy trực tiếp trước khi backup DB**:

| Migration | Bảng tạo mới | Runtime đang dùng | Kết luận |
|-----------|--------------|-------------------|----------|
| `2026_05_01_000100_create_customer_payment_methods_table` | `customer_payment_methods` | `/account/payment-methods`, quan hệ `Customer::paymentMethods()` | Cần cho trang phương thức thanh toán của khách hàng. Có FK tới `customers`, bảng tiền điều kiện đã chạy. |
| `2026_05_02_000100_create_vat_invoices_table` | `vat_invoices` | `/account/invoices`, quan hệ `Customer::vatInvoices()` | Cần cho trang hóa đơn VAT. Có FK tới `customers`, `orders`, các bảng tiền điều kiện đã chạy. |
| `2026_05_02_000200_create_order_returns_table` | `order_returns` | `/account/returns`, quan hệ `Customer::orderReturns()` | Cần cho trang trả hàng/hoàn tiền. Có FK tới `customers`, `orders`, `order_items`, các bảng tiền điều kiện đã chạy. |

Ghi chú vận hành:
- `CustomerAccountController` đã có guard `Schema::hasTable(...)` cho 3 trang trên để tránh lỗi runtime trong giai đoạn migration chưa chạy.
- Backup hiện tại: `storage/app/backups/shopbanhang_20260503_141843.sql`.
- Vì `.env` hiện là `APP_ENV=production`, ngay cả `php artisan migrate --pretend` cũng bị Laravel chặn nếu không xác nhận. Không ép chạy migration trong trạng thái này.
- Đã chạy 3 migration này sau backup bằng `php artisan migrate --force`.
- Sau khi chạy migration, đã kiểm tra lại:
  - `php artisan migrate:status --pending`
  - `php artisan legacy:audit-runtime --fail-on-issues`
- Cần truy cập thủ công các route `/account/payment-methods`, `/account/invoices`, `/account/returns` bằng tài khoản khách hàng để kiểm tra UI/session.
- Rollback nếu cần: restore từ file backup ở trên thay vì dùng rollback migration trên DB đang có dữ liệu thật.

Seeder liên quan:
- `Database\Seeders\CustomerAccountDemoSeeder` tạo dữ liệu demo idempotent cho account pages: một đơn hàng demo, một order item, hai payment methods, một VAT invoice và một return request.
- Seeder đã được kiểm bằng SQLite tạm với `migrate:fresh --seed --force` và chạy `db:seed --force` lần hai để xác nhận không nhân bản dữ liệu.
- `DatabaseSeeder` chỉ tự gọi demo seeder trong môi trường local/demo hoặc khi bật `SEED_CUSTOMER_ACCOUNT_DEMO=true`.
- Seeder này chưa được chạy trên DB `shopbanhang`. Nếu cần dữ liệu demo trên DB hiện tại, chạy có chủ đích: `php artisan db:seed --class=CustomerAccountDemoSeeder --force`.

### 4.2 Chiến lược chung
1. **Chuẩn bị migration mới**
   - Tạo migration theo thứ tự: taxonomy (brand/category) → catalog (products, attributes) → customer → order (orders, order_items, payments, shipping) → marketing (coupons, campaign nếu có) → cms → analytics.
   - Bổ sung seeder cho dữ liệu demo (brand, category, city...).

2. **Script di trú dữ liệu**
   - Tạo command Artisan `php artisan data:migrate-legacy`.
   - Đọc dữ liệu từ bảng cũ (schema legacy) → map sang model mới.
   - Dùng chunk để giảm memory; log progress.
   - Xử lý mapping ID (giữ nguyên id gốc nếu không xung đột, hoặc lưu map `legacy_id`).
   - Ví dụ: `product_categories.id = tbl_category_product.category_id`, `products.id = tbl_product.product_id`.

3. **Giữ mapping trong bảng phụ**
   - Tạo bảng `legacy_migrations` với `legacy_table`, `legacy_id`, `new_table`, `new_id` để debug/truy vết.

4. **Kiểm tra chất lượng**
   - So sánh count bảng cũ vs mới.
   - Kiểm tra số liệu quan trọng (tổng doanh thu, số khách hàng, hàng tồn).
   - Viết test Feature đảm bảo CRUD hoạt động với schema mới.

5. **Triển khai**
   - Chạy migration + data migration trong môi trường staging, so sánh UI → order detail, product list.
   - Sau khi ổn định, gỡ bỏ dependency runtime vào archive `shopbanhang.sql`.

## 5. Các quyết định mở
- **Địa chỉ**: tách bảng `customer_addresses` cho khách (nhiều địa chỉ) vs `order_shipping` snapshot.
- **Payment log**: cần bảng `payment_transactions` lưu raw payload (cổng VNPAY/MoMo/PayPal) để audit.
- **Inventory**: xem xét bảng `inventory_movements` để track nhập/xuất thay vì chỉ số lượng trên `products`.
- **Analytics**: chuẩn hóa `analytics_events` để ghi tracking (view, open email) phục vụ marketing module.
- **Loyalty**: khi triển khai, thêm bảng `loyalty_tiers`, `loyalty_transactions`.

## 6. Công việc cần làm tiếp theo
1. Viết migration skeleton cho nhóm `taxonomy` (brands, product_categories).
2. Dựng migration `products` + `product_variants`, `product_media`.
3. Migration `customers`, `customer_addresses`.
4. Migration `orders`, `order_items`, `order_shipping`, `payments`.
5. Migration marketing (`coupons`), CMS (`blog_categories`, `blog_posts`), analytics (`analytics_daily`, `analytics_visitors`).
6. Viết seeder mẫu & command `data:migrate-legacy`.
7. Cập nhật model Eloquent tương ứng trong `App\Infrastructure\Persistence\Eloquent`.
8. Kiểm tra chất lượng + cập nhật controller/service sử dụng schema mới.

## 7. Ghi chú chuẩn hóa branding storefront
- Brand storefront mặc định đã được gom về config trung tâm:
  - `config('services.storefront_brand.name')`
  - `config('services.storefront_brand.descriptor')`
- Label/copy storefront dùng lại nhiều nơi đã được gom về:
  - `config('storefront.labels.utility_hotline')`
  - `config('storefront.labels.utility_store')`
  - `config('storefront.labels.chat_assistant')`
  - `config('storefront.labels.chat_panel_title')`
  - `config('storefront.labels.support_quick')`
  - `config('storefront.labels.support_connect_title')`
  - `config('storefront.labels.contact_support')`
  - `config('storefront.labels.contact_page')`
- Giá trị mặc định hiện tại:
  - `STOREFRONT_BRAND_NAME=ShopW`
  - `STOREFRONT_BRAND_DESCRIPTOR=Cửa hàng công nghệ`
  - `STOREFRONT_LABEL_TOPBAR_CONTACT_FAST=Liên hệ nhanh`
  - `STOREFRONT_LABEL_CHAT_ASSISTANT=Trợ lý bán hàng`
  - `STOREFRONT_LABEL_CHAT_PANEL_TITLE=Chat hỗ trợ sản phẩm`
  - `STOREFRONT_LABEL_CONTACT_SUPPORT=Liên hệ hỗ trợ`
  - `STOREFRONT_LABEL_CONTACT_PAGE=Trang liên hệ`
  - `STOREFRONT_LABEL_COMPARE_PRODUCTS=So sánh sản phẩm`
  - `STOREFRONT_LABEL_RECOMMEND_FOR_YOU=Gợi ý cho bạn`
  - `STOREFRONT_LABEL_ORDER_TRACKING=Theo dõi đơn hàng`
  - `STOREFRONT_LABEL_BUY_MORE_PRODUCTS=Mua thêm sản phẩm`
  - `STOREFRONT_LABEL_HOTLINE_CONSULTING=Hotline tư vấn`
  - `STOREFRONT_LABEL_HOTLINE_ORDERING=Hotline đặt hàng`
  - `STOREFRONT_LABEL_HOTLINE_SUPPORT=Hotline hỗ trợ`
  - `STOREFRONT_LABEL_HOTLINE_AFTER_SALES=Hotline hậu mãi`
  - `STOREFRONT_LABEL_CONTACT_FAST=Liên hệ nhanh`
  - `STOREFRONT_LABEL_VIEW_PREVIOUS_ORDERS=Xem đơn cũ`
- Hotline fallback mặc định đã được đổi sang placeholder trung tính:
  - `STOREFRONT_CONTACT_PHONE_DISPLAY=1900.0000`
  - `STOREFRONT_CONTACT_PHONE_HREF=19000000`
- Các view storefront như `header`, `footer`, `chatbot`, `hero`, `category`, `detail`, `cart`, `checkout`, `history`, `contact`, `video` đã bỏ literal brand cũ và đọc brand/hotline từ config.
- Các label dùng lại nhiều nơi trong `header`, `hero rail`, `chatbot launcher/panel` và action label của chatbot AI/FAQ đã bỏ hardcode và đọc từ config.
- Các label hỗ trợ theo ngữ cảnh ở `category`, `detail`, `cart`, `checkout`, `history`, `video`, `homepage` như `Hotline tư vấn`, `Hotline hậu mãi`, `Gợi ý cho bạn`, `So sánh sản phẩm`, `Xem đơn cũ` cũng đã được gom vào `config/storefront.php`.
- Một nhóm copy mô tả dài theo ngữ cảnh cũng đã được gom vào `config('storefront.copy.*')`, ví dụ:
  - hỗ trợ ở trang chi tiết sản phẩm
  - mô tả support rail ở giỏ hàng / checkout / login checkout
  - quick-action text ở history / contact / video
- Mục tiêu là để storefront không còn phải sửa từng câu rải rác trong Blade khi cần đổi giọng copy hoặc đổi brand.
- Chatbot config cũng đã bỏ literal brand cũ; message chào, FAQ hotline và prompt hệ thống sẽ lấy brand hoặc hotline từ env/config.
- Nếu cần đổi thương hiệu sau này, ưu tiên sửa `.env` thay vì thay text rải rác trong Blade.
