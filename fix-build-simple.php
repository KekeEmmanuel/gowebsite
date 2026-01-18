<?php
/**
 * Fix Build Symlink - Simple Version
 * Create this file in public_html via cPanel File Manager, then visit: https://www.gotzsafari.com/fix-build-simple.php
 */

$publicHtml = '/home/gotzsafari/public_html';
$buildTarget = $publicHtml . '/build';
$buildSource = '/home/gotzsafari/laravel/public/build';

?>
<!DOCTYPE html>
<html>
<head>
    <title>Fix Build Symlink</title>
    <style>
        body { font-family: Arial; max-width: 800px; margin: 50px auto; padding: 20px; background: #1a1a1a; color: #fff; }
        .success { color: #4CAF50; padding: 10px; background: #2a2a2a; margin: 5px 0; border-left: 4px solid #4CAF50; }
        .error { color: #f44336; padding: 10px; background: #2a2a2a; margin: 5px 0; border-left: 4px solid #f44336; }
        .info { color: #2196F3; padding: 10px; background: #2a2a2a; margin: 5px 0; border-left: 4px solid #2196F3; }
        h1 { color: #4CAF50; }
    </style>
</head>
<body>
    <h1>🔧 Fix Build Symlink</h1>

<?php

// Step 1: Remove existing build
if (file_exists($buildTarget) || is_link($buildTarget)) {
    if (is_link($buildTarget)) {
        unlink($buildTarget);
        echo "<div class='success'>✓ Removed existing symlink</div>";
    } else {
        exec("rm -rf $buildTarget 2>&1", $output, $return);
        if ($return === 0) {
            echo "<div class='success'>✓ Removed existing build directory</div>";
        } else {
            echo "<div class='error'>❌ Failed to remove: " . implode("\n", $output) . "</div>";
        }
    }
} else {
    echo "<div class='info'>No existing build found</div>";
}

// Step 2: Create symlink
$relativePath = '../laravel/public/build';
if (symlink($relativePath, $buildTarget)) {
    echo "<div class='success'>✓ Symlink created: $buildTarget → $relativePath</div>";
} else {
    // Try absolute path
    if (symlink($buildSource, $buildTarget)) {
        echo "<div class='success'>✓ Symlink created (absolute): $buildTarget → $buildSource</div>";
    } else {
        echo "<div class='error'>❌ Failed to create symlink</div>";
        $error = error_get_last();
        if ($error) {
            echo "<div class='error'>Error: " . $error['message'] . "</div>";
        }
    }
}

// Step 3: Verify
if (is_link($buildTarget)) {
    $linkTarget = readlink($buildTarget);
    $resolved = realpath($buildTarget);
    echo "<div class='success'>✓ Symlink verified</div>";
    echo "<div class='info'>Link: $buildTarget → $linkTarget</div>";
    echo "<div class='info'>Resolved: " . ($resolved ?: 'NOT FOUND') . "</div>";
    
    // Test manifest
    $manifest = $buildTarget . '/.vite/manifest.json';
    if (file_exists($manifest)) {
        echo "<div class='success'>✓ Manifest is accessible!</div>";
        echo "<div class='success'><strong>✅ Done! Test: <a href='https://www.gotzsafari.com/build/.vite/manifest.json' target='_blank' style='color: #4CAF50;'>https://www.gotzsafari.com/build/.vite/manifest.json</a></strong></div>";
    } else {
        echo "<div class='error'>❌ Manifest still not found. Check if build directory exists: $buildSource</div>";
    }
} else {
    echo "<div class='error'>❌ Symlink was not created</div>";
}

?>

</body>
</html>
