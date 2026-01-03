<?php
/**
 * Quick script to copy updated TourPackageResource
 * Upload to public_html and run: https://www.gotzsafari.com/copy-resource.php
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);
set_time_limit(300);

$homeDir = '/home/gotzsafari';
$repoPath = $homeDir . '/repositories/gowebsitelaravel';
$laravelPath = $homeDir . '/laravel';
$phpPath = '/opt/cpanel/ea-php82/root/usr/bin/php';

$filesToCopy = [
    'app/Http/Resources/TourPackageResource.php',
];

?>
<!DOCTYPE html>
<html>
<head>
    <title>Copy Resource File</title>
    <style>
        body { font-family: Arial; max-width: 800px; margin: 50px auto; padding: 20px; background: #1a1a1a; color: #fff; }
        .success { color: #4CAF50; padding: 10px; background: #2a2a2a; margin: 5px 0; }
        .error { color: #f44336; padding: 10px; background: #2a2a2a; margin: 5px 0; }
        .info { color: #2196F3; padding: 10px; background: #2a2a2a; margin: 5px 0; }
        h1 { color: #4CAF50; }
    </style>
</head>
<body>
    <div class="info"><strong>Step 0:</strong> Pulling latest changes from Git</div>
<?php
// Step 0: Pull latest changes from Git
exec("cd $repoPath && git pull origin main 2>&1", $output, $return);
if ($return === 0) {
    echo "<div class='success'>✓ Pulled latest changes</div>";
} else {
    echo "<div class='error'>❌ Failed to pull latest changes: <pre>" . implode("\n", $output) . "</pre></div>";
}
?>
    <h1>📋 Copying Updated Resource File</h1>

<?php
foreach ($filesToCopy as $file) {
    $source = $repoPath . '/' . $file;
    $target = $laravelPath . '/' . $file;
    $targetDir = dirname($target);
    
    if (!file_exists($source)) {
        echo "<div class='error'>❌ Source not found: $file</div>";
        continue;
    }
    
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
    }
    
    // Backup existing file before copying
    if (file_exists($target)) {
        $backupPath = $target . '.backup.' . date('Y-m-d_H-i-s');
        copy($target, $backupPath);
    }

    if (copy($source, $target)) {
        chmod($target, 0644);
        echo "<div class='success'>✓ Copied: $file</div>";
    } else {
        echo "<div class='error'>❌ Failed to copy: $file</div>";
    }
}

// Clear all caches
echo "<div class='info'><strong>Step 2:</strong> Clearing all caches</div>";
$cacheCommands = [
    'config:clear',
    'cache:clear',
    'route:clear',
    'view:clear',
    'optimize:clear',
];

foreach ($cacheCommands as $command) {
    exec("$phpPath $laravelPath/artisan $command 2>&1", $output, $return);
    if ($return === 0) {
        echo "<div class='success'>✓ Cleared: $command</div>";
    } else {
        echo "<div class='error'>❌ Failed to clear $command: <pre>" . implode("\n", $output) . "</pre></div>";
    }
}

echo "<div class='success'><strong>✅ Done! Resource file updated. Images should now display correctly.</strong></div>";
?>

</body>
</html>

