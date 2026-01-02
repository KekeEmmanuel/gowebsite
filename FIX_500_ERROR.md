# Fix 500 Internal Server Error

## Problem
After configuring upload limits, you're getting a 500 Internal Server Error.

## Cause
The `.htaccess` file contains `php_value` directives which many shared hosting providers disable for security reasons. Since `.user.ini` is already working, these directives are unnecessary and may cause conflicts.

## Solution

### Step 1: Update .htaccess File

The `.htaccess` file has been updated to remove the `php_value` directives. Upload the updated file to your server:

**File to upload:** `backend/public/.htaccess`
**Upload to:** `/home/gotzsafari/public_html/.htaccess`

### Step 2: Verify .user.ini is Working

The `.user.ini` file is handling all PHP configuration, so the `.htaccess` doesn't need PHP directives.

### Step 3: Test

1. Upload the updated `.htaccess` file
2. Clear your browser cache
3. Test the website
4. Try uploading an image in the admin panel

## What Changed

**Removed from .htaccess:**
```apache
php_value upload_max_filesize 50M
php_value post_max_size 60M
php_value max_execution_time 300
php_value max_input_time 300
php_value memory_limit 256M
```

**Why:**
- `.user.ini` is already handling these settings
- Many hosts disable `php_value` in `.htaccess`
- Having both can cause conflicts and 500 errors

## Current Configuration

✅ **`.user.ini`** - Handles all PHP upload limits (50M upload, 60M POST)
✅ **`.htaccess`** - Only handles URL rewriting and PHP version

## If Error Persists

1. Check Laravel logs: `storage/logs/laravel.log`
2. Check PHP error logs in cPanel
3. Verify `.htaccess` syntax is correct
4. Make sure `.user.ini` is still in `public_html`

