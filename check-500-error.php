<?php
/**
 * 500 Error Diagnostic Script
 * 
 * Upload this file to your server's public_html directory and access it via browser
 * to diagnose 500 Internal Server Error issues.
 * 
 * DELETE THIS FILE after checking for security!
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>500 Error Diagnostic</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 1000px; margin: 20px auto; padding: 20px; background: #f5f5f5; }
        .container { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 20px; }
        h1 { color: #d32f2f; }
        h2 { color: #1976d2; border-bottom: 2px solid #1976d2; padding-bottom: 10px; }
        .success { color: green; font-weight: bold; }
        .error { color: red; font-weight: bold; }
        .warning { color: orange; font-weight: bold; }
        .info { color: #1976d2; }
        pre { background: #f5f5f5; padding: 15px; border-radius: 4px; overflow-x: auto; border-left: 4px solid #1976d2; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #1976d2; color: white; }
        .status-ok { color: green; }
        .status-error { color: red; }
        .status-warning { color: orange; }
    </style>
</head>
<body>
    <h1>🔍 500 Internal Server Error Diagnostic</h1>
    
    <div class="container">
        <h2>1. PHP Configuration Check</h2>
        <table>
            <tr>
                <th>Setting</th>
                <th>Value</th>
                <th>Status</th>
            </tr>
            <?php
            $phpVersion = phpversion();
            $phpIni = php_ini_loaded_file();
            $displayErrors = ini_get('display_errors');
            $errorReporting = error_reporting();
            
            echo "<tr><td>PHP Version</td><td>{$phpVersion}</td><td class='status-ok'>✓</td></tr>";
            echo "<tr><td>php.ini Location</td><td>{$phpIni}</td><td class='status-ok'>✓</td></tr>";
            echo "<tr><td>display_errors</td><td>" . ($displayErrors ? 'On' : 'Off') . "</td><td>" . ($displayErrors ? '<span class="status-ok">✓</span>' : '<span class="status-warning">⚠</span>') . "</td></tr>";
            ?>
        </table>
    </div>

    <div class="container">
        <h2>2. File Existence Check</h2>
        <table>
            <tr>
                <th>File</th>
                <th>Path</th>
                <th>Status</th>
            </tr>
            <?php
            $filesToCheck = [
                '.htaccess' => __DIR__ . '/.htaccess',
                '.user.ini' => __DIR__ . '/.user.ini',
                'index.php' => __DIR__ . '/index.php',
                'laravel/index.php' => dirname(__DIR__) . '/laravel/public/index.php',
            ];
            
            foreach ($filesToCheck as $name => $path) {
                $exists = file_exists($path);
                $readable = $exists ? is_readable($path) : false;
                $status = $exists ? ($readable ? '<span class="status-ok">✓ Exists & Readable</span>' : '<span class="status-error">✗ Exists but not readable</span>') : '<span class="status-error">✗ Not found</span>';
                echo "<tr><td>{$name}</td><td>{$path}</td><td>{$status}</td></tr>";
            }
            ?>
        </table>
    </div>

    <div class="container">
        <h2>3. .htaccess Syntax Check</h2>
        <?php
        $htaccessPath = __DIR__ . '/.htaccess';
        if (file_exists($htaccessPath)) {
            $htaccessContent = file_get_contents($htaccessPath);
            echo "<p><strong>File exists:</strong> <span class='status-ok'>✓</span></p>";
            echo "<p><strong>File size:</strong> " . filesize($htaccessPath) . " bytes</p>";
            echo "<h3>File Contents:</h3>";
            echo "<pre>" . htmlspecialchars($htaccessContent) . "</pre>";
            
            // Check for common issues
            $issues = [];
            if (preg_match('/php_value\s+/i', $htaccessContent)) {
                $issues[] = "⚠ Contains php_value directives (may be disabled by host)";
            }
            if (preg_match('/php_flag\s+/i', $htaccessContent)) {
                $issues[] = "⚠ Contains php_flag directives (may be disabled by host)";
            }
            if (empty(trim($htaccessContent))) {
                $issues[] = "✗ File is empty";
            }
            
            if (empty($issues)) {
                echo "<p class='success'>✓ No obvious syntax issues detected</p>";
            } else {
                echo "<h3>Potential Issues:</h3><ul>";
                foreach ($issues as $issue) {
                    echo "<li class='warning'>{$issue}</li>";
                }
                echo "</ul>";
            }
        } else {
            echo "<p class='error'>✗ .htaccess file not found</p>";
        }
        ?>
    </div>

    <div class="container">
        <h2>4. .user.ini Check</h2>
        <?php
        $userIniPath = __DIR__ . '/.user.ini';
        if (file_exists($userIniPath)) {
            $userIniContent = file_get_contents($userIniPath);
            echo "<p><strong>File exists:</strong> <span class='status-ok'>✓</span></p>";
            echo "<p><strong>File size:</strong> " . filesize($userIniPath) . " bytes</p>";
            echo "<h3>File Contents:</h3>";
            echo "<pre>" . htmlspecialchars($userIniContent) . "</pre>";
        } else {
            echo "<p class='warning'>⚠ .user.ini file not found (may not be needed if using cPanel PHP settings)</p>";
        }
        ?>
    </div>

    <div class="container">
        <h2>5. PHP Error Log Check</h2>
        <?php
        $errorLog = ini_get('error_log');
        if ($errorLog && file_exists($errorLog)) {
            echo "<p><strong>Error log location:</strong> {$errorLog}</p>";
            $logSize = filesize($errorLog);
            echo "<p><strong>Log file size:</strong> " . number_format($logSize) . " bytes</p>";
            
            if ($logSize > 0) {
                $logLines = file($errorLog);
                $recentLines = array_slice($logLines, -20); // Last 20 lines
                echo "<h3>Recent Error Log Entries (last 20 lines):</h3>";
                echo "<pre>" . htmlspecialchars(implode('', $recentLines)) . "</pre>";
            } else {
                echo "<p class='info'>ℹ Error log is empty</p>";
            }
        } else {
            echo "<p class='warning'>⚠ Error log not found or not configured</p>";
            echo "<p><strong>Common locations to check:</strong></p>";
            echo "<ul>";
            echo "<li>/home/gotzsafari/public_html/error_log</li>";
            echo "<li>/home/gotzsafari/logs/error_log</li>";
            echo "<li>Check cPanel → Error Log</li>";
            echo "</ul>";
        }
        ?>
    </div>

    <div class="container">
        <h2>6. Laravel Log Check</h2>
        <?php
        $laravelLogPath = dirname(__DIR__) . '/laravel/storage/logs/laravel.log';
        if (file_exists($laravelLogPath)) {
            echo "<p><strong>Laravel log exists:</strong> <span class='status-ok'>✓</span></p>";
            $logSize = filesize($laravelLogPath);
            echo "<p><strong>Log file size:</strong> " . number_format($logSize) . " bytes</p>";
            
            if ($logSize > 0) {
                $logLines = file($laravelLogPath);
                $recentLines = array_slice($logLines, -30); // Last 30 lines
                echo "<h3>Recent Laravel Log Entries (last 30 lines):</h3>";
                echo "<pre>" . htmlspecialchars(implode('', $recentLines)) . "</pre>";
            } else {
                echo "<p class='info'>ℹ Laravel log is empty</p>";
            }
        } else {
            echo "<p class='warning'>⚠ Laravel log not found at: {$laravelLogPath}</p>";
            echo "<p>This is normal if the error occurs before Laravel loads.</p>";
        }
        ?>
    </div>

    <div class="container">
        <h2>7. File Permissions Check</h2>
        <table>
            <tr>
                <th>File/Directory</th>
                <th>Permissions</th>
                <th>Status</th>
            </tr>
            <?php
            $pathsToCheck = [
                'Current Directory' => __DIR__,
                '.htaccess' => __DIR__ . '/.htaccess',
                '.user.ini' => __DIR__ . '/.user.ini',
                'index.php' => __DIR__ . '/index.php',
            ];
            
            foreach ($pathsToCheck as $name => $path) {
                if (file_exists($path)) {
                    $perms = substr(sprintf('%o', fileperms($path)), -4);
                    $readable = is_readable($path);
                    $writable = is_writable($path);
                    $status = $readable ? '<span class="status-ok">✓ Readable</span>' : '<span class="status-error">✗ Not readable</span>';
                    if ($writable) {
                        $status .= ' <span class="status-warning">⚠ Writable</span>';
                    }
                    echo "<tr><td>{$name}</td><td>{$perms}</td><td>{$status}</td></tr>";
                }
            }
            ?>
        </table>
    </div>

    <div class="container">
        <h2>8. Test PHP Execution</h2>
        <?php
        echo "<p class='success'>✓ PHP is executing correctly (you're seeing this page)</p>";
        echo "<p><strong>Current time:</strong> " . date('Y-m-d H:i:s') . "</p>";
        echo "<p><strong>Server:</strong> " . ($_SERVER['SERVER_SOFTWARE'] ?? 'Unknown') . "</p>";
        ?>
    </div>

    <div class="container">
        <h2>9. Common 500 Error Causes</h2>
        <ul>
            <li><strong>.htaccess syntax error</strong> - Check section 3 above</li>
            <li><strong>php_value/php_flag disabled</strong> - Many hosts disable these in .htaccess</li>
            <li><strong>PHP memory limit exceeded</strong> - Check PHP configuration</li>
            <li><strong>File permissions</strong> - Check section 7 above</li>
            <li><strong>Missing files</strong> - Check section 2 above</li>
            <li><strong>Laravel bootstrap error</strong> - Check Laravel logs in section 6</li>
        </ul>
    </div>

    <div class="container">
        <h2>10. Quick Fixes to Try</h2>
        <ol>
            <li><strong>Rename .htaccess temporarily:</strong> Rename `.htaccess` to `.htaccess.backup` and test if site loads</li>
            <li><strong>Check cPanel Error Log:</strong> Go to cPanel → Metrics → Errors</li>
            <li><strong>Check Apache Error Log:</strong> Usually in cPanel → Metrics → Errors or /home/username/logs/error_log</li>
            <li><strong>Remove php_value from .htaccess:</strong> If section 3 shows php_value directives, remove them</li>
            <li><strong>Check file permissions:</strong> Ensure files are readable (644) and directories are executable (755)</li>
        </ol>
    </div>

    <hr>
    <p><strong>⚠ Security Note:</strong> Delete this file after checking for security!</p>
    <p><strong>📝 Next Steps:</strong> Check the sections above, especially sections 3 (.htaccess), 5 (PHP Error Log), and 6 (Laravel Log) for specific error messages.</p>
</body>
</html>

