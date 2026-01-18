# Files to Delete to Free Up Disk Space

Since you've reached your disk quota, delete these temporary diagnostic/deployment scripts from `public_html` via **cPanel File Manager** or **SSH**.

## 🗑️ Safe to Delete (Temporary Scripts in public_html)

### Diagnostic Scripts
- `check-*.php` (all check scripts)
  - `check-500-error.php`
  - `check-errors.php`
  - `check-laravel-error.php`
  - `check-laravel.php`
  - `check-routes.php`
  - `check-storage.php`
  - `check-upload-limits.php`

### Test Scripts
- `test-*.php` (all test scripts)
  - `test-composer.php`
  - `test-fileinfo.php`
  - `test-mime-polyfill.php`
  - `test-mime-simple.php`
  - `test-optimizers.php`

### Fix Scripts (keep only if still needed)
- `fix-*.php`
  - `fix-build-assets.php` ✅ **KEEP** (might need for future builds)
  - `fix-php-version.php`
  - `fix-php-version-conflict.php` ⚠️ **KEEP until PHP version is fixed**
  - `fix-storage-link.php` ✅ **KEEP** (might need if symlink breaks)
  - `fix-ziggy-routes.php`

### Deployment Scripts
- `deploy-*.php`
  - `deploy-build.php`
  - `deploy-cpanel.php`
  - `deploy-fix.php`
- `pull-*.php`
  - `pull-git.php`
  - `pull-updates.php`
- `copy-*.php`
  - `copy-controller.php`
  - `copy-models.php`
  - `copy-home-vue.php`
- `sync-*.php`
  - `sync-routes.php`
  - `sync-to-server.php`
- `run-*.php`
  - `run-deployment.php`
  - `run-seeders.php`
- `upload-*.php`
  - `upload-build.php`
  - `upload-controller.php`
- `create-env.php`
- `clear-cache.php`
- `cleanup-temp-files.php`
- `enable-fileinfo.php`
- `update-autoload.php`

### Build Files
- `build.zip` (if exists)

### Backup Files
- Any `.backup.*` files (old backups)
- Any `.old` files

## 📋 How to Delete via cPanel File Manager

1. Log into cPanel
2. Go to **File Manager**
3. Navigate to `public_html`
4. Select the files listed above
5. Click **Delete**
6. Confirm deletion

## 📋 How to Delete via SSH

```bash
cd /home/gotzsafari/public_html

# Delete all check scripts
rm -f check-*.php

# Delete all test scripts
rm -f test-*.php

# Delete deployment scripts
rm -f deploy-*.php pull-*.php copy-*.php sync-*.php run-*.php upload-*.php

# Delete other temporary scripts
rm -f clear-cache.php cleanup-temp-files.php enable-fileinfo.php update-autoload.php create-env.php fix-ziggy-routes.php

# Delete build files
rm -f build.zip

# Delete backup files
find . -name "*.backup.*" -type f -delete
find . -name "*.old" -type f -delete
```

## ⚠️ Files to KEEP

- `index.php` (main entry point)
- `.htaccess` (Apache configuration)
- `.user.ini` (PHP configuration)
- `fix-php-version-conflict.php` (keep until PHP version is fixed)
- `fix-storage-link.php` (keep as backup in case symlink breaks)

## 📊 Estimated Space Savings

These temporary scripts are typically 5-20KB each. Deleting ~30-40 scripts should free up **500KB - 1MB** of space, which should be enough to upload the PHP version fix script.

## ✅ After Deleting Files

1. Upload `fix-php-version-conflict.php` to `public_html`
2. Run: `https://www.gotzsafari.com/fix-php-version-conflict.php`
3. This will fix the PHP version conflict causing the 500 error
