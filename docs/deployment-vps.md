# Deployment VPS

Tài liệu này là checklist deploy lên VPS Linux. Repo hiện có `configs/nginx.conf` và `configs/supervisor.conf`, nhưng hai file đó đang là placeholder, nên phần cấu hình dưới đây là mẫu thực dụng cần điều chỉnh theo server thật.

## Chuẩn Bị VPS

Khuyến nghị:

- Ubuntu LTS hoặc Linux tương đương.
- Nginx.
- PHP 8.2+ và PHP-FPM.
- Composer.
- Node.js/npm.
- MySQL hoặc MariaDB.
- Redis nếu dùng cache/session/queue production.
- Supervisor nếu chạy queue worker.
- Certbot nếu dùng Let's Encrypt.

PHP extensions nên có:

```bash
php8.2-cli php8.2-fpm php8.2-mysql php8.2-mbstring php8.2-xml php8.2-curl php8.2-zip php8.2-gd php8.2-bcmath
```

Tạo database và user riêng, không dùng MySQL root cho app.

## Biến Môi Trường Production

Dùng `env.production.example` làm điểm xuất phát, sau đó tạo `.env` thật trên server. Không commit `.env`.

Các biến quan trọng:

```env
APP_NAME="Laravel Shop Website"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

LOG_CHANNEL=stack
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=shopbanhang
DB_USERNAME=laravel_user
DB_PASSWORD=strong-password

CACHE_STORE=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
REDIS_HOST=127.0.0.1

SESSION_SECURE_COOKIE=true
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=lax
```

Nếu chưa dùng Redis, có thể tạm dùng:

```env
CACHE_STORE=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync
```

Nhưng production thật nên có kế hoạch Redis/queue rõ ràng.

## Deploy Lần Đầu

Ví dụ thư mục app:

```bash
/var/www/laravel-shop
```

Các bước:

```bash
git clone <repo-url> /var/www/laravel-shop
cd /var/www/laravel-shop
composer install --no-dev --optimize-autoloader
npm ci
npm run build
cp env.production.example .env
php artisan key:generate
```

Cập nhật `.env` bằng giá trị production thật trước khi migrate/import data.

## Migrate, Seed, Import Data

Nếu deploy schema mới:

```bash
php artisan migrate --force
```

`shopbanhang.sql` là dữ liệu legacy/archive cũ, không phải dữ liệu production hiện hành. Không import dump này khi deploy production mới.

Các command import legacy đã bị guard trong production. Chỉ dùng chúng cho tình huống phục hồi/đối chiếu archive cũ, sau khi backup DB và có kế hoạch rollback rõ ràng.

## Storage Permission

Tạo storage link:

```bash
php artisan storage:link
```

Set quyền:

```bash
chown -R www-data:www-data storage bootstrap/cache public/uploads
chmod -R ug+rw storage bootstrap/cache public/uploads
```

Các thư mục app cần ghi:

- `storage`
- `bootstrap/cache`
- `public/uploads`

Product upload hiện dùng `public/uploads/product`, document dùng `public/uploads/document`, brand asset nằm trong `public/uploads/brand`.

## Nginx Mẫu

```nginx
server {
    listen 80;
    server_name your-domain.com www.your-domain.com;
    root /var/www/laravel-shop/public;

    index index.php index.html;
    charset utf-8;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

Kiểm tra và reload:

```bash
nginx -t
systemctl reload nginx
```

## SSL

```bash
certbot --nginx -d your-domain.com -d www.your-domain.com
```

Sau khi bật SSL:

- `APP_URL=https://your-domain.com`
- `SESSION_SECURE_COOKIE=true`
- Kiểm tra redirect HTTP sang HTTPS.

## Laravel Cache Optimize

Sau khi `.env`, route, config ổn định:

```bash
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Mỗi lần deploy code mới nên clear trước rồi cache lại.

## Queue Và Cron

### Queue


```ini
[program:laravel-shop-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/laravel-shop/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=/var/www/laravel-shop/storage/logs/worker.log
stopwaitsecs=3600
```

Reload Supervisor:

```bash
supervisorctl reread
supervisorctl update
supervisorctl start laravel-shop-worker:*
```

Khi deploy:

```bash
php artisan queue:restart
```

### Cron Scheduler

`app/Console/Kernel.php` hiện chưa có schedule active, nhưng vẫn nên chuẩn bị cron nếu sau này thêm task định kỳ:

```cron
* * * * * cd /var/www/laravel-shop && php artisan schedule:run >> /dev/null 2>&1
```

## Checklist Deploy Mỗi Lần

```bash
cd /var/www/laravel-shop
git pull
composer install --no-dev --optimize-autoloader
npm ci
npm run build
php artisan migrate --force
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan queue:restart
```

Sau đó kiểm tra:

```bash
php artisan route:list --path=api/v1
php artisan catalog:audit-data
php artisan legacy:audit-runtime --fail-on-issues
curl -I https://your-domain.com
curl https://your-domain.com/api/v1/health
```

Kiểm tra thủ công:

- Trang chủ load được.
- Trang chi tiết sản phẩm load được.
- Thêm sản phẩm vào giỏ hàng.
- Vào `/checkout` khi cart có item.
- Admin login được.
- Admin xem sản phẩm và đơn hàng được.
- Ảnh trong `public/uploads` hiển thị đúng.

## Backup Và Rollback

Trước deploy có migration/import data:

- Backup database.
- Backup `.env`.
- Backup `public/uploads`.
- Ghi lại commit/tag release hiện tại.

Backup DB ví dụ:

```bash
mysqldump -u laravel_user -p shopbanhang > backup-$(date +%F-%H%M).sql
```

Rollback code không đủ nếu migration đã thay đổi schema/data. Cần có kế hoạch restore DB hoặc migration rollback đã test.

## Lưu Ý Bảo Mật

- `APP_DEBUG=false`.
- Không public `.env`, `storage`, `vendor`, root project.
- Web root phải trỏ vào `public`.
- Dùng user DB riêng.
- Bật HTTPS.
- Giới hạn SSH/firewall.
- Kiểm tra permission không để world-writable quá rộng.
- Theo dõi `storage/logs/laravel.log`, Nginx log, PHP-FPM log.
