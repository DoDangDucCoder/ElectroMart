# ElectroMart - Electronics E-Commerce Website

## Project Overview

ElectroMart is a Laravel-based electronics e-commerce website built as a learning and local-demo project. It includes a customer storefront, account and order workflows, and an administration area. It is not presented as a production deployment.

## Backend

- **Framework:** Laravel 11 (`^11.9`), PHP 8.2+
- **API:** Versioned REST-style API under `/api/v1`. The current registered route list contains 83 API route entries across 69 URI templates. This count includes routes with different HTTP methods and may change as routes are added or configured.
- **Authentication and authorization:** Laravel Sanctum protects customer and admin API routes. Customer API requests are checked for a customer identity; admin access is additionally guarded by permission middleware. The application also includes role/permission management and Laravel Socialite dependencies.
- **Architecture:** Laravel MVC with Domain, Application, and Infrastructure layers in selected modules. Repository interfaces are bound to Eloquent implementations through the service container. Existing Laravel and legacy modules remain alongside this structure; the whole codebase is not a strict Clean Architecture implementation.
- **Main modules:** Catalog, Checkout, Order, Customer, Marketing, CMS, Reporting, Shared, and Data Migration.

## Database

- **Supported database drivers:** MySQL and MariaDB are configured; SQLite is also present in the Laravel database configuration. The example environment defaults to MySQL.
- **Schema source:** 84 migration files contain 85 `Schema::create` declarations. This is a count of schema-creation declarations, not a count of domain entities or a live database inspection; five declarations use Spatie permission-table names configured dynamically.
- **Core data relationships:**
  - Customers have carts, addresses, and orders.
  - Carts contain cart items linked to products and, optionally, product variants.
  - Orders belong to customers and addresses, and have order items, shipping records, and payment records.
  - Products have variants; product reviews link products and customers.
  - Coupons have redemption records linked to customers and orders.

## Frontend

- **Primary UI:** Laravel Blade views with JavaScript.
- **Interactive components/build:** Vue 3 components, Vite, Tailwind CSS, and Bootstrap.
- **Customer screens:** Home/catalog, product search and detail, cart, checkout, account, order history/tracking, wishlist, blog, and support pages.
- **Admin screens:** Product and category management, customers, orders, inventory, coupons, reviews, roles, and reporting.

## Technical Highlights

- Checkout coordinates order creation, order items, shipping, payment records, coupon redemption, inventory reservation, and cart cleanup inside database transactions.
- Inventory workflows validate stock for products and variants, use row locks when reserving or releasing stock, and invalidate product cache after stock updates.
- Coupon workflows check active periods and usage limits, support per-customer limits, and track reserved, redeemed, and released coupon usage.
- API routes are separated into public, customer, and admin groups with Sanctum authentication and admin permission checks.

## CV-Ready Key Achievements

- **Implemented** transactional checkout workflows coordinating order creation, shipping, payment records, coupon redemption, inventory reservation, and cart cleanup.
- **Developed** row-locked inventory reservation and release flows for products and variants, with stock validation and cache invalidation.
- **Implemented** coupon validation and redemption rules covering validity periods, global and per-customer usage limits, and time-bound reservations.
- **Integrated** a versioned Laravel API with 83 registered route entries across 69 URI templates, using Sanctum authentication and separate customer/admin authorization middleware.

## CV-Ready Project Description

- **Developed** an electronics e-commerce platform with a customer storefront, product catalog, cart, checkout, customer accounts, order tracking, and an admin dashboard.
- **Built** the application with Laravel 11 and PHP 8.2+, using Blade as the primary view layer, Vue 3 for selected interactive components, and Vite for asset builds.
- **Structured** selected backend modules across Domain, Application, and Infrastructure layers, using repository interfaces and Eloquent adapters alongside existing Laravel modules.

## Requirements

- PHP 8.2 or later
- Composer
- Node.js and npm
- MySQL or MariaDB

## Installation

Clone the repository:

```bash
git clone https://github.com/DoDangDucCoder/ElectroMart.git
cd ElectroMart
```

Install backend and frontend dependencies:

```bash
composer install
npm install
```

Create the local environment file. On macOS/Linux or Git Bash:

```bash
cp .env.example .env
```

On Windows PowerShell:

```powershell
Copy-Item .env.example .env
```

Create a MySQL/MariaDB database, then set `DB_DATABASE`, `DB_USERNAME`, and `DB_PASSWORD` in `.env` to match your local database. Generate the application key and run migrations:

```bash
php artisan key:generate
php artisan migrate
php artisan storage:link
```

Build frontend assets and start the local server:

```bash
npm run build
php artisan serve
```

For frontend development with Vite hot reload, run `npm run dev` in a separate terminal. To process queued notifications locally, run `php artisan queue:work` in another terminal.

## Configuration Notes

- The `.env.example` file defaults to MySQL. Update the database name and credentials before running migrations.
- Google and Facebook sign-in require provider credentials in `.env`.
- PayPal-related routes and controller code exist, but the repository does not include the PayPal SDK or its configuration. PayPal checkout requires additional setup and is not ready to use out of the box.

## Author

Do Dang Duc ([GitHub: DoDangDucCoder](https://github.com/DoDangDucCoder))
