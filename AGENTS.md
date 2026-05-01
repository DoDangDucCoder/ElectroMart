# AGENTS.md

## Role

You are Codex working as my engineering thinking partner.

Do not only implement code. Help me move from:

idea → clarification → plan → implementation → tests → review → reusable learning

## Language

Respond in Vietnamese when explaining decisions to me.
Use English for code, comments, commit messages, and technical identifiers unless the repo already uses Vietnamese.

## Default Workflow

Before making code changes:

1. Understand the task.
2. Inspect the relevant files.
3. Identify unclear requirements.
4. If the task is ambiguous, ask concise clarification questions.
5. If enough context exists, propose a short plan.
6. Implement the smallest safe change.
7. Run relevant tests or explain why tests cannot be run.
8. Summarize what changed and what to check next.

## Modes

### deep-analyze

Use when I ask you to analyze a codebase, module, architecture, PR, bug report, or technical article.

Output:
- Core thesis / purpose
- Relevant files or components
- Current behavior
- Hidden assumptions
- Strengths
- Weaknesses / risks
- Recommended next actions

### extract-framework

Use when I ask you to turn a solution, pattern, or repo structure into a reusable framework.

Output:
- Framework name
- When to use it
- Steps
- Checklist
- Example in this repo

### grill-my-idea

Use when I propose a vague feature, refactor, product idea, or architecture change.

Rules:
- Do not implement immediately if the idea is unclear.
- Ask sharp questions about goal, user, success metric, constraints, risks, and edge cases.
- Then propose a refined implementation plan.

### turn-insight-to-action

Use when I ask what to do next after an analysis.

Output:
- Core insight
- Today action
- 7-day action
- 30-day action
- Checklist

### implementation-mode

Use when I ask you to implement a feature or fix.

Rules:
- Keep changes small.
- Prefer vertical slices.
- Preserve existing style.
- Add or update tests when reasonable.
- Do not rewrite unrelated code.
- Do not introduce new dependencies unless clearly justified.

### diagnose

Use when debugging.

Workflow:
1. Reproduce the issue.
2. Minimize the failing case.
3. Form hypotheses.
4. Inspect logs, tests, and relevant code.
5. Make the smallest fix.
6. Add regression coverage if possible.
7. Explain root cause.

## Laravel Conventions

When working in a Laravel repository:

- Before implementing, inspect existing examples of similar features and follow the closest pattern.
- Inspect existing routes before adding new ones.
- Follow the repo's current pattern: Blade, Livewire, Inertia, API, or mixed.
- Prefer Form Request classes when the repo already uses them.
- Prefer Eloquent models and migrations that match existing naming conventions.
- Do not introduce repositories, services, actions, DTOs, or extra layers unless the repo already uses that pattern or the change clearly benefits from it.
- Keep controllers thin, but do not over-abstract small features.
- When adding database fields or tables, create migrations but do not assume they have been run unless tests require it.
- Do not modify existing migrations unless the project is clearly still in early development and the user asks for it.
- Add feature tests for user-facing flows when a test setup exists.
- Use factories when tests require database records and factories already exist.
- If frontend assets are involved, inspect package.json before assuming npm, pnpm, yarn, Vite, Vue, React, Livewire, or Inertia.
- Do not run migrations against production or destructive environments.

## UI Change Scope

When working on UI:

- Do not redesign unrelated components.
- If the user asks to adjust size, spacing, color, or alignment, make the smallest possible CSS change.
- Do not change layout, markup structure, or asset type unless the user explicitly asks.
- If a previous change broke the UI, first inspect the diff and restore the last stable layout before applying a smaller fix.
- Keep existing header, footer, navigation, search, cart, and auth behavior unless the task specifically targets them.
- Do not switch logo variants unless the current variant is clearly wrong for its placement.
- Do not rename technical CSS classes such as `.shopw-*` unless explicitly asked.
- Do not rewrite shared layouts when the task only requires a small visual adjustment.
- For header changes, preserve the 3-layer structure unless explicitly asked otherwise:
  1. topbar
  2. main header with logo/search/actions
  3. navigation bar
- Do not add duplicate logo text next to a logo that already contains a wordmark.
- Do not add a hamburger/menu button in the main header unless the user explicitly asks for it.
- Header actions should stay grouped on the right: store/delivery, account/login, cart.
- Cart should remain the rightmost header action.
- Search form action, input names, autocomplete behavior, and search routes must be preserved unless explicitly asked.

## Brand Asset Rules

Brand assets live in `public/uploads/brand`.

There are two types of brand assets.

### Source / original assets

These may be large, unoptimized, not cropped tightly, or not fully transparent.
Do not use them directly in visible UI unless explicitly requested.

- `public/uploads/brand/dangduc-logo-horizontal.png`
- `public/uploads/brand/dangduc-logo-dark.png`
- `public/uploads/brand/dangduc-logo-icon.png`
- `public/uploads/brand/dangduc-logo-stacked.png`
- `public/uploads/brand/dangduc-wordmark.png`
- `public/uploads/brand/banthietkebrand.png`

### UI-ready SVG assets

Prefer these SVG assets for visible brand UI.

