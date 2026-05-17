# 🛒 Website Thương Mại Điện Tử — Bán Thiết Bị Điện Tử

> **Đồ án tốt nghiệp** — Xây dựng website thương mại điện tử bán thiết bị điện tử
> Framework: **Laravel 11** | PHP 8.2+ | MariaDB/MySQL | Blade + Vite

---

## 📋 Mục lục

- [Tổng quan dự án](#-tổng-quan-dự-án)
- [Công nghệ sử dụng](#-công-nghệ-sử-dụng)
- [Kiến trúc hệ thống](#-kiến-trúc-hệ-thống)
- [Chức năng chi tiết](#-chức-năng-chi-tiết)
- [Cấu trúc thư mục](#-cấu-trúc-thư-mục)
- [Yêu cầu môi trường](#-yêu-cầu-môi-trường)
- [Hướng dẫn cài đặt](#-hướng-dẫn-cài-đặt)
- [Cấu hình môi trường](#-cấu-hình-môi-trường)
- [Tài khoản mặc định](#-tài-khoản-mặc-định)
- [API Documentation](#-api-documentation)
- [Dữ liệu mẫu](#-dữ-liệu-mẫu)
- [Lệnh thường dùng](#-lệnh-thường-dùng)
- [Ảnh giao diện](#-ảnh-giao-diện)
- [Tài liệu tham khảo](#-tài-liệu-tham-khảo)
- [Lưu ý an toàn](#-lưu-ý-an-toàn)

---

## 🎯 Tổng quan dự án

Hệ thống mô phỏng một website bán lẻ thiết bị điện tử hoàn chỉnh, bao gồm:

- **Storefront** — Giao diện mua sắm cho khách hàng (trang chủ, danh mục, chi tiết sản phẩm, giỏ hàng, checkout).
- **Customer Account** — Khu vực tài khoản khách hàng (đăng ký, đăng nhập, quản lý đơn hàng, địa chỉ, wishlist).
- **Admin Dashboard** — Bảng điều khiển quản trị (quản lý sản phẩm, đơn hàng, khách hàng, nội dung, thống kê).
- **RESTful API** — API cho 3 nhóm: public, customer (xác thực), admin (xác thực + phân quyền).

Dự án phục vụ môi trường **học tập / local demo**, chưa triển khai production.

---

## 🛠 Công nghệ sử dụng

| Nhóm | Công nghệ | Phiên bản |
|------|-----------|-----------|
| **Backend** | Laravel Framework | 11.9+ |
| **Ngôn ngữ** | PHP | 8.2+ |
| **Database** | MariaDB / MySQL | 10.x / 8.x |
| **Template Engine** | Blade | (built-in Laravel) |
| **Build Tool** | Vite | 8.x |
| **CSS** | Tailwind CSS + Vanilla CSS | 4.x |
| **JavaScript** | Vue 3 (một số component), Vanilla JS | 3.x |
| **API Auth** | Laravel Sanctum | 4.x |
| **Phân quyền** | spatie/laravel-permission | 6.x |
| **Social Login** | Laravel Socialite (Google, Facebook) | 5.x |
| **PDF Export** | barryvdh/laravel-dompdf | 3.x |
| **API Docs** | L5 Swagger (DarkaOnLine) | 10.x |
| **Queue** | Database driver | (built-in Laravel) |
| **Session/Cache** | Database driver | (built-in Laravel) |
| **Linting** | Laravel Pint | 1.x |

---

## 🏗 Kiến trúc hệ thống

```
┌─────────────────────────────────────────────────────┐
│                    Client Browser                    │
├──────────┬──────────────────┬────────────────────────┤
│Storefront│ Customer Account │    Admin Dashboard     │
│ (Blade)  │    (Blade)       │      (Blade)           │
├──────────┴──────────────────┴────────────────────────┤
│              Laravel Router (web.php + api.php)       │
├───────────────────────┬──────────────────────────────┤
│     Web Controllers   │      API Controllers         │
│  (30 controllers)     │  (13+ controllers)           │
├───────────────────────┴──────────────────────────────┤
│  Middleware: Auth, CSRF, XSS, SQLi, RateLimit, CSP   │
├──────────────────────────────────────────────────────┤
│              Eloquent Models (34 models)              │
├──────────────────────────────────────────────────────┤
│          MariaDB / MySQL (78 migrations)              │
└──────────────────────────────────────────────────────┘
```

### Phân lớp thư mục `app/`

| Thư mục | Vai trò |
|---------|---------|
| `Http/Controllers/` | Controller web (storefront + admin) |
| `Http/Controllers/Api/` | Controller API (public + customer + admin) |
| `Http/Controllers/Admin/` | Controller admin nâng cao |
| `Http/Middleware/` | 15 middleware bảo mật và xác thực |
| `Models/` | 34 Eloquent model |
| `Mail/` | Mailable class (password recovery, coupon) |
| `Events/` + `Listeners/` | Event-driven logic |
| `Domain/` | Business logic layer |
| `Infrastructure/` | Infrastructure concerns |
| `Support/` | Helper và utility classes |

---

## 📦 Chức năng chi tiết

### 🏠 Storefront (Giao diện khách hàng)

| Chức năng | Mô tả |
|-----------|-------|
| Trang chủ | Banner slider, sản phẩm nổi bật, sản phẩm mới, danh mục nổi bật |
| Danh mục sản phẩm | Duyệt theo danh mục, thương hiệu, bộ lọc giá/thuộc tính |
| Chi tiết sản phẩm | Ảnh gallery, thông số, mô tả, đánh giá, sản phẩm liên quan |
| Tìm kiếm | Tìm kiếm sản phẩm với gợi ý realtime |
| Giỏ hàng | Thêm/sửa/xóa sản phẩm, áp dụng mã giảm giá (coupon) |
| Checkout COD | Đặt hàng thanh toán khi nhận hàng |
| Khuyến mãi | Trang hiển thị sản phẩm đang giảm giá |
| So sánh sản phẩm | So sánh thông số nhiều sản phẩm |
| Wishlist | Danh sách sản phẩm yêu thích |
| Blog / Tin tức | Bài viết, tin tức, danh mục bài viết |
| Video | Trang video sản phẩm |
| Liên hệ | Form liên hệ gửi thông tin |
| Trang hỗ trợ | Hướng dẫn mua hàng, hướng dẫn thanh toán, giới thiệu |

### 👤 Customer Account (Tài khoản khách hàng)

| Chức năng | Mô tả |
|-----------|-------|
| Đăng ký / Đăng nhập | Email + password, hoặc Google/Facebook (Socialite) |
| Hồ sơ cá nhân | Cập nhật thông tin, đổi mật khẩu |
| Địa chỉ nhận hàng | Quản lý nhiều địa chỉ (tỉnh/quận/phường) |
| Lịch sử đơn hàng | Xem danh sách và chi tiết đơn hàng |
| Theo dõi đơn hàng | Tracking trạng thái giao hàng |
| Sản phẩm đã xem | Lịch sử sản phẩm đã xem gần đây |

### 🔧 Admin Dashboard (Quản trị)

| Module | Mô tả |
|--------|-------|
| Dashboard | Thống kê tổng quan (doanh thu, đơn hàng, khách hàng, sản phẩm) |
| Sản phẩm | CRUD sản phẩm, ảnh gallery, thuộc tính, biến thể, giá sale |
| Danh mục | Quản lý danh mục sản phẩm (cây phân cấp) |
| Thương hiệu | Quản lý thương hiệu |
| Đơn hàng | Xem, cập nhật trạng thái, in đơn hàng (PDF) |
| Khách hàng | Quản lý thông tin khách hàng |
| Giao hàng | Quản lý phí ship, tình trạng giao hàng |
| Thanh toán | Quản lý thanh toán đơn hàng |
| Mã giảm giá | Tạo và quản lý coupon/voucher |
| Nội dung | Banner/slider, blog, video, tài liệu |
| Liên hệ | Xem tin nhắn liên hệ từ khách hàng |
| Đánh giá | Quản lý đánh giá sản phẩm |
| Bảo hành | Quản lý thông tin bảo hành |
| Đổi trả | Quản lý yêu cầu đổi trả |
| Kho hàng | Quản lý tồn kho |
| Vai trò & Quyền | Phân quyền admin (spatie/laravel-permission) |
| Người dùng admin | Quản lý tài khoản quản trị |
| Cài đặt | Cấu hình website (SEO, thông tin liên hệ, v.v.) |
| Chat Inbox | Xem và trả lời tin nhắn chat |

### 🔌 RESTful API (`/api/v1/`)

**Public API** (không cần xác thực):

- `GET /products` — Danh sách sản phẩm (phân trang, lọc, tìm kiếm)
- `GET /products/{slug}` — Chi tiết sản phẩm
- `GET /categories`, `/categories/tree` — Danh mục
- `GET /brands` — Thương hiệu
- `GET /blog`, `/blog/featured` — Bài viết
- `GET /coupons`, `POST /coupons/validate` — Mã giảm giá
- `GET /reviews` — Đánh giá sản phẩm
- `GET /settings`, `/settings/contact`, `/settings/seo` — Cài đặt
- `GET /health` — Health check
- `GET /docs` — API documentation
- `POST /auth/login`, `/auth/register` — Xác thực

**Customer API** (cần Sanctum token):

- `GET/PUT /user` — Hồ sơ khách hàng
- `GET/POST/PUT/DELETE /cart` — Giỏ hàng
- `GET/POST /orders` — Đơn hàng
- `POST/PUT/DELETE /reviews` — Đánh giá

**Admin API** (cần Sanctum token + phân quyền):

- CRUD `/admin/products`, `/admin/categories`, `/admin/brands`
- `GET/PUT /admin/orders` — Quản lý đơn hàng
- `GET /admin/statistics` — Thống kê doanh thu, sản phẩm

---

## 📁 Cấu trúc thư mục

```
Laravel11_Shop_Website_Full/
├── app/
│   ├── Http/
│   │   ├── Controllers/        # 30 web controllers
│   │   │   ├── Api/            # 13 API controllers
│   │   │   │   └── Admin/      # Admin API controllers
│   │   │   └── Admin/          # Admin web controllers
│   │   ├── Middleware/         # 15 middleware (auth, security)
│   │   ├── Requests/           # Form request validation
│   │   └── Resources/          # API resources
│   ├── Models/                 # 34 Eloquent models
│   ├── Mail/                   # Mailable classes
│   ├── Events/ & Listeners/    # Event system
│   ├── Domain/                 # Business logic
│   └── Support/                # Helpers, utilities
├── config/                     # 18 config files
├── database/
│   ├── migrations/             # 78 migration files
│   ├── seeders/                # 13 seeder files
│   └── factories/              # Model factories
├── resources/
│   └── views/
│       ├── pages/              # 18 nhóm trang storefront
│       ├── admin/              # 40+ module admin
│       ├── layouts/            # Layout chung
│       ├── partials/           # Partial views
│       └── components/         # Blade components
├── routes/
│   ├── web/
│   │   ├── frontend.php        # Route storefront
│   │   └── admin.php           # Route admin
│   └── api.php                 # Route API
├── public/
│   ├── frontend/               # CSS, JS, ảnh storefront
│   ├── backend/                # CSS, JS admin
│   ├── uploads/                # Ảnh upload (sản phẩm, banner...)
│   └── build/                  # Vite build output
├── docs/
│   ├── import/                 # Dữ liệu mẫu CSV/JSON
│   ├── report/                 # Sơ đồ + ảnh báo cáo
│   ├── api/                    # Tài liệu API
│   └── technical/              # Tài liệu kỹ thuật
└── scripts/                    # Script tiện ích
```

---

## 💻 Yêu cầu môi trường

| Yêu cầu | Phiên bản tối thiểu |
|----------|---------------------|
| PHP | 8.2+ |
| Composer | 2.x |
| Node.js | 18+ |
| npm | 9+ |
| MariaDB hoặc MySQL | 10.x / 8.x |
| Git | 2.x |

---

## 🚀 Hướng dẫn cài đặt

### Bước 1: Clone repository

```bash
git clone <repository-url>
cd Laravel11_Shop_Website_Full
```

### Bước 2: Cài đặt dependencies

```bash
composer install
npm install
```

### Bước 3: Tạo file môi trường

```bash
cp .env.example .env
php artisan key:generate
```

> **Windows PowerShell:**
> ```powershell
> Copy-Item .env.example .env
> php artisan key:generate
> ```

### Bước 4: Cấu hình database

Mở file `.env` và cấu hình kết nối database:

```env
DB_CONNECTION=mariadb
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=shopbanhang
DB_USERNAME=root
DB_PASSWORD=
```

> Tạo database `shopbanhang` trước khi chạy migration.

### Bước 5: Chạy migration và seeder

```bash
php artisan migrate
php artisan db:seed
```

> ⚠️ Nếu đang phục hồi từ file backup/dump SQL có sẵn, kiểm tra kỹ trước khi chạy migration để tránh ghi đè dữ liệu.

### Bước 6: Tạo storage link

```bash
php artisan storage:link
```

### Bước 7: Build frontend

```bash
npm run build
```

Hoặc chạy dev server khi đang phát triển:

```bash
npm run dev
```

### Bước 8: Khởi chạy ứng dụng

```bash
php artisan serve
```

Mở trình duyệt tại: **http://127.0.0.1:8000**

---

## ⚙ Cấu hình môi trường

### Các biến `.env` quan trọng

| Biến | Mô tả | Giá trị mặc định |
|------|-------|-------------------|
| `APP_URL` | URL ứng dụng | `http://localhost:8000` |
| `APP_TIMEZONE` | Múi giờ | `Asia/Ho_Chi_Minh` |
| `APP_LOCALE` | Ngôn ngữ | `vi` |
| `DB_CONNECTION` | Loại database | `mysql` |
| `QUEUE_CONNECTION` | Queue driver | `database` |
| `SESSION_DRIVER` | Session driver | `database` |
| `CACHE_STORE` | Cache driver | `database` |
| `MAIL_MAILER` | Mail driver | `log` (local) |
| `FORCE_HTTPS` | Bật HTTPS redirect | `false` |
| `LOCAL_DEMO_SALES_MODE` | Chế độ demo bán hàng | `false` |
| `ADMIN_SELF_REGISTRATION_ENABLED` | Cho phép admin tự đăng ký | `true` |

### Social Login (tuỳ chọn)

```env
GOOGLE_CLIENT_ID=your-google-client-id
GOOGLE_CLIENT_SECRET=your-google-secret
FACEBOOK_CLIENT_ID=your-facebook-client-id
FACEBOOK_CLIENT_SECRET=your-facebook-secret
```

---

## 🔑 Tài khoản mặc định

Sau khi chạy `db:seed`, hệ thống tạo sẵn các tài khoản:

| Vai trò | Email | Mật khẩu |
|---------|-------|-----------|
| Super Admin | `admin@shop.com` | `admin123` |
| Editor | `editor@shop.com` | `editor123` |

> Có thể thay đổi trong `.env` qua biến `ADMIN_SEED_EMAIL`, `ADMIN_SEED_PASSWORD`.

**Truy cập admin:** `http://127.0.0.1:8000/admin`  
**Dashboard sau đăng nhập:** `http://127.0.0.1:8000/dashboard`  
**URL tương thích cũ:** `http://127.0.0.1:8000/admin-dashboard` chuyển hướng về `/dashboard`

---

## 📡 API Documentation

API base URL: `http://127.0.0.1:8000/api/v1`

### Xác thực API

- Dùng **Laravel Sanctum** token-based authentication.
- Đăng nhập qua `POST /api/v1/auth/login` → nhận Bearer token.
- Gửi token trong header: `Authorization: Bearer <token>`.

### Health Check

```bash
curl http://127.0.0.1:8000/api/v1/health
```

### Swagger UI

Truy cập Swagger documentation tại endpoint `/api/v1/docs`.

---

## 🗃 Dữ liệu mẫu

### File dữ liệu mẫu

| File | Mô tả |
|------|-------|
| `docs/import/products-sample-100.csv` | 100 sản phẩm mẫu |
| `docs/import/product-images-manifest.csv` | Manifest ảnh sản phẩm |
| `docs/import/product-images-manifest.json` | Manifest ảnh (JSON) |
| `docs/import/product-images-review.csv` | Danh sách ảnh cần kiểm tra |

### Seeder có sẵn

| Seeder | Mô tả |
|--------|-------|
| `AdminSeeder` | Tài khoản admin + phân quyền |
| `CategorySeeder` | Danh mục sản phẩm |
| `BrandSeeder` | Thương hiệu |
| `ProductSeeder` | Sản phẩm mẫu |
| `CustomerSeeder` | Khách hàng demo |
| `CouponSeeder` | Mã giảm giá |
| `BlogCategorySeeder` + `BlogPostSeeder` | Danh mục và bài viết blog |
| `SettingSeeder` | Cấu hình website |
| `ElectronicsDemoDataSeeder` | Dữ liệu demo thiết bị điện tử |

### Ảnh sản phẩm

Ảnh được lưu theo cấu trúc:

```
public/uploads/products/sample-imported/<SKU>/
```

- `products.thumbnail_path` → ảnh chính sản phẩm.
- `product_media.file_path` → ảnh gallery/phụ.

> ⚠️ Ảnh và dữ liệu mẫu chỉ dùng cho mục đích học tập. Khi triển khai thật cần thay bằng ảnh tự chụp hoặc được cấp quyền.

---

## 🔧 Lệnh thường dùng

### Phát triển

```bash
# Khởi chạy dev server
php artisan serve

# Chạy Vite dev server (hot reload)
npm run dev

# Build frontend production
npm run build

# Chạy queue worker
php artisan queue:work
```

### Database

```bash
# Chạy migration
php artisan migrate

# Chạy seeder
php artisan db:seed

# Xem trạng thái migration
php artisan migrate:status --pending

# Kiểm tra failed jobs
php artisan queue:failed --no-ansi
```

### Cache & Optimize

```bash
# Xóa tất cả cache
php artisan optimize:clear

# Xóa từng loại cache
php artisan view:clear
php artisan config:clear
php artisan cache:clear
php artisan route:clear
```

### Kiểm tra & Lint

```bash
# Lint PHP code
vendor/bin/pint --dirty

# Chạy test
php artisan test

# Xem danh sách route
php artisan route:list
```

---

## 🖼 Ảnh giao diện

Ảnh minh họa giao diện được lưu trong `docs/report/screenshots/`:

| File | Mô tả |
|------|-------|
| `hinh-2-13-trang-chu.png` | Trang chủ storefront |
| `hinh-2-14-danh-sach-san-pham.png` | Danh sách sản phẩm |
| `hinh-2-15-chi-tiet-san-pham.png` | Chi tiết sản phẩm |
| `hinh-2-11-dashboard-admin.png` | Dashboard admin |
| `hinh-2-12-quan-ly-san-pham.png` | Quản lý sản phẩm |
| `hinh-2-16-tai-khoan-khach-hang.png` | Tài khoản khách hàng |
| `hinh-2-17-gio-hang.png` | Giỏ hàng |
| `hinh-2-18-checkout-cod.png` | Checkout COD |

Sơ đồ UML (PlantUML) nằm trong `docs/report/diagrams/`.

---

## 📚 Tài liệu tham khảo

| File | Nội dung |
|------|----------|
| `docs/report/diagrams/PlantUML.md` | Sơ đồ UML hệ thống |
| `docs/report/slide_DATN_*.pptx` | Slide báo cáo đồ án |
| `AGENTS.md` | Quy tắc phát triển cho AI assistant |

---

## ⚠ Lưu ý an toàn

1. **Không** chạy `migrate:fresh` trên database có dữ liệu cần giữ.
2. **Không** import SQL khi chưa tạo backup.
3. **Không** gọi payment gateway thật trong môi trường local/demo.
4. **Không** tạo dữ liệu khách hàng/đơn hàng giả trên môi trường production.
5. **Không** bật hiển thị sản phẩm mẫu nếu chưa rà soát ảnh, giá, tồn kho.
6. **Không** commit mật khẩu thật, SMTP production hoặc API key vào repository.
7. **Luôn** tạo backup trước các thao tác import, cleanup hoặc cập nhật hàng loạt.

---

## 📌 Trạng thái dự án

Dự án đang phục vụ môi trường **học tập / local demo**. Các luồng chính đã được xây dựng hoàn chỉnh:

- ✅ Storefront (trang chủ, danh mục, chi tiết sản phẩm, tìm kiếm)
- ✅ Giỏ hàng & Checkout COD
- ✅ Tài khoản khách hàng (đăng ký, đăng nhập, lịch sử đơn)
- ✅ Admin Dashboard (sản phẩm, đơn hàng, khách hàng, nội dung, thống kê)
- ✅ RESTful API (public + customer + admin) với Sanctum
- ✅ Phân quyền admin (spatie/laravel-permission)
- ✅ Dữ liệu mẫu sản phẩm điện tử
- ✅ Middleware bảo mật (XSS, SQLi, CSRF, Rate Limit, CSP)

**Trước khi triển khai production** cần rà soát: dữ liệu thật, ảnh sản phẩm, tích hợp thanh toán, tích hợp vận chuyển, email SMTP, bảo mật vận hành và kiểm thử tự động.

---

> **Tác giả:** Đồ án tốt nghiệp — Website thương mại điện tử bán thiết bị điện tử
> **Framework:** Laravel 11 • PHP 8.2+ • MariaDB/MySQL
