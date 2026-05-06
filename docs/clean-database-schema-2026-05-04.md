# Clean Database Schema - 2026-05-04

This file records the clean schema after resetting `shopbanhang` with:

```bash
php artisan migrate:fresh --seed --force
```

Backup before reset:

- `storage/app/backups/shopbanhang_pre_fresh_reset_20260504_004011.sql`

## Verification

- Pending migrations: none.
- Runtime legacy audit: passed.
- Legacy `tbl_*` tables: `0`.

## Seeded Data Counts

| Table | Rows |
|-------|------|
| `users` | 2 |
| `brands` | 5 |
| `product_categories` | 10 |
| `products` | 5 |
| `customers` | 3 |
| `customer_addresses` | 2 |
| `coupons` | 4 |
| `settings` | 15 |
| `blog_categories` | 4 |
| `blog_posts` | 4 |
| `orders` | 0 |
| `product_reviews` | 0 |
| `analytics_daily` | 0 |
| `customer_payment_methods` | 0 |
| `vat_invoices` | 0 |
| `order_returns` | 0 |

## Tables Required By Current Migrations

### Laravel/System

- `migrations`
- `users`
- `password_reset_tokens`
- `sessions`
- `cache`
- `cache_locks`
- `jobs`
- `job_batches`
- `failed_jobs`
- `personal_access_tokens`

### Admin/Auth/Permissions

- `permissions`
- `roles`
- `model_has_permissions`
- `model_has_roles`
- `role_has_permissions`

### Catalog

- `brands`
- `product_categories`
- `products`
- `product_category_product`
- `product_media`
- `product_variants`
- `attribute_groups`
- `attributes`
- `attribute_values`
- `product_variant_attribute_value`
- `product_reviews`
- `product_views`
- `product_search_queries`
- `product_recommendation_impressions`
- `search_indexes`

### Customer/Checkout/Order

- `customers`
- `customer_addresses`
- `customer_payment_methods`
- `locations`
- `shipping_fees`
- `carts`
- `cart_items`
- `orders`
- `order_items`
- `order_shipping`
- `order_returns`
- `payments`
- `payment_transactions`
- `vat_invoices`

### Marketing/Loyalty/Automation

- `coupons`
- `coupon_redemptions`
- `customer_segments`
- `segment_filters`
- `segment_members`
- `marketing_campaigns`
- `campaign_channels`
- `campaign_messages`
- `campaign_metrics`
- `loyalty_tiers`
- `loyalty_rules`
- `loyalty_transactions`
- `automation_rules`
- `automation_logs`
- `notification_tokens`
- `push_notifications`

### CMS/Content

- `blog_categories`
- `blog_posts`
- `banners`
- `site_links`
- `site_contacts`
- `documents`
- `videos`
- `news`
- `settings`

### Analytics/Monitoring/Support

- `analytics_daily`
- `analytics_events`
- `page_views`
- `conversion_tracking`
- `user_sessions`
- `metrics`
- `alerts`
- `performance_indexes`
- `chat_conversations`
- `chat_messages`

### Data Migration Tooling

- `data_migrations`
- `migration_mappings`

These tables are part of the current migration set. They are not legacy `tbl_*` tables.
