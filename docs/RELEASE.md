# Havenlytics Realty Release Guide

Developer documentation for preparing a WordPress.org theme release. This checklist helps maintain version consistency and packaging quality across releases.

---

## Before Every Release

### 1. Update Version Numbers

Verify the same version exists in:

* `style.css` — `Version:` header
* `functions.php` — `HVN_REALTY_VERSION` constant
* `readme.txt` — `Stable tag:`
* `readme.txt` — latest `== Changelog ==` entry

Example:

```
1.19.0
```

All four values must match exactly before building the release ZIP.

---

### 2. Update Changelog

Add release notes to:

`readme.txt`

Verify:

* Version matches release version
* `== Upgrade Notice ==` updated for the new release
* New features documented
* Bug fixes documented

The latest changelog entry and `Stable tag` must use the same version number.

---

### 3. Verify Screenshot

Confirm:

`screenshot.png`

exists in the theme root.

WordPress.org requires a screenshot asset in the theme package. The release manifest verifier also checks for this file.

---

### 4. Run Release Manifest Verification

Open PowerShell from theme root.

Run:

```powershell
php bin/verify-release-manifest.php
```

Expected result:

```
Required files: XXX
Present: XXX
Missing: 0

OK — all manifest files are present.
```

Do not build a release ZIP if any files are missing.

---

### 5. Build Release ZIP

Run:

```powershell
.\bin\build-release.ps1
```

Expected result:

```
Release package created successfully.
```

Output:

```
havenlytics-realty-x.x.x.zip
```

The build script reads the version from `style.css` and runs manifest verification automatically before packaging.

---

### 6. Test ZIP

Install the generated ZIP on a fresh WordPress site.

Verify:

* Theme activates
* Homepage loads
* Customizer loads
* No PHP warnings
* No missing assets
* Plugin integration works (with Havenlytics plugin active)

---

### 7. Theme Check

Run the [Theme Check](https://wordpress.org/plugins/theme-check/) plugin.

Verify:

* No REQUIRED errors
* No fatal issues
* No packaging issues

---

### 8. Final WordPress.org Review

Verify:

* Version consistency (`style.css`, `functions.php`, `readme.txt`)
* Screenshot exists
* Readme updated
* Changelog updated
* Upgrade Notice updated
* ZIP built successfully

---

## PowerShell Commands

### Verify Release Manifest

```powershell
php bin/verify-release-manifest.php
```

### Build Release ZIP

```powershell
.\bin\build-release.ps1
```

Optional custom output path:

```powershell
.\bin\build-release.ps1 -OutputPath C:\releases\havenlytics-realty-1.19.0.zip
```

---

## Files Excluded From Release ZIP

These development files should remain in the repository but are excluded from release packages (via `.distignore`):

* `bin/`
* `docs/`
* `.github/`
* `.vscode/`
* `tests/`
* `node_modules/`
* Other dev-only paths listed in `.distignore`

`readme.txt` is always included — WordPress.org requires it in the theme package.

---

## Release Checklist

- [ ] Version updated
- [ ] Changelog updated
- [ ] Upgrade Notice updated
- [ ] Screenshot verified
- [ ] Manifest verification passed
- [ ] ZIP built successfully
- [ ] Fresh install tested
- [ ] Theme Check passed
- [ ] Ready for WordPress.org upload

---

## Related Documentation

* [MIGRATIONS.md](MIGRATIONS.md) — theme upgrade manager and migration framework
