<?php
/**
 * Fix Build Symlink
 * Upload to public_html and run: https://www.gotzsafari.com/fix-build-symlink.php
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
    <title>Fix Build Symlink</title>
    <style>
        body { font-family: Arial; max-width: 800px; margin: 50px auto; padding: 20px; background: #1a1a1a; color: #fff; }
        .success { color: #4CAF50; padding: 10px; background: #2a2a2a; margin: 5px 0; border-left: 4px solid #4CAF50; }
        .error { color: #f44336; padding: 10px; background: #2a2a2a; margin: 5px 0; border-left: 4px solid #f44336; }
        .info { color: #2196F3; padding: 10px; background: #2a2a2a; margin: 5px 0; border-left: 4px solid #2196F3; }
        .warning { color: #FF9800; padding: 10px; background: #2a2a2a; margin: 5px 0; border-left: 4px solid #FF9800; }
        h1 { color: #4CAF50; }
        pre { background: #000; padding: 10px; overflow-x: auto; }
    </style>
</head>
<body>
    <h1>🔧 Fix Build Symlink</h1>

<?php

// Step 1: Check source
echo "<div class='info'><strong>Step 1:</strong> Checking build source directory</div>";
if (is_dir($buildSource)) {
    echo "<div class='success'>✓ Build source exists: $buildSource</div>";
    
    // Check for manifest
    $manifest = $buildSource . '/.vite/manifest.json';
    if (file_exists($manifest)) {
        echo "<div class='success'>✓ Vite manifest exists</div>";
    } else {
        echo "<div class='error'>❌ Vite manifest NOT found: $manifest</div>";
        echo "<div class='warning'>⚠️ You may need to run: npm run build</div>";
    }
} else {
    echo "<div class='error'>❌ Build source NOT found: $buildSource</div>";
    echo "<div class='warning'>⚠️ You need to build the frontend assets first</div>";
    exit;
}

// Step 2: Remove existing build (if broken)
echo "<div class='info'><strong>Step 2:</strong> Removing existing build symlink/directory</div>";
if (file_exists($buildTarget) || is_link($buildTarget)) {
    if (is_link($buildTarget)) {
        $linkTarget = readlink($buildTarget);
        echo "<div class='info'>Found existing symlink pointing to: $linkTarget</div>";
    }
    if (unlink($buildTarget) || rmdir($buildTarget)) {
        echo "<div class='success'>✓ Removed existing build</div>";
    } else {
        // Try recursive delete if it's a directory
        exec("rm -rf $buildTarget 2>&1", $output, $return);
        if ($return === 0) {
            echo "<div class='success'>✓ Removed existing build directory</div>";
        } else {
            echo "<div class='error'>❌ Failed to remove: " . implode("\n", $output) . "</div>";
        }
    }
} else {
    echo "<div class='info'>No existing build found (this is OK)</div>";
}

// Step 3: Create symlink
echo "<div class='info'><strong>Step 3:</strong> Creating symlink</div>";
$relativePath = '../laravel/public/build';
if (symlink($relativePath, $buildTarget)) {
    echo "<div class='success'>✓ Symlink created: $buildTarget → $relativePath</div>";
} else {
    // Try absolute path
    if (symlink($buildSource, $buildTarget)) {
        echo "<div class='success'>✓ Symlink created (absolute): $buildTarget → $buildSource</div>";
    } else {
        echo "<div class='error'>❌ Failed to create symlink</div>";
        echo "<div class='warning'>Error: " . error_get_last()['message'] . "</div>";
    }
}

// Step 4: Verify symlink
echo "<div class='info'><strong>Step 4:</strong> Verifying symlink</div>";
if (is_link($buildTarget)) {
    $linkTarget = readlink($buildTarget);
    $resolved = realpath($buildTarget);
    echo "<div class='success'>✓ Symlink exists</div>";
    echo "<div class='info'>Link target: $linkTarget</div>";
    echo "<div class='info'>Resolved path: " . ($resolved ?: 'NOT FOUND') . "</div>";
    
    if ($resolved === $buildSource) {
        echo "<div class='success'>✓ Symlink resolves correctly!</div>";
    } else {
        echo "<div class='warning'>⚠️ Symlink doesn't resolve to expected path</div>";
    }
    
    // Test manifest accessibility
    $manifest = $buildTarget . '/.vite/manifest.json';
    if (file_exists($manifest)) {
        echo "<div class='success'>✓ Manifest is accessible via symlink</div>";
    } else {
        echo "<div class='error'>❌ Manifest still not accessible</div>";
    }
} else {
    echo "<div class='error'>❌ Symlink was not created or is broken</div>";
}

// Step 5: Test web accessibility
echo "<div class='info'><strong>Step 5:</strong> Testing web accessibility</div>";
$testUrl = 'https://www.gotzsafari.com/build/.vite/manifest.json';
echo "<div class='info'>Test URL: <a href='$testUrl' target='_blank' style='color: #4CAF50;'>$testUrl</a></div>";

$ch = curl_init($testUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_NOBODY, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200) {
    echo "<div class='success'>✓ Manifest is accessible via web (HTTP 200)</div>";
    echo "<div class='success'><strong>✅ Build symlink is working! Homepage should load now.</strong></div>";
} else {
    echo "<div class='error'>❌ Manifest returns HTTP $httpCode</div>";
    echo "<div class='warning'>⚠️ Check Apache configuration or file permissions</div>";
}

?>

</body>
</html>
