<?php
/**
 * Create .env file for Laravel deployment
 * Run this script via browser: https://www.gotzsafari.com/create-env.php
 * IMPORTANT: Delete this file after use!
 */

$envContent = <<<'ENV'
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
ENV;

$envPath = '/home/gotzsafari/laravel/.env';

if (file_put_contents($envPath, $envContent)) {
    echo "<h1>✓ .env file created successfully!</h1>";
    echo "<p>File created at: <code>$envPath</code></p>";
    echo "<p><strong>⚠️ IMPORTANT:</strong> Delete this script (create-env.php) for security!</p>";
    echo "<hr>";
    echo "<h2>Next Steps:</h2>";
    echo "<ol>";
    echo "<li>Delete this script: <code>create-env.php</code></li>";
    echo "<li>Set up MySQL database in cPanel:</li>";
    echo "<ul>";
    echo "<li>Create database: <code>gotzsafari_laravel</code></li>";
    echo "<li>Create user: <code>gotzsafari_gotz</code> with password: <code>gotz@2025</code></li>";
    echo "<li>Add user to database with ALL PRIVILEGES</li>";
    echo "</ul>";
    echo "<li>Run: <code>cd /home/gotzsafari/laravel && composer install --no-dev --optimize-autoloader</code></li>";
    echo "<li>Run: <code>cd /home/gotzsafari/laravel && php artisan key:generate</code></li>";
    echo "<li>Run: <code>cd /home/gotzsafari/laravel && php artisan migrate --force</code></li>";
    echo "<li>Run: <code>cd /home/gotzsafari/laravel && npm install && npm run build</code></li>";
    echo "</ol>";
} else {
    echo "<h1>✗ Error creating .env file</h1>";
    echo "<p>Could not write to: <code>$envPath</code></p>";
    echo "<p>Please check permissions or create the file manually.</p>";
}

