# Contact Hosting Provider - Enable fileinfo Extension

## Problem
The `fileinfo` PHP extension is not enabled, causing the error:
```
Class "finfo" not found
```

## Solution
You need to contact your hosting provider to enable the `fileinfo` extension for PHP 8.1.

## What to Tell Your Hosting Provider

**Subject:** Request to Enable PHP fileinfo Extension

**Message:**
```
Hello,

I need the PHP fileinfo extension enabled for my account (gotzsafari.com).

Current PHP Version: PHP 8.1 (ea-php81)
Extension Needed: fileinfo

This extension is required by Laravel's Media Library to detect MIME types of uploaded files.

Please enable the fileinfo extension for PHP 8.1 on my account.

Thank you!
```

## Alternative: Enable via SSH (if you have access)

If you have SSH access, you can try enabling it yourself:

1. SSH into your server
2. Navigate to PHP extensions directory (usually `/opt/cpanel/ea-php81/root/usr/lib64/php/modules/`)
3. Check if `fileinfo.so` exists
4. Enable it in PHP configuration

However, most shared hosting providers require you to contact them to enable extensions.

## After fileinfo is Enabled

1. Upload the updated `.htaccess` file (which now uses PHP 8.1)
2. Test the website - the 500 error should be resolved
3. The admin panel should load correctly

## Files to Upload

- `backend/public/.htaccess` - Updated to use PHP 8.1 (matches your cPanel setting)

