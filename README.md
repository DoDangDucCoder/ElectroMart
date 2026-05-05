# Website thương mại điện tử bán thiết bị điện tử

Dự án Laravel local phục vụ đồ án tốt nghiệp với đề tài **“Xây dựng website thương mại điện tử bán thiết bị điện tử”**. Hệ thống mô phỏng một website bán lẻ thiết bị điện tử gồm storefront cho khách hàng, khu vực quản trị, tài khoản khách hàng, giỏ hàng, đặt hàng COD và các API phục vụ tích hợp.

## Tổng quan

Hệ thống tập trung vào các luồng nghiệp vụ cốt lõi của thương mại điện tử:

- Hiển thị sản phẩm, danh mục, thương hiệu và chi tiết sản phẩm.
- Tìm kiếm, lọc sản phẩm, giỏ hàng và checkout COD.
- Quản lý tài khoản khách hàng, địa chỉ và lịch sử đơn hàng.
- Quản trị sản phẩm, danh mục, thương hiệu, khách hàng, đơn hàng và nội dung.
- API public/customer/admin với xác thực token bằng Laravel Sanctum.
- Cơ chế phân quyền, middleware, CSRF, session và hàng đợi database.

## Công nghệ sử dụng

| Nhóm | Công nghệ |
|---|---|
| Backend | Laravel 11, PHP 8.2+ |
| Database | MariaDB/MySQL |
| Frontend | Blade, HTML, CSS, JavaScript, Vite |
| API/Auth | Laravel Sanctum |
| Phân quyền | spatie/laravel-permission |
| PDF/Tài liệu | barryvdh/laravel-dompdf, L5 Swagger |
| Queue | Database queue |
| Tooling | Composer, npm, Git |

## Chức năng chính

### Storefront

- Trang chủ, danh sách sản phẩm, trang chi tiết sản phẩm.
- Duyệt sản phẩm theo danh mục, thương hiệu.
- Tìm kiếm và gợi ý sản phẩm.
- Giỏ hàng, checkout COD.
- Trang liên hệ, blog/tin tức, video và các thành phần nội dung.

### Customer

- Đăng ký, đăng nhập, đăng xuất.
- Quản lý hồ sơ và địa chỉ nhận hàng.
- Xem lịch sử đơn hàng.
- Theo dõi thông tin đơn hàng đã đặt.

### Admin

- Quản lý sản phẩm, danh mục, thương hiệu và ảnh sản phẩm.
- Quản lý khách hàng, đơn hàng, thanh toán và giao hàng.
- Quản lý nội dung, banner, blog, video, liên hệ.
- Quản lý vai trò, quyền, cấu hình và dữ liệu vận hành.

### API

- Public API cho dữ liệu công khai.
- Customer API cho tác vụ của khách hàng.
- Admin API cho tác vụ quản trị.
- Xác thực token bằng Laravel Sanctum.

## Dữ liệu mẫu và ảnh sản phẩm

Dữ liệu mẫu chính nằm trong:

- `docs/import/products-sample-100.csv`
- `docs/import/product-images-manifest.csv`
- `docs/import/product-images-manifest.json`
- `docs/import/product-images-review.csv`
- `docs/import/product-images-review.json`

Ảnh sản phẩm mẫu được lưu theo cấu trúc:

```text
public/uploads/products/sample-imported/<SKU>/
```

Quy ước:

- `products.thumbnail_path`: ảnh chính của sản phẩm.
- `product_media.file_path`: ảnh gallery/phụ của sản phẩm.
- `docs/import/product-images-manifest.*`: lưu nguồn ảnh, domain, đường dẫn lưu, kích thước và ghi chú sử dụng.
- `docs/import/product-images-review.*`: danh sách sản phẩm còn thiếu ảnh hoặc cần kiểm tra thêm.

Lưu ý: ảnh và một phần dữ liệu sản phẩm dùng cho mục đích học tập/local demo. Khi triển khai kinh doanh thật cần thay bằng ảnh tự chụp, ảnh nhà cung cấp hoặc ảnh được cấp quyền chính thức.

## Yêu cầu môi trường