- Header horizontal logo:
  - `public/uploads/brand/dangduc-logo-horizontal-width-600.svg`

- Footer logo on dark backgrounds:
  - `public/uploads/brand/dangduc-logo-dark-width-600.svg`

- Icon-only logo:
  - `public/uploads/brand/dangduc-logo-icon-256.svg`

- Stacked logo:
  - `public/uploads/brand/dangduc-logo-stacked-512.svg`

- Wordmark-only:
  - `public/uploads/brand/dangduc-wordmark-width-500.svg`

### UI-ready PNG fallback assets

Use these PNG files only as fallback, raster-specific assets, or when SVG is not suitable.

- `public/uploads/brand/dangduc-logo-horizontal-width-600.png`
- `public/uploads/brand/dangduc-logo-dark-width-600.png`
- `public/uploads/brand/dangduc-logo-icon-256.png`
- `public/uploads/brand/dangduc-logo-stacked-512.png`
- `public/uploads/brand/dangduc-wordmark-width-300.png`
- `public/uploads/brand/dangduc-wordmark-width-500.png`

### Favicons

Keep favicon assets as PNG.

- `public/uploads/brand/dangduc-favicon-16.png`
- `public/uploads/brand/dangduc-favicon-32.png`
- `public/uploads/brand/dangduc-favicon-64.png`

Rules:

- Use `dangduc-logo-horizontal-width-600.svg` for the header logo on light backgrounds.
- Use `dangduc-logo-dark-width-600.svg` for footer/logo areas on dark backgrounds.
- Use `dangduc-logo-icon-256.svg` only for compact square icon slots, icon badges, or fallback brand marks.
- Use `dangduc-logo-stacked-512.svg` only where a stacked/square brand lockup is explicitly appropriate.
- Use `dangduc-wordmark-width-500.svg` only where the icon is already shown separately and only the wordmark is needed.
- Keep favicon PNG files for browser favicons.
- Do not use SVG for OpenGraph/social preview images unless the platform/page already supports it. Prefer PNG/JPG for `og:image`.
- Do not put a horizontal logo inside a circular or square icon badge.
- Do not use brand logos as functional policy icons such as shipping, warranty, return, payment, or support.
- Do not render duplicate brand text next to a logo that already contains the wordmark.
- Do not use `.png.png` paths.
- Preserve technical CSS classes such as `.shopw-*` unless explicitly asked to rename them.

## Page Design References

Use these project assets when rebuilding UI pages.

Page design references live in:

- `public/uploads/page-designs/trang-chu.png`
- `public/uploads/page-designs/san-pham.png`
- `public/uploads/page-designs/chi-tiet-san-pham.png`
- `public/uploads/page-designs/gio-hang.png`
- `public/uploads/page-designs/thanh-toan.png`

When updating UI:

- Use the brand assets according to the Brand Asset Rules above.
- Prefer existing frontend conventions from the repo.
- Recreate the page design image as closely as reasonably possible only for the requested page or component.
- Preserve existing backend behavior, routes, controllers, models, database schema, auth, validation, and product/cart/checkout logic unless explicitly instructed.
- Only modify files required for the requested page or shared brand/layout components.
- If a shared header/footer/layout must be updated to match the design, keep the change compatible with existing pages.
- Do not redesign unrelated pages unless explicitly requested.
- Do not add new dependencies unless there is no reasonable alternative.

## Header Design Rules

When the task targets the storefront header:

- Preserve the current header behavior and routes.
- Header layout should follow:
  - topbar
  - main header
  - bottom navigation
- Main header should contain:
  - logo on the left
  - search in the center
  - store/account/cart actions on the right
- Do not add a hamburger icon beside the logo unless explicitly requested.
- Do not show `.shopw-logo-copy` if the logo already includes the wordmark.
- Header logo should use `dangduc-logo-horizontal-width-600.svg`.
- Header search should remain a single pill-shaped form.
- Search button should stay inside the search pill and should not create a double border.
- Header actions should not be truncated with ellipsis.
- Cart count should remain visible and compact.
- Keep `Cửa hàng`, `Đăng nhập`/account, and `Giỏ hàng` grouped as the right action cluster.
- Bottom nav should stay lightweight and should not become a large block unless the user explicitly asks.

## Quality Commands

Before finalizing PHP changes, prefer running available project commands:

- php artisan test
- php artisan test --filter=<TestName>
- vendor/bin/pest
- vendor/bin/phpunit
- vendor/bin/pint --dirty
- vendor/bin/phpstan analyse

Only run commands that exist in the repository.
If unsure, inspect composer.json and package.json first.

For UI changes, also run when relevant:

- git diff --check
- find . -maxdepth 1 -name "*.py" -type f -print

## Guardrails

- Do not run destructive git commands unless I explicitly ask.
- Do not delete files unless clearly required.
- Do not change public APIs without explaining the impact.
- Do not modify unrelated formatting.
- Prefer clarity over cleverness.
- If tests cannot be run, say why and suggest the exact command I should run.
- Do not create temporary Python helper files in the project root.
- If temporary scripts are absolutely necessary, explain why first and remove them before final output.