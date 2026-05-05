# Demo Data Lifecycle

This project can run with synthetic electronics demo data for learning, UI review, smoke testing, and local demos. The demo data is intentionally separate from legacy archive data and future real business data.

## Purpose

`ElectronicsDemoDataSeeder` creates a realistic electronics catalog without using `shopbanhang.sql`, legacy `tbl_*` tables, scraped commercial content, or copyrighted product images. It is useful when:

- learning the admin, storefront, cart, checkout, customer, inventory, coupon, and blog flows;
- testing the modern database schema with a larger catalog;
- preparing screenshots or local demos before real product data exists.

The seeder is idempotent and is not wired into `DatabaseSeeder` by default.

## Seed Electronics Demo Data

Run this only on local, learning, demo, or intentionally prepared staging databases:

```bash
php artisan db:seed --class=ElectronicsDemoDataSeeder --force
```

The seeder uses stable identifiers such as `DEMO-` SKU prefixes, demo customer emails, demo coupon codes, and demo order codes so the data can be found and cleaned later.

## Cleanup Dry-Run

Always inspect the cleanup plan first:

```bash
php artisan electronics-demo:cleanup
```

Default behavior is dry-run only. It prints the tables, counts, IDs, codes, skipped records, and risk warnings. It does not delete records.

## Delete Demo Data Later

After a database backup and explicit approval, run:

```bash
php artisan electronics-demo:cleanup --confirm
```

This deletes only the records matched by the cleanup command. It uses a database transaction and deletes in foreign-key-safe order.

To also clear database sessions and log out active users:

```bash
php artisan electronics-demo:cleanup --confirm --include-sessions
```

Do not run `--confirm` before reviewing the dry-run output and taking a backup.

## Smoke Cleanup vs Electronics Demo Cleanup

Use `smoke:cleanup` for older smoke-test artifacts created during cleanup and audit work:

```bash
php artisan smoke:cleanup
```

Use `electronics-demo:cleanup` for data created by `ElectronicsDemoDataSeeder` and the post-demo COD smoke order:

```bash
php artisan electronics-demo:cleanup
```

The commands intentionally target different data sets. Run both dry-runs before any cleanup if the database contains both smoke records and electronics demo records.

## Data Kept

`electronics-demo:cleanup` is designed to keep base and operational data:

- admin users;
- roles and permissions;
- base customers such as `customer1`, `customer2`, and `customer3`;
- base coupons such as `WELCOME10`, `SAVE50K`, `VIP20`, and `BLACKFRIDAY`;
- original seeded products, categories, and brands that were not created by the electronics demo seeder;
- non-demo blog post view counters.

Rows are skipped when the command detects non-demo references that make deletion unsafe.

## Data Removed

When run with `--confirm`, the electronics demo cleanup targets:

- demo products with `DEMO-` SKU/source markers;
- related product variants;
- related product/category pivots;
- demo brands and product categories when they have no non-demo references;
- demo customers such as `demo.customer01@example.com` through `demo.customer08@example.com`;
- demo customer addresses, carts, and cart items;
- demo orders such as `DEMO-ORDER-0001` through `DEMO-ORDER-0008`;
- the post-demo smoke checkout order `DKDXJRVUZE`;
- related order items, payments, shipping rows, invoices, returns, and coupon redemptions when present;
- demo coupons such as `DEMO10`, `FREESHIP`, `ELECTRO50K`, `LAPTOP200K`, and `AUDIO15`;
- demo blog categories and posts;
- demo analytics/search/recommendation/product-view rows unless `--include-analytics=0` is used;
- sessions only when `--include-sessions` is provided.

## Production Transition Checklist

Before switching from learning/demo mode to real business data:

1. Backup the current database.
2. Run `php artisan electronics-demo:cleanup` and review every table/count/ID.
3. Run `php artisan smoke:cleanup` if earlier smoke records also need review.
4. Get explicit approval before any `--confirm` cleanup.
5. Run `php artisan electronics-demo:cleanup --confirm` only after backup and approval.
6. Run `php artisan smoke:cleanup --confirm` only if the dry-run plan is accepted.
7. Add or import real products through production-ready admin/import flows, not `shopbanhang.sql`.
8. Configure real mail on the production environment. Do not commit SMTP credentials.
9. Configure a real queue worker and Supervisor process for production.
10. Configure production payment gateways only when merchant credentials and callback URLs are ready.
11. Smoke test admin, storefront, cart, COD checkout, customer account, and order management after cleanup.
12. Re-run runtime checks:

```bash
php artisan migrate:status --pending
php artisan legacy:audit-runtime --fail-on-issues
php artisan queue:failed --no-ansi
```

## Warnings

- Do not run `migrate:fresh` on a database that contains real or production-like data.
- Do not import `shopbanhang.sql` into the modern runtime database.
- Do not run cleanup commands with `--confirm` before taking a backup.
- Do not commit real mail, payment, AI, database, or admin credentials.
- Keep local/dev mail safe unless intentionally configuring production mail:

```env
MAIL_MAILER=log
LOG_LEVEL=debug
MAIL_TIMEOUT=5
```
