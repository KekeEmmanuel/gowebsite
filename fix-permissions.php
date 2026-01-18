<?php
/**
 * Fix File Permissions and Revert .htaccess
 * Upload to public_html and run: https://www.gotzsafari.com/fix-permissions.php
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

$publicHtmlPath = '/home/gotzsafari/public_html';
$htaccessPath = $publicHtmlPath . '/.htaccess';

?>
<!DOCTYPE html>
<html>
<head>
    <title>Fix Permissions & Revert .htaccess</title>
    <style>
        body { font-family: Arial; max-width: 800px; margin: 50px auto; padding: 20px; background: #1a1a1a; color: #fff; }
        .success { color: #4CAF50; padding: 10px; background: #2a2a2a; margin: 5px 0; }
        .error { color: #f44336; padding: 10px; background: #2a2a2a; margin: 5px 0; }
        .info { color: #2196F3; padding: 10px; background: #2a2a2a; margin: 5px 0; }
        .warning { color: #FF9800; padding: 10px; background: #2a2a2a; margin: 5px 0; }
        h1 { color: #4CAF50; }
        pre { background: #000; padding: 10px; overflow-x: auto; }
    </style>
</head>
<body>
    <h1>🔧 Fix Permissions & Revert .htaccess</h1>

<?php
// Step 1: Revert .htaccess to simple version
echo "<div class='info'><strong>Step 1:</strong> Reverting .htaccess to simple version</div>";

$simpleHtaccess = '<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Handle X-XSRF-Token Header
    RewriteCond %{HTTP:x-xsrf-token} .
    RewriteRule .* - [E=HTTP_X_XSRF_TOKEN:%{HTTP:X-XSRF-Token}]

    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>';

// Backup current .htaccess
if (file_exists($htaccessPath)) {
    $backupPath = $htaccessPath . '.backup.' . date('Y-m-d_H-i-s');
    copy($htaccessPath, $backupPath);
    echo "<div class='success'>✓ Backup created: " . basename($backupPath) . "</div>";
}

// Write simple .htaccess
if (file_put_contents($htaccessPath, $simpleHtaccess)) {
    echo "<div class='success'>✓ .htaccess reverted to simple version</div>";
} else {
    echo "<div class='error'>❌ Failed to write .htaccess</div>";
}

// Step 2: Fix file permissions
echo "<div class='info'><strong>Step 2:</strong> Fixing file permissions</div>";

$filesToFix = [
    $htaccessPath,
    $publicHtmlPath . '/index.php',
    $publicHtmlPath . '/.user.ini',
];

foreach ($filesToFix as $file) {
    if (file_exists($file)) {
        if (chmod($file, 0644)) {
            echo "<div class='success'>✓ Fixed permissions: " . basename($file) . " (644)</div>";
        } else {
            echo "<div class='error'>❌ Failed to fix permissions: " . basename($file) . "</div>";
        }
    }
}

// Fix directory permissions
if (is_dir($publicHtmlPath)) {
    if (chmod($publicHtmlPath, 0755)) {
        echo "<div class='success'>✓ Fixed directory permissions: public_html (755)</div>";
    } else {
        echo "<div class='error'>❌ Failed to fix directory permissions</div>";
    }
}

// Step 3: Check Laravel directory permissions
$laravelPath = '/home/gotzsafari/laravel';
if (is_dir($laravelPath)) {
    echo "<div class='info'><strong>Step 3:</strong> Checking Laravel directory permissions</div>";
    
    $laravelDirs = [
        $laravelPath . '/storage',
        $laravelPath . '/bootstrap/cache',
    ];
    
    foreach ($laravelDirs as $dir) {
        if (is_dir($dir)) {
            if (chmod($dir, 0755)) {
                echo "<div class='success'>✓ Fixed permissions: " . basename($dir) . " (755)</div>";
            }
        }
    }
}

echo "<div class='success'><strong>✅ Done! Try accessing your site now.</strong></div>";
echo "<div class='warning'>⚠️ <strong>Note:</strong> PHP version will be handled by cPanel settings. Check cPanel → Select PHP Version to ensure PHP 8.2 is selected.</div>";
?>

</body>
</html>
