<?php
/**
 * Clean Up Temporary Files
 * Run this script via browser: https://www.gotzsafari.com/cleanup-temp-files.php
 * IMPORTANT: Delete this file after use!
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

$publicHtmlPath = '/home/gotzsafari/public_html';

function logOutput($message) {
    echo "<p style='color: #4CAF50;'>✓ $message</p>";
    flush();
}

function logError($message) {
    echo "<p style='color: #f44336;'>✗ $message</p>";
    flush();
}

function logInfo($message) {
    echo "<p style='color: #2196F3;'>ℹ $message</p>";
    flush();
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Clean Up Temporary Files</title>
    <style>
        body { font-family: monospace; padding: 20px; background: #1a1a1a; color: #fff; }
        .container { max-width: 900px; margin: 0 auto; }
        h1 { color: #4CAF50; }
        .step { background: #2a2a2a; padding: 15px; margin: 10px 0; border-left: 4px solid #4CAF50; }
        .warning { border-left-color: #FFD700; }
        pre { background: #1a1a1a; padding: 10px; overflow-x: auto; font-size: 11px; }
    </style>
</head>
<body>
<div class="container">
    <h1>🧹 Clean Up Temporary Files</h1>
    <p>Cleaning up temporary deployment and diagnostic files at <?php echo date('Y-m-d H:i:s'); ?></p>
    <hr>

<?php

// List of temporary files to delete
$tempFiles = [
    // Deployment scripts
    'sync-to-server.php',
    'deploy-build.php',
    'pull-updates.php',
    'sync-routes.php',
    'run-deployment.php',
    'create-env.php',
    'upload-build.php',
    
    // Diagnostic scripts
    'check-routes.php',
    'check-errors.php',
    'check-laravel.php',
    'test-composer.php',
    'fix-ziggy-routes.php',
    'clear-cache.php',
    'fix-build-assets.php',
    'fix-php-version.php',
    'run-seeders.php',
    
    // Build files
    'build.zip',
    
    // This cleanup script itself (will be deleted last)
    'cleanup-temp-files.php',
];

$deletedCount = 0;
$notFoundCount = 0;
$errorCount = 0;

echo "<div class='step'>";
echo "<h3>Files to Delete:</h3>";
echo "<ul>";

foreach ($tempFiles as $file) {
    $filePath = $publicHtmlPath . '/' . $file;
    
    if (file_exists($filePath)) {
        if (unlink($filePath)) {
            logOutput("Deleted: $file");
            $deletedCount++;
            echo "<li style='color: #4CAF50;'>✓ $file</li>";
        } else {
            logError("Failed to delete: $file");
            $errorCount++;
            echo "<li style='color: #f44336;'>✗ $file (failed)</li>";
        }
    } else {
        logInfo("Not found (already deleted): $file");
        $notFoundCount++;
        echo "<li style='color: #888;'>— $file (not found)</li>";
    }
}

echo "</ul>";
echo "</div>";

// Also check for any backup files that might have been created
echo "<div class='step warning'>";
echo "<h3>Additional Cleanup:</h3>";

// Check for backup files in Laravel directory
$laravelPath = '/home/gotzsafari/laravel';
$backupPatterns = [
    $laravelPath . '/routes/web.php.backup.*',
    $laravelPath . '/public/build.backup.*',
];

$backupFiles = [];
foreach ($backupPatterns as $pattern) {
    $files = glob($pattern);
    if ($files) {
        $backupFiles = array_merge($backupFiles, $files);
    }
}

if (!empty($backupFiles)) {
    logInfo("Found " . count($backupFiles) . " backup file(s):");
    foreach ($backupFiles as $backupFile) {
        $fileName = basename($backupFile);
        echo "<p style='color: #888;'>— $fileName</p>";
        logInfo("You may want to delete these manually if no longer needed");
    }
} else {
    logOutput("No backup files found");
}

echo "</div>";

echo "<hr>";
echo "<h2>✅ Cleanup Summary</h2>";
echo "<div class='step'>";
echo "<strong>Results:</strong><br>";
echo "✓ Deleted: $deletedCount file(s)<br>";
echo "— Not found: $notFoundCount file(s)<br>";
if ($errorCount > 0) {
    echo "<span style='color: #f44336;'>✗ Errors: $errorCount file(s)</span><br>";
}
echo "</div>";

if ($deletedCount > 0) {
    echo "<div class='step warning'>";
    echo "<h3>⚠️  IMPORTANT:</h3>";
    echo "<p><strong>This cleanup script will delete itself after you refresh or close this page.</strong></p>";
    echo "<p>If it's still here, you can delete it manually from cPanel File Manager.</p>";
    echo "</div>";
}

?>

</div>
</body>
</html>

<?php
// Delete this script itself after execution
if (file_exists(__FILE__)) {
    // Schedule deletion (will be deleted after script execution)
    register_shutdown_function(function() {
        @unlink(__FILE__);
    });
}
?>

