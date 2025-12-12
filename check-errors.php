<?php
/**
 * Check Laravel Error Logs
 * Run this script via browser: https://www.gotzsafari.com/check-errors.php
 * IMPORTANT: Delete this file after use!
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

$laravelPath = '/home/gotzsafari/laravel';
$logFile = $laravelPath . '/storage/logs/laravel.log';

?>
<!DOCTYPE html>
<html>
<head>
    <title>Laravel Error Check</title>
    <style>
        body { font-family: monospace; padding: 20px; background: #1a1a1a; color: #fff; }
        .container { max-width: 1200px; margin: 0 auto; }
        h1 { color: #4CAF50; }
        .error { background: #3a1a1a; padding: 15px; margin: 10px 0; border-left: 4px solid #f44336; }
        .info { background: #2a2a2a; padding: 15px; margin: 10px 0; border-left: 4px solid #4CAF50; }
        pre { background: #1a1a1a; padding: 10px; overflow-x: auto; border: 1px solid #444; max-height: 500px; overflow-y: auto; }
        .success { color: #4CAF50; }
        .warning { color: #FFD700; }
    </style>
</head>
<body>
<div class="container">
    <h1>🔍 Laravel Error Check</h1>
    <hr>

<?php

// Check if log file exists
if (file_exists($logFile)) {
    echo "<div class='info'>";
    echo "<strong>✓ Log file found:</strong> $logFile<br>";
    echo "<strong>File size:</strong> " . filesize($logFile) . " bytes<br>";
    echo "<strong>Last modified:</strong> " . date('Y-m-d H:i:s', filemtime($logFile)) . "<br>";
    echo "</div>";
    
    // Read last 100 lines of log
    $lines = file($logFile);
    $lastLines = array_slice($lines, -100);
    
    echo "<div class='error'>";
    echo "<h3>Last 100 lines of Laravel log:</h3>";
    echo "<pre>" . htmlspecialchars(implode('', $lastLines)) . "</pre>";
    echo "</div>";
} else {
    echo "<div class='error'>";
    echo "<strong>✗ Log file not found:</strong> $logFile<br>";
    echo "<strong>Storage directory exists:</strong> " . (is_dir($laravelPath . '/storage') ? "Yes" : "No") . "<br>";
    echo "<strong>Storage/logs directory exists:</strong> " . (is_dir($laravelPath . '/storage/logs') ? "Yes" : "No") . "<br>";
    echo "</div>";
}

// Check .env file
$envFile = $laravelPath . '/.env';
if (file_exists($envFile)) {
    echo "<div class='info'>";
    echo "<strong>✓ .env file exists</strong><br>";
    $envContent = file_get_contents($envFile);
    // Check for APP_KEY
    if (strpos($envContent, 'APP_KEY=base64:') !== false) {
        echo "<span class='success'>✓ APP_KEY is set</span><br>";
    } else {
        echo "<span class='warning'>⚠ APP_KEY is NOT set or invalid</span><br>";
    }
    // Check for database config
    if (strpos($envContent, 'DB_CONNECTION=mysql') !== false) {
        echo "<span class='success'>✓ Database connection is set to MySQL</span><br>";
    }
    echo "</div>";
} else {
    echo "<div class='error'>";
    echo "<strong>✗ .env file not found</strong><br>";
    echo "</div>";
}

// Check storage permissions
$storagePath = $laravelPath . '/storage';
if (is_dir($storagePath)) {
    $perms = substr(sprintf('%o', fileperms($storagePath)), -4);
    echo "<div class='info'>";
    echo "<strong>Storage permissions:</strong> $perms<br>";
    if ($perms >= '0755') {
        echo "<span class='success'>✓ Permissions look good</span><br>";
    } else {
        echo "<span class='warning'>⚠ Permissions might be too restrictive</span><br>";
    }
    echo "</div>";
}

// Check bootstrap/cache permissions
$bootstrapCachePath = $laravelPath . '/bootstrap/cache';
if (is_dir($bootstrapCachePath)) {
    $perms = substr(sprintf('%o', fileperms($bootstrapCachePath)), -4);
    echo "<div class='info'>";
    echo "<strong>Bootstrap cache permissions:</strong> $perms<br>";
    echo "</div>";
}

// Check if vendor directory exists
if (is_dir($laravelPath . '/vendor')) {
    echo "<div class='info'>";
    echo "<span class='success'>✓ Vendor directory exists</span><br>";
    echo "</div>";
} else {
    echo "<div class='error'>";
    echo "<strong>✗ Vendor directory does NOT exist</strong><br>";
    echo "</div>";
}

// Check if artisan exists
if (file_exists($laravelPath . '/artisan')) {
    echo "<div class='info'>";
    echo "<span class='success'>✓ Artisan file exists</span><br>";
    echo "</div>";
} else {
    echo "<div class='error'>";
    echo "<strong>✗ Artisan file does NOT exist</strong><br>";
    echo "</div>";
}

// Try to get PHP error from error_log
$errorLog = $laravelPath . '/error_log';
if (file_exists($errorLog)) {
    echo "<div class='error'>";
    echo "<h3>PHP Error Log (last 50 lines):</h3>";
    $errorLines = file($errorLog);
    $lastErrorLines = array_slice($errorLines, -50);
    echo "<pre>" . htmlspecialchars(implode('', $lastErrorLines)) . "</pre>";
    echo "</div>";
}

?>

    <hr>
    <p><strong>⚠️  SECURITY WARNING:</strong> Delete this file (check-errors.php) immediately after use!</p>
</div>
</body>
</html>

