<?php
/**
 * Laravel Error Checker
 * 
 * This script reads the Laravel log and finds the actual error messages
 * 
 * DELETE THIS FILE after checking for security!
 */

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Laravel Error Check</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 1200px; margin: 20px auto; padding: 20px; background: #f5f5f5; }
        .container { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 20px; }
        h1 { color: #d32f2f; }
        h2 { color: #1976d2; }
        .error { color: red; font-weight: bold; background: #ffebee; padding: 10px; border-left: 4px solid red; margin: 10px 0; }
        .warning { color: orange; background: #fff3e0; padding: 10px; border-left: 4px solid orange; margin: 10px 0; }
        pre { background: #f5f5f5; padding: 15px; border-radius: 4px; overflow-x: auto; border-left: 4px solid #1976d2; max-height: 600px; overflow-y: auto; }
        .timestamp { color: #666; font-size: 0.9em; }
    </style>
</head>
<body>
    <h1>🔍 Laravel Error Check</h1>
    
    <?php
    $laravelLogPath = dirname(__DIR__) . '/laravel/storage/logs/laravel.log';
    
    if (!file_exists($laravelLogPath)) {
        echo "<div class='error'>✗ Laravel log not found at: {$laravelLogPath}</div>";
        exit;
    }
    
    $logContent = file_get_contents($laravelLogPath);
    $logSize = filesize($laravelLogPath);
    
    echo "<div class='container'>";
    echo "<h2>Log File Info</h2>";
    echo "<p><strong>Path:</strong> {$laravelLogPath}</p>";
    echo "<p><strong>Size:</strong> " . number_format($logSize) . " bytes (" . number_format($logSize / 1024 / 1024, 2) . " MB)</p>";
    echo "</div>";
    
    // Find all error entries
    preg_match_all('/\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\].*?(?=\[\d{4}-\d{2}-\d{2}|$)/s', $logContent, $matches, PREG_SET_ORDER);
    
    // Get last 10 error entries
    $recentErrors = array_slice($matches, -10);
    
    if (empty($recentErrors)) {
        echo "<div class='container'>";
        echo "<h2>No Recent Errors Found</h2>";
        echo "<p>The log file exists but no recent error entries were found.</p>";
        echo "</div>";
    } else {
        echo "<div class='container'>";
        echo "<h2>Recent Error Entries (Last 10)</h2>";
        
        foreach (array_reverse($recentErrors) as $index => $match) {
            $timestamp = $match[1];
            $errorContent = trim($match[0]);
            
            // Check if it's a real error (not just a stack trace)
            if (preg_match('/local\.ERROR|local\.CRITICAL|local\.ALERT|local\.EMERGENCY/i', $errorContent)) {
                echo "<div class='error'>";
                echo "<h3>Error Entry #" . (count($recentErrors) - $index) . "</h3>";
                echo "<p class='timestamp'>Timestamp: {$timestamp}</p>";
                echo "<pre>" . htmlspecialchars($errorContent) . "</pre>";
                echo "</div>";
            } elseif (preg_match('/Exception|Error|Fatal|Parse error|syntax error/i', $errorContent)) {
                echo "<div class='warning'>";
                echo "<h3>Warning/Exception Entry #" . (count($recentErrors) - $index) . "</h3>";
                echo "<p class='timestamp'>Timestamp: {$timestamp}</p>";
                echo "<pre>" . htmlspecialchars(substr($errorContent, 0, 2000)) . (strlen($errorContent) > 2000 ? "\n... (truncated)" : "") . "</pre>";
                echo "</div>";
            }
        }
        echo "</div>";
    }
    
    // Also check for specific error patterns
    echo "<div class='container'>";
    echo "<h2>Error Pattern Search</h2>";
    
    $patterns = [
        '500' => '/500|Internal Server Error/i',
        '500 Error' => '/500.*error|internal.*server.*error/i',
        'Class not found' => '/Class.*not found|Class.*does not exist/i',
        'Method not found' => '/Method.*not found|Call to undefined method/i',
        'File not found' => '/File.*not found|No such file/i',
        'Permission denied' => '/Permission denied|access denied/i',
        'Memory limit' => '/memory.*limit|Allowed memory size/i',
        'Parse error' => '/Parse error|syntax error/i',
        'Fatal error' => '/Fatal error/i',
    ];
    
    foreach ($patterns as $name => $pattern) {
        if (preg_match($pattern, $logContent)) {
            $matches = [];
            preg_match_all($pattern, $logContent, $matches);
            echo "<p class='error'>✗ Found <strong>{$name}</strong>: " . count($matches[0]) . " occurrence(s)</p>";
        }
    }
    echo "</div>";
    
    // Show last 50 lines of log
    echo "<div class='container'>";
    echo "<h2>Last 50 Lines of Log</h2>";
    $logLines = file($laravelLogPath);
    $lastLines = array_slice($logLines, -50);
    echo "<pre>" . htmlspecialchars(implode('', $lastLines)) . "</pre>";
    echo "</div>";
    ?>
    
    <hr>
    <p><strong>⚠ Security Note:</strong> Delete this file after checking for security!</p>
</body>
</html>

