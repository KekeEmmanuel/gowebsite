<?php
/**
 * Clear Laravel Log File Content (truncate to 0 bytes)
 * Upload to public_html and run: https://www.gotzsafari.com/clear-laravel-log.php
 * 
 * This script will try to clear the log file content without deleting it.
 * Sometimes this works even when deletion doesn't.
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

$laravelPath = '/home/gotzsafari/laravel';
$logFile = $laravelPath . '/storage/logs/laravel.log';

?>
<!DOCTYPE html>
<html>
<head>
    <title>Clear Laravel Log</title>
    <style>
        body { font-family: Arial; max-width: 900px; margin: 50px auto; padding: 20px; background: #1a1a1a; color: #fff; }
        .success { color: #4CAF50; padding: 10px; background: #2a2a2a; margin: 5px 0; }
        .error { color: #f44336; padding: 10px; background: #2a2a2a; margin: 5px 0; }
        .info { color: #2196F3; padding: 10px; background: #2a2a2a; margin: 5px 0; }
        .warning { color: #FF9800; padding: 10px; background: #2a2a2a; margin: 5px 0; }
        h1 { color: #4CAF50; }
        .section { background: #2a2a2a; padding: 15px; margin: 10px 0; border-left: 4px solid #4CAF50; }
    </style>
</head>
<body>
    <h1>🗑️ Clear Laravel Log File</h1>

<?php
echo "<div class='section'>";
echo "<h3>Step 1: Check Log File</h3>";

if (!file_exists($logFile)) {
    echo "<div class='error'>❌ Log file does not exist</div>";
    exit;
}

$fileSize = filesize($logFile);
$fileSizeMB = round($fileSize / 1024 / 1024, 2);
echo "<div class='info'>📊 Current log file size: " . number_format($fileSize) . " bytes ($fileSizeMB MB)</div>";

if ($fileSize == 0) {
    echo "<div class='info'>ℹ Log file is already empty</div>";
    exit;
}
echo "</div>";

echo "<div class='section'>";
echo "<h3>Step 2: Try to Clear Log File</h3>";

// Method 1: file_put_contents with empty string
echo "<div class='info'>Trying Method 1: file_put_contents (truncate)...</div>";
if (@file_put_contents($logFile, '') !== false) {
    $newSize = filesize($logFile);
    echo "<div class='success'>✓ SUCCESS! Log file cleared using file_put_contents</div>";
    echo "<div class='info'>📊 New file size: " . number_format($newSize) . " bytes</div>";
    
    // Write a test entry
    $testEntry = "[" . date('Y-m-d H:i:s') . "] local.INFO: Log file cleared successfully\n";
    if (@file_put_contents($logFile, $testEntry, FILE_APPEND) !== false) {
        echo "<div class='success'>✓ Successfully wrote test entry - log file is writable!</div>";
    } else {
        echo "<div class='warning'>⚠️ Could not write test entry - file may still have permission issues</div>";
    }
} else {
    echo "<div class='error'>❌ Method 1 failed</div>";
    
    // Method 2: fopen with truncate mode
    echo "<div class='info'>Trying Method 2: fopen with truncate mode...</div>";
    $handle = @fopen($logFile, 'w');
    if ($handle !== false) {
        fclose($handle);
        $newSize = filesize($logFile);
        echo "<div class='success'>✓ SUCCESS! Log file cleared using fopen</div>";
        echo "<div class='info'>📊 New file size: " . number_format($newSize) . " bytes</div>";
    } else {
        echo "<div class='error'>❌ Method 2 failed</div>";
        
        // Method 3: Try to read and check if we can at least read it
        echo "<div class='info'>Trying Method 3: Check file access...</div>";
        if (is_readable($logFile)) {
            echo "<div class='info'>✓ File is readable</div>";
        } else {
            echo "<div class='error'>❌ File is not readable</div>";
        }
        
        if (is_writable($logFile)) {
            echo "<div class='info'>✓ File is writable (but clearing failed - this is strange)</div>";
        } else {
            echo "<div class='error'>❌ File is NOT writable</div>";
        }
        
        echo "<div class='error'><strong>All methods failed. Manual steps required:</strong></div>";
    }
}
echo "</div>";

echo "<div class='section'>";
echo "<h3>✅ Summary</h3>";

if (file_exists($logFile)) {
    $finalSize = filesize($logFile);
    if ($finalSize == 0 || $finalSize < 1000) {
        echo "<div class='success'><strong>✓ SUCCESS!</strong> Log file has been cleared.</div>";
        echo "<div class='info'>📊 Freed up approximately $fileSizeMB MB of disk space</div>";
        echo "<div class='info'>Laravel can now write new log entries.</div>";
    } else {
        echo "<div class='error'><strong>❌ ISSUE:</strong> Could not clear log file.</div>";
        echo "<div class='info'><strong>Manual Steps:</strong></div>";
        echo "<div class='info'>1. Go to cPanel → File Manager</div>";
        echo "<div class='info'>2. Navigate to: <code>laravel/storage/logs/</code></div>";
        echo "<div class='info'>3. Try to <strong>DELETE</strong> laravel.log (sometimes deletion works when permission changes don't)</div>";
        echo "<div class='info'>4. Create a new empty file named <code>laravel.log</code></div>";
        echo "<div class='info'>5. Or contact your hosting provider to fix file ownership</div>";
    }
}
echo "</div>";
?>

</body>
</html>
