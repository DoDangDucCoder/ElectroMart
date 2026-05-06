# Legacy Cleanup Backlog

This backlog tracks remaining `tbl_*` references after the runtime migration audit passed.

## Current Decision

- Do not drop legacy `tbl_*` tables yet.
- Do not delete `app/Models/*` legacy models yet.
- Keep guarded archive/import commands only for recovery or data comparison.
- Treat `shopbanhang.sql` and `database/import_legacy_tables.sql` as archive material, not setup input.

## Runtime Status

`php artisan legacy:audit-runtime --fail-on-issues` currently passes.

The scanned runtime areas do not contain direct `tbl_*` dependencies. The default audit scans runtime code plus non-legacy console commands, while excluding guarded legacy migration/import tooling.

Use this command for the normal deploy/runtime gate:

```bash
php artisan legacy:audit-runtime --fail-on-issues
```

Use this command for cleanup planning when intentionally reviewing guarded legacy tooling too:

```bash
php artisan legacy:audit-runtime --include-legacy-tooling
```

Remaining references are intentionally outside the active runtime surface, mostly:

- guarded migration/import services,
- guarded console commands,
- legacy model definitions,
- old migration/archive setup files,
- documentation and thesis/report notes.

## Legacy Models To Remove Later

Remove these only after all admin screens, exports/imports, docs, and migration tools no longer need legacy comparison:

- `app/Models/Admin.php`
- `app/Models/Attribute.php`
- `app/Models/CatePost.php`
- `app/Models/CategoryProductModel.php`
- `app/Models/City.php`
- `app/Models/Comment.php`
- `app/Models/Contact.php`
- `app/Models/Coupon.php`
- `app/Models/Customer.php`
- `app/Models/Feeship.php`
- `app/Models/Gallery.php`
- `app/Models/Icons.php`
- `app/Models/Login.php`
- `app/Models/Order.php`
- `app/Models/OrderDetails.php`
- `app/Models/Product.php`
- `app/Models/ProductAttribute.php`
- `app/Models/Province.php`
- `app/Models/Rating.php`
- `app/Models/RolesUser.php`
- `app/Models/Shipping.php`
- `app/Models/Slider.php`
- `app/Models/Social.php`
- `app/Models/SocialCustomers.php`
- `app/Models/Statistic.php`
- `app/Models/Video.php`
- `app/Models/Visitors.php`
- `app/Models/Wards.php`

## Active `app/Models` That Are Not Legacy Cleanup Targets

Keep these unless a separate refactor moves them into the infrastructure namespace:

- `app/Models/User.php` - Laravel admin/auth user model.
- `app/Models/Post.php` - compatibility wrapper around `Infrastructure\Persistence\Eloquent\Cms\BlogPost`; it does not point to `tbl_posts`.
- `app/Models/News.php` - modern `news` table model.
- `app/Models/Document.php` - modern document model.
- `app/Models/PushNotification.php` - modern notification model.
- `app/Models/Roles.php` - role model used by the current permission/auth layer.

## Guarded Legacy Tooling To Keep Temporarily

- `app/Application/DataMigration/Services/LegacyDataMigrator.php`
- `app/Application/DataMigration/Services/LegacySqlImporter.php`
- `app/Console/Commands/ImportLegacyDatabaseCommand.php`
- `app/Console/Commands/LoadLegacyArchiveDataCommand.php`
- `app/Console/Commands/MigrateLegacyData.php`
- `app/Console/Commands/MigrateLegacyDataCommand.php`
- `app/Console/Commands/SetupLegacyTablesCommand.php`
- `database/setup_legacy_tables.php`
- `database/import_legacy_tables.sql`

These files must stay guarded and must not be part of normal setup/deploy.

Current guard status:

- Production blocks `legacy:load-archive`, `import:legacy-database`, `setup:legacy-tables`, `data:migrate-legacy`, and `migrate:legacy-data`.
- `--env=local` still requires explicit confirm flags for archive import/setup or one-time data migration.
- `admin:normalize-auth` can be checked with `--dry-run`; legacy admin backfill has a separate `--confirm-legacy-admin-backfill` flag.

## Cleanup Gates

Before deleting legacy code or dropping legacy DB tables:

