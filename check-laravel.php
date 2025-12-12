<?php
/**
 * Laravel Installation Diagnostic Script
 * Run this script via browser: https://www.gotzsafari.com/check-laravel.php
 */

$laravelPath = '/home/gotzsafari/laravel';
$phpPath = '/usr/local/bin/php';

?>
<!DOCTYPE html>
<html>
<head>
    <title>Laravel Installation Check</title>
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
    <h1>🔍 Laravel Installation Diagnostic</h1>
    <hr>

<?php

echo "<div class='step'>";
echo "<h3>1. Checking Laravel Directory</h3>";
if (is_dir($laravelPath)) {
    echo "<p style='color: green;'>✓ Directory exists: $laravelPath</p>";
    echo "<p>Directory permissions: " . substr(sprintf('%o', fileperms($laravelPath)), -4) . "</p>";
} else {
    echo "<p style='color: red;'>✗ Directory does not exist: $laravelPath</p>";
}
echo "</div>";

echo "<div class='step'>";
echo "<h3>2. Checking artisan file</h3>";
$artisanPath = "$laravelPath/artisan";
if (file_exists($artisanPath)) {
    echo "<p style='color: green;'>✓ artisan file exists: $artisanPath</p>";
    echo "<p>File permissions: " . substr(sprintf('%o', fileperms($artisanPath)), -4) . "</p>";
    echo "<p>File size: " . filesize($artisanPath) . " bytes</p>";
    echo "<p>Is readable: " . (is_readable($artisanPath) ? "Yes" : "No") . "</p>";
    echo "<p>Is executable: " . (is_executable($artisanPath) ? "Yes" : "No") . "</p>";
} else {
    echo "<p style='color: red;'>✗ artisan file does not exist: $artisanPath</p>";
}
echo "</div>";

echo "<div class='step'>";
echo "<h3>3. Checking PHP</h3>";
if (file_exists($phpPath)) {
    echo "<p style='color: green;'>✓ PHP exists: $phpPath</p>";
    echo "<p>Is executable: " . (is_executable($phpPath) ? "Yes" : "No") . "</p>";
    exec("$phpPath --version 2>&1", $phpVersion, $phpReturn);
    if ($phpReturn === 0) {
        echo "<p>PHP Version:</p>";
        echo "<pre>" . htmlspecialchars(implode("\n", $phpVersion)) . "</pre>";
    } else {
        echo "<p style='color: red;'>✗ PHP version check failed</p>";
    }
} else {
    echo "<p style='color: red;'>✗ PHP does not exist: $phpPath</p>";
}
echo "</div>";

echo "<div class='step'>";
echo "<h3>4. Testing artisan command</h3>";
if (file_exists($artisanPath)) {
    chdir($laravelPath);
    $testCmd = "$phpPath artisan --version 2>&1";
    echo "<p>Running: <code>$testCmd</code></p>";
    exec($testCmd, $artisanOutput, $artisanReturn);
    echo "<p>Exit code: $artisanReturn</p>";
    if (!empty($artisanOutput)) {
        echo "<pre>" . htmlspecialchars(implode("\n", $artisanOutput)) . "</pre>";
    } else {
        echo "<p style='color: red;'>No output returned</p>";
    }
    
    // Try with full path
    echo "<h4>Testing with full path:</h4>";
    $testCmd2 = "$phpPath $artisanPath --version 2>&1";
    echo "<p>Running: <code>$testCmd2</code></p>";
    exec($testCmd2, $artisanOutput2, $artisanReturn2);
    echo "<p>Exit code: $artisanReturn2</p>";
    if (!empty($artisanOutput2)) {
        echo "<pre>" . htmlspecialchars(implode("\n", $artisanOutput2)) . "</pre>";
    } else {
        echo "<p style='color: red;'>No output returned</p>";
    }
} else {
    echo "<p style='color: red;'>Cannot test - artisan file does not exist</p>";
}
echo "</div>";

echo "<div class='step'>";
echo "<h3>5. Checking .env file</h3>";
$envPath = "$laravelPath/.env";
if (file_exists($envPath)) {
    echo "<p style='color: green;'>✓ .env file exists: $envPath</p>";
    echo "<p>File permissions: " . substr(sprintf('%o', fileperms($envPath)), -4) . "</p>";
    echo "<p>File size: " . filesize($envPath) . " bytes</p>";
} else {
    echo "<p style='color: red;'>✗ .env file does not exist: $envPath</p>";
}
echo "</div>";

echo "<div class='step'>";
echo "<h3>6. Checking vendor directory</h3>";
$vendorPath = "$laravelPath/vendor";
if (is_dir($vendorPath)) {
    echo "<p style='color: green;'>✓ vendor directory exists</p>";
    $vendorAutoload = "$vendorPath/autoload.php";
    if (file_exists($vendorAutoload)) {
        echo "<p style='color: green;'>✓ vendor/autoload.php exists</p>";
    } else {
        echo "<p style='color: red;'>✗ vendor/autoload.php does not exist</p>";
    }
} else {
    echo "<p style='color: red;'>✗ vendor directory does not exist - Composer dependencies may not be installed</p>";
}
echo "</div>";

echo "<div class='step'>";
echo "<h3>7. Checking bootstrap directory</h3>";
$bootstrapPath = "$laravelPath/bootstrap";
if (is_dir($bootstrapPath)) {
    echo "<p style='color: green;'>✓ bootstrap directory exists</p>";
    $bootstrapApp = "$bootstrapPath/app.php";
    if (file_exists($bootstrapApp)) {
        echo "<p style='color: green;'>✓ bootstrap/app.php exists</p>";
    } else {
        echo "<p style='color: red;'>✗ bootstrap/app.php does not exist</p>";
    }
} else {
    echo "<p style='color: red;'>✗ bootstrap directory does not exist</p>";
}
echo "</div>";

echo "<div class='step'>";
echo "<h3>8. Reading artisan file content</h3>";
if (file_exists($artisanPath)) {
    $artisanContent = file_get_contents($artisanPath);
    echo "<p>First 500 characters of artisan file:</p>";
    echo "<pre style='max-height: 200px; overflow-y: auto;'>" . htmlspecialchars(substr($artisanContent, 0, 500)) . "</pre>";
}
echo "</div>";

echo "<div class='step'>";
echo "<h3>9. Testing PHP error reporting</h3>";
$testPhp = "$phpPath -r 'error_reporting(E_ALL); ini_set(\"display_errors\", 1); require \"$laravelPath/vendor/autoload.php\";' 2>&1";
echo "<p>Running: <code>$testPhp</code></p>";
exec($testPhp, $phpTestOutput, $phpTestReturn);
echo "<p>Exit code: $phpTestReturn</p>";
if (!empty($phpTestOutput)) {
    echo "<pre>" . htmlspecialchars(implode("\n", $phpTestOutput)) . "</pre>";
} else {
    echo "<p>No output</p>";
}
echo "</div>";

echo "<div class='step'>";
echo "<h3>10. Listing Laravel directory contents</h3>";
if (is_dir($laravelPath)) {
    $files = scandir($laravelPath);
    $files = array_filter($files, function($file) {
        return $file !== '.' && $file !== '..';
    });
    echo "<p>Files and directories in Laravel root:</p>";
    echo "<ul>";
    foreach (array_slice($files, 0, 30) as $file) {
        $filePath = "$laravelPath/$file";
        $type = is_dir($filePath) ? "DIR" : "FILE";
        echo "<li>$file ($type)</li>";
    }
    echo "</ul>";
}
echo "</div>";

?>

</div>
</body>
</html>

