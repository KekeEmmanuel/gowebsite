# Fix 413 Content Too Large Error - Upload Limits Configuration

This guide will help you fix the "413 Content Too Large" error when uploading images on your hosted site.

## Quick Fix Steps

### Step 1: Upload Configuration Files to Server

Upload these files to your server:

1. **`.user.ini`** → Upload to `/home/gotzsafari/public_html/` (public_html directory)
2. **`.htaccess`** → Already exists, but needs to be updated (see Step 2)

### Step 2: Update .htaccess File

The `.htaccess` file in `public_html` has been updated with PHP upload limits. If you need to update it manually, add these lines after the `AddHandler` directive:

```apache
# Increase upload and POST size limits
php_value upload_max_filesize 50M
php_value post_max_size 60M
php_value max_execution_time 300
php_value max_input_time 300
php_value memory_limit 256M
```

### Step 3: Check Current Limits

1. Upload `check-upload-limits.php` to your `public_html` directory
2. Access it via: `https://www.gotzsafari.com/check-upload-limits.php`
3. Review the current limits and recommendations
4. **DELETE the file after checking** for security

### Step 4: Alternative - cPanel PHP Settings

If `.user.ini` and `.htaccess` don't work, try cPanel:

1. Log into cPanel
2. Go to **"Select PHP Version"** or **"MultiPHP INI Editor"**
3. Select PHP 8.2
4. Click **"Options"** or **"Editor"**
5. Set these values:
   - `upload_max_filesize` = `50M`
   - `post_max_size` = `60M`
   - `max_execution_time` = `300`
   - `max_input_time` = `300`
   - `memory_limit` = `256M`
6. Click **"Save"**

### Step 5: Restart PHP (if needed)

After making changes:

1. In cPanel, go to **"Select PHP Version"**
2. Switch to a different PHP version temporarily
3. Switch back to PHP 8.2
4. This forces PHP to reload configuration

Or contact your hosting provider to restart PHP-FPM.

## File Locations on Server

```
/home/gotzsafari/
├── public_html/          ← Upload .user.ini here
│   ├── .htaccess        ← Update this file
│   └── check-upload-limits.php  ← Upload for testing
└── laravel/
    └── .user.ini        ← Optional: also upload here
```

## Configuration Values Explained

- **upload_max_filesize (50M)**: Maximum size of a single uploaded file
- **post_max_size (60M)**: Maximum size of POST data (must be larger than upload_max_filesize)
- **max_execution_time (300)**: Maximum script execution time in seconds
- **max_input_time (300)**: Maximum time to parse input data
- **memory_limit (256M)**: Maximum memory a script can use

## Troubleshooting

### If .htaccess doesn't work:
- Some hosts disable `php_value` in .htaccess
- Use `.user.ini` instead (cPanel reads this automatically)
- Or use cPanel's PHP settings interface

### If .user.ini doesn't work:
- Make sure it's in the correct directory (public_html)
- Check file permissions (should be 644)
- Wait a few minutes for cPanel to process it
- Try the cPanel PHP settings method instead

### If still getting 413 error:
1. Check Nginx configuration (if using Nginx):
   - Look for `client_max_body_size` directive
   - Should be at least 60M
   - Contact hosting provider if you can't access this

2. Check Apache configuration (if using Apache):
   - Look for `LimitRequestBody` directive
   - Should be at least 60M
   - Contact hosting provider if you can't access this

3. Check Laravel validation:
   - Review form validation rules
   - Make sure max file size matches PHP limits

## Testing

After making changes:

1. Upload a test image (try 5MB, 10MB, 20MB)
2. Check the diagnostic script output
3. Try uploading through your admin panel
4. Check Laravel logs if errors persist: `storage/logs/laravel.log`

## Security Note

**Always delete diagnostic/test files after use:**
- `check-upload-limits.php`
- Any other temporary PHP files in public_html

## Need More Help?

If you're still experiencing issues:
1. Check Laravel logs: `storage/logs/laravel.log`
2. Check PHP error logs (in cPanel)
3. Contact your hosting provider with the error details
4. Share the diagnostic script output for troubleshooting

