<?php
/**
 * Verify Build Setup
 * Upload to public_html and run: https://www.gotzsafari.com/verify-build.php
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

$homeDir = '/home/gotzsafari';
$publicHtmlPath = $homeDir . '/public_html';
$laravelPath = $homeDir . '/laravel';
$buildSymlink = $publicHtmlPath . '/build';
$buildTarget = $laravelPath . '/public/build';

?>
<!DOCTYPE html>
<html>
<head>
    <title>Verify Build Setup</title>
    <style>
        body { font-family: Arial; max-width: 900px; margin: 50px auto; padding: 20px; background: #1a1a1a; color: #fff; }
        .success { color: #4CAF50; padding: 10px; background: #2a2a2a; margin: 5px 0; border-left: 4px solid #4CAF50; }
        .error { color: #f44336; padding: 10px; background: #2a2a2a; margin: 5px 0; border-left: 4px solid #f44336; }
        .info { color: #2196F3; padding: 10px; background: #2a2a2a; margin: 5px 0; border-left: 4px solid #2196F3; }
        .warning { color: #FF9800; padding: 10px; background: #2a2a2a; margin: 5px 0; border-left: 4px solid #FF9800; }
        h1 { color: #4CAF50; }
        h2 { color: #4CAF50; margin-top: 30px; }
        pre { background: #000; padding: 10px; overflow-x: auto; font-size: 12px; }
        code { background: #2a2a2a; padding: 2px 6px; border-radius: 3px; }
    </style>
</head>
<body>
    <h1>✅ Verify Build Setup</h1>

<?php

// Check 1: Build symlink
echo "<h2>1. Build Symlink</h2>";
if (is_link($buildSymlink)) {
    $linkTarget = readlink($buildSymlink);
    $resolvedTarget = realpath($buildSymlink);
    
    echo "<div class='success'>✓ Build symlink exists: <code>$buildSymlink</code></div>";
    echo "<div class='info'>Link target: <code>$linkTarget</code></div>";
    
    if ($resolvedTarget && $resolvedTarget === $buildTarget) {
        echo "<div class='success'>✓ Symlink resolves to correct location: <code>$resolvedTarget</code></div>";
    } else {
        echo "<div class='warning'>⚠️ Symlink resolves to: <code>" . ($resolvedTarget ?: 'NOT FOUND') . "</code></div>";
        echo "<div class='info'>Expected: <code>$buildTarget</code></div>";
    }
} elseif (is_dir($buildSymlink)) {
    echo "<div class='warning'>⚠️ Build exists as directory (not symlink): <code>$buildSymlink</code></div>";
    echo "<div class='info'>This might work, but symlink is preferred</div>";
} else {
    echo "<div class='error'>❌ Build symlink NOT found: <code>$buildSymlink</code></div>";
}

// Check 2: Build target directory
echo "<h2>2. Build Target Directory</h2>";
if (is_dir($buildTarget)) {
    echo "<div class='success'>✓ Build directory exists: <code>$buildTarget</code></div>";
    
    // Check for manifest
    $manifest = $buildTarget . '/.vite/manifest.json';
    if (file_exists($manifest)) {
        echo "<div class='success'>✓ Vite manifest exists</div>";
        $manifestData = json_decode(file_get_contents($manifest), true);
        if ($manifestData) {
            $entryCount = count($manifestData);
            echo "<div class='info'>✓ Manifest contains $entryCount entries</div>";
            
            // Check for marketing entry
            $hasMarketing = false;
            foreach ($manifestData as $key => $value) {
                if (strpos($key, 'marketing') !== false || strpos($key, 'main.ts') !== false) {
                    $hasMarketing = true;
                    echo "<div class='success'>✓ Found marketing entry: <code>$key</code></div>";
                    if (isset($value['file'])) {
                        echo "<div class='info'>  → File: <code>" . $value['file'] . "</code></div>";
                    }
                    break;
                }
            }
            if (!$hasMarketing) {
                echo "<div class='warning'>⚠️ No marketing entry found in manifest</div>";
            }
        }
    } else {
        echo "<div class='error'>❌ Vite manifest NOT found: <code>$manifest</code></div>";
    }
    
    // List some files in build directory
    $buildFiles = glob($buildTarget . '/assets/*.{js,css}', GLOB_BRACE);
    if (count($buildFiles) > 0) {
        echo "<div class='success'>✓ Found " . count($buildFiles) . " asset files in build directory</div>";
        echo "<div class='info'>Sample files:</div>";
        echo "<pre>";
        foreach (array_slice($buildFiles, 0, 5) as $file) {
            echo basename($file) . "\n";
        }
        echo "</pre>";
    } else {
        echo "<div class='warning'>⚠️ No asset files found in build directory</div>";
    }
} else {
    echo "<div class='error'>❌ Build directory NOT found: <code>$buildTarget</code></div>";
    echo "<div class='warning'>⚠️ You need to run: <code>npm run build</code> in the Laravel directory</div>";
}

// Check 3: Test asset accessibility
echo "<h2>3. Asset Accessibility</h2>";
if (is_link($buildSymlink) || is_dir($buildSymlink)) {
    $testUrl = 'https://www.gotzsafari.com/build/.vite/manifest.json';
    echo "<div class='info'>Testing: <a href='$testUrl' target='_blank' style='color: #4CAF50;'>$testUrl</a></div>";
    
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
    } else {
        echo "<div class='error'>❌ Manifest returns HTTP $httpCode</div>";
    }
} else {
    echo "<div class='warning'>⚠️ Cannot test - build symlink/directory not found</div>";
}

// Check 4: Homepage test
echo "<h2>4. Homepage Test</h2>";
echo "<div class='info'>Try accessing: <a href='https://www.gotzsafari.com/' target='_blank' style='color: #4CAF50;'>https://www.gotzsafari.com/</a></div>";
echo "<div class='info'>Open browser console (F12) and check for:</div>";
echo "<ul>";
echo "<li>JavaScript errors</li>";
echo "<li>404 errors for <code>/build/assets/</code> files</li>";
echo "<li>Network tab showing failed asset requests</li>";
echo "</ul>";

// Summary
echo "<hr>";
echo "<h2>📋 Summary</h2>";
if (is_link($buildSymlink) && is_dir($buildTarget) && file_exists($buildTarget . '/.vite/manifest.json')) {
    echo "<div class='success'><strong>✅ Build setup looks good!</strong></div>";
    echo "<div class='info'>If homepage is still blank, check:</div>";
    echo "<ol>";
    echo "<li>Browser console for JavaScript errors</li>";
    echo "<li>Network tab for failed asset requests</li>";
    echo "<li>Laravel logs for server-side errors</li>";
    echo "</ol>";
} else {
    echo "<div class='warning'><strong>⚠️ Build setup needs attention</strong></div>";
    echo "<div class='info'>Fix the issues above, then test the homepage again.</div>";
}

?>

</body>
</html>
