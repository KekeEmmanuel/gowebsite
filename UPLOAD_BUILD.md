# Upload Build Files to Server

## Build Complete ✅

The build files are ready in: `backend/public/build/`

## Upload Instructions

### Option 1: Via cPanel File Manager (Recommended)

1. **Compress the build folder:**
   - On your Mac, navigate to: `/Users/mdegy/goweb/backend/public/`
   - Right-click the `build` folder
   - Select "Compress" (creates `build.zip`)

2. **Upload to server:**
   - Log into cPanel → File Manager
   - Navigate to `/home/gotzsafari/laravel/public/`
   - Upload `build.zip`
   - Extract it (right-click → Extract)
   - Delete `build.zip` after extraction

3. **Verify:**
   - Check that `/home/gotzsafari/laravel/public/build/manifest.json` exists
   - Visit: `https://www.gotzsafari.com/build/manifest.json` (should show JSON)

### Option 2: Via FTP/SFTP

1. **Connect to server:**
   - Host: `gotzsafari.com` (or your server IP)
   - Username: `gotzsafari`
   - Password: Your cPanel password
   - Port: 21 (FTP) or 22 (SFTP)

2. **Upload files:**
   - Navigate to: `/laravel/public/`
   - Upload the entire `build` folder
   - Ensure all files are uploaded (manifest.json, assets folder, etc.)

3. **Verify:**
   - Check file permissions (should be 644 for files, 755 for directories)
   - Visit: `https://www.gotzsafari.com/build/manifest.json`

### Option 3: Via SSH (if available)

```bash
# On your Mac, compress the build folder
cd /Users/mdegy/goweb/backend/public
tar -czf build.tar.gz build/

# Upload via SCP
scp build.tar.gz gotzsafari@gotzsafari.com:/home/gotzsafari/laravel/public/

# SSH into server
ssh gotzsafari@gotzsafari.com

# Extract on server
cd /home/gotzsafari/laravel/public
tar -xzf build.tar.gz
rm build.tar.gz

# Set permissions
chmod -R 755 build
chmod 644 build/manifest.json
```

## What to Upload

Upload the entire `build` folder containing:
- `manifest.json` (required)
- `assets/` directory (all CSS and JS files)

## After Upload

1. **Test the manifest:**
   - Visit: `https://www.gotzsafari.com/build/manifest.json`
   - Should show JSON content (not 404)

2. **Test the homepage:**
   - Visit: `https://www.gotzsafari.com/`
   - Should load with all assets working

3. **If still 404:**
   - Verify symlink exists: `/home/gotzsafari/public_html/build` → `../laravel/public/build`
   - Check file permissions
   - Clear browser cache

## File Structure on Server

```
/home/gotzsafari/
├── laravel/
│   └── public/
│       └── build/          ← Upload here
│           ├── manifest.json
│           └── assets/
│               ├── app-*.css
│               ├── app-*.js
│               └── ...
└── public_html/
    └── build -> ../laravel/public/build  ← Symlink (should already exist)
```

## Quick Check Script

After uploading, create this file in `public_html` to verify:

```php
<?php
$buildPath = '/home/gotzsafari/laravel/public/build';
$manifest = $buildPath . '/manifest.json';

if (file_exists($manifest)) {
    echo "✓ manifest.json exists<br>";
    echo "Size: " . filesize($manifest) . " bytes<br>";
    $data = json_decode(file_get_contents($manifest), true);
    echo "Entries: " . count($data) . "<br>";
} else {
    echo "❌ manifest.json NOT found";
}
?>
```
