# Repo Cleanup Status - 2026-03-31

## Muc tieu
- Tach `git status` hien tai thanh cac nhom de review va commit an toan hon.
- Phan biet ro:
  - code that / test that
  - scaffold da don
  - generated/tooling
  - runtime uploads
  - migration/archive can review rieng

## Full Test Status
- Lenh da chay:
  - `php artisan test`
- Ket qua:
  - `154 passed / 1284 assertions`

## Tong quan theo nhom
- `core_code`: 137
- `scaffold_cleanup_deletions`: 230
- `generated_or_tooling`: 8969
- `runtime_uploads`: 45
- `docs_and_reports`: 5
- `tests_real`: 29
- `legacy_archive_or_disabled`: 1
- `other_review`: 123

## Tong quan theo root path
- `vendor`: 8917
- `app`: 282
- `resources`: 140
- `tests`: 101
- `public`: 64
- `database`: 15
- `config`: 6
- `docs`: 3
- `routes`: 3
- `composer.lock`: 1
- `bootstrap`: 1
- `.phpunit.result.cache`: 1
- `phpunit.xml`: 1
- `.gitignore`: 1
- `.env.example`: 1
- `composer.json`: 1
- `.env`: 1

## Nhom 1: Code that dang active
Day la nhom uu tien cao nhat neu can chot commit chuc nang/refactor.

Vi du:
- `app/Application/AI/Services/ChatbotService.php`
- `app/Application/Catalog/Services/ProductService.php`
- `app/Application/Checkout/Services/CheckoutService.php`
- `app/Http/Middleware/IPWhitelistMiddleware.php`
- `config/services.php`
- `routes/api.php`
- `routes/web/admin.php`
- `routes/web/frontend.php`

Khuyen nghi:
- Commit cung voi `tests_real`.
- Day la nhom code co gia tri runtime thuc.

## Nhom 2: Test that va regression that
Day la nhom nen di cung voi code that.

Vi du:
- `tests/Feature/AdminApiSecurityTest.php`
- `tests/Feature/ChatbotFeatureTest.php`
- `tests/Feature/HomepageTest.php`
- `tests/Feature/StorefrontLayoutShellTest.php`
- `tests/Unit/PolicyDefaultsTest.php`
- `tests/Unit/ProductPricingServiceTest.php`

Khuyen nghi:
- Stage cung `core_code`.
- Day la nhom can giu de khoa regression sau cleanup.

## Nhom 3: Scaffold cleanup deletions
Day la nhung file placeholder/TODO/command sinh code da bi loai khoi repo active.

Vi du:
- `app/Application/AI/Models/*`
- `app/Application/AI/Recommendations/*`
- `app/Application/Automation/*`
- `app/Application/PWA/*`
- `app/Http/Controllers/Mobile/*`
- `app/Http/Controllers/Analytics/*`
- `app/Console/Commands/Create*`
- `tests/Integration/*`
- `tests/Performance/*`
- `tests/Utilities/*`

Khuyen nghi:
- Commit rieng thanh mot batch `cleanup scaffold dead code`.
- De doc lich su Git va rollback de hon.

## Nhom 4: Generated/tooling
Nhom nay dang chiem gan nhu toan bo do on cua `git status`.

Vi du:
- `vendor/**`
- `public/build/**`
- `.env`
- `.phpunit.result.cache`
- `public/component-examples/**`
- `public/ui-demo.html`
- `resources/js/accessibility/**`
- `resources/js/features/**`
- placeholder CSS/JS/Vue da xoa

Trang thai mong muon:
- Khong coi day la source of truth cua repo.
- `vendor`, `.env`, `.phpunit.result.cache`, `public/build` nen tiep tuc o trang thai untracked/ignored.

Khuyen nghi:
- Khong stage lai tru khi co ly do dac biet.
- De `.gitignore` tiep tuc che nhom nay.

## Nhom 5: Runtime uploads
Day la du lieu runtime, khong nen gop chung vao commit cleanup code neu chua co quy uoc ro.

Vi du:
- `public/uploads/attribute/*.png`
- `public/uploads/product/no-image.svg`

Khuyen nghi:
- Review rieng.
- Neu day la fixture can thiet cho test/demo, commit theo batch tai nguyen mau.
- Neu day la upload runtime local, de ngoai commit.

## Nhom 6: Docs va bao cao
Vi du:
- `docs/data-migration-plan.md`
- `docs/dead-code-audit-2026-03-22.md`
- `docs/technical-handover-2026-03-23.md`
- `.env.example`
- `.gitignore`

Khuyen nghi:
- Co the commit chung voi batch cleanup hoac tach rieng thanh `docs + repo hygiene`.

## Nhom 7: Legacy archive / disabled
Vi du:
- `database/migrations_disabled/`

Khuyen nghi:
- Giu rieng de the hien ro migration active va migration archive.

## Nhom 8: Other review
Day la nhom chua nen commit gom trong mot lan vi van con lai hon hop.

Vi du:
- xoa/move migration legacy trong `database/migrations`
- mot so file `public/frontend/css/*` cu
- mot so view/admin resource legacy da duoc refactor

