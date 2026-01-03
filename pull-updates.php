<?php
/**
 * Pull Latest Changes from Git
 * Run this script via browser: https://www.gotzsafari.com/pull-updates.php
 * IMPORTANT: Delete this file after use!
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);
set_time_limit(120);

$homeDir = '/home/gotzsafari';
$repoPath = $homeDir . '/repositories/gowebsitelaravel';
$laravelPath = $homeDir . '/laravel';

function logOutput($message) {
    echo "<p style='color: green;'>✓ $message</p>";
    flush();
}

function logError($message) {
    echo "<p style='color: red;'>✗ $message</p>";
    flush();
}

function runCommand($command, $description, $cwd = null) {
    global $laravelPath;
    logOutput("$description...");
    $fullCommand = $cwd ? "cd $cwd && $command" : "cd $laravelPath && $command";
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
    <title>Pull Git Updates</title>
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
    <h1>🔄 Pull Git Updates</h1>
    <p>Pulling latest changes from repository at <?php echo date('Y-m-d H:i:s'); ?></p>
    <hr>

<?php

// Check if repository directory exists
if (!is_dir($repoPath)) {
    logError("Repository directory not found: $repoPath");
    echo "</div></body></html>";
    exit;
}

// Check if .git directory exists in repository
if (!is_dir("$repoPath/.git")) {
    logError("Git repository not found in: $repoPath");
    echo "</div></body></html>";
    exit;
}

// Check if Laravel directory exists
if (!is_dir($laravelPath)) {
    logError("Laravel directory not found: $laravelPath");
    echo "</div></body></html>";
    exit;
}

// Pull latest changes from repository
runCommand(
    "git pull origin main",
    "Pulling latest changes from Git repository",
    $repoPath
);

// Copy updated files to Laravel directory
logOutput("Copying updated files to Laravel directory...");
$filesToCopy = [
    'database/seeders/DestinationSeeder.php',
    'database/seeders/ItinerarySeeder.php',
    'database/seeders/LodgeSeeder.php',
    'database/seeders/ContactChannelSeeder.php',
    'database/seeders/ContactQuickFactSeeder.php',
    'database/seeders/DatabaseSeeder.php',
    'routes/web.php',
    'app/Http/Controllers/Admin/ContactMessageController.php',
    'app/Http/Controllers/Admin/ContactChannelController.php',
    'app/Http/Controllers/Admin/ContactQuickFactController.php',
    'app/Http/Controllers/Admin/TourPackageController.php',
];

foreach ($filesToCopy as $file) {
    $sourceFile = "$repoPath/$file";
    $targetFile = "$laravelPath/$file";
    $targetDir = dirname($targetFile);
    
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
    }
    
    if (file_exists($sourceFile)) {
        if (copy($sourceFile, $targetFile)) {
            logOutput("Copied: $file");
        } else {
            logError("Failed to copy: $file");
        }
    } else {
        logError("Source file not found: $sourceFile");
    }
}

// Clear route cache after copying routes file
if (in_array('routes/web.php', $filesToCopy)) {
    logOutput("Clearing route cache...");
    $phpPath = '';
    $phpPaths = [
        '/opt/cpanel/ea-php82/root/usr/bin/php',
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
    
    $command = "cd $laravelPath && $phpPath artisan route:clear 2>&1";
    $output = [];
    $returnVar = 0;
    exec($command, $output, $returnVar);
    
    if ($returnVar === 0) {
        logOutput("Route cache cleared successfully");
    } else {
        logError("Failed to clear route cache");
    }
}

echo "<hr>";
echo "<h2>Update Summary</h2>";
echo "<div class='step'>";
echo "<strong>Completed:</strong><br>";
echo "✓ Git pull executed<br>";
echo "</div>";

echo "<div class='step'>";
echo "<h3>⚠️  SECURITY WARNING:</h3>";
echo "<p><strong>Delete this file (pull-updates.php) immediately after use!</strong></p>";
echo "</div>";

?>

</div>
</body>
</html>

