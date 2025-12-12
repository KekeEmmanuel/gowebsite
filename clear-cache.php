<?php
/**
 * Clear Laravel Caches
 * Run this script via browser: https://www.gotzsafari.com/clear-cache.php
 * IMPORTANT: Delete this file after use!
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);
set_time_limit(120);

$laravelPath = '/home/gotzsafari/laravel';

function logOutput($message) {
    echo "<p style='color: green;'>✓ $message</p>";
    flush();
}

function logError($message) {
    echo "<p style='color: red;'>✗ $message</p>";
    flush();
}

function runCommand($command, $description) {
    global $laravelPath;
    logOutput("$description...");
    $fullCommand = "cd $laravelPath && $command";
    $output = [];
    $returnVar = 0;
    exec($fullCommand . ' 2>&1', $output, $returnVar);
    
    echo "<p>Running: <code style='color: #888;'>" . htmlspecialchars($fullCommand) . "</code></p>";
    
    if ($returnVar === 0) {
        logOutput("$description completed successfully");
        if (!empty($output)) {
            echo "<pre style='background: #1a1a1a; padding: 10px; color: #0f0; font-size: 11px; max-height: 200px; overflow-y: auto;'>" . htmlspecialchars(implode("\n", $output)) . "</pre>";
        }
        return true;
    } else {
        logError("$description failed (exit code: $returnVar)");
        if (!empty($output)) {
            echo "<pre style='background: #1a1a1a; padding: 10px; color: #f00; font-size: 11px; max-height: 200px; overflow-y: auto;'>" . htmlspecialchars(implode("\n", $output)) . "</pre>";
        }
        return false;
    }
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Clear Laravel Caches</title>
    <style>
        body { font-family: monospace; padding: 20px; background: #1a1a1a; color: #fff; }
        .container { max-width: 900px; margin: 0 auto; }
        h1 { color: #4CAF50; }
        .step { background: #2a2a2a; padding: 15px; margin: 10px 0; border-left: 4px solid #4CAF50; }
        .error { background: #3a1a1a; border-left-color: #f44336; }
        pre { background: #1a1a1a; padding: 10px; overflow-x: auto; }
    </style>
</head>
<body>
<div class="container">
    <h1>🧹 Clear Laravel Caches</h1>
    <p>Clearing caches at <?php echo date('Y-m-d H:i:s'); ?></p>
    <hr>

<?php

// Find PHP path
$phpPath = '';
$phpPaths = [
    '/opt/cpanel/ea-php82/root/usr/bin/php',  // PHP 8.2
    '/opt/cpanel/ea-php83/root/usr/bin/php',  // PHP 8.3
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

logOutput("Using PHP: $phpPath");

// Check if artisan exists
if (!file_exists("$laravelPath/artisan")) {
    logError("Laravel artisan file not found at $laravelPath/artisan");
    echo "</div></body></html>";
    exit;
}

// Clear all caches
runCommand(
    "$phpPath artisan cache:clear",
    "Clearing application cache"
);

runCommand(
    "$phpPath artisan config:clear",
    "Clearing configuration cache"
);

runCommand(
    "$phpPath artisan route:clear",
    "Clearing route cache"
);

runCommand(
    "$phpPath artisan view:clear",
    "Clearing view cache"
);

// Regenerate caches
runCommand(
    "$phpPath artisan config:cache",
    "Caching configuration"
);

runCommand(
    "$phpPath artisan route:cache",
    "Caching routes"
);

runCommand(
    "$phpPath artisan view:cache",
    "Caching views"
);

echo "<hr>";
echo "<h2>Cache Clear Summary</h2>";
echo "<div class='step'>";
echo "<strong>Completed:</strong><br>";
echo "✓ All caches cleared and regenerated<br>";
echo "</div>";

echo "<div class='step'>";
echo "<h3>⚠️  SECURITY WARNING:</h3>";
echo "<p><strong>Delete this file (clear-cache.php) immediately after use!</strong></p>";
echo "</div>";

?>

</div>
</body>
</html>

