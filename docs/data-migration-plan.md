# Chuẩn Hóa Dữ Liệu & Kế Hoạch Migration

## 1. Mục tiêu
- Bỏ phụ thuộc `shopbanhang.sql`, thay bằng migration chuẩn của Laravel.
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

4. **Kiểm thử**
   - So sánh count bảng cũ vs mới.
   - Kiểm tra số liệu quan trọng (tổng doanh thu, số khách hàng, hàng tồn).
   - Viết test Feature đảm bảo CRUD hoạt động với schema mới.

5. **Triển khai**
   - Chạy migration + data migration trong môi trường staging, so sánh UI → order detail, product list.
   - Sau khi ổn định, gỡ bỏ dependency `shopbanhang.sql`.

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
8. Kiểm thử + cập nhật controller/service sử dụng schema mới.
