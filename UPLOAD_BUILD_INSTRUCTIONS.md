# Upload Frontend Build Assets to Server

## Build Complete ✅
The frontend assets have been built successfully in `public/build/`

## Upload Instructions

### Option 1: Using cPanel File Manager (Recommended)

1. **Log into cPanel** at `https://www.gotzsafari.com/cpanel`

2. **Open File Manager**
   - Navigate to: `/home/gotzsafari/laravel/public/`

3. **Delete existing build folder (if it exists)**
   - If there's a `build` folder, delete it first

4. **Upload the build folder**
   - Click "Upload" button
   - Select the entire `public/build` folder from your local machine
   - Wait for upload to complete

5. **Set permissions**
   - Right-click on the `build` folder
   - Select "Change Permissions"
   - Set to `755` (or `775` if needed)

### Option 2: Using FTP/SFTP

1. **Connect to your server via FTP/SFTP**
   - Host: `www.gotzsafari.com` or your server IP
   - Username: Your cPanel username
   - Password: Your cPanel password
   - Port: 21 (FTP) or 22 (SFTP)

2. **Navigate to**: `/home/gotzsafari/laravel/public/`

3. **Upload the `build` folder**
   - Upload the entire `build` folder from `public/build/` on your local machine
   - Ensure all files and subdirectories are uploaded

### Option 3: Using Terminal/SSH (if you have SSH access)

```bash
# From your local machine, navigate to the backend directory
cd /Users/mdegy/goweb/backend

# Use scp to upload (replace with your actual credentials)
scp -r public/build username@www.gotzsafari.com:/home/gotzsafari/laravel/public/
```

## Verify Upload

After uploading, verify the files are in place:
- Check: `https://www.gotzsafari.com/build/manifest.json` (should be accessible)
- The Laravel app should now load with all frontend assets

## Clean Up Deployment Scripts

After verifying the site works, **DELETE these files from `public_html`** for security:
- `create-env.php`
- `run-deployment.php`
- `deploy-cpanel.php`
- `check-laravel.php` (if exists)
- `test-composer.php` (if exists)

## Your Build Location

**Local build folder**: `/Users/mdegy/goweb/backend/public/build/`

**Server destination**: `/home/gotzsafari/laravel/public/build/`

