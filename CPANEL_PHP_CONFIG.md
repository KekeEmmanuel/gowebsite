# Configure PHP Upload Limits in cPanel - Step by Step Guide

This guide will walk you through configuring PHP upload limits directly in cPanel.

## Step 1: Log into cPanel

1. Go to: `https://www.gotzsafari.com/cpanel` (or your cPanel URL)
2. Enter your username and password
3. Click **"Log in"**

## Step 2: Find PHP Configuration

Look for one of these options in your cPanel dashboard:

**Option A: "Select PHP Version"** (Most common)
- Look for an icon labeled **"Select PHP Version"** or **"PHP Version"**
- Click on it

**Option B: "MultiPHP INI Editor"**
- Look for **"MultiPHP INI Editor"** or **"PHP Configuration"**
- Click on it

**Option C: "Software" → "Select PHP Version"**
- Click on **"Software"** in the main menu
- Then click **"Select PHP Version"**

## Step 3: Select PHP 8.2

1. You should see a dropdown or list of PHP versions
2. Select **PHP 8.2** (or the version you're using)
3. Click **"Set as current"** or **"Set version"** if needed

## Step 4: Open PHP Options/Editor

After selecting PHP 8.2, you'll see one of these options:

**If you see "Options" button:**
- Click **"Options"** next to PHP 8.2
- This opens the PHP configuration editor

**If you see "Editor" or "Edit" button:**
- Click **"Editor"** or **"Edit"**
- This opens the PHP INI editor

**If you're already in MultiPHP INI Editor:**
- Make sure PHP 8.2 is selected
- You should see a list of PHP settings

## Step 5: Configure Upload Settings

You'll see a list of PHP settings. Find and update these values:

### Setting 1: upload_max_filesize
1. Find **"upload_max_filesize"** in the list
2. Click on it or the value next to it
3. Change the value to: **50M**
4. Click **"Save"** or **"Apply"**

### Setting 2: post_max_size
1. Find **"post_max_size"** in the list
2. Click on it or the value next to it
3. Change the value to: **60M** (must be larger than upload_max_filesize)
4. Click **"Save"** or **"Apply"**

### Setting 3: max_execution_time
1. Find **"max_execution_time"** in the list
2. Click on it or the value next to it
3. Change the value to: **300**
4. Click **"Save"** or **"Apply"**

### Setting 4: max_input_time
1. Find **"max_input_time"** in the list
2. Click on it or the value next to it
3. Change the value to: **300**
4. Click **"Save"** or **"Apply"**

### Setting 5: memory_limit
1. Find **"memory_limit"** in the list
2. Click on it or the value next to it
3. Change the value to: **256M**
4. Click **"Save"** or **"Apply"**

## Step 6: Save All Changes

After updating all settings:

1. Look for a **"Save"** or **"Save Changes"** button at the bottom
2. Click it to apply all changes
3. You should see a success message

## Step 7: Verify Changes

1. Upload `check-upload-limits.php` to your `public_html` directory
2. Access it via: `https://www.gotzsafari.com/check-upload-limits.php`
3. Check that all values show **"✓ OK"**
4. **DELETE the file after checking** for security

## Alternative: Manual INI Editor

If you see a **"Switch to PHP INI Editor"** or **"Edit INI File"** option:

1. Click on it
2. You'll see a text editor with PHP configuration
3. Find and update these lines (or add them if they don't exist):

```ini
upload_max_filesize = 50M
post_max_size = 60M
max_execution_time = 300
max_input_time = 300
memory_limit = 256M
```

4. Click **"Save"** or **"Save Changes"**

## Troubleshooting

### Can't find PHP settings?
- Look in **"Software"** section
- Check **"Advanced"** section
- Contact your hosting provider for help

### Settings don't save?
- Make sure you have permission to edit PHP settings
- Some hosts require you to contact support
- Try the `.user.ini` file method instead

### Settings saved but not working?
1. Wait 2-3 minutes for changes to take effect
2. Clear your browser cache
3. Try uploading a test file
4. Check the diagnostic script output

### Need to restart PHP?
1. In cPanel, go back to **"Select PHP Version"**
2. Switch to PHP 8.1 temporarily
3. Switch back to PHP 8.2
4. This forces PHP to reload configuration

## Quick Reference: Values to Set

| Setting | Value | Why |
|---------|-------|-----|
| `upload_max_filesize` | `50M` | Maximum single file upload size |
| `post_max_size` | `60M` | Maximum POST data (must be > upload_max_filesize) |
| `max_execution_time` | `300` | Maximum script execution time (5 minutes) |
| `max_input_time` | `300` | Maximum time to parse input data |
| `memory_limit` | `256M` | Maximum memory PHP can use |

## After Configuration

1. ✅ Test file uploads in your admin panel
2. ✅ Check diagnostic script to verify limits
3. ✅ Try uploading images of various sizes (5MB, 10MB, 20MB, 30MB)
4. ✅ Monitor Laravel logs if issues persist: `storage/logs/laravel.log`

## Need Help?

If you're still having issues:
1. Take a screenshot of your cPanel PHP settings page
2. Check the diagnostic script output
3. Contact your hosting provider with the error details
4. Share the diagnostic results for troubleshooting

