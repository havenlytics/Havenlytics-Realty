# Havenlytics Realty — Migrations

Developer documentation for the theme upgrade and migration framework introduced in **1.16.0**.

This framework runs **theme-owned migrations only** — Customizer `theme_mod` copies and similar idempotent settings updates. It does not modify plugin data, posts, or user content.

---

## Overview

| Component | File | Purpose |
|-----------|------|---------|
| Upgrade Manager | `inc/core/class-hvn-realty-upgrade-manager.php` | Tracks installed version, runs pending migrations, logs results |
| Migration callbacks | `inc/core/class-hvn-realty-migrations.php` | Individual migration implementations |
| Boot hook | `functions.php` | Loads classes and calls `HVN_Realty_Upgrade_Manager::boot()` on `after_setup_theme` |

---

## How It Works

1. On `after_setup_theme`, `HVN_Realty_Upgrade_Manager::boot()` compares:
   * **Installed version** — stored in `hvn_realty_version` option
   * **Current version** — `HVN_REALTY_VERSION` constant from `functions.php`

2. If the installed version is older than the current package version, pending migrations are executed in semver order.

3. Each migration runs **once** per site. Success, failure, and skip states are appended to `hvn_realty_migration_log`.

4. After migrations complete, `hvn_realty_version` is updated to the current package version.

### First install

When `hvn_realty_version` is empty (fresh install), all registered migrations up to the current version run once, then the installed version is set. Migrations must be safe on fresh sites (no-op when there is nothing to migrate).

### Re-run protection

A migration is skipped if its version already appears in the log with `status: success`.

---

## Options

| Option | Constant | Description |
|--------|----------|-------------|
| `hvn_realty_version` | `HVN_Realty_Upgrade_Manager::VERSION_OPTION` | Last migrated theme version |
| `hvn_realty_migration_log` | `HVN_Realty_Upgrade_Manager::MIGRATION_LOG_OPTION` | Array of migration run entries (max 50) |

### Log entry shape

```php
array(
    'version'   => '1.16.0',
    'status'    => 'success', // success|failed|skipped
    'timestamp' => 1717862400,
    'message'   => '',
)
```

---

## Public Helpers

```php
hvn_realty_get_installed_version();  // string — stored migration version
hvn_realty_has_migrated( '1.16.0' ); // bool — whether a migration succeeded
hvn_realty_get_migration_log();    // array — log entries
```

---

## Registering a New Migration

1. Add a static method to `HVN_Realty_Migrations` in `inc/core/class-hvn-realty-migrations.php`.

2. Register it in `HVN_Realty_Upgrade_Manager::$migrations` using the **theme release version** as the key:

```php
protected static $migrations = array(
    '1.16.0' => array( 'HVN_Realty_Migrations', 'migrate_1160_locations_to_taxonomies' ),
    '1.20.0' => array( 'HVN_Realty_Migrations', 'migrate_1200_example' ),
);
```

3. Bump `HVN_REALTY_VERSION`, `style.css`, and `readme.txt` to match.

### Migration rules

* **Idempotent** — safe to run multiple times; check before writing.
* **Theme settings only** — use `theme_mod`, theme options, or theme-owned flags.
* **Non-destructive** — do not delete legacy keys unless explicitly required and documented.
* **Return `true`** on success, `false` on failure.
* **Do not** migrate on every page load outside the upgrade manager.

---

## Migration Version History

### 1.16.0 — `migrate_1160_locations_to_taxonomies`

**Introduced with:** Property Taxonomies homepage section (replaces Property Locations UI).

**Purpose:** Copy legacy Property Locations Customizer `theme_mod` values into the new Property Taxonomies keys when the new key is unset.

**Mappings:**

| New key | Legacy key(s) |
|---------|---------------|
| `hvn_realty_home_taxonomies_source` | `hvn_realty_home_locations_source` |
| `hvn_realty_home_taxonomies_title` | `hvn_realty_home_locations_title` |
| `hvn_realty_home_taxonomies_subtitle` | `hvn_realty_home_locations_subtitle` |
| `hvn_realty_home_taxonomies_count` | `hvn_realty_home_locations_count` |
| `hvn_realty_home_show_property_taxonomies` | `hvn_realty_home_show_property_locations`, `hvn_realty_home_show_property_categories` |

**Additional behavior:** If taxonomies source is still unset but legacy location settings exist, sets `hvn_realty_home_taxonomies_source` to `locations`.

**Legacy keys:** Preserved for read-time fallback compatibility. Not deleted by this migration.

---

## What Is Not Handled by Migrations

The following use **one-time launch flags** or **read-time defaults** instead of the migration framework:

| Feature | Mechanism | Flag / location |
|---------|-----------|-----------------|
| Theme launch (homepage, menu) | `inc/setup/theme-launch.php` | `hvn_realty_launch_complete` |
| Footer widget seeding | `inc/setup/theme-footer-widgets.php` | `hvn_realty_footer_widgets_seeded` |
| Property sidebar widgets | `inc/setup/theme-property-sidebar-widgets.php` | `hvn_realty_default_widgets_inserted` |
| Hero search onboarding defaults | `hvn_realty_launch_seed_search_layout_defaults()` | Runs only during first launch |

Do not add launch/onboarding behavior to the migration framework unless it must run on **existing site upgrades**, not only fresh imports.

---

## Debugging

Check installed version:

```php
echo hvn_realty_get_installed_version();
```

Check whether a migration ran:

```php
var_dump( hvn_realty_has_migrated( '1.16.0' ) );
var_dump( hvn_realty_get_migration_log() );
```

In WP-CLI (if available):

```bash
wp option get hvn_realty_version
wp option get hvn_realty_migration_log --format=json
```

---

## Release Checklist (Migrations)

When adding a migration for a new release:

- [ ] Migration method is idempotent
- [ ] Registered in `$migrations` with correct version key
- [ ] Documented in this file under **Migration Version History**
- [ ] Changelog entry in `readme.txt` describes user-facing impact
- [ ] Tested on a site with legacy settings and on a fresh install

See [RELEASE.md](RELEASE.md) for the full release workflow.