1. Run `php artisan legacy:audit-runtime --fail-on-issues`.
2. Run `rg "tbl_" app routes resources database docs README.md`.
3. Confirm remaining matches are only archive/report references or are deliberately removed in the same change.
4. Confirm no `app/Models/*` legacy model is referenced by active code.
5. Confirm no command can import old dump data without explicit production guard and confirmation flag.
6. Create a fresh DB backup.
7. Drop or archive legacy tables only after the application works against normalized schema.

## Next Feature Slices

Recommended order for deletion/migration verification:

1. Admin legacy screens.
2. Catalog legacy models: product, category, brand, gallery, attributes.
3. CMS/media legacy models: slider, banner, video, contact, blog.
4. Customer/order/coupon legacy models.
5. Location/shipping legacy models.
6. Final archive/doc cleanup.

## Legacy Vs Modern Count Snapshot

Read-only count snapshot from `shopbanhang` after normalized schema migrations:

| Legacy table | Count | Modern table | Count | Cleanup note |
|--------------|-------|--------------|-------|--------------|
| `tbl_brand` | 2 | `brands` | 15 | Modern has data; verify content parity before archive/drop. |
| `tbl_category_product` | 2 | `product_categories` | 4 | Modern has data; verify content parity before archive/drop. |
| `tbl_product` | 2 | `products` | 38 | Modern has data; verify content parity before archive/drop. |
| `tbl_product_attribute` | 0 | `product_variants` | 0 | No legacy rows currently. |
| `tbl_gallery` | 33 | `product_media` | 139 | Modern has data; verify media mapping before archive/drop. |
| `tbl_category_post` | 1 | `blog_categories` | 1 | Counts match; verify content parity. |
| `tbl_posts` | 1 | `blog_posts` | 1 | Counts match; verify content parity. |
| `tbl_coupon` | 1 | `coupons` | 1 | Counts match; verify code/value parity. |
| `tbl_customers` | 1 | `customers` | 7 | Modern has more data; verify whether legacy customer is represented. |
| `tbl_shipping` | 0 | `customer_addresses` | 7 | No legacy shipping rows currently. |
| `tbl_order` | 0 | `orders` | 7 | No legacy order rows currently. |
| `tbl_order_details` | 0 | `order_items` | 7 | No legacy order detail rows currently. |
| `tbl_payment` | 0 | `payments` | 7 | No legacy payment rows currently. |
| `tbl_feeship` | 2 | `shipping_fees` | 2 | Counts match; verify location mapping. |
| `tbl_slider` | 1 | `banners` | 1 | Counts match; verify active banner mapping. |
| `tbl_icons` | 2 | `site_links` | 2 | Counts match; verify link/category mapping. |
| `tbl_infomation` | 1 | `site_contacts` | 1 | Counts match; verify contact fields. |
| `tbl_comment` | 33 | `product_reviews` | 0 | Do not drop until comment/review migration decision is made. |
| `tbl_rating` | 4 | `product_reviews` | 0 | Do not drop until rating/review migration decision is made. |
| `tbl_statistical` | 5 | `analytics_daily` | 0 | Do not drop until analytics migration decision is made. |
| `tbl_videos` | 1 | `videos` | 1 | Counts match; verify video URL/title mapping. |

High-risk holdouts before DB cleanup:

- `tbl_comment`
- `tbl_rating`
- `tbl_statistical`

These tables still contain legacy rows while the mapped modern tables are empty. See `docs/legacy-holdout-migration-plan.md` before writing any mutating migration command or dropping tables.

Read-only command:

```bash
php artisan legacy:audit-holdouts --show-samples
```

Proposed mapping preview:

```bash
php artisan legacy:audit-holdouts --show-proposed --limit=50
```

Guarded migration dry-run:

```bash
php artisan legacy:migrate-holdouts --dry-run
```

Guarded write mode, only after backup and explicit approval:

```bash
php artisan legacy:migrate-holdouts --confirm-legacy-holdout-migration --skip-orphans
```

Current blocker:

- `tbl_rating.rating_id = 38` points to legacy `product_id = 39`, but no matching `tbl_product.product_id = 39` or `products.id = 39` exists. Treat as orphan data unless a manual mapping is supplied.

Holdout migration result:

- `tbl_comment` migrated into `product_reviews`: `33` rows.
- `tbl_rating` migrated into `product_reviews`: `3` rows.
- `tbl_rating.rating_id = 38` intentionally skipped as orphan data.
- `tbl_statistical` migrated into `analytics_daily`: `5` rows.
- Command was run with `--confirm-legacy-holdout-migration --skip-orphans` after backup.
