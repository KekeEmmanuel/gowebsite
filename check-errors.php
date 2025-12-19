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
        .warning { background: #3a3a1a; padding: 15px; margin: 10px 0; border-left: 4px solid #FFD700; }
        pre { background: #1a1a1a; padding: 10px; overflow-x: auto; border: 1px solid #444; max-height: 500px; overflow-y: auto; font-size: 11px; }
        .success { color: #4CAF50; }
        .warning-text { color: #FFD700; }
    </style>
</head>
<body>
<div class="container">
    <h1>🔍 Laravel Error Check</h1>
    <p>Checking for errors at <?php echo date('Y-m-d H:i:s'); ?></p>
    <hr>

<?php

// Check if log file exists
if (file_exists($logFile)) {
    echo "<div class='info'>";
    echo "<strong>✓ Log file found:</strong> $logFile<br>";
    echo "<strong>File size:</strong> " . filesize($logFile) . " bytes<br>";
    echo "<strong>Last modified:</strong> " . date('Y-m-d H:i:s', filemtime($logFile)) . "<br>";
    echo "</div>";
    
    // Read last 200 lines of log
    $lines = file($logFile);
    $lastLines = array_slice($lines, -200);
    
    echo "<div class='error'>";
    echo "<h3>Last 200 lines of Laravel log:</h3>";
    echo "<pre>" . htmlspecialchars(implode('', $lastLines)) . "</pre>";
    echo "</div>";
} else {
    echo "<div class='warning'>";
    echo "<strong>⚠ Log file does NOT exist:</strong> $logFile<br>";
    echo "This might mean Laravel hasn't written any errors yet, or the path is incorrect.<br>";
    echo "</div>";
}

// Check storage/logs directory
$logsDir = $laravelPath . '/storage/logs';
if (is_dir($logsDir)) {
    echo "<div class='info'>";
    echo "<span class='success'>✓ Logs directory exists</span><br>";
    
    // List all log files
    $logFiles = glob($logsDir . '/*.log');
    if (!empty($logFiles)) {
        echo "<strong>Log files found:</strong><br>";
        foreach ($logFiles as $file) {
            $fileName = basename($file);
            $fileSize = filesize($file);
            $lastModified = date('Y-m-d H:i:s', filemtime($file));
            echo "— $fileName ($fileSize bytes, modified: $lastModified)<br>";
        }
    }
    echo "</div>";
} else {
    echo "<div class='error'>";
    echo "<strong>✗ Logs directory does NOT exist:</strong> $logsDir<br>";
    echo "</div>";
}

// Check .env file
$envFile = $laravelPath . '/.env';
if (file_exists($envFile)) {
    echo "<div class='info'>";
    echo "<span class='success'>✓ .env file exists</span><br>";
    echo "</div>";
} else {
    echo "<div class='error'>";
    echo "<strong>✗ .env file does NOT exist</strong><br>";
    echo "</div>";
}

// Check storage permissions
$storagePath = $laravelPath . '/storage';
if (is_dir($storagePath)) {
    $isWritable = is_writable($storagePath);
    echo "<div class='" . ($isWritable ? 'info' : 'error') . "'>";
    echo ($isWritable ? "<span class='success'>✓" : "<strong>✗") . " Storage directory is " . ($isWritable ? "writable" : "NOT writable") . "</span><br>";
    echo "</div>";
} else {
    echo "<div class='error'>";
    echo "<strong>✗ Storage directory does NOT exist</strong><br>";
    echo "</div>";
}

// Check bootstrap/cache permissions
$bootstrapCachePath = $laravelPath . '/bootstrap/cache';
if (is_dir($bootstrapCachePath)) {
    $isWritable = is_writable($bootstrapCachePath);
    echo "<div class='" . ($isWritable ? 'info' : 'error') . "'>";
    echo ($isWritable ? "<span class='success'>✓" : "<strong>✗") . " Bootstrap cache directory is " . ($isWritable ? "writable" : "NOT writable") . "</span><br>";
    echo "</div>";
} else {
    echo "<div class='error'>";
    echo "<strong>✗ Bootstrap cache directory does NOT exist</strong><br>";
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
    echo "You may need to run: <code>composer install</code><br>";
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
    echo "<h3>PHP Error Log (last 100 lines):</h3>";
    $errorLines = file($errorLog);
    $lastErrorLines = array_slice($errorLines, -100);
    echo "<pre>" . htmlspecialchars(implode('', $lastErrorLines)) . "</pre>";
    echo "</div>";
}

// Check public_html error_log
$publicErrorLog = '/home/gotzsafari/public_html/error_log';
if (file_exists($publicErrorLog)) {
    echo "<div class='error'>";
    echo "<h3>Public HTML Error Log (last 100 lines):</h3>";
    $errorLines = file($publicErrorLog);
    $lastErrorLines = array_slice($errorLines, -100);
    echo "<pre>" . htmlspecialchars(implode('', $lastErrorLines)) . "</pre>";
    echo "</div>";
}

// Check PHP version
echo "<div class='info'>";
echo "<h3>PHP Information:</h3>";
echo "<strong>PHP Version:</strong> " . phpversion() . "<br>";
echo "<strong>PHP SAPI:</strong> " . php_sapi_name() . "<br>";
echo "</div>";

?>

    <hr>
    <p><strong>⚠️  SECURITY WARNING:</strong> Delete this file (check-errors.php) immediately after use!</p>
</div>
</body>
</html>
