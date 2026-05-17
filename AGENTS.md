# AGENTS.md

Guidelines for Codex when working in this repository.

This project is a Laravel e-commerce website. Follow these rules to avoid unnecessary changes, over-engineering, destructive commands, and unrelated refactors.

## Project Overview

This project is a Laravel 11 e-commerce website for selling electronic devices.

It is built for a graduation project / local demo environment, not a production-ready deployment.

The system includes:

- Storefront for customers: home page, product listing, product detail, search, cart, checkout COD, promotions, wishlist, comparison, blog, video, and contact pages.
- Customer account area: registration, login, social login, profile management, addresses, order history, order tracking, and recently viewed products.
- Admin dashboard: product management, categories, brands, orders, customers, shipping, payments, coupons, content, reviews, warranty, returns, inventory, roles, permissions, admin users, settings, and chat inbox.
- RESTful API under `/api/v1` for public, customer, and admin use cases.
- API authentication with Laravel Sanctum.
- Admin authorization with spatie/laravel-permission.
- Blade-based frontend with Vite, Tailwind CSS, Vanilla CSS/JS, and some Vue 3 components.
- Database-backed features using MariaDB/MySQL, migrations, seeders, queues, sessions, and cache.

The codebase should be treated as an existing Laravel e-commerce system. Changes should be small, safe, and focused on the requested task.

## 1. Think Before Coding

Before editing any file:

- Read the related route, controller, model, Blade view, CSS, and JavaScript files first.
- State the likely root cause before making changes.
- If there are multiple possible causes, inspect the code instead of guessing.
- Prefer the simplest fix that directly solves the reported issue.
- Do not ask unnecessary clarification questions when the issue can be investigated from the codebase.

For bugs, follow this flow:

1. Reproduce or identify the broken flow.
2. Locate the related route/controller/view/script.
3. Explain the root cause.
4. Apply the smallest safe fix.
5. Verify the result.

## 2. Simplicity First

Use the minimum code required to solve the task.

Do not add:

- New features that were not requested.
- New packages unless absolutely necessary.
- New abstractions for one-time logic.
- Complex error handling for unlikely scenarios.
- Large rewrites when a small patch is enough.

If a solution becomes too large, stop and simplify it.

## 3. Surgical Changes

Only edit files directly related to the user request.

Do not:

- Refactor unrelated code.
- Reformat unrelated files.
- Rename variables or functions unless required.
- Change UI design when the task is only about fixing logic.
- Change business rules unless explicitly requested.
- Delete old code unless your own change made it unused.

Every changed line must be traceable to the task.

## 4. Laravel Project Rules

This is a Laravel project using Blade, CSS, JavaScript, and database-backed features.

When working with frontend bugs:

- Check Blade files in `resources/views`.
- Check CSS files in `public/frontend/css`.
- Check JavaScript files loaded by the current page.
- Check shared partials before editing page-specific files.
- Avoid changing global CSS unless the issue is truly global.

When working with cart, checkout, product, account, or admin issues:

- Check `routes/web.php`.
- Check the related controller.
- Check related model if the issue involves database data.
- Check related Blade views.
- Check JavaScript/AJAX flow if the issue happens without page reload.
- Check session/database logic if the issue involves cart count, order, user, or checkout state.

When working with admin layout issues:

- Check admin layout Blade first.
- Check sidebar/header wrapper structure.
- Check CSS height, flex, overflow, position, and container rules.
- Do not change menu items, colors, fonts, or permissions unless requested.

## 5. Verification Commands

Use relevant commands depending on the change.

Common Laravel commands:

```bash
php artisan route:list
php artisan optimize:clear
php artisan view:clear
php artisan config:clear
php artisan cache:clear
php artisan test
npm run build
```

Do not run all commands automatically. Choose the smallest verification that matches the change.

Examples:

- For route/controller changes, check the route and relevant page flow.
- For Blade-only changes, clear views if needed and verify the affected page.
- For CSS/JavaScript changes, verify the affected screen and avoid rebuilding unless assets require it.
- For cart/checkout/order logic, test the exact affected flow.
- For admin layout fixes, verify the affected admin page and nearby layout behavior.

## 6. Safety Rules

Do not run destructive commands unless explicitly requested.

Avoid:

- `php artisan migrate:fresh`
- `php artisan migrate:refresh`
- `php artisan db:wipe`
- Deleting uploaded files
- Resetting git history
- Removing database records
- Changing `.env` values
- Installing new packages without approval
- Changing production-related configuration without approval

If a destructive command seems necessary, explain why and ask first.

## 7. Database and Migration Rules

Before changing database structure:

- Inspect existing migrations, models, relationships, and seeders.
- Do not create a new migration if the issue can be solved in existing logic.
- Do not modify existing migrations unless the project is clearly still in local development and the user asked for it.
- Prefer safe, forward-only migrations for existing projects.
- Do not assume the database can be reset.

When working with Eloquent:

- Check fillable/guarded fields.
- Check relationships before writing custom queries.
- Avoid N+1 queries when touching list pages.
- Do not change table or column names unless explicitly requested.

## 8. Output Style

When responding after a change, Codex should include:

1. What was inspected.
2. The root cause.
3. What was changed.
4. How it was verified.
5. Any remaining risk or follow-up.

Keep the explanation short and specific.

## 9. Final Rule

Be conservative.

Codex should act like a careful maintainer of an existing Laravel e-commerce project, not like someone rewriting the project from scratch.

## Codex Working Mode

When using Codex CLI:

- Before editing, summarize the files inspected and the likely root cause.
- Prefer `rg`, `php artisan route:list`, and targeted file reads before changing code.
- For frontend Blade/CSS/JS issues, inspect the loaded layout/partials before editing.
- For database-related issues, do not run destructive migration commands.
- If verification needs database data that is unavailable, explain what could not be verified.
- Keep the final response in Vietnamese unless the user asks otherwise.

## 10. Security Skills

### vibe-security-scanner

A specialized skill for scanning Laravel/Vibe code for 20 common security vulnerabilities:

1. Hardcoded Secrets - API keys, database passwords, tokens in code
2. SQL Injection - String concatenation in queries
3. XSS (Cross-Site Scripting) - Unescaped user input in HTML/Blade
4. IDOR (Insecure Direct Object Reference) - ID manipulation without auth checks
5. Slopsquatting - Typosquatted or non-existent library dependencies
6. Brute Force - Missing rate limiting on auth endpoints
7. Mass Assignment - Unguarded model properties
8. Insecure Deserialization - Unsafe pickle/YAML/unserialize() usage
9. SSRF (Server-Side Request Forgery) - User-supplied URLs without validation
10. Path Traversal - File operations with `../` sequences
11. CSRF (Cross-Site Request Forgery) - Missing CSRF token validation
12. Broken Access Control - Missing backend authorization checks
13. Weak Password Hashing - MD5/SHA1/plaintext password storage
14. JWT None Algorithm - JWT with `algorithm: "none"` or weak secrets
15. CORS Misconfiguration - Overly permissive Access-Control-Allow-Origin
16. Unrestricted File Upload - Missing file type/size validation
17. Verbose Error Messages - Stack traces in production responses
18. Missing Rate Limit - No request throttling on endpoints
19. Race Condition - Concurrent operations without locking
20. Outdated Dependency - Packages with known CVEs

**Usage:**
```
Use the vibe-security-scanner skill to scan Laravel11_Shop_Website_Full for all 20 vulnerabilities
```

**Output:** Identifies vulnerabilities by severity (Critical/High/Medium/Low) with file paths, line numbers, explanations, and suggested fixes.