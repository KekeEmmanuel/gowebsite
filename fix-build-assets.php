<?php
/**
 * Fix Build Assets Access
 * Run this script via browser: https://www.gotzsafari.com/fix-build-assets.php
 * IMPORTANT: Delete this file after use!
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

$publicHtml = '/home/gotzsafari/public_html';
$laravelPath = '/home/gotzsafari/laravel';
$buildSource = $laravelPath . '/public/build';
$buildTarget = $publicHtml . '/build';

?>
<!DOCTYPE html>
<html>
<head>
    <title>Fix Build Assets</title>
    <style>
        body { font-family: monospace; padding: 20px; background: #1a1a1a; color: #fff; }
        .container { max-width: 900px; margin: 0 auto; }
        h1 { color: #4CAF50; }
        .info { background: #2a2a2a; padding: 15px; margin: 10px 0; border-left: 4px solid #4CAF50; }
        .error { background: #3a1a1a; padding: 15px; margin: 10px 0; border-left: 4px solid #f44336; }
        .success { color: #4CAF50; }
        .warning { color: #FFD700; }
        pre { background: #1a1a1a; padding: 10px; overflow-x: auto; border: 1px solid #444; }
    </style>
</head>
<body>
<div class="container">
    <h1>🔧 Fix Build Assets Access</h1>
    <hr>

<?php

echo "<div class='info'>";
echo "<h3>Checking build folder locations...</h3>";

// Check source build folder
if (is_dir($buildSource)) {
    echo "<span class='success'>✓ Build folder exists at: $buildSource</span><br>";
    $files = scandir($buildSource);
    $fileCount = count(array_filter($files, function($f) { return $f !== '.' && $f !== '..'; }));
    echo "Files in build folder: $fileCount<br>";
} else {
    echo "<span class='warning'>✗ Build folder does NOT exist at: $buildSource</span><br>";
}

// Check target build folder
if (is_dir($buildTarget)) {
    echo "<span class='success'>✓ Build folder exists at: $buildTarget</span><br>";
} else {
    echo "<span class='warning'>✗ Build folder does NOT exist at: $buildTarget</span><br>";
}

echo "</div>";

// Try to create symlink or copy
if (is_dir($buildSource) && !is_dir($buildTarget)) {
    echo "<div class='info'>";
    echo "<h3>Creating build folder access...</h3>";
    
    // Try symlink first
    if (symlink($buildSource, $buildTarget)) {
        echo "<span class='success'>✓ Created symlink: $buildTarget -> $buildSource</span><br>";
    } else {
        // If symlink fails, try copying
        echo "<span class='warning'>Symlink failed, trying to copy files...</span><br>";
        if (!is_dir($buildTarget)) {
            mkdir($buildTarget, 0755, true);
        }
        $copyCmd = "cp -r $buildSource/* $buildTarget/ 2>&1";
        exec($copyCmd, $copyOutput, $copyReturn);
        if ($copyReturn === 0) {
            echo "<span class='success'>✓ Copied build files to: $buildTarget</span><br>";
        } else {
            echo "<span class='warning'>✗ Copy failed: " . implode("\n", $copyOutput) . "</span><br>";
        }
    }
    echo "</div>";
} elseif (is_dir($buildTarget)) {
    echo "<div class='info'>";
    echo "<span class='success'>✓ Build folder already exists at target location</span><br>";
    echo "</div>";
}

// Verify manifest.json is accessible
$manifestPath = $buildTarget . '/manifest.json';
if (file_exists($manifestPath)) {
    echo "<div class='info'>";
    echo "<span class='success'>✓ manifest.json exists</span><br>";
    $manifest = json_decode(file_get_contents($manifestPath), true);
    if ($manifest) {
        echo "Manifest contains " . count($manifest) . " entries<br>";
    }
    echo "</div>";
} else {
    echo "<div class='error'>";
    echo "<span class='warning'>✗ manifest.json does NOT exist at: $manifestPath</span><br>";
    echo "</div>";
}

?>

    <hr>
    <p><strong>⚠️  SECURITY WARNING:</strong> Delete this file (fix-build-assets.php) immediately after use!</p>
</div>
</body>
</html>

