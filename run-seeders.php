<?php
/**
 * Run Database Seeders
 * Run this script via browser: https://www.gotzsafari.com/run-seeders.php
 * IMPORTANT: Delete this file after use!
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);
set_time_limit(300); // 5 minutes

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
            echo "<pre style='background: #1a1a1a; padding: 10px; color: #0f0; font-size: 11px; max-height: 300px; overflow-y: auto;'>" . htmlspecialchars(implode("\n", $output)) . "</pre>";
        }
        return true;
    } else {
        logError("$description failed (exit code: $returnVar)");
        if (!empty($output)) {
            echo "<pre style='background: #1a1a1a; padding: 10px; color: #f00; font-size: 11px; max-height: 300px; overflow-y: auto;'>" . htmlspecialchars(implode("\n", $output)) . "</pre>";
        }
        return false;
    }
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Run Database Seeders</title>
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
    <h1>🌱 Run Database Seeders</h1>
    <p>Starting seeder execution at <?php echo date('Y-m-d H:i:s'); ?></p>
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

// Run database seeders
logOutput("Running database seeders...");
runCommand(
    "$phpPath artisan db:seed --force",
    "Running all database seeders"
);

// Run seeders that don't require fileinfo extension first
$simpleSeeders = [
    'ContactChannelSeeder',
    'ContactQuickFactSeeder',
    'FeatureCardSeeder',
    'AboutStatSeeder',
    'AboutHighlightSeeder',
];

// Seeders that require fileinfo extension (may fail)
$imageSeeders = [
    'DestinationSeeder',
    'ItinerarySeeder',
    'TourPackageSeeder',
    'LodgeSeeder',
];

echo "<hr>";
echo "<h2>Running Simple Seeders (No Images)</h2>";

foreach ($simpleSeeders as $seeder) {
    runCommand(
        "$phpPath artisan db:seed --class=$seeder --force",
        "Running $seeder"
    );
}

echo "<hr>";
echo "<h2>Running Image Seeders (May Require fileinfo Extension)</h2>";

foreach ($imageSeeders as $seeder) {
    runCommand(
        "$phpPath artisan db:seed --class=$seeder --force",
        "Running $seeder"
    );
}

echo "<hr>";
echo "<h2>Seeder Summary</h2>";
echo "<div class='step'>";
echo "<strong>Completed:</strong><br>";
echo "✓ All seeders executed<br>";
echo "</div>";

echo "<div class='step'>";
echo "<h3>⚠️  SECURITY WARNING:</h3>";
echo "<p><strong>Delete this file (run-seeders.php) immediately after use!</strong></p>";
echo "</div>";

?>

</div>
</body>
</html>

