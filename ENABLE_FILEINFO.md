# Enable PHP fileinfo Extension - Fix "Class finfo not found" Error

## Problem
The error `Class "finfo" not found` occurs because the PHP `fileinfo` extension is not enabled. This extension is required by Laravel's Media Library (Spatie) to detect MIME types of uploaded files.

## Solution: Enable fileinfo Extension in cPanel

### Method 1: cPanel PHP Extensions (Recommended)

1. **Log into cPanel**
   - Go to: `https://www.gotzsafari.com/cpanel`

2. **Find "Select PHP Version" or "PHP Extensions"**
   - Look for **"Select PHP Version"** in cPanel
   - Click on it

3. **Select PHP 8.2**
   - Make sure PHP 8.2 is selected

4. **Click "Extensions" or "Extensions" tab**
   - You should see a list of PHP extensions

5. **Enable fileinfo**
   - Look for **"fileinfo"** in the list
   - Check the box next to it to enable it
   - Click **"Save"** or **"Apply"**

6. **Verify**
   - The extension should now be enabled
   - Wait 1-2 minutes for changes to take effect

### Method 2: Alternative - Contact Hosting Provider

If you can't find the Extensions option in cPanel:
1. Contact your hosting provider
2. Ask them to enable the `fileinfo` PHP extension for PHP 8.2
3. They can do this via their server configuration

## Verify Extension is Enabled

After enabling, you can verify by:

1. **Upload a test script** (temporary):
   ```php
   <?php
   // test-fileinfo.php
   if (extension_loaded('fileinfo')) {
       echo "✓ fileinfo extension is enabled";
   } else {
       echo "✗ fileinfo extension is NOT enabled";
   }
   ?>
   ```

2. **Access it via browser**: `https://www.gotzsafari.com/test-fileinfo.php`

3. **Delete the test file** after checking

## What This Fixes

- ✅ Admin panel will load without 500 errors
- ✅ Media Library can detect MIME types
- ✅ File uploads will work correctly
- ✅ Image URLs will generate properly

## After Enabling

1. Clear Laravel cache:
   ```bash
   php artisan cache:clear
   php artisan config:clear
   ```

2. Test the admin panel - it should now load without errors

