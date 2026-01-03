<?php
/**
 * Clear Laravel Caches
 * 
 * Upload this file to public_html and run it via browser:
 * https://www.gotzsafari.com/clear-cache.php
 * 
 * IMPORTANT: Delete this file after use!
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

$laravelPath = '/home/gotzsafari/laravel';

?>
<!DOCTYPE html>
<html>
<head>
    <title>Clear Laravel Caches</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; background: #1a1a1a; color: #fff; }
        .success { color: #4CAF50; padding: 15px; background: #2a2a2a; border-left: 4px solid #4CAF50; margin: 10px 0; }
        .error { color: #f44336; padding: 15px; background: #2a2a2a; border-left: 4px solid #f44336; margin: 10px 0; }
        .info { color: #2196F3; padding: 15px; background: #2a2a2a; border-left: 4px solid #2196F3; margin: 10px 0; }
        pre { background: #1a1a1a; padding: 10px; overflow-x: auto; font-size: 12px; border: 1px solid #444; }
        h1 { color: #4CAF50; }
        code { background: #2a2a2a; padding: 2px 6px; border-radius: 3px; }
    </style>
</head>
<body>
    <h1>🧹 Clear Laravel Caches</h1>
    <p>Clearing Laravel application caches...</p>
    <hr>

<?php

function logOutput($message, $type = 'info') {
    $class = $type === 'error' ? 'error' : ($type === 'success' ? 'success' : 'info');
    echo "<div class='$class'>$message</div>";
    flush();
}

// Check if Laravel directory exists
if (!is_dir($laravelPath)) {
    logOutput("❌ Laravel directory not found: $laravelPath", 'error');
    exit;
}

logOutput("✓ Laravel directory found: $laravelPath", 'success');

// Find PHP executable
$phpPath = '';
$phpPaths = [
    '/opt/cpanel/ea-php82/root/usr/bin/php',
    '/opt/cpanel/ea-php81/root/usr/bin/php',
    '/opt/cpanel/ea-php83/root/usr/bin/php',
    '/usr/local/bin/php',
    '/usr/bin/php',
    'php',
];

foreach ($phpPaths as $path) {
    if ($path === 'php' || file_exists($path)) {
        $phpPath = $path;
        break;
    }
}

if (empty($phpPath)) {
    logOutput("❌ PHP executable not found", 'error');
    exit;
}

logOutput("✓ PHP executable found: <code>$phpPath</code>", 'success');
logOutput("ℹ PHP version: " . shell_exec("$phpPath -v | head -1"), 'info');

// Commands to run
$commands = [
    'config:clear' => 'Clearing configuration cache',
    'cache:clear' => 'Clearing application cache',
    'route:clear' => 'Clearing route cache',
    'view:clear' => 'Clearing view cache',
    'optimize:clear' => 'Clearing all optimized caches',
];

$results = [];

foreach ($commands as $command => $description) {
    logOutput("🔄 $description...", 'info');
    
    $fullCommand = "cd $laravelPath && $phpPath artisan $command 2>&1";
    $output = [];
    $returnVar = 0;
    exec($fullCommand, $output, $returnVar);
    
    if ($returnVar === 0) {
        logOutput("✓ $description completed", 'success');
        $results[$command] = ['status' => 'success', 'output' => $output];
    } else {
        logOutput("❌ $description failed (exit code: $returnVar)", 'error');
        $results[$command] = ['status' => 'error', 'output' => $output, 'code' => $returnVar];
    }
    
    // Show output if available
    if (!empty($output)) {
        $outputText = implode("\n", $output);
        if (strlen($outputText) < 500) {
            echo "<pre style='background: #2a2a2a; padding: 10px; margin: 5px 0; font-size: 11px;'>" . htmlspecialchars($outputText) . "</pre>";
        }
    }
}

// Summary
echo "<hr>";
echo "<h2>📋 Summary</h2>";

$successCount = 0;
$errorCount = 0;

foreach ($results as $command => $result) {
    if ($result['status'] === 'success') {
        $successCount++;
        echo "<div class='success'>✓ <code>php artisan $command</code> - Success</div>";
    } else {
        $errorCount++;
        echo "<div class='error'>❌ <code>php artisan $command</code> - Failed (exit code: {$result['code']})</div>";
    }
}

echo "<div class='info'>";
echo "<p><strong>Results:</strong> $successCount successful, $errorCount failed</p>";
echo "</div>";

// Check if caches were cleared
$cachePath = $laravelPath . '/bootstrap/cache';
if (is_dir($cachePath)) {
    $cacheFiles = glob($cachePath . '/*.php');
    $cacheCount = count($cacheFiles);
    if ($cacheCount > 0) {
        logOutput("ℹ Remaining cache files: $cacheCount", 'info');
        echo "<pre style='font-size: 11px;'>";
        foreach ($cacheFiles as $file) {
            echo htmlspecialchars(basename($file)) . "\n";
        }
        echo "</pre>";
    } else {
        logOutput("✓ All cache files cleared", 'success');
    }
}

?>

    <hr>
    <div class="info">
        <h3>✅ Cache Clear Complete!</h3>
        <p><strong>Next Steps:</strong></p>
        <ol>
            <li>Test your website - changes should now be active</li>
            <li>If you updated AppServiceProvider, the new MIME type detector should be in effect</li>
            <li>Test the tour packages page: <a href="/admin/tour-packages" style="color: #4CAF50;">/admin/tour-packages</a></li>
            <li><strong>Delete this script file for security!</strong></li>
        </ol>
    </div>

    <div class="error">
        <p><strong>⚠ Security Note:</strong> Please delete this file (clear-cache.php) from your server after use!</p>
    </div>
</body>
</html>