- PHP 8.2 trở lên.
- Composer.
- Node.js và npm.
- MariaDB hoặc MySQL.
- Git.

## Cài đặt local

1. Cài thư viện PHP:

```bash
composer install
```

2. Cài thư viện frontend:

```bash
npm install
```

3. Tạo file môi trường:

```bash
cp .env.example .env
php artisan key:generate
```

Trên Windows PowerShell có thể dùng:

```powershell
Copy-Item .env.example .env
php artisan key:generate
```

4. Cấu hình database trong `.env`:

```env
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mariadb
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=shopbanhang
DB_USERNAME=root
DB_PASSWORD=

MAIL_MAILER=log
QUEUE_CONNECTION=database
```

Không commit mật khẩu thật, SMTP production hoặc thông tin gateway thật vào repository.

5. Chạy migration và seeder nếu dựng database mới:

```bash
php artisan migrate
php artisan db:seed
```

Nếu đang phục hồi từ backup/dump local đã có sẵn, kiểm tra kỹ trước khi chạy migration hoặc seeder để tránh ghi đè dữ liệu.

6. Tạo storage link nếu cần dùng file trong `storage/app/public`:

```bash
php artisan storage:link
```

7. Build hoặc chạy frontend:

```bash
npm run build
```

Hoặc khi phát triển:

```bash
npm run dev
```

8. Chạy ứng dụng:

```bash
php artisan serve
```

Mở trình duyệt tại:

```text
http://127.0.0.1:8000
```

## Lệnh kiểm tra thường dùng

```bash
php artisan migrate:status --pending
php artisan legacy:audit-runtime --fail-on-issues
php artisan queue:failed --no-ansi
```

Khi có thay đổi PHP, có thể chạy thêm nếu công cụ tồn tại trong môi trường:

```bash
vendor/bin/pint --dirty
php artisan test
```

Khi có thay đổi frontend:

```bash
npm run build
```

## Một số tài liệu trong dự án

- `docs/setup.md`: hướng dẫn setup chi tiết.
- `docs/architecture.md`: kiến trúc hệ thống.
- `docs/data-model.md`: mô hình dữ liệu.
- `docs/request-flows.md`: luồng request/nghiệp vụ.
- `docs/demo-data-lifecycle.md`: vòng đời dữ liệu demo.
- `docs/report/diagrams`: sơ đồ dùng cho báo cáo.
- `docs/report/screenshots`: ảnh giao diện dùng cho báo cáo.

## Ảnh giao diện

Một số ảnh minh họa giao diện đã được lưu trong `docs/report/screenshots`:

- `hinh-2-13-trang-chu.png`
- `hinh-2-14-danh-sach-san-pham.png`
- `hinh-2-15-chi-tiet-san-pham.png`
- `hinh-2-11-dashboard-admin.png`
- `hinh-2-12-quan-ly-san-pham.png`
- `hinh-2-16-tai-khoan-khach-hang.png`
- `hinh-2-17-gio-hang.png`
- `hinh-2-18-checkout-cod.png`

## Lưu ý an toàn

- Không chạy `migrate:fresh` trên database có dữ liệu cần giữ.
- Không import SQL khi chưa backup.
- Không gọi payment gateway thật trong môi trường local/demo.
- Không tạo dữ liệu khách hàng/đơn hàng giả trên môi trường thật.
- Không bật hiển thị sản phẩm mẫu nếu chưa rà soát ảnh, giá, tồn kho và quyền sử dụng nội dung.
- Luôn tạo backup trước các thao tác import, cleanup hoặc cập nhật dữ liệu hàng loạt.

## Trạng thái đồ án

Dự án đang phục vụ môi trường học tập/local demo. Các luồng chính của website thương mại điện tử đã được xây dựng, bao gồm storefront, admin, customer account, checkout COD, API, dữ liệu mẫu sản phẩm và cơ chế ảnh sản phẩm. Trước khi triển khai production cần rà soát lại dữ liệu thật, ảnh sản phẩm, thanh toán, vận chuyển, email, bảo mật vận hành và kiểm thử tự động.
