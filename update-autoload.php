<?php
/**
 * Update Composer autoloader to include mime-polyfill
 * Upload to public_html and run: https://www.gotzsafari.com/update-autoload.php
 */

$homeDir = '/home/gotzsafari';
$laravelPath = $homeDir . '/laravel';
$repoPath = $homeDir . '/repositories/gowebsitelaravel';

?>
<!DOCTYPE html>
<html>
<head>
    <title>Update Autoloader</title>
    <style>
        body { font-family: Arial; max-width: 800px; margin: 50px auto; padding: 20px; background: #1a1a1a; color: #fff; }
        .success { color: #4CAF50; padding: 10px; background: #2a2a2a; margin: 5px 0; }
        .error { color: #f44336; padding: 10px; background: #2a2a2a; margin: 5px 0; }
        .info { color: #2196F3; padding: 10px; background: #2a2a2a; margin: 5px 0; }
        h1 { color: #4CAF50; }
        pre { background: #000; padding: 10px; overflow-x: auto; }
    </style>
</head>
<body>
    <h1>🔄 Updating Composer Autoloader</h1>

<?php

// Step 1: Copy updated composer.json
echo "<div class='info'><strong>Step 1:</strong> Copying updated composer.json</div>";
$composerJsonSource = $repoPath . '/composer.json';
$composerJsonTarget = $laravelPath . '/composer.json';

if (file_exists($composerJsonSource)) {
    if (copy($composerJsonSource, $composerJsonTarget)) {
        echo "<div class='success'>✓ Copied composer.json</div>";
    } else {
        echo "<div class='error'>❌ Failed to copy composer.json</div>";
        exit;
    }
} else {
    echo "<div class='error'>❌ composer.json not found in repository</div>";
    exit;
}

// Step 2: Run composer dump-autoload
echo "<div class='info'><strong>Step 2:</strong> Regenerating autoloader</div>";
$phpPath = '/opt/cpanel/ea-php82/root/usr/bin/php';
$composerPath = $homeDir . '/bin/composer';

// Try to find composer
if (!file_exists($composerPath)) {
    $composerPath = 'composer'; // Try system composer
}

$command = "cd $laravelPath && $phpPath $composerPath dump-autoload 2>&1";
exec($command, $output, $return);

if ($return === 0) {
    echo "<div class='success'>✓ Autoloader regenerated successfully</div>";
    echo "<pre>" . implode("\n", $output) . "</pre>";
} else {
    echo "<div class='error'>❌ Failed to regenerate autoloader</div>";
    echo "<pre>" . implode("\n", $output) . "</pre>";
    echo "<div class='info'>Trying alternative method...</div>";
    
    // Try with system composer
    $command2 = "cd $laravelPath && composer dump-autoload 2>&1";
    exec($command2, $output2, $return2);
    if ($return2 === 0) {
        echo "<div class='success'>✓ Autoloader regenerated with system composer</div>";
        echo "<pre>" . implode("\n", $output2) . "</pre>";
    } else {
        echo "<div class='error'>❌ Both methods failed</div>";
        echo "<pre>" . implode("\n", $output2) . "</pre>";
    }
}

// Step 3: Verify polyfill is loaded
echo "<div class='info'><strong>Step 3:</strong> Verifying polyfill</div>";
$polyfillPath = $laravelPath . '/bootstrap/mime-polyfill.php';
if (file_exists($polyfillPath)) {
    echo "<div class='success'>✓ Polyfill file exists</div>";
    
    // Check if it's in autoload files
    $autoloadFiles = $laravelPath . '/vendor/composer/autoload_files.php';
    if (file_exists($autoloadFiles)) {
        $content = file_get_contents($autoloadFiles);
        if (strpos($content, 'mime-polyfill') !== false) {
            echo "<div class='success'>✓ Polyfill is in autoload files</div>";
        } else {
            echo "<div class='error'>❌ Polyfill NOT in autoload files - composer dump-autoload may have failed</div>";
        }
    }
} else {
    echo "<div class='error'>❌ Polyfill file not found</div>";
}

echo "<div class='success'><strong>✅ Done! The polyfill should now load automatically.</strong></div>";
echo "<div class='info'>Try uploading an image again to test.</div>";
?>

</body>
</html>

