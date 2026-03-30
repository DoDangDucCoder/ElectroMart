# Git Pathspec Helpers

Use these files with Git pathspec staging:

- `git add --pathspec-from-file=docs/git-pathspecs/commit-1-core-and-tests.txt`
- `git add --pathspec-from-file=docs/git-pathspecs/commit-2-database-hygiene.txt`
- `git add --pathspec-from-file=docs/git-pathspecs/commit-3-legacy-assets-and-views.txt`

Suggested commit messages:

1. `refactor(core): stabilize runtime services and real regression tests`
2. `cleanup(database): normalize active migrations and archive legacy schema`
3. `refactor(ui): consolidate legacy views and storefront/admin assets`

Notes:

- These helpers intentionally exclude `vendor`, `public/build`, `.env`, `.phpunit.result.cache`, and `public/uploads/**`.
- The broad scaffold deletion batch is still separate from these 3 commit helpers.
