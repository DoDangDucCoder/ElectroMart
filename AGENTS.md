# AGENTS.md

Guidelines for Codex when working in this repository.

This project is a Laravel e-commerce website. Follow these rules to avoid unnecessary changes, over-engineering, and unrelated refactors.

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