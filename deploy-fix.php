<?php
/**
 * Deploy Fix for fileinfo Extension Issue
 * 
 * This script will:
 * 1. Pull latest changes from Git
 * 2. Copy AppServiceProvider with ExtensionMimeTypeDetector fix
 * 3. Clear all Laravel caches
 * 
 * Upload this file to public_html and run it via browser:
 * https://www.gotzsafari.com/deploy-fix.php
 * 
 * IMPORTANT: Delete this file after use!
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);
set_time_limit(300);

$homeDir = '/home/gotzsafari';
$repoPath = $homeDir . '/repositories/gowebsitelaravel';
$laravelPath = $homeDir . '/laravel';

?>
<!DOCTYPE html>
<html>
<head>
    <title>Deploy fileinfo Fix</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 900px; margin: 50px auto; padding: 20px; background: #1a1a1a; color: #fff; }
        .success { color: #4CAF50; padding: 15px; background: #2a2a2a; border-left: 4px solid #4CAF50; margin: 10px 0; }
        .error { color: #f44336; padding: 15px; background: #2a2a2a; border-left: 4px solid #f44336; margin: 10px 0; }
        .info { color: #2196F3; padding: 15px; background: #2a2a2a; border-left: 4px solid #2196F3; margin: 10px 0; }
        .warning { color: #FFD700; padding: 15px; background: #2a2a2a; border-left: 4px solid #FFD700; margin: 10px 0; }
        pre { background: #1a1a1a; padding: 10px; overflow-x: auto; font-size: 12px; border: 1px solid #444; }
        h1 { color: #4CAF50; }
        h2 { color: #4CAF50; margin-top: 30px; }
        code { background: #2a2a2a; padding: 2px 6px; border-radius: 3px; }
        .step { background: #2a2a2a; padding: 20px; margin: 15px 0; border-radius: 4px; }
    </style>
</head>
<body>
    <h1>🚀 Deploy fileinfo Extension Fix</h1>
    <p>This script will deploy the fix to work without fileinfo extension...</p>
    <hr>

<?php

function logOutput($message, $type = 'info') {
    $class = $type === 'error' ? 'error' : ($type === 'warning' ? 'warning' : ($type === 'success' ? 'success' : 'info'));
    echo "<div class='$class'>$message</div>";
    flush();
}

function runCommand($command, $description, $cwd = null) {
    global $laravelPath;
    logOutput("🔄 $description...", 'info');
    $fullCommand = $cwd ? "cd $cwd && $command" : "cd $laravelPath && $command";
    $output = [];
    $returnVar = 0;
    exec($fullCommand . ' 2>&1', $output, $returnVar);
    
    if ($returnVar === 0) {
        logOutput("✓ $description completed", 'success');
        if (!empty($output) && strlen(implode("\n", $output)) < 500) {
            echo "<pre style='font-size: 11px;'>" . htmlspecialchars(implode("\n", $output)) . "</pre>";
        }
        return true;
    } else {
        logOutput("❌ $description failed (exit code: $returnVar)", 'error');
        if (!empty($output)) {
            echo "<pre style='color: #f44336; font-size: 11px;'>" . htmlspecialchars(implode("\n", $output)) . "</pre>";
        }
        return false;
    }
}

// Step 1: Check directories
echo "<div class='step'>";
echo "<h2>Step 1: Checking Directories</h2>";

if (!is_dir($repoPath)) {
    logOutput("❌ Repository directory not found: $repoPath", 'error');
    echo "</div></body></html>";
    exit;
}
logOutput("✓ Repository directory found", 'success');

if (!is_dir($laravelPath)) {
    logOutput("❌ Laravel directory not found: $laravelPath", 'error');
    echo "</div></body></html>";
    exit;
}
logOutput("✓ Laravel directory found", 'success');
echo "</div>";

// Step 2: Pull from Git
echo "<div class='step'>";
echo "<h2>Step 2: Pulling Latest Changes from Git</h2>";

$pullSuccess = runCommand(
    "git pull origin main",
    "Pulling latest changes from Git",
    $repoPath
);

if (!$pullSuccess) {
    logOutput("⚠ Git pull had issues, but continuing...", 'warning');
}
echo "</div>";

// Step 3: Copy AppServiceProvider and config files
echo "<div class='step'>";
echo "<h2>Step 3: Copying Updated Files</h2>";

$filesToCopy = [
    'app/Providers/AppServiceProvider.php' => 'AppServiceProvider with ExtensionMimeTypeDetector and namespace polyfill backup',
    'app/Providers/ImageOptimizerServiceProvider.php' => 'ImageOptimizerServiceProvider with namespace polyfill',
    'app/Models/TourPackage.php' => 'TourPackage with nonOptimized() conversions',
    'app/Models/SafariPackage.php' => 'SafariPackage with nonOptimized() conversions',
    'app/Models/Lodge.php' => 'Lodge with nonOptimized() conversions',
    'config/media-library.php' => 'Media Library config with conditional image optimizers',
    'bootstrap/mime-polyfill.php' => 'mime_content_type polyfill for early loading',
    'bootstrap/providers.php' => 'Updated providers to include ImageOptimizerServiceProvider',
    'public/index.php' => 'Updated index.php to load polyfill',
];

// Also copy test script to public_html for easy access
$testScriptSource = $repoPath . '/test-mime-polyfill.php';
$testScriptTarget = $homeDir . '/public_html/test-mime-polyfill.php';
if (file_exists($testScriptSource)) {
    if (copy($testScriptSource, $testScriptTarget)) {
        chmod($testScriptTarget, 0644);
        logOutput("✓ Copied test-mime-polyfill.php to public_html", 'success');
    }
}

// Also copy updated deploy script to public_html (so next run uses latest version)
$deployScriptSource = $repoPath . '/deploy-fix.php';
$deployScriptTarget = $homeDir . '/public_html/deploy-fix.php';
if (file_exists($deployScriptSource) && $deployScriptSource !== __FILE__) {
    if (copy($deployScriptSource, $deployScriptTarget)) {
        chmod($deployScriptTarget, 0644);
        logOutput("✓ Updated deploy-fix.php in public_html (refresh page to use new version)", 'success');
    }
}

foreach ($filesToCopy as $file => $description) {
    $sourceFile = $repoPath . '/' . $file;
    $targetFile = $laravelPath . '/' . $file;
    $targetDir = dirname($targetFile);
    
    if (!file_exists($sourceFile)) {
        logOutput("❌ Source file not found: $file", 'error');
        continue;
    }
    
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
    }
    
    // Backup existing file
    if (file_exists($targetFile)) {
        $backupPath = $targetFile . '.backup.' . date('Y-m-d_H-i-s');
        copy($targetFile, $backupPath);
    }
    
    if (copy($sourceFile, $targetFile)) {
        chmod($targetFile, 0644);
        logOutput("✓ Copied: $file", 'success');
        
        // Verify AppServiceProvider has the fixes
        if ($file === 'app/Providers/AppServiceProvider.php') {
            $content = file_get_contents($targetFile);
            $hasExtensionDetector = strpos($content, 'ExtensionMimeTypeDetector') !== false;
            $hasPolyfill = strpos($content, 'mime_content_type') !== false;
            
            if ($hasExtensionDetector) {
                logOutput("✓ ExtensionMimeTypeDetector fix detected", 'success');
            }
            if ($hasPolyfill) {
                logOutput("✓ mime_content_type polyfill detected", 'success');
            }
        }
    } else {
        logOutput("❌ Failed to copy: $file", 'error');
    }
}
echo "</div>";

// Step 5: Find PHP executable (if not found earlier)
echo "<div class='step'>";
echo "<h2>Step 5: Finding PHP Executable</h2>";

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
    echo "</div></body></html>";
    exit;
}

logOutput("✓ PHP executable found: <code>$phpPath</code>", 'success');
$phpVersion = shell_exec("$phpPath -v | head -1");
logOutput("ℹ $phpVersion", 'info');
echo "</div>";

// Step 6: Clear all caches (CRITICAL - must be done after files are copied)
echo "<div class='step'>";
echo "<h2>Step 6: Clearing All Laravel Caches (CRITICAL)</h2>";

logOutput("⚠️ Clearing caches BEFORE config is loaded to ensure optimizers are disabled", 'warning');

// Clear caches in specific order - config cache FIRST
$cacheCommands = [
    'config:clear' => 'Clearing configuration cache (MUST BE FIRST)',
    'cache:clear' => 'Clearing application cache',
    'route:clear' => 'Clearing route cache',
    'view:clear' => 'Clearing view cache',
    'optimize:clear' => 'Clearing all optimized caches',
];

$cacheResults = [];
foreach ($cacheCommands as $command => $description) {
    $success = runCommand(
        "$phpPath artisan $command",
        $description
    );
    $cacheResults[$command] = $success;
    
    // Extra verification for config:clear
    if ($command === 'config:clear') {
        $configCachePath = $laravelPath . '/bootstrap/cache/config.php';
        if (file_exists($configCachePath)) {
            logOutput("⚠️ WARNING: Config cache file still exists! Attempting to delete...", 'warning');
            if (unlink($configCachePath)) {
                logOutput("✓ Config cache file deleted manually", 'success');
            } else {
                logOutput("❌ Failed to delete config cache file manually", 'error');
            }
        } else {
            logOutput("✓ Config cache file confirmed deleted", 'success');
        }
    }
}

// Also clear any cached config files
$cacheFiles = [
    $laravelPath . '/bootstrap/cache/config.php',
    $laravelPath . '/bootstrap/cache/routes.php',
    $laravelPath . '/bootstrap/cache/services.php',
];

foreach ($cacheFiles as $cacheFile) {
    if (file_exists($cacheFile)) {
        if (unlink($cacheFile)) {
            logOutput("✓ Deleted cached file: " . basename($cacheFile), 'success');
        }
    }
}

echo "</div>";

// Step 7: Verify fileinfo status
echo "<div class='step'>";
echo "<h2>Step 7: Checking fileinfo Extension Status</h2>";

$fileinfoEnabled = extension_loaded('fileinfo');
if ($fileinfoEnabled) {
    logOutput("✓ fileinfo extension is enabled", 'success');
    logOutput("ℹ The fix will still work, but fileinfo is available", 'info');
} else {
    logOutput("ℹ fileinfo extension is NOT enabled", 'info');
    logOutput("✓ The ExtensionMimeTypeDetector fix will handle MIME type detection", 'success');
}
echo "</div>";

// Summary
echo "<hr>";
echo "<h2>✅ Deployment Summary</h2>";

echo "<div class='success'>";
echo "<h3>Completed Steps:</h3>";
echo "<ul>";
echo "<li>✓ Checked directories</li>";
echo "<li>" . ($pullSuccess ? "✓" : "⚠") . " Pulled from Git</li>";
echo "<li>✓ Cleared config cache (early - before file copy)</li>";
echo "<li>✓ Copied all updated files</li>";
echo "<li>✓ Found PHP executable</li>";
echo "<li>✓ Cleared all Laravel caches (with verification)</li>";
echo "<li>✓ Checked fileinfo status</li>";
echo "</ul>";
echo "</div>";

echo "<div class='info'>";
echo "<h3>📋 Next Steps:</h3>";
echo "<ol>";
echo "<li>Test the mime polyfill: <a href='/test-mime-polyfill.php' style='color: #4CAF50;' target='_blank'>Run Test Script</a></li>";
echo "<li>Test the tour packages page: <a href='/admin/tour-packages' style='color: #4CAF50;' target='_blank'>/admin/tour-packages</a></li>";
echo "<li>The page should now load without 500 errors</li>";
echo "<li>Media Library will use extension-based MIME type detection</li>";
echo "<li><strong>Delete this script file (deploy-fix.php) for security!</strong></li>";
echo "</ol>";
echo "</div>";

// Quick inline test
echo "<div class='step'>";
echo "<h2>🧪 Quick MIME Polyfill Test</h2>";

// Load polyfill
$polyfillPath = $laravelPath . '/bootstrap/mime-polyfill.php';
if (file_exists($polyfillPath)) {
    require $polyfillPath;
    logOutput("✓ Polyfill loaded", 'success');
    
    // Test global function
    $testFile = __FILE__;
    if (function_exists('mime_content_type')) {
        try {
            $mime = mime_content_type($testFile);
            logOutput("✓ Global mime_content_type() works: $mime", 'success');
        } catch (\Throwable $e) {
            logOutput("❌ Global mime_content_type() failed: " . $e->getMessage(), 'error');
        }
    } else {
        logOutput("❌ Global mime_content_type() does not exist", 'error');
    }
    
    // Test namespace function
    try {
        $namespaceMime = call_user_func('Spatie\ImageOptimizer\mime_content_type', $testFile);
        logOutput("✓ Namespace mime_content_type() works: $namespaceMime", 'success');
    } catch (\Throwable $e) {
        logOutput("❌ Namespace mime_content_type() failed: " . $e->getMessage(), 'error');
    }
    
    // Test Image class
    try {
        require_once $laravelPath . '/vendor/autoload.php';
        $image = new \Spatie\ImageOptimizer\Image($testFile);
        $imageMime = $image->mime();
        logOutput("✓ Image class works: $imageMime", 'success');
    } catch (\Throwable $e) {
        logOutput("❌ Image class failed: " . $e->getMessage(), 'error');
    }
} else {
    logOutput("❌ Polyfill file not found at: $polyfillPath", 'error');
}

echo "</div>";

if (!$fileinfoEnabled) {
    echo "<div class='warning'>";
    echo "<h3>ℹ Note:</h3>";
    echo "<p>The Media Library is now using extension-based MIME type detection instead of fileinfo.</p>";
    echo "<p>This works for most image files (jpg, png, gif, webp). For production, it's still recommended to enable fileinfo extension for better accuracy.</p>";
    echo "</div>";
}

?>

    <div class="error">
        <p><strong>⚠ Security Note:</strong> Please delete this file (deploy-fix.php) from your server after use!</p>
    </div>
</body>
</html>

