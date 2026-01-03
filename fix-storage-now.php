<?php
/**
 * Fix storage symlink - Self-contained
 * Upload to public_html and run: https://www.gotzsafari.com/fix-storage-now.php
 */

$homeDir = '/home/gotzsafari';
$laravelPath = $homeDir . '/laravel';
$publicHtml = $homeDir . '/public_html';

$storageLink = $publicHtml . '/storage';
$storageTarget = $laravelPath . '/storage/app/public';

?>
<!DOCTYPE html>
<html>
<head>
    <title>Fix Storage Link</title>
    <style>
        body { font-family: Arial; max-width: 800px; margin: 50px auto; padding: 20px; background: #1a1a1a; color: #fff; }
        .success { color: #4CAF50; padding: 10px; background: #2a2a2a; margin: 5px 0; border-left: 4px solid #4CAF50; }
        .error { color: #f44336; padding: 10px; background: #2a2a2a; margin: 5px 0; border-left: 4px solid #f44336; }
        .info { color: #2196F3; padding: 10px; background: #2a2a2a; margin: 5px 0; border-left: 4px solid #2196F3; }
        .warning { color: #FFD700; padding: 10px; background: #2a2a2a; margin: 5px 0; border-left: 4px solid #FFD700; }
        h1 { color: #4CAF50; }
        pre { background: #000; padding: 10px; overflow-x: auto; font-size: 12px; }
    </style>
</head>
<body>
    <h1>🔗 Fix Storage Symlink</h1>

<?php

echo "<div class='info'><strong>Step 1:</strong> Checking current state</div>";

// Check if storage link exists
if (is_link($storageLink)) {
    $linkTarget = readlink($storageLink);
    echo "<div class='info'>Current symlink points to: <code>$linkTarget</code></div>";
    
    if (realpath($storageLink) === realpath($storageTarget)) {
        echo "<div class='success'>✓ Symlink is already correct!</div>";
    } else {
        echo "<div class='warning'>⚠ Symlink points to wrong location. Will fix...</div>";
        unlink($storageLink);
    }
} else if (file_exists($storageLink)) {
    echo "<div class='warning'>⚠ Storage link exists but is not a symlink (it's a file/directory)</div>";
    if (is_dir($storageLink)) {
        echo "<div class='info'>Removing directory...</div>";
        exec("rm -rf $storageLink 2>&1", $rmOutput, $rmReturn);
        if ($rmReturn === 0) {
            echo "<div class='success'>✓ Directory removed</div>";
        } else {
            echo "<div class='error'>❌ Failed to remove directory</div>";
            echo "<pre>" . implode("\n", $rmOutput) . "</pre>";
        }
    } else {
        unlink($storageLink);
        echo "<div class='success'>✓ File removed</div>";
    }
} else {
    echo "<div class='info'>No existing storage link found</div>";
}

echo "<div class='info'><strong>Step 2:</strong> Ensuring target directory exists</div>";
if (!is_dir($storageTarget)) {
    if (mkdir($storageTarget, 0755, true)) {
        echo "<div class='success'>✓ Created target directory: $storageTarget</div>";
    } else {
        echo "<div class='error'>❌ Failed to create target directory</div>";
    }
} else {
    echo "<div class='success'>✓ Target directory exists: $storageTarget</div>";
}

echo "<div class='info'><strong>Step 3:</strong> Creating symlink</div>";
if (symlink($storageTarget, $storageLink)) {
    echo "<div class='success'>✓ Symlink created successfully!</div>";
    echo "<div class='info'>Link: <code>$storageLink</code> → <code>$storageTarget</code></div>";
} else {
    echo "<div class='error'>❌ Failed to create symlink directly</div>";
    echo "<div class='info'>Trying via artisan command...</div>";
    
    $phpPath = '/opt/cpanel/ea-php82/root/usr/bin/php';
    if (file_exists($phpPath)) {
        chdir($laravelPath);
        exec("$phpPath artisan storage:link 2>&1", $artisanOutput, $artisanReturn);
        if ($artisanReturn === 0) {
            echo "<div class='success'>✓ Symlink created via artisan!</div>";
            echo "<pre>" . implode("\n", $artisanOutput) . "</pre>";
        } else {
            echo "<div class='error'>❌ Artisan command also failed</div>";
            echo "<pre>" . implode("\n", $artisanOutput) . "</pre>";
        }
    } else {
        echo "<div class='error'>❌ PHP executable not found at: $phpPath</div>";
    }
}

echo "<div class='info'><strong>Step 4:</strong> Verifying symlink</div>";
if (is_link($storageLink)) {
    $finalTarget = readlink($storageLink);
    $finalRealPath = realpath($storageLink);
    $targetRealPath = realpath($storageTarget);
    
    echo "<div class='info'>Symlink target: <code>$finalTarget</code></div>";
    echo "<div class='info'>Symlink real path: <code>$finalRealPath</code></div>";
    echo "<div class='info'>Target real path: <code>$targetRealPath</code></div>";
    
    if ($finalRealPath === $targetRealPath) {
        echo "<div class='success'><strong>✅ Symlink verified and working correctly!</strong></div>";
        
        // Test file access
        $testFile = $storageTarget . '/.gitkeep';
        if (file_exists($testFile)) {
            $webPath = '/storage/.gitkeep';
            $fullWebPath = $publicHtml . $webPath;
            if (file_exists($fullWebPath) || is_link($fullWebPath)) {
                echo "<div class='success'>✓ Files are accessible via web at: <code>$webPath</code></div>";
            } else {
                echo "<div class='warning'>⚠ Files may not be accessible via web yet</div>";
            }
        }
    } else {
        echo "<div class='error'>❌ Symlink verification failed - paths don't match</div>";
    }
} else {
    echo "<div class='error'>❌ Symlink was not created</div>";
}

echo "<div class='success'><strong>✅ Done! Check the results above. If symlink is working, your images should now display.</strong></div>";
?>

</body>
</html>

