<?php
/**
 * Check Current PHP Version and Configuration
 * Upload to public_html and run: https://www.gotzsafari.com/check-php-version.php
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

$htaccessPath = '/home/gotzsafari/public_html/.htaccess';

?>
<!DOCTYPE html>
<html>
<head>
    <title>Check PHP Version</title>
    <style>
        body { font-family: Arial; max-width: 900px; margin: 50px auto; padding: 20px; background: #1a1a1a; color: #fff; }
        .success { color: #4CAF50; padding: 10px; background: #2a2a2a; margin: 5px 0; }
        .error { color: #f44336; padding: 10px; background: #2a2a2a; margin: 5px 0; }
        .info { color: #2196F3; padding: 10px; background: #2a2a2a; margin: 5px 0; }
        .warning { color: #FF9800; padding: 10px; background: #2a2a2a; margin: 5px 0; }
        h1 { color: #4CAF50; }
        pre { background: #000; padding: 10px; overflow-x: auto; }
        .section { background: #2a2a2a; padding: 15px; margin: 10px 0; border-left: 4px solid #4CAF50; }
        table { width: 100%; border-collapse: collapse; margin: 10px 0; }
        th, td { padding: 8px; text-align: left; border-bottom: 1px solid #444; }
        th { background: #333; }
    </style>
</head>
<body>
    <h1>🔍 Check PHP Version & Configuration</h1>

<?php
echo "<div class='section'>";
echo "<h3>Current PHP Information</h3>";

$phpVersion = phpversion();
$phpMajor = (int)explode('.', $phpVersion)[0];
$phpMinor = (int)explode('.', $phpVersion)[1];

echo "<table>";
echo "<tr><th>Setting</th><th>Value</th><th>Status</th></tr>";
echo "<tr><td>PHP Version</td><td><strong>$phpVersion</strong></td><td>" . 
     ($phpVersion >= '8.2' ? "<span style='color: #4CAF50;'>✓ OK</span>" : "<span style='color: #f44336;'>❌ Need 8.2+</span>") . 
     "</td></tr>";
echo "<tr><td>PHP SAPI</td><td>" . php_sapi_name() . "</td><td>-</td></tr>";
echo "<tr><td>PHP Binary</td><td>" . PHP_BINARY . "</td><td>-</td></tr>";
echo "<tr><td>php.ini Location</td><td>" . php_ini_loaded_file() . "</td><td>-</td></tr>";
echo "</table>";

if ($phpVersion < '8.2') {
    echo "<div class='error'><strong>❌ PROBLEM:</strong> PHP version is $phpVersion, but Laravel requires PHP 8.2+</div>";
    echo "<div class='error'>The error 'Call to undefined method ReflectionFunction::isAnonymous()' confirms PHP 8.1 is active.</div>";
} else {
    echo "<div class='success'><strong>✓ PHP version is correct (8.2+)</strong></div>";
}
echo "</div>";

echo "<div class='section'>";
echo "<h3>.htaccess Configuration</h3>";

if (file_exists($htaccessPath)) {
    $htaccessContent = file_get_contents($htaccessPath);
    
    // Check for PHP 8.2 handler
    $hasPhp82 = strpos($htaccessContent, 'AddHandler application/x-httpd-ea-php82') !== false;
    $hasPhp81 = strpos($htaccessContent, 'AddHandler application/x-httpd-ea-php81') !== false;
    
    echo "<table>";
    echo "<tr><th>Check</th><th>Status</th></tr>";
    echo "<tr><td>PHP 8.2 Handler</td><td>" . 
         ($hasPhp82 ? "<span style='color: #4CAF50;'>✓ Found</span>" : "<span style='color: #f44336;'>❌ Missing</span>") . 
         "</td></tr>";
    echo "<tr><td>PHP 8.1 Handler</td><td>" . 
         ($hasPhp81 ? "<span style='color: #FF9800;'>⚠️ Found (conflict!)</span>" : "<span style='color: #4CAF50;'>✓ Not found</span>") . 
         "</td></tr>";
    echo "</table>";
    
    if ($hasPhp81 && !$hasPhp82) {
        echo "<div class='error'><strong>❌ PROBLEM:</strong> .htaccess has PHP 8.1 handler but not PHP 8.2</div>";
    } elseif ($hasPhp81 && $hasPhp82) {
        echo "<div class='warning'><strong>⚠️ WARNING:</strong> Both PHP 8.1 and 8.2 handlers found. The last one wins.</div>";
    }
    
    // Show .htaccess content
    echo "<div class='info'><strong>.htaccess content:</strong></div>";
    echo "<pre>" . htmlspecialchars($htaccessContent) . "</pre>";
} else {
    echo "<div class='error'>❌ .htaccess file not found</div>";
}
echo "</div>";

echo "<div class='section'>";
echo "<h3>PHP Extensions</h3>";

$requiredExtensions = ['fileinfo', 'openssl', 'pdo', 'mbstring', 'tokenizer', 'xml', 'ctype', 'json'];
echo "<table>";
echo "<tr><th>Extension</th><th>Status</th></tr>";

foreach ($requiredExtensions as $ext) {
    $loaded = extension_loaded($ext);
    echo "<tr><td>$ext</td><td>" . 
         ($loaded ? "<span style='color: #4CAF50;'>✓ Loaded</span>" : "<span style='color: #f44336;'>❌ Not loaded</span>") . 
         "</td></tr>";
}
echo "</table>";
echo "</div>";

echo "<div class='section'>";
echo "<h3>Recommendations</h3>";

if ($phpVersion < '8.2') {
    echo "<div class='error'><strong>Action Required:</strong></div>";
    echo "<div class='info'>1. Contact your hosting provider to enable PHP 8.2</div>";
    echo "<div class='info'>2. Ask them to restart Apache/web server</div>";
    echo "<div class='info'>3. Verify PHP 8.2 is installed: <code>which php82</code> or <code>ls /opt/cpanel/ea-php82/</code></div>";
    echo "<div class='info'>4. If PHP 8.2 is not available, request it from your hosting provider</div>";
} else {
    echo "<div class='success'>✓ PHP version is correct. If you're still seeing errors, clear Laravel cache.</div>";
}
echo "</div>";
?>

</body>
</html>
