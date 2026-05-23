# PRODUCTION READINESS — DangDuc Shop

> **Phiên bản đánh giá:** 2026-05 · **Tác giả:** Principal Engineer Review  
> **Môi trường mục tiêu:** VPS đơn, Laravel 11, MariaDB, PHP 8.3, Ubuntu 22.04

---

## 1. Cấu hình VPS đề xuất

### Mức tối thiểu (50–200 đơn/tháng)
| Thành phần | Cấu hình |
|---|---|
| CPU | 2 vCPU |
| RAM | 4 GB |
| Disk | 40 GB SSD |
| Bandwidth | 1 TB/tháng |
| OS | Ubuntu 22.04 LTS |
| Nhà cung cấp | DigitalOcean, Vultr, BKNS, Viettel IDC |

### Mức thoải mái (200–2000 đơn/tháng)
| Thành phần | Cấu hình |
|---|---|
| CPU | 4 vCPU |
| RAM | 8 GB |
| Disk | 80 GB SSD |
| Bandwidth | 3 TB/tháng |

---

## 2. Stack cài đặt trên VPS

### Nginx
```nginx
# /etc/nginx/sites-available/dangducshop
server {
    listen 80;
    server_name dangducshop.com www.dangducshop.com;
    root /var/www/dangducshop/public;
    index index.php;

    # Gzip
    gzip on;
    gzip_types text/plain text/css application/json application/javascript text/xml application/xml image/svg+xml;
    gzip_min_length 1024;
    gzip_comp_level 5;

    # Static assets cache
    location ~* \.(css|js|png|jpg|jpeg|gif|ico|svg|woff2?)$ {
        expires 30d;
        add_header Cache-Control "public, immutable";
        access_log off;
    }

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_read_timeout 60;
        fastcgi_buffers 16 16k;
        fastcgi_buffer_size 32k;
    }

    # Bảo vệ .env và các file nhạy cảm
    location ~ /\.(env|git|htaccess) {
        deny all;
    }

    # Upload size
    client_max_body_size 20M;
}
```

### PHP-FPM (`/etc/php/8.3/fpm/pool.d/www.conf`)
```ini
pm = dynamic
pm.max_children = 20
pm.start_servers = 5
pm.min_spare_servers = 3
pm.max_spare_servers = 10
pm.max_requests = 500
```

### OPcache (`/etc/php/8.3/fpm/conf.d/10-opcache.ini`)
```ini
opcache.enable=1
opcache.memory_consumption=256
opcache.interned_strings_buffer=16
opcache.max_accelerated_files=10000
opcache.revalidate_freq=0       ; 0 = không recheck file (production)
opcache.validate_timestamps=0   ; tắt khi không có deploy tự động
opcache.fast_shutdown=1
opcache.enable_cli=0
```

---

## 3. Redis (Cache / Session / Queue)

### Cài đặt
```bash
sudo apt install redis-server
sudo systemctl enable redis
```

### Cấu hình `.env` khi có Redis
```env
CACHE_STORE=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=your_strong_password_here
REDIS_PORT=6379
```

### `config/database.php` — Tách DB riêng cho cache và session
```php
'redis' => [
    'cache' => [
        'url' => env('REDIS_URL'),
        'host' => env('REDIS_HOST', '127.0.0.1'),
        'password' => env('REDIS_PASSWORD', null),
        'port' => env('REDIS_PORT', '6379'),
        'database' => '1',  // DB 1 cho cache
    ],
    'session' => [
        'database' => '2',  // DB 2 cho session
    ],
    'queue' => [
        'database' => '3',  // DB 3 cho queue
    ],
],
```

---

## 4. Queue Worker / Supervisor

### Cài Supervisor
```bash
sudo apt install supervisor
```

### `/etc/supervisor/conf.d/dangducshop-worker.conf`
```ini
[program:dangducshop-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/dangducshop/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/log/supervisor/dangducshop-worker.log
stopwaitsecs=3600
```

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start dangducshop-worker:*
```

---

## 5. Backup Database

### Script backup hàng ngày (`/root/backup-dangducshop.sh`)
```bash
#!/bin/bash
DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR=/var/backups/dangducshop
mkdir -p $BACKUP_DIR

# Dump DB
mysqldump -u root shopbanhang | gzip > $BACKUP_DIR/db_$DATE.sql.gz

