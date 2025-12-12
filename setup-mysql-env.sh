#!/bin/bash
# MySQL Database and .env Setup Script for Laravel Deployment
# Run this script in cPanel Terminal

LARAVEL_PATH="/home/gotzsafari/laravel"
DB_NAME="gotzsafari_laravel"
DB_USER="gotzsafari_gotz"
DB_PASS="gotz@2025"

echo "=== Laravel MySQL Setup Script ==="
echo ""

# Create .env file
echo "Creating .env file..."
cat > "$LARAVEL_PATH/.env" << 'ENVEOF'
APP_NAME="Go Tanzania Safari"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://www.gotzsafari.com

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=gotzsafari_laravel
DB_USERNAME=gotzsafari_gotz
DB_PASSWORD=gotz@2025

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

MEMCACHED_HOST=127.0.0.1

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=mail.gotzsafari.com
MAIL_PORT=587
MAIL_USERNAME=noreply@gotzsafari.com
MAIL_PASSWORD=
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@gotzsafari.com"
MAIL_FROM_NAME="${APP_NAME}"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

VITE_APP_NAME="${APP_NAME}"
ENVEOF

echo "✓ .env file created at $LARAVEL_PATH/.env"
echo ""
echo "=== Next Steps ==="
echo "1. Create MySQL database in cPanel:"
echo "   - Database name: laravel (will become: $DB_NAME)"
echo "   - User: gotz (will become: $DB_USER)"
echo "   - Password: $DB_PASS"
echo "   - Grant ALL PRIVILEGES to user on database"
echo ""
echo "2. Run the following commands:"
echo "   cd $LARAVEL_PATH"
echo "   composer install --no-dev --optimize-autoloader"
echo "   php artisan key:generate"
echo "   php artisan migrate --force"
echo "   npm install && npm run build"
echo "   php artisan storage:link"
echo ""
echo "3. Delete deployment scripts for security:"
echo "   rm /home/gotzsafari/public_html/deploy-cpanel.php"
echo "   rm /home/gotzsafari/public_html/create-env.php"
echo ""