Khuyen nghi:
- Tach tiep thanh 2 batch nho:
  - `database + migration hygiene`
  - `legacy asset/view cleanup`

## Split cua `other_review`

### A. Database / migration hygiene
- So luong: `15`

Bao gom:
- legacy migration dang bi xoa khoi active path:
  - `database/migrations/2025_01_15_000000_create_catalog_tables.php`
  - `database/migrations/2025_01_15_000100_create_legacy_tables.php`
  - `database/migrations/2025_01_15_000200_create_legacy_core_tables.php`
  - `database/migrations/2025_10_24_184153_create_analytics_events_table.php`
- migration moi dang active:
  - `database/migrations/2025_10_23_001280_create_carts_table.php`
  - `database/migrations/2025_10_23_001290_create_cart_items_table.php`
  - `database/migrations/2026_03_18_000000_create_personal_access_tokens_table.php`
  - `database/migrations/2026_03_24_000100_add_sale_metadata_to_products_and_order_items_tables.php`
  - `database/migrations/2026_03_24_001100_add_search_and_recommendation_indexes_to_product_views_table.php`
  - `database/migrations/2026_03_24_001200_create_product_discovery_analytics_tables.php`
  - `database/migrations/2026_03_24_001300_create_chat_conversations_and_messages_tables.php`
  - `database/migrations/2026_03_24_001400_add_status_fields_to_chat_conversations_table.php`
  - `database/migrations/2026_03_26_000100_add_campaign_fields_to_tbl_slider.php`
- seed can review cung nhom nay:
  - `database/seeders/AdminSeeder.php`
- archive diem den cua 4 migration cu:
  - `database/migrations_disabled/`
  - chi tiet ly do archive duoc ghi o `database/migrations_disabled/README.md`

Khuyen nghi commit:
- `database/migrations/**`
- `database/seeders/AdminSeeder.php`
- `database/migrations_disabled/**`

Luu y:
- `AdminSeeder` van la file active, khong phai dead scaffold.
- 4 migration legacy o active path khong nen commit duoi dang `delete only`.
- Can stage cung ban archive trong `database/migrations_disabled` de lich su Git doc duoc y nghia `retire + archive`.

Ten commit goi y:
- `cleanup(database): normalize active migrations and archive legacy schema`

### B. Legacy asset / view cleanup
- So luong: `107`

Bao gom:
- asset frontend/admin da doi shell:
  - `public/frontend/css/main.css`
  - `public/frontend/css/shopw-theme.css`
  - `public/backend/css/admin-refresh.css`
  - `public/frontend/js/performance-optimizer.js`
  - `public/sw.js`
- xoa asset cu:
  - `public/frontend/css/dark-light-mode.css`
  - `public/frontend/css/dark-mode-components.css`
  - `public/frontend/js/dark-light-mode.js`
  - `public/ui-design-showcase.html`
- vue component can review cung nhom nay:
  - `resources/js/components/ProductCard.vue`
  - `resources/js/components/ProductListItem.vue`
- view storefront/admin da refactor:
  - `resources/views/home.blade.php`
  - `resources/views/layout.blade.php`
  - `resources/views/pages/include/slider.blade.php`
  - `resources/views/pages/sanpham/search.blade.php`
  - `resources/views/pages/sanpham/show_details.blade.php`
  - `resources/views/pages/video/video.blade.php`
  - `resources/views/admin/dashboard.blade.php`
  - `resources/views/admin/add_product.blade.php`
  - `resources/views/admin/all_product.blade.php`
  - `resources/views/admin/chat_inbox/`
  - `resources/views/admin/product_profit/`
  - `resources/views/partials/`

Da retire theming/demo legacy trong batch nay:
- `app/Console/Commands/ThemeManagementCommand.php`
- `public/dark-mode-demo.html`
- `public/modern-upgrade-demo.html`

Da xac nhan:
- khong con tham chieu runtime toi `components.theme-toggle`
- khong con tham chieu toi `dark-mode-demo`
- khong con tham chieu toi `modern-upgrade-demo`

Khuyen nghi commit:
- `public/frontend/**`
- `public/backend/**`
- `resources/views/**`
- `resources/js/components/ProductCard.vue`
- `resources/js/components/ProductListItem.vue`

Ten commit goi y:
- `refactor(ui): consolidate legacy views and storefront/admin assets`

## Thu tu stage/commit de xuat
1. `core_code + tests_real`
2. `docs_and_reports`
3. `scaffold_cleanup_deletions`
4. `database + migration hygiene`
5. `legacy asset/view cleanup`
6. `runtime_uploads` chi commit neu xac dinh la fixture can giu
7. Khong stage lai `generated_or_tooling`

## Ket luan
- Repo da o trang thai tot hon ro rang:
  - khong con test xanh gia trong suite dang chay
  - khong con `TODO/FIXME/assertTrue(true)` trong `app/resources/config/routes/tests/Feature/tests/Unit`
  - full suite van xanh
- Viec con lai chu yeu la tach commit cho de doc lich su, khong con la sua loi runtime lon.
