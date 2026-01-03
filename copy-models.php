<?php
/**
 * Quick script to copy updated model files
 * Upload to public_html and run: https://www.gotzsafari.com/copy-models.php
 */

$homeDir = '/home/gotzsafari';
$repoPath = $homeDir . '/repositories/gowebsitelaravel';
$laravelPath = $homeDir . '/laravel';

$filesToCopy = [
    'app/Models/TourPackage.php',
    'app/Models/SafariPackage.php',
    'app/Models/Lodge.php',
    'bootstrap/mime-polyfill.php',
    'app/Providers/AppServiceProvider.php',
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

// Clear config cache
$phpPath = '/opt/cpanel/ea-php82/root/usr/bin/php';
if (file_exists($phpPath)) {
    echo "<div class='success'>🔄 Clearing config cache...</div>";
    exec("$phpPath $laravelPath/artisan config:clear 2>&1", $output, $return);
    if ($return === 0) {
        echo "<div class='success'>✓ Config cache cleared</div>";
    } else {
        echo "<div class='error'>❌ Failed to clear cache</div>";
    }
}

echo "<div class='success'><strong>✅ Done! Files copied. Try uploading an image now.</strong></div>";
?>

</body>
</html>

