<?php
/**
 * Sync Changes from Git to Server
 * 
 * This script automatically syncs all changes from Git to the Laravel installation.
 * Run this script via browser: https://www.gotzsafari.com/sync-to-server.php
 * 
 * IMPORTANT: Delete this file after use for security!
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);
set_time_limit(300); // 5 minutes

$homeDir = '/home/gotzsafari';
$repoPath = $homeDir . '/repositories/gowebsitelaravel';
$laravelPath = $homeDir . '/laravel';

function logOutput($message) {
    echo "<p style='color: #4CAF50;'>✓ $message</p>";
    flush();
}

function logError($message) {
    echo "<p style='color: #f44336;'>✗ $message</p>";
    flush();
}

function logWarning($message) {
    echo "<p style='color: #FFD700;'>⚠ $message</p>";
    flush();
}

function logInfo($message) {
    echo "<p style='color: #2196F3;'>ℹ $message</p>";
    flush();
}

function runCommand($command, $description, $cwd = null) {
    global $laravelPath;
    logInfo("$description...");
    $fullCommand = $cwd ? "cd $cwd && $command" : "cd $laravelPath && $command";
    $output = [];
    $returnVar = 0;
    exec($fullCommand . ' 2>&1', $output, $returnVar);
    
    echo "<p style='color: #888; font-size: 11px;'>Running: <code>" . htmlspecialchars($fullCommand) . "</code></p>";
    
    if ($returnVar === 0) {
        logOutput("$description completed successfully");
        if (!empty($output) && count($output) > 0) {
            $outputText = implode("\n", $output);
            // Only show output if it's not too long
            if (strlen($outputText) < 2000) {
                echo "<pre style='background: #1a1a1a; padding: 10px; color: #0f0; font-size: 11px; max-height: 200px; overflow-y: auto; margin: 5px 0;'>" . htmlspecialchars($outputText) . "</pre>";
            }
        }
        return true;
    } else {
        logError("$description failed (exit code: $returnVar)");
        if (!empty($output)) {
            echo "<pre style='background: #1a1a1a; padding: 10px; color: #f00; font-size: 11px; max-height: 200px; overflow-y: auto; margin: 5px 0;'>" . htmlspecialchars(implode("\n", $output)) . "</pre>";
        }
        return false;
    }
}

function copyFile($source, $target) {
    $targetDir = dirname($target);
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
    }
    
    if (file_exists($source)) {
        if (copy($source, $target)) {
            return true;
        } else {
            logError("Failed to copy: " . basename($source));
            return false;
        }
    } else {
        logWarning("Source file not found: " . basename($source));
        return false;
    }
}

function copyDirectory($source, $target) {
    if (!is_dir($source)) {
        return false;
    }
    
    if (!is_dir($target)) {
        mkdir($target, 0755, true);
    }
    
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($source, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );
    
    $copied = 0;
    foreach ($files as $file) {
        $targetPath = $target . DIRECTORY_SEPARATOR . $files->getSubPathName();
        
        if ($file->isDir()) {
            if (!is_dir($targetPath)) {
                mkdir($targetPath, 0755, true);
            }
        } else {
            if (copyFile($file->getRealPath(), $targetPath)) {
                $copied++;
            }
        }
    }
    
    return $copied;
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Sync to Server</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; padding: 20px; background: #1a1a1a; color: #fff; }
        .container { max-width: 1000px; margin: 0 auto; }
        h1 { color: #4CAF50; margin-bottom: 10px; }
        .subtitle { color: #888; margin-bottom: 20px; }
        .step { background: #2a2a2a; padding: 15px; margin: 10px 0; border-left: 4px solid #4CAF50; border-radius: 4px; }
        .error { border-left-color: #f44336; }
        .warning { border-left-color: #FFD700; }
        .info { border-left-color: #2196F3; }
        pre { background: #1a1a1a; padding: 10px; overflow-x: auto; border-radius: 4px; }
        .summary { background: #2a2a2a; padding: 20px; margin: 20px 0; border-radius: 4px; }
        .summary-item { padding: 5px 0; }
        hr { border: none; border-top: 1px solid #444; margin: 20px 0; }
    </style>
</head>
<body>
<div class="container">
    <h1>🔄 Sync Changes to Server</h1>
    <p class="subtitle">Syncing latest changes from Git repository at <?php echo date('Y-m-d H:i:s'); ?></p>
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

echo "<div class='step'>";
echo "<h3>Step 1: Pull Latest Changes from Git</h3>";

// Pull latest changes from repository
$pullSuccess = runCommand(
    "git pull origin main",
    "Pulling latest changes from Git repository",
    $repoPath
);

if (!$pullSuccess) {
    logError("Git pull failed. Please check the repository.");
    echo "</div>";
    echo "</div></body></html>";
    exit;
}

echo "</div>";

// Find PHP path
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

echo "<div class='step'>";
echo "<h3>Step 2: Sync Files to Laravel Directory</h3>";

// Files and directories to sync
$filesToSync = [
    // Routes
    'routes/web.php',
    'routes/api.php',
    
    // Controllers
    'app/Http/Controllers',
    
    // Models
    'app/Models',
    
    // Resources
    'app/Http/Resources',
    
    // Migrations
    'database/migrations',
    
    // Seeders
    'database/seeders',
    
    // Middleware
    'app/Http/Middleware',
    
    // Config (be careful with this)
    // 'config', // Uncomment if you need to sync config files
    
    // Views
    'resources/views',
    
    // Frontend Vue files
    'resources/js',
    'resources/css',
    'resources/marketing',
    
    // Public assets (if any)
    // 'public', // Uncomment if you need to sync public files
    
    // Bootstrap
    'bootstrap/app.php',
    
    // Composer and package files
    'composer.json',
    'package.json',
    'vite.config.js',
    'tailwind.config.js',
    'postcss.config.js',
];

$filesCopied = 0;
$directoriesCopied = 0;
$errors = 0;

foreach ($filesToSync as $item) {
    $sourcePath = "$repoPath/$item";
    $targetPath = "$laravelPath/$item";
    
    if (is_file($sourcePath)) {
        if (copyFile($sourcePath, $targetPath)) {
            $filesCopied++;
            logOutput("Copied file: $item");
        } else {
            $errors++;
        }
    } elseif (is_dir($sourcePath)) {
        $copied = copyDirectory($sourcePath, $targetPath);
        if ($copied > 0) {
            $directoriesCopied++;
            logOutput("Copied directory: $item ($copied files)");
        } else {
            logWarning("Directory empty or failed: $item");
        }
    } else {
        logWarning("Path not found: $item");
    }
}

echo "<p><strong>Summary:</strong> $filesCopied files, $directoriesCopied directories synced</p>";
if ($errors > 0) {
    logError("$errors errors occurred during file sync");
}
echo "</div>";

// Check if composer.json changed
$composerChanged = false;
if (file_exists("$repoPath/composer.json") && file_exists("$laravelPath/composer.json")) {
    $repoComposer = file_get_contents("$repoPath/composer.json");
    $laravelComposer = file_get_contents("$laravelPath/composer.json");
    if ($repoComposer !== $laravelComposer) {
        $composerChanged = true;
    }
}

if ($composerChanged) {
    echo "<div class='step warning'>";
    echo "<h3>Step 3: Composer Dependencies Changed</h3>";
    logWarning("composer.json has changed. Running composer install...");
    
    // Find composer
    $composerPath = '';
    $composerPaths = [
        '/usr/local/bin/composer',
        '/usr/bin/composer',
        'composer',
    ];
    foreach ($composerPaths as $path) {
        if ($path === 'composer' || file_exists($path)) {
            $composerPath = $path;
            break;
        }
    }
    
    if (empty($composerPath) || $composerPath === 'composer') {
        // Try composer.phar
        if (file_exists("$laravelPath/composer.phar")) {
            $composerPath = "$phpPath $laravelPath/composer.phar";
        } else {
            logError("Composer not found. Please install Composer dependencies manually.");
        }
    }
    
    if (!empty($composerPath)) {
        runCommand(
            "$composerPath install --no-dev --optimize-autoloader --ignore-platform-reqs",
            "Installing Composer dependencies",
            $laravelPath
        );
    }
    echo "</div>";
}

// Clear caches
echo "<div class='step'>";
echo "<h3>Step 4: Clear Laravel Caches</h3>";

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

echo "</div>";

// Check if package.json changed
$packageChanged = false;
if (file_exists("$repoPath/package.json") && file_exists("$laravelPath/package.json")) {
    $repoPackage = file_get_contents("$repoPath/package.json");
    $laravelPackage = file_get_contents("$laravelPath/package.json");
    if ($repoPackage !== $laravelPackage) {
        $packageChanged = true;
    }
}

if ($packageChanged) {
    echo "<div class='step warning'>";
    echo "<h3>Step 5: Frontend Dependencies Changed</h3>";
    logWarning("package.json has changed. You may need to run 'npm install' and 'npm run build' manually.");
    logInfo("Note: Frontend build should be done locally and uploaded separately.");
    echo "</div>";
}

// Check for new migrations
echo "<div class='step info'>";
echo "<h3>Step 6: Database Migrations</h3>";
logInfo("Checking for new migrations...");

$migrationFiles = glob("$laravelPath/database/migrations/*.php");
$migrationCount = count($migrationFiles);
logInfo("Found $migrationCount migration files");

logInfo("Note: Run migrations manually if needed: php artisan migrate");
echo "</div>";

echo "<hr>";
echo "<div class='summary'>";
echo "<h2>✅ Sync Summary</h2>";
echo "<div class='summary-item'>✓ Git pull completed</div>";
echo "<div class='summary-item'>✓ $filesCopied files synced</div>";
echo "<div class='summary-item'>✓ $directoriesCopied directories synced</div>";
echo "<div class='summary-item'>✓ Laravel caches cleared and regenerated</div>";
if ($composerChanged) {
    echo "<div class='summary-item'>✓ Composer dependencies updated</div>";
}
if ($packageChanged) {
    echo "<div class='summary-item'>⚠ Frontend dependencies changed - rebuild needed</div>";
}
echo "</div>";

echo "<div class='step error'>";
echo "<h3>⚠️  SECURITY WARNING:</h3>";
echo "<p><strong>Delete this file (sync-to-server.php) immediately after use!</strong></p>";
echo "<p>This script has access to your entire codebase and should not be publicly accessible.</p>";
echo "</div>";

?>

</div>
</body>
</html>

