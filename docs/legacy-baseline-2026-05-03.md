# Legacy Baseline - 2026-05-03

## Database

- Current database: `shopbanhang`
- Backup created at: `storage/app/backups/shopbanhang_20260503_141843.sql`
- Backup size: `2,914,693` bytes
- Post-migration backup created at: `storage/app/backups/shopbanhang_post_migration_20260503_145352.sql`
- Post-migration backup size: `2,920,757` bytes

## Migration Status

Initial pending migrations reviewed before schema change:

- `2026_05_01_000100_create_customer_payment_methods_table`
- `2026_05_02_000100_create_vat_invoices_table`
- `2026_05_02_000200_create_order_returns_table`

These migrations create schema required by customer account pages. See `docs/data-migration-plan.md`.

After backup, these migrations were run successfully with:

```bash
php artisan migrate --force
```

Post-run status:

```text
No pending migrations.
```

Created tables verified:

- `customer_payment_methods`
- `vat_invoices`
- `order_returns`

Verified columns:

- `customer_payment_methods`: `id`, `customer_id`, `type`, `provider`, `holder_name`, `account_identifier`, `expiry_month`, `expiry_year`, `is_default`, `is_verified`, `meta`, `created_at`, `updated_at`, `deleted_at`
- `vat_invoices`: `id`, `code`, `customer_id`, `order_id`, `type`, `tax_code`, `company_name`, `company_address`, `email`, `value`, `status`, `issued_at`, `meta`, `created_at`, `updated_at`, `deleted_at`
- `order_returns`: `id`, `code`, `customer_id`, `order_id`, `order_item_id`, `quantity`, `reason`, `refund_amount`, `refund_method`, `status`, `refunded_at`, `meta`, `created_at`, `updated_at`, `deleted_at`

Row counts immediately after migration:

- `customer_payment_methods`: `0`
- `vat_invoices`: `0`
- `order_returns`: `0`

Authenticated render smoke test with existing active customer id `2`:

- `CustomerAccountController::paymentMethods`: rendered successfully.
- `CustomerAccountController::invoices`: rendered successfully.
- `CustomerAccountController::returns`: rendered successfully.

Unauthenticated route smoke test:

- `/account/payment-methods`: `302` to login.
- `/account/invoices`: `302` to login.
- `/account/returns`: `302` to login.

Seeder verification:

- Added `Database\Seeders\CustomerAccountDemoSeeder` for account-page demo data.
- `DatabaseSeeder` only auto-runs it in local/demo setup or when `SEED_CUSTOMER_ACCOUNT_DEMO=true`.
- Verified with an isolated local database during the cleanup pass.
- Verified idempotency by running `db:seed --force` a second time.
- Isolated local verification result: `orders=1`, `order_items=1`, `customer_payment_methods=2`, `vat_invoices=1`, `order_returns=1`.
- This demo seeder was not run on `shopbanhang`; current production-like DB counts for the three new tables remain `0`.

## Existing Legacy Tables

The following `tbl_*` tables exist in `shopbanhang` at baseline time:

- `tbl_admin`
- `tbl_attribute`
- `tbl_brand`
- `tbl_category_post`
- `tbl_category_product`
- `tbl_comment`
- `tbl_coupon`
- `tbl_customers`
- `tbl_feeship`
- `tbl_gallery`
- `tbl_icons`
- `tbl_infomation`
- `tbl_order`
- `tbl_order_details`
- `tbl_payment`
- `tbl_posts`
- `tbl_product`
- `tbl_product_attribute`
- `tbl_quanhuyen`
- `tbl_rating`
- `tbl_shipping`
- `tbl_slider`
- `tbl_social`
- `tbl_social_customers`
- `tbl_statistical`
- `tbl_tinhthanhpho`
- `tbl_videos`
- `tbl_visitors`
- `tbl_xaphuongthitran`

## Runtime Legacy Audit

Command:

```bash
php artisan legacy:audit-runtime --fail-on-issues
```

Result:

```text
Runtime legacy audit passed.
```

This means the guarded runtime paths scanned by the audit command currently have no direct `tbl_*` dependency.

The audit command also supports a cleanup-planning mode:

```bash
php artisan legacy:audit-runtime --include-legacy-tooling
```

That mode intentionally reports guarded archive/import tooling references so they can be tracked before final deletion.

## Legacy Command Guard Smoke Tests

Production guard checks:

- `php artisan legacy:load-archive --dump=/path/to/legacy-archive.sql`: blocked in production.
- `php artisan import:legacy-database --dump=/path/to/legacy-archive.sql`: blocked in production.
- `php artisan setup:legacy-tables`: blocked in production.
- `php artisan data:migrate-legacy`: blocked in production.
- `php artisan migrate:legacy-data`: blocked in production.

Local-without-confirm checks:

- `php artisan legacy:load-archive --env=local --dump=/path/to/legacy-archive.sql`: blocked without `--confirm-legacy-archive`.
- `php artisan import:legacy-database --env=local --dump=/path/to/legacy-archive.sql`: blocked without `--confirm-legacy-archive`.
- `php artisan setup:legacy-tables --env=local`: blocked without `--confirm-legacy-archive`.
- `php artisan data:migrate-legacy --env=local`: blocked without `--confirm-legacy-migration`.
- `php artisan migrate:legacy-data --env=local`: blocked without `--confirm-legacy-migration`.

Admin auth command:

- `php artisan admin:normalize-auth --dry-run`: dry run succeeds without mutating data.
- `php artisan admin:normalize-auth --dry-run --confirm-legacy-admin-backfill`: dry run succeeds without mutating data.

## Legacy Holdout Migration

Backup before holdout write:

- `storage/app/backups/shopbanhang_pre_holdout_migration_20260504_003023.sql`

Command executed:

```bash
php artisan legacy:migrate-holdouts --env=local --confirm-legacy-holdout-migration --skip-orphans
```

Result:

- `product_reviews`: `36` rows after migration.
- Legacy comment reviews: `33`.
- Legacy rating reviews: `3`.
- `analytics_daily`: `5` rows after migration.
- Skipped orphan: `tbl_rating.rating_id = 38`, `product_id = 39`.
- Idempotency checked by running the command a second time; counts did not increase.
