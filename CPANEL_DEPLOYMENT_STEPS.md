# cPanel Deployment Steps

## Overview
This guide will help you deploy the Laravel application to cPanel, replacing the existing WordPress installation.

## Prerequisites
- Git repository cloned to `/home/gotzsafari/repositories/gowebsitelaravel`
- cPanel access
- Database credentials ready

## Deployment Method 1: Using PHP Deployment Script (Recommended)

1. **Upload the deployment script:**
   - Upload `deploy-cpanel.php` to `public_html` via File Manager
   - Or upload via FTP to `/home/gotzsafari/public_html/`

2. **Run the script:**
   - Navigate to: `https://www.gotzsafari.com/deploy-cpanel.php`
   - The script will:
     - Backup WordPress to `wordpress-backups/wordpress-YYYY-MM-DD-HHMMSS`
     - Move Laravel files to `/home/gotzsafari/laravel`
     - Move public files to `public_html`
     - Update `index.php` paths
     - Set proper permissions
     - Create `.env` file

3. **Complete the setup:**
   - SSH into your server or use Terminal in cPanel
   - Run the following commands:

```bash
cd /home/gotzsafari/laravel

# Install Composer dependencies
composer install --no-dev --optimize-autoloader

# Generate application key
php artisan key:generate

# Run migrations
php artisan migrate --force

# Install and build frontend assets
npm install
npm run build

# Run seeders (optional)
php artisan db:seed
```

4. **Configure .env file:**
   - Edit `/home/gotzsafari/laravel/.env`
   - Update database credentials:
     ```
     DB_CONNECTION=pgsql
     DB_HOST=localhost
     DB_PORT=5432
     DB_DATABASE=your_database_name
     DB_USERNAME=your_database_user
     DB_PASSWORD=your_database_password
     ```
   - Update `APP_URL`:
     ```
     APP_URL=https://www.gotzsafari.com
     ```
   - Set `APP_ENV=production` and `APP_DEBUG=false`

5. **Set up storage link:**
```bash
cd /home/gotzsafari/laravel
php artisan storage:link
```

6. **Delete deployment script:**
   - Remove `deploy-cpanel.php` from `public_html` for security

## Deployment Method 2: Manual File Manager Steps

If you prefer manual steps via File Manager:

1. **Backup WordPress:**
   - Navigate to `public_html` in File Manager
   - Select all files and folders
   - Click "Compress" to create a backup archive
   - Move the archive to `wordpress-backups/`

2. **Move Laravel files:**
   - Navigate to `repositories/gowebsitelaravel`
   - Select all files and folders
   - Click "Copy"
   - Navigate to home directory
   - Create folder `laravel` if it doesn't exist
   - Paste files into `laravel`

3. **Move public files:**
   - Navigate to `laravel/public`
   - Select all files
   - Click "Copy"
   - Navigate to `public_html`
   - Clear existing files (except `.htaccess` if needed)
   - Paste files

4. **Update index.php:**
   - Edit `public_html/index.php`
   - Change:
     ```php
     require __DIR__.'/../vendor/autoload.php';
     ```
     to:
     ```php
     require __DIR__.'/../laravel/vendor/autoload.php';
     ```
   - Change:
     ```php
     $app = require_once __DIR__.'/../bootstrap/app.php';
     ```
     to:
     ```php
     $app = require_once __DIR__.'/../laravel/bootstrap/app.php';
     ```

5. **Set permissions:**
   - Right-click `laravel/storage` → Permissions → 775
   - Right-click `laravel/bootstrap/cache` → Permissions → 775

6. **Continue with steps 3-6 from Method 1**

## Post-Deployment Checklist

- [ ] WordPress backed up
- [ ] Laravel files in `/home/gotzsafari/laravel`
- [ ] Public files in `public_html`
- [ ] `index.php` paths updated
- [ ] `.env` file configured
- [ ] Composer dependencies installed
- [ ] Application key generated
- [ ] Database migrations run
- [ ] Frontend assets built
- [ ] Storage link created
- [ ] Permissions set correctly
- [ ] Deployment script deleted
- [ ] Website accessible at `https://www.gotzsafari.com`

## Troubleshooting

### 500 Internal Server Error
- Check file permissions (storage and bootstrap/cache should be 775)
- Check `.env` file exists and is configured correctly
- Check Laravel logs: `laravel/storage/logs/laravel.log`

### Database Connection Error
- Verify database credentials in `.env`
- Ensure database exists in cPanel MySQL/PostgreSQL
- Check database user has proper permissions

### Assets Not Loading
- Run `npm run build` again
- Check `public/build` directory exists
- Clear browser cache

### Permission Denied
- Set storage and bootstrap/cache to 775
- Ensure web server user owns the files

## Security Notes

1. **Delete deployment script** after use
2. **Set APP_DEBUG=false** in production
3. **Use strong APP_KEY** (generated with `php artisan key:generate`)
4. **Protect .env file** (should not be web-accessible)
5. **Review file permissions** regularly

