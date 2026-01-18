<?php
/**
 * Diagnose Build Issue
 * Create this file in public_html, then visit: https://www.gotzsafari.com/diagnose-build.php
 */

$publicHtml = '/home/gotzsafari/public_html';
$buildTarget = $publicHtml . '/build';
$buildSource = '/home/gotzsafari/laravel/public/build';

?>
<!DOCTYPE html>
<html>
<head>
    <title>Diagnose Build</title>
    <style>
        body { font-family: Arial; max-width: 900px; margin: 50px auto; padding: 20px; background: #1a1a1a; color: #fff; }
        .success { color: #4CAF50; padding: 10px; background: #2a2a2a; margin: 5px 0; border-left: 4px solid #4CAF50; }
        .error { color: #f44336; padding: 10px; background: #2a2a2a; margin: 5px 0; border-left: 4px solid #f44336; }
        .info { color: #2196F3; padding: 10px; background: #2a2a2a; margin: 5px 0; border-left: 4px solid #2196F3; }
        .warning { color: #FF9800; padding: 10px; background: #2a2a2a; margin: 5px 0; border-left: 4px solid #FF9800; }
        h1 { color: #4CAF50; }
        h2 { color: #4CAF50; margin-top: 30px; }
        pre { background: #000; padding: 10px; overflow-x: auto; font-size: 12px; }
    </style>
</head>
<body>
    <h1>🔍 Diagnose Build Issue</h1>

<?php

// Check 1: Build symlink
echo "<h2>1. Build Symlink</h2>";
if (is_link($buildTarget)) {
    $linkTarget = readlink($buildTarget);
    $resolved = realpath($buildTarget);
    echo "<div class='success'>✓ Symlink exists</div>";
    echo "<div class='info'>Link target: <code>$linkTarget</code></div>";
    echo "<div class='info'>Resolved path: <code>" . ($resolved ?: 'NOT FOUND') . "</code></div>";
    
    if ($resolved && $resolved === $buildSource) {
        echo "<div class='success'>✓ Symlink resolves correctly</div>";
    } else {
        echo "<div class='error'>❌ Symlink doesn't resolve to expected path</div>";
        echo "<div class='info'>Expected: <code>$buildSource</code></div>";
    }
} elseif (is_dir($buildTarget)) {
    echo "<div class='warning'>⚠️ Build exists as directory (not symlink)</div>";
} else {
    echo "<div class='error'>❌ Build symlink/directory NOT found</div>";
}

// Check 2: Build source directory
echo "<h2>2. Build Source Directory</h2>";
if (is_dir($buildSource)) {
    echo "<div class='success'>✓ Build directory exists: <code>$buildSource</code></div>";
    
    // List contents
    $contents = scandir($buildSource);
    $files = array_filter($contents, function($f) { return $f !== '.' && $f !== '..'; });
    echo "<div class='info'>Contents: " . implode(', ', array_slice($files, 0, 10)) . "</div>";
    
    // Check for .vite directory
    $viteDir = $buildSource . '/.vite';
    if (is_dir($viteDir)) {
        echo "<div class='success'>✓ .vite directory exists</div>";
        
        // Check for manifest
        $manifest = $viteDir . '/manifest.json';
        if (file_exists($manifest)) {
            echo "<div class='success'>✓ manifest.json exists</div>";
            $manifestSize = filesize($manifest);
            echo "<div class='info'>File size: " . number_format($manifestSize) . " bytes</div>";
            
            // Try to read it
            $manifestContent = file_get_contents($manifest);
            $manifestData = json_decode($manifestContent, true);
            if ($manifestData) {
                echo "<div class='success'>✓ Manifest is valid JSON with " . count($manifestData) . " entries</div>";
            } else {
                echo "<div class='error'>❌ Manifest is not valid JSON</div>";
            }
        } else {
            echo "<div class='error'>❌ manifest.json NOT found in .vite directory</div>";
        }
    } else {
        echo "<div class='error'>❌ .vite directory NOT found</div>";
        echo "<div class='warning'>⚠️ You may need to run: <code>npm run build</code></div>";
    }
} else {
    echo "<div class='error'>❌ Build directory does NOT exist: <code>$buildSource</code></div>";
    echo "<div class='warning'>⚠️ You need to build the frontend assets first</div>";
}

// Check 3: Permissions
echo "<h2>3. Permissions</h2>";
if (is_dir($buildSource)) {
    $perms = substr(sprintf('%o', fileperms($buildSource)), -4);
    echo "<div class='info'>Build directory permissions: <code>$perms</code></div>";
    
    if (is_link($buildTarget)) {
        $linkPerms = substr(sprintf('%o', fileperms($buildTarget)), -4);
        echo "<div class='info'>Symlink permissions: <code>$linkPerms</code></div>";
    }
}

// Check 4: .htaccess FollowSymLinks
echo "<h2>4. Apache Configuration</h2>";
$htaccess = $publicHtml . '/.htaccess';
if (file_exists($htaccess)) {
    $htaccessContent = file_get_contents($htaccess);
    if (strpos($htaccessContent, 'FollowSymLinks') !== false || strpos($htaccessContent, 'Options') !== false) {
        echo "<div class='success'>✓ .htaccess contains Options directive</div>";
        if (strpos($htaccessContent, 'FollowSymLinks') !== false) {
            echo "<div class='success'>✓ FollowSymLinks is enabled</div>";
        } else {
            echo "<div class='warning'>⚠️ FollowSymLinks might not be enabled</div>";
        }
    } else {
        echo "<div class='warning'>⚠️ .htaccess might need FollowSymLinks</div>";
    }
} else {
    echo "<div class='error'>❌ .htaccess not found</div>";
}

// Check 5: Test file access via PHP
echo "<h2>5. File Access Test</h2>";
if (is_link($buildTarget)) {
    $manifest = $buildTarget . '/.vite/manifest.json';
    if (file_exists($manifest)) {
        echo "<div class='success'>✓ PHP can access manifest via symlink</div>";
        echo "<div class='info'>Path: <code>$manifest</code></div>";
    } else {
        echo "<div class='error'>❌ PHP cannot access manifest via symlink</div>";
        echo "<div class='info'>Tried: <code>$manifest</code></div>";
        
        // Try direct path
        $directManifest = $buildSource . '/.vite/manifest.json';
        if (file_exists($directManifest)) {
            echo "<div class='success'>✓ But direct path works: <code>$directManifest</code></div>";
            echo "<div class='warning'>⚠️ This means the symlink is broken or Apache can't follow it</div>";
        }
    }
}

// Recommendations
echo "<hr>";
echo "<h2>📋 Recommendations</h2>";
echo "<div class='info'>";

if (!is_dir($buildSource)) {
    echo "<p><strong>1. Build the frontend assets:</strong></p>";
    echo "<pre>cd /home/gotzsafari/laravel\nnpm run build</pre>";
} elseif (!file_exists($buildSource . '/.vite/manifest.json')) {
    echo "<p><strong>1. Rebuild the frontend assets:</strong></p>";
    echo "<pre>cd /home/gotzsafari/laravel\nnpm run build</pre>";
} elseif (!is_link($buildTarget) || !file_exists($buildTarget . '/.vite/manifest.json')) {
    echo "<p><strong>1. Recreate the symlink:</strong></p>";
    echo "<pre>cd /home/gotzsafari/public_html\nrm -rf build\nln -s ../laravel/public/build build</pre>";
    echo "<p><strong>2. Or ensure .htaccess allows symlinks:</strong></p>";
    echo "<pre>Add to .htaccess:\nOptions +FollowSymLinks</pre>";
}

echo "</div>";

?>

</body>
</html>
