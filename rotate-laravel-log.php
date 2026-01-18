<?php
/**
 * Rotate Laravel Log File (creates new file with proper permissions)
 * Upload to public_html and run: https://www.gotzsafari.com/rotate-laravel-log.php
 * 
 * This script will:
 * 1. Archive the current log file
 * 2. Create a new empty log file
 * 3. The new file should have writable permissions
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

$laravelPath = '/home/gotzsafari/laravel';
$logPath = $laravelPath . '/storage/logs';
$logFile = $logPath . '/laravel.log';
$archiveFile = $logPath . '/laravel-' . date('Y-m-d_His') . '.log';

?>
<!DOCTYPE html>
<html>
<head>
    <title>Rotate Laravel Log</title>
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
    <h1>🔄 Rotate Laravel Log File</h1>

<?php
echo "<div class='section'>";
echo "<h3>Step 1: Check Current Log File</h3>";

if (!file_exists($logFile)) {
    echo "<div class='error'>❌ Log file does not exist</div>";
    exit;
}

$fileSize = filesize($logFile);
$fileSizeMB = round($fileSize / 1024 / 1024, 2);
echo "<div class='info'>📊 Current log file size: " . number_format($fileSize) . " bytes ($fileSizeMB MB)</div>";

if ($fileSize > 0) {
    echo "<div class='warning'>⚠️ Log file is large ($fileSizeMB MB). Archiving it will free up space.</div>";
}
echo "</div>";

echo "<div class='section'>";
echo "<h3>Step 2: Archive Current Log File</h3>";

// Try to copy the log file to archive
if (@copy($logFile, $archiveFile)) {
    echo "<div class='success'>✓ Log file archived to: " . basename($archiveFile) . "</div>";
    
    // Check archive file size
    $archiveSize = filesize($archiveFile);
    echo "<div class='info'>📊 Archive size: " . number_format($archiveSize) . " bytes</div>";
} else {
    echo "<div class='error'>❌ Failed to archive log file</div>";
    echo "<div class='info'>Trying alternative method...</div>";
    
    // Alternative: read and write
    $content = @file_get_contents($logFile);
    if ($content !== false && @file_put_contents($archiveFile, $content) !== false) {
        echo "<div class='success'>✓ Log file archived using alternative method</div>";
    } else {
        echo "<div class='error'>❌ Could not archive log file. You may need to delete it manually or use SSH.</div>";
        echo "<div class='info'>Continuing to create new log file...</div>";
    }
}
echo "</div>";

echo "<div class='section'>";
echo "<h3>Step 3: Create New Log File</h3>";

// Delete old log file
if (file_exists($logFile)) {
    if (@unlink($logFile)) {
        echo "<div class='success'>✓ Old log file deleted</div>";
    } else {
        echo "<div class='warning'>⚠️ Could not delete old log file (may need manual deletion)</div>";
    }
}

// Create new empty log file
if (@touch($logFile)) {
    echo "<div class='success'>✓ New log file created</div>";
    
    // Try to set permissions
    @chmod($logFile, 0664);
    
    // Check if writable
    if (is_writable($logFile)) {
        echo "<div class='success'>✓ New log file is writable!</div>";
        
        // Write a test entry
        $testEntry = "[" . date('Y-m-d H:i:s') . "] local.INFO: Log file rotated successfully\n";
        if (@file_put_contents($logFile, $testEntry, FILE_APPEND) !== false) {
            echo "<div class='success'>✓ Successfully wrote test entry to log file</div>";
        }
    } else {
        echo "<div class='warning'>⚠️ New log file is still not writable</div>";
        echo "<div class='info'>You may need to contact your hosting provider to fix file ownership</div>";
    }
    
    // Show file info
    $perms = fileperms($logFile);
    $permsString = substr(sprintf('%o', $perms), -4);
    echo "<div class='info'>📋 New log file permissions: $permsString</div>";
    
    $newSize = filesize($logFile);
    echo "<div class='info'>📊 New log file size: " . number_format($newSize) . " bytes</div>";
} else {
    echo "<div class='error'>❌ Failed to create new log file</div>";
    echo "<div class='info'>Try creating it manually via cPanel File Manager or SSH</div>";
}
echo "</div>";

echo "<div class='section'>";
echo "<h3>✅ Summary</h3>";

if (file_exists($logFile) && is_writable($logFile)) {
    echo "<div class='success'><strong>✓ SUCCESS!</strong> Log file has been rotated and the new file is writable.</div>";
    echo "<div class='info'>Laravel can now write new log entries.</div>";
    
    if (file_exists($archiveFile)) {
        echo "<div class='info'>📦 Old log archived to: " . basename($archiveFile) . "</div>";
        echo "<div class='warning'>⚠️ You can delete the archive file later to free up space if needed.</div>";
    }
} else {
    echo "<div class='error'><strong>❌ ISSUE:</strong> Log file is still not writable.</div>";
    echo "<div class='info'><strong>Next steps:</strong></div>";
    echo "<div class='info'>1. Contact your hosting provider</div>";
    echo "<div class='info'>2. Ask them to fix ownership of: <code>$logPath</code></div>";
    echo "<div class='info'>3. Or use SSH: <code>chown -R youruser:nobody $logPath</code></div>";
}
echo "</div>";
?>

</body>
</html>
