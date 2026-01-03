<?php
/**
 * Fix storage symlink for media files
 * Upload to public_html and run: https://www.gotzsafari.com/fix-storage-link.php
 */

$homeDir = '/home/gotzsafari';
$laravelPath = $homeDir . '/laravel';
$publicHtml = $homeDir . '/public_html';

?>
<!DOCTYPE html>
<html>
<head>
    <title>Fix Storage Link</title>
    <style>
        body { font-family: Arial; max-width: 900px; margin: 50px auto; padding: 20px; background: #1a1a1a; color: #fff; }
        .success { color: #4CAF50; padding: 10px; background: #2a2a2a; margin: 5px 0; }
        .error { color: #f44336; padding: 10px; background: #2a2a2a; margin: 5px 0; }
        .info { color: #2196F3; padding: 10px; background: #2a2a2a; margin: 5px 0; }
        h1 { color: #4CAF50; }
    </style>
</head>
<body>
    <h1>🔗 Fix Storage Symlink</h1>

<?php

$storageLink = $publicHtml . '/storage';
$storageTarget = $laravelPath . '/storage/app/public';

echo "<div class='info'><strong>Step 1:</strong> Check current symlink</div>";

if (is_link($storageLink)) {
    $linkTarget = readlink($storageLink);
    echo "<div class='info'>Current symlink points to: $linkTarget</div>";
    
    if (realpath($storageLink) === realpath($storageTarget)) {
        echo "<div class='success'>✓ Symlink is correct!</div>";
    } else {
        echo "<div class='error'>❌ Symlink points to wrong location. Removing...</div>";
        if (unlink($storageLink)) {
            echo "<div class='success'>✓ Removed old symlink</div>";
        } else {
            echo "<div class='error'>❌ Failed to remove old symlink</div>";
            exit;
        }
    }
} else if (file_exists($storageLink)) {
    echo "<div class='error'>❌ Storage link exists but is not a symlink (it's a file/directory). Removing...</div>";
    if (is_dir($storageLink)) {
        // Try to remove directory (dangerous, but necessary)
        exec("rm -rf $storageLink 2>&1", $rmOutput, $rmReturn);
        if ($rmReturn === 0) {
            echo "<div class='success'>✓ Removed directory</div>";
        } else {
            echo "<div class='error'>❌ Failed to remove directory. Output: " . implode("\n", $rmOutput) . "</div>";
            exit;
        }
    } else {
        unlink($storageLink);
    }
} else {
    echo "<div class='info'>No existing symlink found</div>";
}

// Step 2: Ensure target directory exists
echo "<div class='info'><strong>Step 2:</strong> Ensure target directory exists</div>";
if (!is_dir($storageTarget)) {
    mkdir($storageTarget, 0755, true);
    echo "<div class='success'>✓ Created target directory</div>";
} else {
    echo "<div class='success'>✓ Target directory exists</div>";
}

// Step 3: Create symlink
echo "<div class='info'><strong>Step 3:</strong> Creating symlink</div>";
if (symlink($storageTarget, $storageLink)) {
    echo "<div class='success'>✓ Symlink created successfully!</div>";
    echo "<div class='info'>Link: $storageLink → $storageTarget</div>";
} else {
    echo "<div class='error'>❌ Failed to create symlink</div>";
    echo "<div class='info'>Trying alternative method via artisan...</div>";
    
    // Try via artisan
    $phpPath = '/opt/cpanel/ea-php82/root/usr/bin/php';
    if (file_exists($phpPath)) {
        chdir($laravelPath);
        exec("$phpPath artisan storage:link 2>&1", $artisanOutput, $artisanReturn);
        if ($artisanReturn === 0) {
            echo "<div class='success'>✓ Symlink created via artisan!</div>";
        } else {
            echo "<div class='error'>❌ Artisan also failed</div>";
            echo "<pre>" . implode("\n", $artisanOutput) . "</pre>";
        }
    }
}

// Step 4: Verify
echo "<div class='info'><strong>Step 4:</strong> Verifying symlink</div>";
if (is_link($storageLink)) {
    $linkTarget = readlink($storageLink);
    if (realpath($storageLink) === realpath($storageTarget)) {
        echo "<div class='success'>✓ Symlink verified and working!</div>";
        
        // Test file access
        $testFile = $storageTarget . '/.gitkeep';
        if (file_exists($testFile)) {
            $webPath = '/storage/.gitkeep';
            $fullWebPath = $publicHtml . $webPath;
            if (file_exists($fullWebPath) || is_link($fullWebPath)) {
                echo "<div class='success'>✓ Files are accessible via web!</div>";
            } else {
                echo "<div class='error'>❌ Files still not accessible via web</div>";
            }
        }
    } else {
        echo "<div class='error'>❌ Symlink verification failed</div>";
    }
} else {
    echo "<div class='error'>❌ Symlink was not created</div>";
}

echo "<div class='success'><strong>✅ Done! Try accessing your images now.</strong></div>";
?>

</body>
</html>

