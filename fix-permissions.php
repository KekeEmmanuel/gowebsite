<?php
/**
 * Fix File Permissions
 * Upload to public_html and run: https://www.gotzsafari.com/fix-permissions.php
 * This script will fix file and directory permissions
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

$publicHtmlPath = '/home/gotzsafari/public_html';
$laravelPath = '/home/gotzsafari/laravel';

?>
<!DOCTYPE html>
<html>
<head>
    <title>Fix File Permissions</title>
    <style>
        body { font-family: Arial; max-width: 900px; margin: 50px auto; padding: 20px; background: #1a1a1a; color: #fff; }
        .success { color: #4CAF50; padding: 10px; background: #2a2a2a; margin: 5px 0; border-left: 4px solid #4CAF50; }
        .error { color: #f44336; padding: 10px; background: #2a2a2a; margin: 5px 0; border-left: 4px solid #f44336; }
        .info { color: #2196F3; padding: 10px; background: #2a2a2a; margin: 5px 0; border-left: 4px solid #2196F3; }
        .warning { color: #FF9800; padding: 10px; background: #2a2a2a; margin: 5px 0; border-left: 4px solid #FF9800; }
        h1 { color: #4CAF50; }
        pre { background: #000; padding: 10px; overflow-x: auto; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin: 10px 0; }
        th, td { padding: 8px; text-align: left; border-bottom: 1px solid #444; }
        th { background: #2a2a2a; }
    </style>
</head>
<body>
    <h1>🔧 Fix File Permissions</h1>

<?php
echo "<div class='info'><strong>Current PHP Version:</strong> " . phpversion() . "</div>";
echo "<div class='info'><strong>Current User:</strong> " . get_current_user() . "</div>";
echo "<div class='info'><strong>Current Working Directory:</strong> " . getcwd() . "</div>";

// Function to get file permissions in readable format
function getPerms($file) {
    if (!file_exists($file)) return 'N/A';
    $perms = fileperms($file);
    return substr(sprintf('%o', $perms), -4);
}

// Function to set permissions
function setPerms($file, $perms) {
    if (!file_exists($file)) {
        return ['success' => false, 'message' => 'File does not exist'];
    }
    if (chmod($file, $perms)) {
        return ['success' => true, 'message' => 'Permissions updated'];
    } else {
        return ['success' => false, 'message' => 'Failed to update permissions'];
    }
}

// Check and fix public_html permissions
echo "<h2>📁 Checking public_html Permissions</h2>";

$publicHtmlFiles = [
    'index.php',
    '.htaccess',
    '.user.ini',
    'fix-php-version-conflict.php',
    'fix-permissions.php',
];

echo "<table>";
echo "<tr><th>File</th><th>Current Permissions</th><th>Status</th><th>Action</th></tr>";

foreach ($publicHtmlFiles as $file) {
    $fullPath = $publicHtmlPath . '/' . $file;
    $exists = file_exists($fullPath);
    $currentPerms = $exists ? getPerms($fullPath) : 'N/A';
    
    if ($exists) {
        // Files should be 644 (rw-r--r--)
        $targetPerms = 0644;
        $targetPermsStr = '0644';
        
        if ($currentPerms !== $targetPermsStr) {
            $result = setPerms($fullPath, $targetPerms);
            if ($result['success']) {
                echo "<tr><td>$file</td><td>$currentPerms</td><td style='color: #4CAF50;'>✓ Fixed</td><td>Changed to $targetPermsStr</td></tr>";
            } else {
                echo "<tr><td>$file</td><td>$currentPerms</td><td style='color: #f44336;'>✗ Error</td><td>{$result['message']}</td></tr>";
            }
        } else {
            echo "<tr><td>$file</td><td>$currentPerms</td><td style='color: #4CAF50;'>✓ OK</td><td>No change needed</td></tr>";
        }
    } else {
        echo "<tr><td>$file</td><td>N/A</td><td style='color: #FF9800;'>⚠ Not Found</td><td>-</td></tr>";
    }
}

echo "</table>";

// Check directory permissions
echo "<h2>📂 Checking Directory Permissions</h2>";

$directories = [
    $publicHtmlPath => 'public_html',
    $publicHtmlPath . '/storage' => 'public_html/storage',
    $laravelPath => 'laravel',
    $laravelPath . '/storage' => 'laravel/storage',
    $laravelPath . '/storage/app' => 'laravel/storage/app',
    $laravelPath . '/storage/app/public' => 'laravel/storage/app/public',
    $laravelPath . '/bootstrap/cache' => 'laravel/bootstrap/cache',
];

echo "<table>";
echo "<tr><th>Directory</th><th>Current Permissions</th><th>Status</th><th>Action</th></tr>";

foreach ($directories as $dirPath => $dirName) {
    $exists = is_dir($dirPath);
    $currentPerms = $exists ? getPerms($dirPath) : 'N/A';
    
    if ($exists) {
        // Directories should be 755 (rwxr-xr-x)
        $targetPerms = 0755;
        $targetPermsStr = '0755';
        
        if ($currentPerms !== $targetPermsStr) {
            $result = setPerms($dirPath, $targetPerms);
            if ($result['success']) {
                echo "<tr><td>$dirName</td><td>$currentPerms</td><td style='color: #4CAF50;'>✓ Fixed</td><td>Changed to $targetPermsStr</td></tr>";
            } else {
                echo "<tr><td>$dirName</td><td>$currentPerms</td><td style='color: #f44336;'>✗ Error</td><td>{$result['message']}</td></tr>";
            }
        } else {
            echo "<tr><td>$dirName</td><td>$currentPerms</td><td style='color: #4CAF50;'>✓ OK</td><td>No change needed</td></tr>";
        }
    } else {
        echo "<tr><td>$dirName</td><td>N/A</td><td style='color: #FF9800;'>⚠ Not Found</td><td>-</td></tr>";
    }
}

echo "</table>";

// Check if fix-php-version-conflict.php exists and is accessible
echo "<h2>🔍 Checking fix-php-version-conflict.php</h2>";

$fixScript = $publicHtmlPath . '/fix-php-version-conflict.php';
if (file_exists($fixScript)) {
    $perms = getPerms($fixScript);
    $readable = is_readable($fixScript);
    
    echo "<div class='success'>✓ File exists: $fixScript</div>";
    echo "<div class='info'>Current permissions: $perms</div>";
    echo "<div class='" . ($readable ? 'success' : 'error') . "'>" . ($readable ? '✓' : '✗') . " File is " . ($readable ? 'readable' : 'NOT readable') . "</div>";
    
    if (!$readable || $perms !== '0644') {
        echo "<div class='info'>Attempting to fix permissions...</div>";
        if (chmod($fixScript, 0644)) {
            echo "<div class='success'>✓ Permissions fixed! Try accessing the file again.</div>";
        } else {
            echo "<div class='error'>✗ Failed to fix permissions. You may need to fix this via cPanel File Manager.</div>";
        }
    }
} else {
    echo "<div class='warning'>⚠ File not found: $fixScript</div>";
    echo "<div class='info'>You need to upload fix-php-version-conflict.php to public_html first.</div>";
}

// Check .htaccess for any restrictions
echo "<h2>📄 Checking .htaccess</h2>";

$htaccessPath = $publicHtmlPath . '/.htaccess';
if (file_exists($htaccessPath)) {
    $htaccessContent = file_get_contents($htaccessPath);
    
    // Check for any deny rules that might block PHP files
    if (preg_match('/deny.*\.php/i', $htaccessContent)) {
        echo "<div class='warning'>⚠ .htaccess contains rules that might deny PHP files</div>";
        echo "<pre>" . htmlspecialchars($htaccessContent) . "</pre>";
    } else {
        echo "<div class='success'>✓ .htaccess doesn't appear to block PHP files</div>";
    }
} else {
    echo "<div class='warning'>⚠ .htaccess not found</div>";
}

// Summary
echo "<hr>";
echo "<h2>✅ Summary</h2>";
echo "<div class='info'>";
echo "<p><strong>What was checked:</strong></p>";
echo "<ul>";
echo "<li>File permissions in public_html</li>";
echo "<li>Directory permissions</li>";
echo "<li>Accessibility of fix-php-version-conflict.php</li>";
echo "<li>.htaccess restrictions</li>";
echo "</ul>";
echo "</div>";

echo "<div class='success'>";
echo "<p><strong>Next Steps:</strong></p>";
echo "<ol>";
echo "<li>If permissions were fixed, try accessing <a href='/fix-php-version-conflict.php' style='color: #4CAF50;'>fix-php-version-conflict.php</a> again</li>";
echo "<li>If you still get 403, check cPanel File Manager → Right-click file → Change Permissions → Set to 644</li>";
echo "<li>Make sure the file is in the correct location: <code>/home/gotzsafari/public_html/fix-php-version-conflict.php</code></li>";
echo "</ol>";
echo "</div>";

echo "<div class='warning'>⚠ <strong>Security:</strong> Delete this script (fix-permissions.php) after use!</div>";
?>

</body>
</html>
