Archived migrations live in this directory.

Rules:
- Active migrations belong in `database/migrations`.
- Disabled or replaced legacy migrations belong in `database/migrations_disabled`.
- Laravel does not load this directory during normal `migrate` runs.

Keep a migration here only when it has been intentionally retired from the
active path and is still useful as historical reference.

Archived in this cleanup batch:
- `2025_01_15_000000_create_catalog_tables.php`
  Reason: retired legacy catalog bootstrap, replaced by the normalized `2025_10_23_*` catalog chain.
- `2025_01_15_000100_create_legacy_tables.php`
  Reason: retired legacy catch-all schema bootstrap, no longer part of the active migration path.
- `2025_01_15_000200_create_legacy_core_tables.php`
  Reason: retired legacy core bootstrap, superseded by the normalized active schema set.
- `2025_10_24_184153_create_analytics_events_table.php`
  Reason: duplicate/retired analytics event bootstrap. The active analytics path keeps the `2025_10_23_002700_*` chain instead.

Expected review/stage behavior:
- stage the deleted files from `database/migrations`
- stage the matching archived copies in `database/migrations_disabled`
- keep both changes in the same commit so the history reads as a move/retirement, not an unexplained deletion