# Giữ 30 ngày
find $BACKUP_DIR -name "*.sql.gz" -mtime +30 -delete

# Backup uploads
tar -czf $BACKUP_DIR/uploads_$DATE.tar.gz /var/www/dangducshop/storage/app/public /var/www/dangducshop/public/uploads
find $BACKUP_DIR -name "uploads_*.tar.gz" -mtime +7 -delete
```

### Cron
```
0 2 * * * /root/backup-dangducshop.sh >> /var/log/backup-dangducshop.log 2>&1
```

---

## 6. CDN / Object Storage (Roadmap)

**Hiện tại:** Ảnh lưu trực tiếp tại `public/uploads/` — **chỉ phù hợp cho VPS đơn**

**Khi traffic tăng (1000+ user):** Chuyển sang:
- **Cloudflare** (miễn phí, cache CDN toàn cầu, DDoS protection)
- **Bunny CDN** (~1$/100GB, latency tốt ở Việt Nam)
- **AWS S3 / DigitalOcean Spaces** cho object storage

**Cách tích hợp khi sẵn sàng:**
1. Set `FILESYSTEM_DISK=s3` trong `.env`
2. Cấu hình `config/filesystems.php` với credentials
3. Chạy `php artisan storage:link` lại

---

## 7. Monitoring / Logging

### Logging production (`config/logging.php`)
```php
// Thêm channel 'daily' và set LOG_CHANNEL=daily trong .env
'daily' => [
    'driver' => 'daily',
    'path' => storage_path('logs/laravel.log'),
    'level' => 'warning',  // Chỉ log warning trở lên ở production
    'days' => 14,
],
```

### Monitoring đề xuất
| Công cụ | Mục đích | Chi phí |
|---|---|---|
| **UptimeRobot** | Kiểm tra uptime mỗi 5 phút | Miễn phí |
| **Laravel Telescope** | Debug query/request (chỉ bật dev) | Miễn phí |
| **Sentry** | Error tracking production | Miễn phí 5K events/tháng |
| **Netdata** | Server metrics (CPU, RAM, disk) | Miễn phí tự host |

---

## 8. Security Checklist Production

| # | Mục | Trạng thái | Hành động |
|---|---|---|---|
| 1 | `APP_DEBUG=false` | ✅ Đã tắt trong `.env` | — |
| 2 | `.env` bị gitignore | ✅ Đã có trong `.gitignore` | — |
| 3 | HTTPS / TLS | ⚠️ Chưa có (localhost) | Bật Let's Encrypt khi deploy |
| 4 | Rate limit login admin | ✅ `throttle:5,1` | — |
| 5 | Rate limit API | ✅ `throttle:10,1` | — |
| 6 | Rate limit checkout/contact | ✅ **Đã thêm** — `/confirm-order: 5,1` · `/lien-he: 3,1` | ✅ Phase 2A |
| 7 | Rate limit forgot password | ✅ **Đã thêm** — `/recover-pass: 5,1` · admin forgot: `5,1` | ✅ Phase 3 |
| 8 | Rate limit autocomplete/review | ✅ **Đã thêm** — `/autocomplete-ajax: 60,1` · `/send-comment: 10,1` | ✅ Phase 3 |
| 9 | Rate limit login customer | ✅ **Đã thêm** — `/login-customer: 10,1` · `/add-customer: 10,1` | ✅ Phase 3 |
| 10 | CSRF | ✅ Blade @csrf + middleware | — |
| 11 | File upload validate (mimes) | ✅ Whitelist `jpg,jpeg,png,gif,webp` — **đã loại SVG** | ✅ Phase 3 |
| 12 | Security headers | ✅ `SecurityHeadersMiddleware` đã đăng ký global | ✅ Phase 3 |
| 13 | DB password mạnh | ⚠️ `.env` đang để trống | Đặt password khi lên production |
| 14 | `LOG_LEVEL=debug` ở production | ✅ Đã đổi thành `warning` | ✅ Phase 2A |
| 15 | Backup tự động | ❌ Chưa cấu hình | Xem mục 5 |
| 16 | Admin 2FA | ❌ Chưa có | Roadmap |
| 17 | Tồn kho race condition | ✅ **Đã fix** — `lockForUpdate()` trong DB transaction | ✅ Phase 3 |
| 18 | Coupon race condition | ✅ **Đã fix** — atomic UPDATE `WHERE used_count < usage_limit` | ✅ Phase 3 |
| 19 | Đơn trùng double-submit | ✅ **Đã fix** — idempotency key session-based | ✅ Phase 3 |

### Security Headers (đã có trong `SecurityHeadersMiddleware`)
```
X-Content-Type-Options: nosniff
X-Frame-Options: SAMEORIGIN (local) / DENY (production)
X-XSS-Protection: 1; mode=block
Referrer-Policy: strict-origin-when-cross-origin
Permissions-Policy: geolocation=(), microphone=(), camera=()
Content-Security-Policy: (CSP đầy đủ)
Strict-Transport-Security: (chỉ bật khi HTTPS)
```

### Security Headers Nginx (backup — thêm vào `server {}` nếu muốn double layer)
```nginx
add_header X-Frame-Options "SAMEORIGIN" always;
add_header X-XSS-Protection "1; mode=block" always;
add_header X-Content-Type-Options "nosniff" always;
add_header Referrer-Policy "strict-origin-when-cross-origin" always;
add_header Permissions-Policy "camera=(), microphone=(), geolocation=()" always;
# Bật sau khi có HTTPS:
# add_header Strict-Transport-Security "max-age=31536000; includeSubDomains" always;
```


---

## 9. Scale Roadmap

### Giai đoạn 1 — 100 user đồng thời (~50–500 đơn/tháng)
**Cấu hình:** VPS 4vCPU / 8GB RAM  
**Stack:** Nginx + PHP-FPM + MariaDB + Redis + Supervisor  
**Không cần thay đổi gì thêm** so với thiết kế hiện tại

- [x] Bật OPcache
- [x] Redis cho cache/session
- [x] Queue worker qua Supervisor
- [x] Cloudflare free (DNS + CDN + DDoS)
- [ ] Backup tự động hàng ngày

---

### Giai đoạn 2 — 1.000 user đồng thời (~500–5.000 đơn/tháng)
**Nâng cấp cần làm:**
- Tăng VPS lên 8 vCPU / 16 GB RAM
- Bật Cloudflare Pro (Page Rules, cache rule)
- Chuyển uploads lên Object Storage (S3/Spaces)
- Bật `opcache.validate_timestamps=0` (cần deploy script reset OPcache)
- Cấu hình MariaDB `innodb_buffer_pool_size = 4G`
- Xem xét tách Redis ra server riêng nếu RAM chật

---

### Giai đoạn 3 — 10.000 user đồng thời (~5.000–50.000 đơn/tháng)
**Nâng cấp cần làm:**
- **Read Replica MySQL:** Tách read/write bằng `config/database.php` connections `mysql` và `mysql_read`
- **Horizontal scaling web:** Load balancer (Nginx/HAProxy) + 2-3 app server (sticky session hoặc session trên Redis)
- **CDN đầy đủ:** Bunny CDN / Cloudflare Enterprise cho assets
- **Queue dedicated server:** Tách worker ra server riêng
- **Monitoring:** Datadog hoặc New Relic

---

### Giai đoạn 4 — 1.000.000 user (~triệu đơn/năm)
> Đây là giai đoạn cần đội ngũ kỹ thuật chuyên nghiệp. Roadmap:

- Tách microservice theo domain: Catalog, Order, Customer, Notification
- Kafka / RabbitMQ cho event-driven architecture
- Elasticsearch cho search
- MySQL Cluster / Vitess cho sharding
- Kubernetes cho container orchestration
- CDN multi-region
- Redis Cluster

**Lưu ý:** Ở quy mô này, kiến trúc phải được thiết kế lại từ đầu. Không nên cố giữ Laravel monolith.

---

## 10. Lệnh cần chạy trước khi deploy production

```bash
# Tối ưu autoload
composer install --optimize-autoloader --no-dev

# Cache config, route, view
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Build assets
npm ci
npm run build

# Chạy migration (KHÔNG dùng fresh/refresh)
php artisan migrate --force

# Đảm bảo storage link
php artisan storage:link

# Khởi động queue worker
sudo supervisorctl restart dangducshop-worker:*
```

---

*Tài liệu này được tạo bởi Principal Engineer Review — 2026-05-23*
