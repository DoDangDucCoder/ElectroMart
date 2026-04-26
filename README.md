<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

# 🛍️ Laravel Shop Website (Laravel 11)

Dự án **Laravel11_Shop_Website_Full** là website thương mại điện tử được xây dựng bằng **Laravel 11**, hỗ trợ đầy đủ các tính năng quản lý sản phẩm, người dùng, giỏ hàng, thanh toán, v.v.

---

## 🚀 1. Yêu cầu hệ thống

Trước khi cài đặt, đảm bảo bạn đã cài:

- **PHP >= 8.2**
- **Composer**
- **Node.js & npm**
- **MySQL hoặc PostgreSQL**
- **Git**

## 🧩 2. Clone source về máy

git clone https://github.com/HieuTanGitHub/Laravel11_Shop_Website_Full.git

Di chuyển vào thư mục dự án:

cd Laravel11_Shop_Website_Full

## ⚙️ 3. Cài đặt các thư viện PHP (vendor)

composer install hay composer update

## 🛠️ 4. Cấu hình môi trường .env

Mở file .env và sửa các thông tin kết nối database:

## 🛠️ 4.1 Nạp dữ liệu thật từ `shopbanhang.sql`

APP_NAME="Laravel Shop"
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=tendatabase
DB_USERNAME=root
DB_PASSWORD=


Tạo key cho ứng dụng:
php artisan key:generate

Nạp dump thật và chuyển sang schema hiện tại:
php artisan data:load-real --dump=shopbanhang.sql --force

Nếu chỉ muốn seed demo để test nhanh, giữ nguyên `DatabaseSeeder` hiện tại và chạy:
php artisan db:seed

## ⚙️ 5. Chạy ứng dụng
Chạy server Laravel:
php artisan serve

Mở trình duyệt và truy cập:
👉 http://127.0.0.1:8000

## 🚀 6. Contact
fb/hieutruong19 | zalo 0932023992 fix code, any question | YT tutorials: https://www.youtube.com/@hieutancodeproject

## 🚀 7. Web Images
<img width="1358" height="1154" alt="Image" src="https://github.com/user-attachments/assets/19d9f5a2-eeb7-4ca7-9215-d732f778b842" />
<img width="1358" height="1605" alt="Image" src="https://github.com/user-attachments/assets/ba16f255-21bb-4cdb-865f-c02f05d7dd77" />
<img width="1358" height="2532" alt="Image" src="https://github.com/user-attachments/assets/17813296-e7c9-44be-ae32-ae34f0723961" />
<img width="1358" height="1794" alt="Image" src="https://github.com/user-attachments/assets/4d9eab3e-14fa-4a07-a733-ac55f8b8d628" />
<img width="1358" height="1113" alt="Image" src="https://github.com/user-attachments/assets/d51b21a4-6d2b-4129-bccf-977df33aef82" />
<img width="1358" height="1514" alt="Image" src="https://github.com/user-attachments/assets/236f8fff-1332-447c-9884-b5815f28da6c" />
