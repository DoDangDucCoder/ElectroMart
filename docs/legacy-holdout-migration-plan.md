# Legacy Holdout Migration Plan

This plan covers legacy tables that still contain rows while their likely modern targets are empty.

## Current Holdouts

| Legacy table | Rows | Modern target | Rows | Decision |
|--------------|------|---------------|------|----------|
| `tbl_comment` | 33 | `product_reviews` | 0 | Needs explicit migration/archive decision. |
| `tbl_rating` | 4 | `product_reviews` | 0 | Needs explicit migration/archive decision. |
| `tbl_statistical` | 5 | `analytics_daily` | 0 | Needs explicit migration/archive decision. |

Do not drop these tables until this file is resolved.

## Read-Only Audit Command

Use this command to check mapping readiness without mutating data:

```bash
php artisan legacy:audit-holdouts
```

Use this command as a blocking cleanup gate:

```bash
php artisan legacy:audit-holdouts --fail-on-issues
```

Use this command to inspect blocked sample rows:

```bash
php artisan legacy:audit-holdouts --show-samples
```

Use this command to inspect proposed target rows without writing them:

```bash
php artisan legacy:audit-holdouts --show-proposed --limit=50
```

Current result:

| Legacy table | Legacy rows | Ready rows | Blocked rows | Primary blocker |
|--------------|-------------|------------|--------------|-----------------|
| `tbl_comment` | 33 | 33 | 0 | none |
| `tbl_rating` | 4 | 3 | 1 | orphan legacy `product_id` |
| `tbl_statistical` | 5 | 5 | 0 | none |

Migration status:

- Backup before write: `storage/app/backups/shopbanhang_pre_holdout_migration_20260504_003023.sql`.
- Write command executed outside production with explicit confirmation:
  `php artisan legacy:migrate-holdouts --env=local --confirm-legacy-holdout-migration --skip-orphans`
- Idempotency verified by running the same command a second time; counts did not increase.
- Migrated rows:
  - `product_reviews`: 36 total legacy-derived rows (`33` comments + `3` ratings).
  - `analytics_daily`: 5 legacy-derived rows.
- Skipped orphan:
  - `tbl_rating.rating_id = 38`, `product_id = 39`.

Blocked sample:

| Legacy table | Legacy ID | Legacy product ID | Legacy product | Modern product | Blocker |
|--------------|-----------|-------------------|----------------|----------------|---------|
| `tbl_rating` | 38 | 39 | missing | missing | orphan legacy `product_id` |

Current proposed mapping summary:

- `tbl_comment`: creates approved `product_reviews` rows with `rating = 5`, `author_name = comment_name`, `content = comment`, and `meta.legacy` in the eventual mutating command.
- `tbl_rating`: creates rating-only approved `product_reviews` rows with content such as `Legacy rating import`; skips orphan `rating_id = 38` unless manual mapping is supplied.
- `tbl_statistical`: creates/updates `analytics_daily` rows keyed by `order_date`.

## Proposed Mapping

### `tbl_comment` -> `product_reviews`

Suggested mapping:

- `comment_id` -> `product_reviews.id` only if preserving legacy IDs is explicitly desired.
- `comment_product_id` -> `product_reviews.product_id` after mapping legacy product IDs to modern product IDs.
- `comment_name` -> `author_name`.
- `comment` -> `content`.
- `comment_parent_comment` -> `parent_id` after mapping comment IDs.
- `comment_status = 0` -> `status = approved`, `is_public = true`, `approved_at = comment_date`.
- other statuses -> `status = pending` or `rejected` after manual confirmation.
- `comment_date` -> `created_at`/`updated_at` when parseable.
- original legacy row should be stored in `meta.legacy`.

Risk:

- Product ID mapping may not be 1:1 if modern products were seeded independently instead of migrated from legacy.
- Parent comment mapping requires two passes.

### `tbl_rating` -> `product_reviews`

Suggested options:

- Option A: merge ratings into matching migrated comments when product/customer identity can be inferred.
- Option B: create rating-only reviews with generated content such as `Legacy rating import`.
- Option C: archive only and do not migrate, if ratings are not user-visible or cannot be attributed safely.

Risk:

- `tbl_rating` has only `product_id` and `rating`; no customer/comment identity is available.
- Current blocked row `rating_id = 38` points to `product_id = 39`, which exists in neither `tbl_product` nor `products`. Treat it as orphan data unless a manual product mapping is supplied.

### `tbl_statistical` -> `analytics_daily`

Suggested mapping:

- `order_date` -> `date`.
- `total_order` -> `orders_count`.
- `sales` -> `sales`.
- `profit` -> `profit`.
- `quantity` -> `items_sold`.
- `visits` cannot be inferred unless another source is used.
- original legacy row should be stored in `meta.legacy`.

Risk:

- The admin dashboard now reads `analytics_daily`, so migrating these rows could make historical dashboard charts visible again.
- Confirm date format and currency assumptions before import.

## Safe Next Step

The guarded migration command now exists:

```bash
php artisan legacy:migrate-holdouts --dry-run
```

Write mode is intentionally guarded:

```bash
php artisan legacy:migrate-holdouts --confirm-legacy-holdout-migration --skip-orphans
```

Safety behavior:

- `--dry-run` shows the plan and writes no data.
- Write mode is disabled in `production`.
- Write mode requires `--confirm-legacy-holdout-migration`.
- If orphan rows exist, write mode also requires `--skip-orphans`.
- Current orphan: `tbl_rating.rating_id = 38` with `product_id = 39`.

Before running write mode:

1. Export a fresh DB backup.
2. Produce a dry-run report that shows every legacy row and proposed target row.
3. Confirm product ID mapping for `tbl_comment`/`tbl_rating`.
4. Decide whether to skip orphan `tbl_rating.rating_id = 38` or supply a manual mapping in a separate change.
5. Run write mode only outside production, then verify `product_reviews` and `analytics_daily` counts.
