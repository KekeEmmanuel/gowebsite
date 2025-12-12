<?php
/**
 * Laravel Deployment Script - Run Deployment Commands
 * Run this script via browser: https://www.gotzsafari.com/run-deployment.php
 * IMPORTANT: Delete this file after deployment for security!
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);
set_time_limit(600); // 10 minutes

$laravelPath = '/home/gotzsafari/laravel';
$output = [];
$errors = [];

function logOutput($message) {
    global $output;
    $output[] = $message;
    echo "<p style='color: green;'>✓ $message</p>";
    flush();
}

function logError($message) {
    global $errors;
    $errors[] = $message;
    echo "<p style='color: red;'>✗ $message</p>";
    flush();
}

function runCommand($command, $description) {
    logOutput("$description...");
    logOutput("Running: <code style='color: #888;'>$command</code>");
    $output = [];
    $returnVar = 0;
    exec($command . ' 2>&1', $output, $returnVar);
    
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
        } else {
            echo "<pre style='background: #1a1a1a; padding: 10px; color: #f00; font-size: 11px;'>No output returned. Command may not exist or path may be incorrect.</pre>";
        }
        return false;
    }
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Laravel Deployment - Run Commands</title>
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
    <h1>🚀 Laravel Deployment - Running Commands</h1>
    <p>Starting deployment commands at <?php echo date('Y-m-d H:i:s'); ?></p>
    <hr>

<?php

// Change to Laravel directory
chdir($laravelPath);

// Find PHP path first (needed for composer.phar) - prefer PHP 8.2+ if available
$phpPath = '';
$phpVersion = '';
$phpPaths = [
    '/opt/cpanel/ea-php82/root/usr/bin/php',  // PHP 8.2
    '/opt/cpanel/ea-php83/root/usr/bin/php',  // PHP 8.3
    '/opt/cpanel/ea-php84/root/usr/bin/php',  // PHP 8.4
    '/usr/local/bin/php',
    '/usr/bin/php',
    'php',
];
foreach ($phpPaths as $path) {
    if ($path === 'php' || file_exists($path)) {
        $versionOutput = shell_exec("$path -v 2>/dev/null");
        if ($versionOutput) {
            // Check if version is 8.2 or higher
            if (preg_match('/PHP (\d+)\.(\d+)/', $versionOutput, $matches)) {
                $major = (int)$matches[1];
                $minor = (int)$matches[2];
                if ($major > 8 || ($major === 8 && $minor >= 2)) {
                    $phpPath = $path;
                    $phpVersion = "$major.$minor";
                    break;
                }
            }
        }
    }
}
// If no PHP 8.2+ found, use the first available PHP
if (empty($phpPath)) {
    foreach ($phpPaths as $path) {
        if ($path === 'php' || file_exists($path)) {
            $phpPath = $path;
            $versionOutput = shell_exec("$path -v 2>/dev/null");
            if ($versionOutput && preg_match('/PHP (\d+\.\d+)/', $versionOutput, $matches)) {
                $phpVersion = $matches[1];
            }
            break;
        }
    }
}

// Find Composer path
$composerPath = '';
$possibleComposerPaths = [
    '/usr/local/bin/composer',
    '/usr/bin/composer',
    '/usr/local/cpanel/3rdparty/bin/composer',
    '/opt/cpanel/composer/bin/composer',
    'composer', // fallback
];
foreach ($possibleComposerPaths as $path) {
    $testCmd = "which $path 2>/dev/null || command -v $path 2>/dev/null || [ -f $path ] && echo $path";
    exec($testCmd, $output, $return);
    if ($return === 0 && !empty($output) && trim($output[0])) {
        $composerPath = trim($output[0]);
        if (file_exists($composerPath)) {
            break;
        }
    }
}
if (empty($composerPath)) {
    // Try to find composer.phar in Laravel directory
    $composerPhar = "$laravelPath/composer.phar";
    if (file_exists($composerPhar) && filesize($composerPhar) > 1000) {
        // File exists and is not empty (composer.phar should be at least a few MB)
        $composerPath = "$phpPath $composerPhar";
        logOutput("Using existing composer.phar");
    } else {
        // Download composer.phar if not found or empty/corrupted
        if (file_exists($composerPhar) && filesize($composerPhar) === 0) {
            logOutput("composer.phar exists but is empty (0 bytes). Re-downloading...");
            unlink($composerPhar); // Delete empty file
        } else {
            logOutput("Composer not found, downloading composer.phar...");
        }
        
        $composerUrl = "https://getcomposer.org/download/latest-stable/composer.phar";
        
        // Try curl first
        $downloadCmd = "curl -L -sS $composerUrl -o $composerPhar 2>&1";
        logOutput("Attempting download with curl...");
        exec($downloadCmd, $curlOutput, $curlReturn);
        
        // Check if download succeeded
        if ($curlReturn !== 0 || !file_exists($composerPhar) || filesize($composerPhar) < 1000) {
            logOutput("curl failed, trying wget...");
            // Try wget
            $downloadCmd = "wget --no-check-certificate -O $composerPhar $composerUrl 2>&1";
            exec($downloadCmd, $wgetOutput, $wgetReturn);
        }
        
        // Verify download
        if (file_exists($composerPhar) && filesize($composerPhar) > 1000) {
            chmod($composerPhar, 0755);
            $composerPath = "$phpPath $composerPhar";
            logOutput("Composer downloaded successfully (" . round(filesize($composerPhar) / 1024 / 1024, 2) . " MB)");
        } else {
            $composerPath = "composer"; // last resort
            logError("Could not download Composer. File size: " . (file_exists($composerPhar) ? filesize($composerPhar) : 0) . " bytes");
            if (!empty($curlOutput)) {
                logError("curl output: " . implode("\n", $curlOutput));
            }
            if (!empty($wgetOutput)) {
                logError("wget output: " . implode("\n", $wgetOutput));
            }
        }
    }
}

// Find npm path
$npmOutput = shell_exec("which npm 2>/dev/null || command -v npm 2>/dev/null");
$npmPath = $npmOutput ? trim($npmOutput) : '';
if (empty($npmPath) || !file_exists($npmPath)) {
    // Try common npm paths
    $npmPaths = ['/usr/bin/npm', '/usr/local/bin/npm', '/opt/cpanel/ea-nodejs18/bin/npm', 'npm'];
    foreach ($npmPaths as $path) {
        if (file_exists($path) || $path === 'npm') {
            $npmPath = $path;
            break;
        }
    }
}

logOutput("Using PHP: $phpPath");
logOutput("Using Composer: $composerPath");
logOutput("Using npm: $npmPath");

// Step 1: Install Composer dependencies
logOutput("Checking if vendor directory exists...");
if (!is_dir("$laravelPath/vendor")) {
    logOutput("vendor directory does not exist. Installing Composer dependencies...");
    
    // Set HOME or COMPOSER_HOME environment variable for Composer
    $composerHome = "$laravelPath/.composer";
    if (!is_dir($composerHome)) {
        mkdir($composerHome, 0755, true);
    }
    
    // Set environment variables
    $homeDir = getenv('HOME') ?: '/home/gotzsafari';
    putenv("HOME=$homeDir");
    putenv("COMPOSER_HOME=$composerHome");
    
    // Build composer command with platform requirement ignores if needed
    $composerFlags = '--no-dev --optimize-autoloader --verbose';
    
    // Check PHP version - if less than 8.3, ignore platform requirements
    // (Some packages like zipstream-php 3.2.0 require PHP 8.3+)
    if (empty($phpVersion) || version_compare($phpVersion, '8.3', '<')) {
        logOutput("PHP version $phpVersion detected. Using --ignore-platform-reqs to bypass version and extension checks.");
        $composerFlags .= ' --ignore-platform-reqs';
    } else {
        // Still ignore missing extensions
        $composerFlags .= ' --ignore-platform-req=ext-fileinfo --ignore-platform-req=ext-exif';
    }
    
    runCommand(
        "cd $laravelPath && HOME=$homeDir COMPOSER_HOME=$composerHome $composerPath install $composerFlags 2>&1",
        "Installing Composer dependencies"
    );
    
    // Verify vendor directory was created
    if (!is_dir("$laravelPath/vendor")) {
        logError("vendor directory was not created after composer install!");
        logError("This is required for Laravel to work. Please check composer.json and try again.");
    } else {
        logOutput("✓ vendor directory created successfully");
    }
} else {
    logOutput("✓ vendor directory already exists");
}

// Check if artisan exists
if (!file_exists("$laravelPath/artisan")) {
    logError("Laravel artisan file not found at $laravelPath/artisan");
    logError("Please verify that Laravel files were copied correctly");
} else {
    // Step 2: Generate application key
    runCommand(
        "cd $laravelPath && $phpPath artisan key:generate --force",
        "Generating application key"
    );

    // Step 3: Run migrations
    runCommand(
        "cd $laravelPath && $phpPath artisan migrate --force",
        "Running database migrations"
    );

    // Step 4: Create storage link
    runCommand(
        "cd $laravelPath && $phpPath artisan storage:link",
        "Creating storage symbolic link"
    );
}

// Step 5: Build frontend assets
if (empty($npmPath) || $npmPath === 'npm') {
    // Check if npm actually exists
    exec("which npm 2>/dev/null", $npmCheck, $npmCheckReturn);
    if ($npmCheckReturn !== 0) {
        logError("npm not found. Skipping frontend asset build.");
        logError("You may need to install Node.js/npm or build assets locally and upload them.");
    } else {
        runCommand(
            "cd $laravelPath && npm install && npm run build",
            "Building frontend assets"
        );
    }
} else {
    runCommand(
        "cd $laravelPath && $npmPath install && $npmPath run build",
        "Building frontend assets"
    );
}

// Step 6: Optimize Laravel
if (file_exists("$laravelPath/artisan")) {
    runCommand(
        "cd $laravelPath && $phpPath artisan config:cache && $phpPath artisan route:cache && $phpPath artisan view:cache",
        "Optimizing Laravel (config, routes, views)"
    );
}

echo "<hr>";
echo "<h2>Deployment Summary</h2>";
echo "<div class='step'>";
echo "<strong>Completed Steps:</strong><br>";
echo "1. Composer dependencies: " . (count($errors) === 0 ? "✓" : "✗") . "<br>";
echo "2. Application key: " . (count($errors) === 0 ? "✓" : "✗") . "<br>";
echo "3. Database migrations: " . (count($errors) === 0 ? "✓" : "✗") . "<br>";
echo "4. Storage link: " . (count($errors) === 0 ? "✓" : "✗") . "<br>";
echo "5. Frontend assets: " . (count($errors) === 0 ? "✓" : "✗") . "<br>";
echo "6. Laravel optimization: " . (count($errors) === 0 ? "✓" : "✗") . "<br>";
echo "</div>";

if (count($errors) > 0) {
    echo "<div class='step error'>";
    echo "<strong>Errors:</strong><br>";
    foreach ($errors as $error) {
        echo "- $error<br>";
    }
    echo "</div>";
} else {
    echo "<div class='step'>";
    echo "<h3>✅ Deployment Completed Successfully!</h3>";
    echo "<p>Your Laravel application should now be live at: <a href='https://www.gotzsafari.com' style='color: #4CAF50;'>https://www.gotzsafari.com</a></p>";
    echo "</div>";
}

echo "<div class='step'>";
echo "<h3>⚠️  SECURITY WARNING:</h3>";
echo "<p><strong>Delete these files immediately after deployment:</strong></p>";
echo "<ul>";
echo "<li><code>create-env.php</code></li>";
echo "<li><code>run-deployment.php</code></li>";
echo "<li><code>deploy-cpanel.php</code></li>";
echo "</ul>";
echo "</div>";

?>

</div>
</body>
</html>

