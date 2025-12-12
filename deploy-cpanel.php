<?php
/**
 * Laravel cPanel Deployment Script
 * 
 * This script automates the deployment of Laravel application to cPanel
 * Run this script via browser: https://www.gotzsafari.com/deploy-cpanel.php
 * 
 * IMPORTANT: Delete this file after deployment for security!
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);
set_time_limit(300); // 5 minutes

$homeDir = '/home/gotzsafari';
$repoPath = $homeDir . '/repositories/gowebsitelaravel';
$laravelPath = $homeDir . '/laravel';
$publicHtml = $homeDir . '/public_html';
$backupPath = $homeDir . '/wordpress-backups/wordpress-' . date('Y-m-d-His');

$output = [];
$errors = [];

function logOutput($message) {
    global $output;
    $output[] = $message;
    echo "<p style='color: green;'>✓ $message</p>";
    flush();
}

function logError($message) {
    global $errors;
    $errors[] = $message;
    echo "<p style='color: red;'>✗ $message</p>";
    flush();
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Laravel cPanel Deployment</title>
    <style>
        body { font-family: monospace; padding: 20px; background: #1a1a1a; color: #fff; }
        .container { max-width: 900px; margin: 0 auto; }
        h1 { color: #4CAF50; }
        .step { background: #2a2a2a; padding: 15px; margin: 10px 0; border-left: 4px solid #4CAF50; }
        .error { background: #3a1a1a; border-left-color: #f44336; }
        pre { background: #1a1a1a; padding: 10px; overflow-x: auto; }
    </style>
</head>
<body>
<div class="container">
    <h1>🚀 Laravel cPanel Deployment</h1>
    <p>Starting deployment at <?php echo date('Y-m-d H:i:s'); ?></p>
    <hr>

<?php

// Step 1: Backup WordPress
logOutput("Step 1: Backing up WordPress from public_html...");
if (!is_dir($backupPath)) {
    if (!mkdir($backupPath, 0755, true)) {
        logError("Failed to create backup directory");
    }
}

if (is_dir($publicHtml)) {
    $backupCmd = "cp -r $publicHtml/* $backupPath/ 2>&1";
    exec($backupCmd, $backupOutput, $backupReturn);
    if ($backupReturn === 0) {
        logOutput("WordPress backed up to: $backupPath");
    } else {
        logError("Backup failed: " . implode("\n", $backupOutput));
    }
} else {
    logError("public_html directory not found");
}

// Step 2: Move Laravel files
logOutput("Step 2: Moving Laravel files to /home/gotzsafari/laravel...");
if (!is_dir($laravelPath)) {
    if (!mkdir($laravelPath, 0755, true)) {
        logError("Failed to create laravel directory");
    }
}

if (is_dir($repoPath)) {
    $moveCmd = "cp -r $repoPath/* $laravelPath/ 2>&1";
    exec($moveCmd, $moveOutput, $moveReturn);
    if ($moveReturn === 0) {
        logOutput("Laravel files moved successfully");
    } else {
        logError("Move failed: " . implode("\n", $moveOutput));
    }
} else {
    logError("Repository path not found: $repoPath");
}

// Step 3: Move public folder contents to public_html
logOutput("Step 3: Moving public folder contents to public_html...");
$publicPath = $laravelPath . '/public';
if (is_dir($publicPath)) {
    // Clear public_html first (except .htaccess if exists)
    $clearCmd = "find $publicHtml -mindepth 1 -maxdepth 1 ! -name '.htaccess' -exec rm -rf {} + 2>&1";
    exec($clearCmd);
    
    $copyPublicCmd = "cp -r $publicPath/* $publicHtml/ 2>&1";
    exec($copyPublicCmd, $copyPublicOutput, $copyPublicReturn);
    if ($copyPublicReturn === 0) {
        logOutput("Public files moved to public_html");
    } else {
        logError("Public copy failed: " . implode("\n", $copyPublicOutput));
    }
} else {
    logError("Public directory not found: $publicPath");
}

// Step 4: Update public_html/index.php
logOutput("Step 4: Updating public_html/index.php...");
$indexFile = $publicHtml . '/index.php';
if (file_exists($indexFile)) {
    $indexContent = file_get_contents($indexFile);
    // Update paths to point to laravel directory
    $indexContent = str_replace(
        "__DIR__.'/../vendor/autoload.php'",
        "__DIR__.'/../laravel/vendor/autoload.php'",
        $indexContent
    );
    $indexContent = str_replace(
        "__DIR__.'/../bootstrap/app.php'",
        "__DIR__.'/../laravel/bootstrap/app.php'",
        $indexContent
    );
    file_put_contents($indexFile, $indexContent);
    logOutput("index.php updated");
} else {
    logError("index.php not found");
}

// Step 5: Set permissions
logOutput("Step 5: Setting file permissions...");
$permissions = [
    "chmod -R 755 $laravelPath",
    "chmod -R 755 $publicHtml",
    "chmod -R 775 $laravelPath/storage",
    "chmod -R 775 $laravelPath/bootstrap/cache",
];
foreach ($permissions as $cmd) {
    exec($cmd . " 2>&1", $permOutput, $permReturn);
    if ($permReturn === 0) {
        logOutput("Permissions set: " . explode(' ', $cmd)[2]);
    }
}

// Step 6: Create .env file if it doesn't exist
logOutput("Step 6: Checking .env file...");
$envFile = $laravelPath . '/.env';
if (!file_exists($envFile)) {
    $envExample = $laravelPath . '/.env.example';
    if (file_exists($envExample)) {
        copy($envExample, $envFile);
        logOutput(".env file created from .env.example");
        logOutput("⚠️  IMPORTANT: Update .env file with your database credentials!");
    } else {
        logError(".env.example not found");
    }
} else {
    logOutput(".env file already exists");
}

echo "<hr>";
echo "<h2>Deployment Summary</h2>";
echo "<div class='step'>";
echo "<strong>Completed Steps:</strong><br>";
echo "1. WordPress backup: " . (is_dir($backupPath) ? "✓" : "✗") . "<br>";
echo "2. Laravel files moved: " . (is_dir($laravelPath) ? "✓" : "✗") . "<br>";
echo "3. Public files moved: " . (is_dir($publicHtml) ? "✓" : "✗") . "<br>";
echo "4. index.php updated: " . (file_exists($indexFile) ? "✓" : "✗") . "<br>";
echo "5. Permissions set: ✓<br>";
echo "6. .env file: " . (file_exists($envFile) ? "✓" : "✗") . "<br>";
echo "</div>";

if (count($errors) > 0) {
    echo "<div class='step error'>";
    echo "<strong>Errors:</strong><br>";
    foreach ($errors as $error) {
        echo "- $error<br>";
    }
    echo "</div>";
}

echo "<div class='step'>";
echo "<h3>Next Steps:</h3>";
echo "<ol>";
echo "<li>Update .env file with your database credentials</li>";
echo "<li>Run: <code>cd $laravelPath && composer install --no-dev --optimize-autoloader</code></li>";
echo "<li>Run: <code>cd $laravelPath && php artisan key:generate</code></li>";
echo "<li>Run: <code>cd $laravelPath && php artisan migrate --force</code></li>";
echo "<li>Run: <code>cd $laravelPath && npm install && npm run build</code></li>";
echo "<li>Delete this deployment script for security!</li>";
echo "</ol>";
echo "</div>";

echo "<p><strong>⚠️  SECURITY WARNING:</strong> Delete this file (deploy-cpanel.php) immediately after deployment!</p>";
?>

</div>
</body>
</html>

