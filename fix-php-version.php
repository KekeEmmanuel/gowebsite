<?php
/**
 * Fix PHP Version for Laravel
 * Run this script via browser: https://www.gotzsafari.com/fix-php-version.php
 * IMPORTANT: Delete this file after use!
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

$publicHtml = '/home/gotzsafari/public_html';
$htaccessPath = $publicHtml . '/.htaccess';

?>
<!DOCTYPE html>
<html>
<head>
    <title>Fix PHP Version</title>
    <style>
        body { font-family: monospace; padding: 20px; background: #1a1a1a; color: #fff; }
        .container { max-width: 900px; margin: 0 auto; }
        h1 { color: #4CAF50; }
        .info { background: #2a2a2a; padding: 15px; margin: 10px 0; border-left: 4px solid #4CAF50; }
        .error { background: #3a1a1a; padding: 15px; margin: 10px 0; border-left: 4px solid #f44336; }
        .success { color: #4CAF50; }
        .warning { color: #FFD700; }
        pre { background: #1a1a1a; padding: 10px; overflow-x: auto; border: 1px solid #444; }
    </style>
</head>
<body>
<div class="container">
    <h1>🔧 Fix PHP Version for Laravel</h1>
    <hr>

<?php

// Check current PHP version
$currentPhpVersion = PHP_VERSION;
echo "<div class='info'>";
echo "<strong>Current PHP Version (Web Server):</strong> $currentPhpVersion<br>";
if (version_compare($currentPhpVersion, '8.2.0', '<')) {
    echo "<span class='warning'>⚠ PHP version is too old! Laravel 12 requires PHP 8.2+</span><br>";
} else {
    echo "<span class='success'>✓ PHP version is compatible</span><br>";
}
echo "</div>";

// Check if ReflectionFunction::isAnonymous() exists
if (method_exists('ReflectionFunction', 'isAnonymous')) {
    echo "<div class='info'>";
    echo "<span class='success'>✓ ReflectionFunction::isAnonymous() method exists</span><br>";
    echo "</div>";
} else {
    echo "<div class='error'>";
    echo "<strong>✗ ReflectionFunction::isAnonymous() method does NOT exist</strong><br>";
    echo "This method requires PHP 7.1+. Your web server is using an older version.<br>";
    echo "</div>";
}

// Check .htaccess file
echo "<div class='info'>";
echo "<h3>Checking .htaccess file</h3>";
if (file_exists($htaccessPath)) {
    echo "<span class='success'>✓ .htaccess file exists</span><br>";
    $htaccessContent = file_get_contents($htaccessPath);
    echo "<strong>Current .htaccess content:</strong><br>";
    echo "<pre>" . htmlspecialchars($htaccessContent) . "</pre>";
    
    // Check if PHP version handler is set
    if (preg_match('/AddHandler.*php/i', $htaccessContent) || preg_match('/<IfModule.*mod_php/i', $htaccessContent)) {
        echo "<span class='warning'>⚠ PHP handler found in .htaccess</span><br>";
    } else {
        echo "<span class='warning'>⚠ No PHP handler found in .htaccess</span><br>";
    }
} else {
    echo "<span class='warning'>⚠ .htaccess file does NOT exist</span><br>";
}
echo "</div>";

// Try to update .htaccess
if (version_compare($currentPhpVersion, '8.2.0', '<') || !method_exists('ReflectionFunction', 'isAnonymous')) {
    echo "<div class='error'>";
    echo "<h3>Attempting to fix PHP version...</h3>";
    
    // Read existing .htaccess
    $htaccessContent = file_exists($htaccessPath) ? file_get_contents($htaccessPath) : '';
    
    // Remove any existing PHP handler lines
    $htaccessContent = preg_replace('/AddHandler.*php.*\n/i', '', $htaccessContent);
    $htaccessContent = preg_replace('/<IfModule.*mod_php.*?<\/IfModule>/is', '', $htaccessContent);
    
    // Add PHP 8.2 handler at the beginning
    $phpHandler = "# Force PHP 8.2\n";
    $phpHandler .= "AddHandler application/x-httpd-ea-php82 .php\n";
    $phpHandler .= "# Alternative if above doesn't work:\n";
    $phpHandler .= "# AddHandler application/x-httpd-php82 .php\n";
    $phpHandler .= "\n";
    
    $htaccessContent = $phpHandler . $htaccessContent;
    
    // Write updated .htaccess
    if (file_put_contents($htaccessPath, $htaccessContent)) {
        echo "<span class='success'>✓ .htaccess file updated</span><br>";
        echo "<strong>New .htaccess content:</strong><br>";
        echo "<pre>" . htmlspecialchars($htaccessContent) . "</pre>";
        echo "<p><strong>⚠️ IMPORTANT:</strong> Refresh this page to check if PHP version changed.</p>";
    } else {
        echo "<span class='warning'>✗ Could not write to .htaccess file. Please check permissions.</span><br>";
    }
    echo "</div>";
} else {
    echo "<div class='info'>";
    echo "<span class='success'>✓ PHP version is correct. No changes needed.</span><br>";
    echo "</div>";
}

?>

    <hr>
    <p><strong>⚠️  SECURITY WARNING:</strong> Delete this file (fix-php-version.php) immediately after use!</p>
</div>
</body>
</html>

