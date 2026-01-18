<?php
/**
 * Fix Laravel Log File Permissions and Accessibility
 * Upload to public_html and run: https://www.gotzsafari.com/fix-laravel-logs.php
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

$laravelPath = '/home/gotzsafari/laravel';
$logPath = $laravelPath . '/storage/logs';
$logFile = $logPath . '/laravel.log';

?>
<!DOCTYPE html>
<html>
<head>
    <title>Fix Laravel Logs</title>
    <style>
        body { font-family: Arial; max-width: 900px; margin: 50px auto; padding: 20px; background: #1a1a1a; color: #fff; }
        .success { color: #4CAF50; padding: 10px; background: #2a2a2a; margin: 5px 0; }
        .error { color: #f44336; padding: 10px; background: #2a2a2a; margin: 5px 0; }
        .info { color: #2196F3; padding: 10px; background: #2a2a2a; margin: 5px 0; }
        .warning { color: #FF9800; padding: 10px; background: #2a2a2a; margin: 5px 0; }
        h1 { color: #4CAF50; }
        pre { background: #000; padding: 10px; overflow-x: auto; font-size: 11px; max-height: 400px; overflow-y: auto; }
        .section { background: #2a2a2a; padding: 15px; margin: 10px 0; border-left: 4px solid #4CAF50; }
    </style>
</head>
<body>
    <h1>🔧 Fix Laravel Log File Permissions</h1>

<?php
echo "<div class='section'>";
echo "<h3>Step 1: Check Laravel Directory Structure</h3>";

// Check if Laravel directory exists
if (!is_dir($laravelPath)) {
    echo "<div class='error'>❌ Laravel directory not found: $laravelPath</div>";
    exit;
} else {
    echo "<div class='success'>✓ Laravel directory exists</div>";
}

// Check storage directory
$storagePath = $laravelPath . '/storage';
if (!is_dir($storagePath)) {
    echo "<div class='error'>❌ Storage directory not found</div>";
} else {
    echo "<div class='success'>✓ Storage directory exists</div>";
    
    // Try to fix storage permissions
    if (@chmod($storagePath, 0755)) {
        echo "<div class='success'>✓ Fixed storage directory permissions (755)</div>";
    } else {
        echo "<div class='warning'>⚠️ Could not change storage permissions (may need SSH)</div>";
    }
}

// Check logs directory
if (!is_dir($logPath)) {
    echo "<div class='warning'>⚠️ Logs directory not found - creating it</div>";
    if (@mkdir($logPath, 0755, true)) {
        echo "<div class='success'>✓ Created logs directory</div>";
    } else {
        echo "<div class='error'>❌ Failed to create logs directory</div>";
    }
} else {
    echo "<div class='success'>✓ Logs directory exists</div>";
    
    // Try to fix logs directory permissions
    if (@chmod($logPath, 0755)) {
        echo "<div class='success'>✓ Fixed logs directory permissions (755)</div>";
    } else {
        echo "<div class='warning'>⚠️ Could not change logs directory permissions</div>";
    }
}
echo "</div>";

echo "<div class='section'>";
echo "<h3>Step 2: Check Log File</h3>";

// Check if log file exists
if (!file_exists($logFile)) {
    echo "<div class='warning'>⚠️ Log file does not exist - creating it</div>";
    
    // Try to create the log file
    if (@touch($logFile)) {
        echo "<div class='success'>✓ Created log file</div>";
    } else {
        echo "<div class='error'>❌ Failed to create log file</div>";
        echo "<div class='info'>Trying to create via file_put_contents...</div>";
        if (@file_put_contents($logFile, '') !== false) {
            echo "<div class='success'>✓ Created log file via file_put_contents</div>";
        } else {
            echo "<div class='error'>❌ Still failed. You may need to create it manually via SSH or cPanel File Manager.</div>";
        }
    }
} else {
    echo "<div class='success'>✓ Log file exists</div>";
    
    // Check file size
    $fileSize = filesize($logFile);
    $fileSizeMB = round($fileSize / 1024 / 1024, 2);
    echo "<div class='info'>📊 Log file size: " . number_format($fileSize) . " bytes ($fileSizeMB MB)</div>";
    
    if ($fileSize > 100 * 1024 * 1024) { // 100MB
        echo "<div class='warning'>⚠️ Log file is very large ($fileSizeMB MB). Consider rotating it.</div>";
    }
    
    // Check if file is readable
    if (is_readable($logFile)) {
        echo "<div class='success'>✓ Log file is readable</div>";
    } else {
        echo "<div class='error'>❌ Log file is NOT readable</div>";
    }
    
    // Check if file is writable
    if (is_writable($logFile)) {
        echo "<div class='success'>✓ Log file is writable</div>";
    } else {
        echo "<div class='error'>❌ Log file is NOT writable</div>";
    }
}

// Try to fix log file permissions
if (file_exists($logFile)) {
    if (@chmod($logFile, 0664)) {
        echo "<div class='success'>✓ Set log file permissions to 664</div>";
    } else {
        echo "<div class='warning'>⚠️ Could not change log file permissions (may need SSH)</div>";
    }
    
    // Check current permissions
    $perms = fileperms($logFile);
    $permsString = substr(sprintf('%o', $perms), -4);
    echo "<div class='info'>📋 Current permissions: $permsString</div>";
}
echo "</div>";

echo "<div class='section'>";
echo "<h3>Step 3: Test Log File Access</h3>";

// Try to read the log file
if (file_exists($logFile) && is_readable($logFile)) {
    echo "<div class='info'>📖 Reading last 20 lines of log file...</div>";
    
    $lines = file($logFile);
    $lastLines = array_slice($lines, -20);
    
    if (count($lastLines) > 0) {
        echo "<div class='success'>✓ Successfully read log file</div>";
        echo "<pre>" . htmlspecialchars(implode('', $lastLines)) . "</pre>";
    } else {
        echo "<div class='info'>ℹ Log file is empty</div>";
    }
} else {
    echo "<div class='error'>❌ Cannot read log file</div>";
}

// Try to write to the log file
if (file_exists($logFile) && is_writable($logFile)) {
    $testMessage = "\n[" . date('Y-m-d H:i:s') . "] test.INFO: Log file write test successful\n";
    if (@file_put_contents($logFile, $testMessage, FILE_APPEND) !== false) {
        echo "<div class='success'>✓ Successfully wrote test message to log file</div>";
    } else {
        echo "<div class='error'>❌ Failed to write to log file</div>";
    }
} else {
    echo "<div class='error'>❌ Cannot write to log file</div>";
}
echo "</div>";

echo "<div class='section'>";
echo "<h3>Step 4: Check Storage Subdirectories</h3>";

$storageDirs = [
    'framework/cache',
    'framework/sessions',
    'framework/views',
    'app/public',
];

foreach ($storageDirs as $dir) {
    $fullPath = $storagePath . '/' . $dir;
    if (is_dir($fullPath)) {
        echo "<div class='success'>✓ $dir exists</div>";
        @chmod($fullPath, 0755);
    } else {
        echo "<div class='warning'>⚠️ $dir does not exist</div>";
        if (@mkdir($fullPath, 0755, true)) {
            echo "<div class='success'>✓ Created $dir</div>";
        }
    }
}
echo "</div>";

echo "<div class='section'>";
echo "<h3>✅ Summary</h3>";
echo "<div class='info'>If you still can't access logs, try:</div>";
echo "<div class='info'>1. Via SSH: <code>chmod -R 755 /home/gotzsafari/laravel/storage</code></div>";
echo "<div class='info'>2. Via SSH: <code>chmod -R 664 /home/gotzsafari/laravel/storage/logs/*.log</code></div>";
echo "<div class='info'>3. Check file ownership: <code>ls -la /home/gotzsafari/laravel/storage/logs/</code></div>";
echo "<div class='info'>4. Files should be owned by your user or the web server user (usually 'nobody' or 'apache')</div>";
echo "</div>";
?>

</body>
</html>
