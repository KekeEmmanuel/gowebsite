<?php
/**
 * Fix storage symlink - Simple version
 * Upload to public_html and run: https://www.gotzsafari.com/fix-storage.php
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
        .success { color: #4CAF50; padding: 10px; background: #2a2a2a; margin: 5px 0; }
        .error { color: #f44336; padding: 10px; background: #2a2a2a; margin: 5px 0; }
        .info { color: #2196F3; padding: 10px; background: #2a2a2a; margin: 5px 0; }
        h1 { color: #4CAF50; }
    </style>
</head>
<body>
    <h1>🔗 Fix Storage Symlink</h1>

<?php

// Remove existing link/file/directory
if (file_exists($storageLink)) {
    if (is_link($storageLink)) {
        unlink($storageLink);
        echo "<div class='info'>Removed existing symlink</div>";
    } else if (is_dir($storageLink)) {
        exec("rm -rf $storageLink 2>&1", $output, $return);
        echo "<div class='info'>Removed existing directory</div>";
    } else {
        unlink($storageLink);
        echo "<div class='info'>Removed existing file</div>";
    }
}

// Ensure target exists
if (!is_dir($storageTarget)) {
    mkdir($storageTarget, 0755, true);
    echo "<div class='info'>Created target directory</div>";
}

// Create symlink
if (symlink($storageTarget, $storageLink)) {
    echo "<div class='success'>✓ Storage symlink created successfully!</div>";
    echo "<div class='info'>Link: $storageLink → $storageTarget</div>";
} else {
    echo "<div class='error'>❌ Failed to create symlink directly</div>";
    echo "<div class='info'>Trying via artisan...</div>";
    
    $phpPath = '/opt/cpanel/ea-php82/root/usr/bin/php';
    chdir($laravelPath);
    exec("$phpPath artisan storage:link 2>&1", $output, $return);
    if ($return === 0) {
        echo "<div class='success'>✓ Storage symlink created via artisan!</div>";
    } else {
        echo "<div class='error'>❌ Both methods failed</div>";
        echo "<pre>" . implode("\n", $output) . "</pre>";
    }
}

// Verify
if (is_link($storageLink) && realpath($storageLink) === realpath($storageTarget)) {
    echo "<div class='success'><strong>✅ Storage symlink is working! Images should now display.</strong></div>";
} else {
    echo "<div class='error'><strong>❌ Storage symlink verification failed</strong></div>";
}

?>

</body>
</html>

