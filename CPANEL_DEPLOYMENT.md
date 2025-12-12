# cPanel Deployment Guide for Laravel Application

This guide will help you deploy your Laravel application to cPanel at `https://www.gotzsafari.com/cpanel`.

## Prerequisites

- cPanel access with SSH enabled (recommended)
- PHP 8.1+ (check in cPanel → Select PHP Version)
- Composer installed (or use cPanel's Composer)
- Node.js and npm (for building frontend assets)
- MySQL/PostgreSQL database created in cPanel
- Domain/subdomain configured

## Step 1: Prepare Your Files

### Option A: Upload via File Manager (if SSH not available)
1. Compress your `backend` folder (excluding `node_modules`, `vendor`, `.env`)
2. Upload the zip file to cPanel File Manager
3. Extract it in your desired location (e.g., `public_html/` or a subdirectory)

### Option B: Upload via Git (Recommended)
1. In cPanel, go to **Git Version Control**
2. Clone your repository:
   ```
   Repository URL: https://github.com/your-username/your-repo.git
   Repository Branch: main
   Clone Directory: /home/username/public_html (or your desired path)
   ```

## Step 2: Set Document Root

**IMPORTANT:** Laravel's `public` folder must be your document root.

### If deploying to root domain (gotzsafari.com):
1. In cPanel, go to **File Manager**
2. Move contents of `public` folder to `public_html`
3. Move all other files one level up (outside `public_html`)
4. Your structure should be:
   ```
   /home/username/
   ├── public_html/          (document root - contains index.php, .htaccess)
   │   ├── index.php
   │   ├── .htaccess
   │   ├── build/
   │   └── images/
   ├── app/
   ├── bootstrap/
   ├── config/
   ├── database/
   ├── resources/
   ├── routes/
   ├── storage/
   ├── vendor/
   └── .env
   ```

### If deploying to subdirectory (gotzsafari.com/cpanel):
1. Create folder: `public_html/cpanel`
2. Copy `public` folder contents to `public_html/cpanel`
3. Update `public_html/cpanel/index.php` to point to correct paths:
   ```php
   require __DIR__.'/../../vendor/autoload.php';
   $app = require_once __DIR__.'/../../bootstrap/app.php';
   ```

## Step 3: Configure Environment File

1. In cPanel File Manager, navigate to your Laravel root directory
2. Copy `.env.example` to `.env` (if it exists) or create `.env`
3. Edit `.env` with your production settings:

```env
APP_NAME="Go Tanzania Safari"
APP_ENV=production
APP_KEY=base64:YOUR_APP_KEY_HERE
APP_DEBUG=false
APP_URL=https://www.gotzsafari.com

# Database Configuration
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_user
DB_PASSWORD=your_database_password

# Mail Configuration (if needed)
MAIL_MAILER=smtp
MAIL_HOST=mail.gotzsafari.com
MAIL_PORT=587
MAIL_USERNAME=noreply@gotzsafari.com
MAIL_PASSWORD=your_mail_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@gotzsafari.com
MAIL_FROM_NAME="${APP_NAME}"

# Session & Cache
SESSION_DRIVER=file
CACHE_DRIVER=file
QUEUE_CONNECTION=sync

# Media Library
MEDIA_DISK=public
```

**Generate APP_KEY:**
- Via SSH: `php artisan key:generate`
- Or manually add a base64 encoded key

## Step 4: Install Dependencies

### Via SSH (Recommended):
```bash
cd /home/username/public_html  # or your Laravel root
composer install --no-dev --optimize-autoloader
npm install
npm run build
```

### Via cPanel Terminal:
1. Go to **Terminal** in cPanel
2. Navigate to your project directory
3. Run the same commands above

### Via cPanel Composer:
1. Go to **Composer** in cPanel
2. Select your project directory
3. Click **Install** (it will run `composer install`)

## Step 5: Set Permissions

Via SSH or Terminal:
```bash
cd /home/username/public_html  # or your Laravel root
chmod -R 755 storage bootstrap/cache
chown -R username:username storage bootstrap/cache
```

**Important directories:**
- `storage/` - must be writable (755 or 775)
- `bootstrap/cache/` - must be writable (755 or 775)

## Step 6: Create Database

1. In cPanel, go to **MySQL Databases**
2. Create a new database (e.g., `username_gotzsafari`)
3. Create a new MySQL user
4. Add user to database with ALL PRIVILEGES
5. Note down the database name, username, and password
6. Update your `.env` file with these credentials

## Step 7: Run Migrations and Seeders

Via SSH or Terminal:
```bash
cd /home/username/public_html  # or your Laravel root
php artisan migrate --force
php artisan db:seed --force
```

Or via cPanel Cron Jobs (one-time):
1. Go to **Cron Jobs** in cPanel
2. Create a one-time cron job:
   ```bash
   cd /home/username/public_html && php artisan migrate --force && php artisan db:seed --force
   ```

## Step 8: Optimize Laravel

Via SSH or Terminal:
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

## Step 9: Configure .htaccess

Ensure `public_html/.htaccess` exists and contains:

```apache
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
```

## Step 10: Test Your Deployment

1. Visit `https://www.gotzsafari.com` (or your configured URL)
2. Check if the homepage loads
3. Test API endpoints (e.g., `/api/contact-channels`)
4. Test admin panel (e.g., `/admin/login`)
5. Check Laravel logs: `storage/logs/laravel.log`

## Troubleshooting

### 500 Internal Server Error
- Check file permissions (`storage/`, `bootstrap/cache/`)
- Check `.env` file exists and is configured correctly
- Check `storage/logs/laravel.log` for errors
- Verify PHP version is 8.1+

### Database Connection Error
- Verify database credentials in `.env`
- Check database user has proper permissions
- Ensure database exists in cPanel

### Assets Not Loading
- Run `npm run build` again
- Check `public/build/` directory exists
- Verify file permissions on `public/` directory

### Route Not Found
- Run `php artisan route:clear`
- Run `php artisan route:cache`
- Check `.htaccess` is in `public_html/`

### Permission Denied
```bash
chmod -R 755 storage bootstrap/cache
chown -R username:username storage bootstrap/cache
```

## Post-Deployment Checklist

- [ ] `.env` file configured with production values
- [ ] `APP_DEBUG=false` in production
- [ ] Database migrations run successfully
- [ ] Seeders run (contact channels, quick facts)
- [ ] Frontend assets built (`npm run build`)
- [ ] File permissions set correctly
- [ ] Laravel optimized (`php artisan optimize`)
- [ ] SSL certificate installed (if using HTTPS)
- [ ] Admin panel accessible
- [ ] Contact form working
- [ ] Media uploads working (check `storage/app/public/`)

## Maintenance Commands

After updates, run these via SSH:
```bash
cd /home/username/public_html
composer install --no-dev --optimize-autoloader
npm install
npm run build
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

## Security Notes

1. **Never commit `.env` file** - it contains sensitive information
2. **Set `APP_DEBUG=false`** in production
3. **Use strong database passwords**
4. **Keep Laravel and dependencies updated**
5. **Regular backups** of database and files
6. **Use HTTPS** (SSL certificate)

## Support

If you encounter issues:
1. Check `storage/logs/laravel.log`
2. Check cPanel error logs
3. Verify PHP version compatibility
4. Ensure all dependencies are installed

