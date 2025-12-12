<?php
/**
 * Quick Copy Home.vue
 * Run this script via browser: https://www.gotzsafari.com/copy-home-vue.php
 * IMPORTANT: Delete this file after use!
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

$repoPath = '/home/gotzsafari/repositories/gowebsitelaravel';
$laravelPath = '/home/gotzsafari/laravel';

$sourceFile = "$repoPath/resources/marketing/pages/Home.vue";
$targetFile = "$laravelPath/resources/marketing/pages/Home.vue";
$targetDir = dirname($targetFile);

?>
<!DOCTYPE html>
<html>
<head>
    <title>Copy Home.vue</title>
    <style>
        body { font-family: monospace; padding: 20px; background: #1a1a1a; color: #fff; }
        .container { max-width: 900px; margin: 0 auto; }
        h1 { color: #4CAF50; }
        .success { color: #4CAF50; }
        .error { color: #f44336; }
    </style>
</head>
<body>
<div class="container">
    <h1>📋 Copy Home.vue</h1>
    <hr>

<?php

if (!is_dir($targetDir)) {
    mkdir($targetDir, 0755, true);
    echo "<p class='success'>✓ Created directory: " . dirname($targetFile) . "</p>";
}

if (file_exists($sourceFile)) {
    if (copy($sourceFile, $targetFile)) {
        echo "<p class='success'>✓ Successfully copied Home.vue</p>";
        echo "<p>Source: $sourceFile</p>";
        echo "<p>Target: $targetFile</p>";
        
        // Check file size
        $sourceSize = filesize($sourceFile);
        $targetSize = filesize($targetFile);
        echo "<p>File size: " . number_format($sourceSize) . " bytes</p>";
        
        if ($sourceSize === $targetSize) {
            echo "<p class='success'>✓ File sizes match - copy verified</p>";
        } else {
            echo "<p class='error'>✗ File sizes don't match!</p>";
        }
    } else {
        echo "<p class='error'>✗ Failed to copy Home.vue</p>";
    }
} else {
    echo "<p class='error'>✗ Source file not found: $sourceFile</p>";
}

echo "<hr>";
echo "<p><strong>⚠️  SECURITY WARNING:</strong> Delete this file (copy-home-vue.php) immediately after use!</p>";

?>

</div>
</body>
</html>

