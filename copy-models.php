<?php
/**
 * Quick script to copy updated model files
 * Upload to public_html and run: https://www.gotzsafari.com/copy-models.php
 */

$homeDir = '/home/gotzsafari';
$repoPath = $homeDir . '/repositories/gowebsitelaravel';
$laravelPath = $homeDir . '/laravel';

// First, pull latest changes from Git
echo "<div class='info'><strong>Step 0:</strong> Pulling latest changes from Git</div>";
chdir($repoPath);
exec("git pull origin main 2>&1", $gitOutput, $gitReturn);
if ($gitReturn === 0) {
    echo "<div class='success'>✓ Pulled latest changes</div>";
} else {
    echo "<div class='error'>⚠ Git pull had issues, but continuing...</div>";
}

$filesToCopy = [
    'app/Models/TourPackage.php',
    'app/Models/SafariPackage.php',
    'app/Models/Lodge.php',
    'bootstrap/mime-polyfill.php',
    'app/Providers/AppServiceProvider.php',
    'app/Providers/MediaLibraryServiceProvider.php',
    'bootstrap/providers.php',
    'artisan',
];

?>
<!DOCTYPE html>
<html>
<head>
    <title>Copy Model Files</title>
    <style>
        body { font-family: Arial; max-width: 800px; margin: 50px auto; padding: 20px; background: #1a1a1a; color: #fff; }
        .success { color: #4CAF50; padding: 10px; background: #2a2a2a; margin: 5px 0; }
        .error { color: #f44336; padding: 10px; background: #2a2a2a; margin: 5px 0; }
        h1 { color: #4CAF50; }
    </style>
</head>
<body>
    <h1>📋 Copying Updated Model Files</h1>

<?php

foreach ($filesToCopy as $file) {
    $source = $repoPath . '/' . $file;
    $target = $laravelPath . '/' . $file;
    $targetDir = dirname($target);
    
    if (!file_exists($source)) {
        echo "<div class='error'>❌ Source not found: $file</div>";
        continue;
    }
    
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
    }
    
    if (copy($source, $target)) {
        chmod($target, 0644);
        echo "<div class='success'>✓ Copied: $file</div>";
    } else {
        echo "<div class='error'>❌ Failed to copy: $file</div>";
    }
}

// Clear all caches
$phpPath = '/opt/cpanel/ea-php82/root/usr/bin/php';
if (file_exists($phpPath)) {
    echo "<div class='info'><strong>Step 4:</strong> Clearing all caches</div>";
    $cacheCommands = ['config:clear', 'cache:clear', 'route:clear', 'view:clear', 'optimize:clear'];
    foreach ($cacheCommands as $cmd) {
        exec("$phpPath $laravelPath/artisan $cmd 2>&1", $output, $return);
        if ($return === 0) {
            echo "<div class='success'>✓ Cleared: $cmd</div>";
        }
    }
}

echo "<div class='success'><strong>✅ Done! Files copied. Try uploading an image now.</strong></div>";
?>

</body>
</html>

