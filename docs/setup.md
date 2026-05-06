# Setup Local

Tài liệu này dùng để dựng môi trường local cho `Laravel11_Shop_Website_Full`. Mục tiêu là chạy được storefront, admin, API, test và frontend build trên máy dev.

## Yêu Cầu Môi Trường

| Thành phần | Phiên bản/ghi chú |
| --- | --- |
| PHP | `>= 8.2`, theo `composer.json`. |
| Composer | Dùng để cài Laravel và package PHP. |
| Node.js + npm | Dùng cho Vite, Vue, Tailwind/Bootstrap asset. |
| MySQL hoặc MariaDB | Database chính khi chạy local với data thật. |
| Git | Quản lý source. |
| PHP extensions | `pdo_mysql`, `mbstring`, `openssl`, `fileinfo`, `tokenizer`, `xml`, `ctype`, `json`, `curl`. |


## Cài Dependency

Từ thư mục root project:

```bash
composer install
npm install
```

Nếu đã có `vendor` hoặc `node_modules` nhưng vừa pull code mới, vẫn nên chạy lại hai lệnh trên khi `composer.lock` hoặc `package-lock.json` thay đổi.

## Cấu Hình `.env`

Tạo file `.env`:

```bash
cp .env.example .env
php artisan key:generate
```

PowerShell:

```powershell
Copy-Item .env.example .env
php artisan key:generate
```

Cấu hình local tối thiểu:

```env
APP_NAME="Laravel Shop"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=shopbanhang
DB_USERNAME=root
DB_PASSWORD=

CACHE_STORE=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync
MAIL_MAILER=log
MAIL_TIMEOUT=5
LOG_LEVEL=debug
```

Nếu chạy social login, mail SMTP, PayPal hoặc bank transfer, kiểm tra thêm trong `config/services.php` và các biến tương ứng trong `.env`. Không cần cấu hình các cổng thanh toán để chạy flow cart/checkout cơ bản.

## Kiểm Tra Email Local

Local/dev nên dùng mailer log để tránh gửi email ra ngoài:

```env
MAIL_MAILER=log
LOG_LEVEL=debug
MAIL_TIMEOUT=5
```

Với cấu hình này, các email như quên mật khẩu khách hàng/admin được ghi vào `storage/logs/laravel.log`. Có thể xem nhanh bằng PowerShell:

```powershell
Get-Content storage/logs/laravel.log -Tail 120
```

Không commit SMTP username/password thật. Khi deploy production, cấu hình SMTP riêng trên server hoặc secret manager và giữ `MAIL_TIMEOUT` ở giá trị hữu hạn như `5` giây để tránh request bị treo lâu nếu mail server không phản hồi.

## Tạo Database

Tạo database rỗng:

```sql
CREATE DATABASE shopbanhang CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

## Migrate, Seed, Import Data

Project có hai cách dựng data local.

### Dữ liệu legacy cũ từ dump

Repo có file `shopbanhang.sql`, nhưng đây là dữ liệu legacy/archive cũ, không phải dữ liệu production hiện hành. Không dùng dump này để bootstrap catalog mới hoặc deploy production.

Các command legacy import đã được guard. Chỉ chạy khi cần phục hồi/đối chiếu archive cũ, sau khi backup DB và hiểu rõ dữ liệu sẽ được nạp:

```bash
php artisan legacy:load-archive --dump=/path/to/legacy-archive.sql --force --confirm-legacy-archive
```

Nếu không có lý do archive rõ ràng, bỏ qua bước này.

### Cách nhẹ hơn: migration + seeder

```bash
php artisan migrate
php artisan db:seed
```

Seeder hiện có trong `database/seeders`: admin, brand, category, product, customer, coupon, settings, blog.

`CustomerAccountDemoSeeder` tạo dữ liệu demo cho các trang tài khoản khách hàng mới (`payment-methods`, `invoices`, `returns`). Seeder này chỉ tự chạy qua `DatabaseSeeder` trong môi trường local/demo hoặc khi `SEED_CUSTOMER_ACCOUNT_DEMO=true`. Trên DB thật, nếu cần dữ liệu demo thì gọi rõ:

```bash
php artisan db:seed --class=CustomerAccountDemoSeeder --force
```

Nếu chỉ cần catalog demo:

```bash
php artisan demo:import-catalog
```

Không chạy command demo này trên production.

Khi đã có catalog thay thế, có thể rà trước và purge dữ liệu demo DummyJSON:

```bash
php artisan catalog:purge-demo --source=dummyjson --dry-run
php artisan catalog:purge-demo --source=dummyjson --confirm-replacement-ready
```

### Command dữ liệu nên biết

```bash
php artisan catalog:audit-data
php artisan legacy:audit-runtime --fail-on-issues
php artisan admin:normalize-auth --dry-run
```

Các command `legacy:load-archive`, `import:legacy-database`, `setup:legacy-tables`, `data:migrate-legacy`, và `migrate:legacy-data` chỉ dành cho archive/migration cũ. Không chạy các command này trong setup thường ngày.

Trước khi chạy command có tác động ghi/xóa dữ liệu, mở file trong `app/Console/Commands` để xem chính xác nó làm gì.

## Chạy App Local

Mở hai terminal:

```bash
php artisan serve
```

```bash
npm run dev
```

URL thường dùng:

| URL | Mục đích |
| --- | --- |
| `http://127.0.0.1:8000` | Storefront. |
| `http://127.0.0.1:8000/admin` | Admin login. |
| `http://127.0.0.1:8000/api/v1/health` | API health check. |
| `http://127.0.0.1:8000/gio-hang` | Cart page. |
| `http://127.0.0.1:8000/checkout` | Checkout page, cần có cart item. |

## Build Frontend

Build production asset:

```bash
npm run build
```

Vite entry nằm trong `vite.config.js`:

- `resources/css/tailwind.css`
- `resources/css/app.css`
- `resources/js/app.js`

Project có Vue 3, Pinia và Vue Router trong dependency, nhưng UI chính hiện vẫn là Blade. Đừng giả định toàn bộ storefront là SPA.

## Lỗi Setup Thường Gặp

| Lỗi | Cách xử lý |
| --- | --- |
| `No application encryption key has been specified` | Chạy `php artisan key:generate`. |
| Không kết nối được DB | Kiểm tra MySQL đang chạy, database đã tạo, `DB_*` trong `.env` đúng. |
| Trang 500 sau khi sửa `.env` | Chạy `php artisan optimize:clear`. |
| CSS/JS không load | Chạy `npm run dev` khi dev hoặc `npm run build` khi dùng asset build. Kiểm tra `public/build/manifest.json`. |
| Admin login không vào được | Chạy seeder/admin setup hoặc `php artisan admin:normalize-auth --dry-run` để kiểm tra quyền. |
| Cart/checkout lỗi dữ liệu | Kiểm tra đã import product/category/brand chưa; cart cần product có tồn kho. |
| Permission ghi file upload/cache | Đảm bảo `storage`, `bootstrap/cache`, `public/uploads` ghi được. |

## Lệnh Kiểm Tra Sau Setup

```bash
php artisan route:list --path=api/v1
npm run build
git diff --check
```
