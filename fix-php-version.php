<?php
/**
 * Fix PHP Version - Force PHP 8.2 in .htaccess
 * Run this script via browser: https://www.gotzsafari.com/fix-php-version.php
 * IMPORTANT: Delete this file after use!
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

$publicHtmlPath = '/home/gotzsafari/public_html';
$htaccessFile = $publicHtmlPath . '/.htaccess';

function logOutput($message) {
    echo "<p style='color: #4CAF50;'>✓ $message</p>";
    flush();
}

function logError($message) {
    echo "<p style='color: #f44336;'>✗ $message</p>";
    flush();
}

function logInfo($message) {
    echo "<p style='color: #2196F3;'>ℹ $message</p>";
    flush();
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Fix PHP Version</title>
    <style>
        body { font-family: monospace; padding: 20px; background: #1a1a1a; color: #fff; }
        .container { max-width: 900px; margin: 0 auto; }
        h1 { color: #4CAF50; }
        .step { background: #2a2a2a; padding: 15px; margin: 10px 0; border-left: 4px solid #4CAF50; }
        .error { border-left-color: #f44336; }
        pre { background: #1a1a1a; padding: 10px; overflow-x: auto; font-size: 11px; }
    </style>
</head>
<body>
<div class="container">
    <h1>🔧 Fix PHP Version</h1>
    <p>Fixing PHP version at <?php echo date('Y-m-d H:i:s'); ?></p>
    <hr>

<?php

// Check current PHP version
logInfo("Current PHP version: " . phpversion());
logInfo("PHP SAPI: " . php_sapi_name());

// Check if .htaccess exists
if (!file_exists($htaccessFile)) {
    logError(".htaccess file not found at: $htaccessFile");
    echo "<p>Creating new .htaccess file...</p>";
    
    // Create new .htaccess with PHP 8.2 directive
    $htaccessContent = "# Force PHP 8.2
AddHandler application/x-httpd-ea-php82 .php

# Alternative method (if AddHandler doesn't work)
<FilesMatch "\.(php|php8|php7|phtml)$">
    SetHandler application/x-httpd-ea-php82
</FilesMatch>

<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Handle X-XSRF-Token Header
    RewriteCond %{HTTP:x-xsrf-token} .
    RewriteRule .* - [E=HTTP_X_XSRF_TOKEN:%{HTTP:X-XSRF-Token}]

    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
";
    
    if (file_put_contents($htaccessFile, $htaccessContent)) {
        logOutput("Created .htaccess file with PHP 8.2 directive");
    } else {
        logError("Failed to create .htaccess file");
    }
} else {
    logOutput(".htaccess file found");
    
    // Read current .htaccess
    $currentContent = file_get_contents($htaccessFile);
    
    // Check if PHP 8.2 directive already exists
    if (strpos($currentContent, 'application/x-httpd-ea-php82') !== false || strpos($currentContent, 'ea-php82') !== false) {
        logInfo("PHP 8.2 directive already exists in .htaccess");
        
        // Check if it's at the top
        if (strpos($currentContent, '# Force PHP 8.2') === 0 || strpos($currentContent, 'AddHandler application/x-httpd-ea-php82') === 0) {
            logOutput("PHP 8.2 directive is correctly positioned");
        } else {
            logInfo("PHP 8.2 directive exists but may not be at the top. Ensuring it's at the beginning...");
            
            // Remove existing PHP handler directives
            $currentContent = preg_replace('/^.*?AddHandler\s+application\/x-httpd-ea-php\d+.*?\n/m', '', $currentContent);
            $currentContent = preg_replace('/^.*?#\s*Force\s+PHP.*?\n/m', '', $currentContent);
            $currentContent = preg_replace('/<FilesMatch[^>]*>.*?<\/FilesMatch>\s*/is', '', $currentContent);
            
        // Add PHP 8.2 directive at the beginning
        $phpDirective = "# Force PHP 8.2\nAddHandler application/x-httpd-ea-php82 .php\n\n# Alternative method (if AddHandler doesn't work)\n<FilesMatch \"\\.(php|php8|php7|phtml)$\">\n    SetHandler application/x-httpd-ea-php82\n</FilesMatch>\n\n";
        $currentContent = $phpDirective . $currentContent;
            
            if (file_put_contents($htaccessFile, $currentContent)) {
                logOutput("Updated .htaccess to ensure PHP 8.2 directive is at the top");
            } else {
                logError("Failed to update .htaccess");
            }
        }
    } else {
        logInfo("PHP 8.2 directive not found. Adding it...");
        
        // Remove any existing PHP handler directives
        $currentContent = preg_replace('/^.*?AddHandler\s+application\/x-httpd-ea-php\d+.*?\n/m', '', $currentContent);
        $currentContent = preg_replace('/^.*?#\s*Force\s+PHP.*?\n/m', '', $currentContent);
        $currentContent = preg_replace('/<FilesMatch[^>]*>.*?<\/FilesMatch>\s*/is', '', $currentContent);
        
        // Add PHP 8.2 directive at the beginning
        $phpDirective = "# Force PHP 8.2\nAddHandler application/x-httpd-ea-php82 .php\n\n# Alternative method (if AddHandler doesn't work)\n<FilesMatch \"\\.(php|php8|php7|phtml)$\">\n    SetHandler application/x-httpd-ea-php82\n</FilesMatch>\n\n";
        $currentContent = $phpDirective . $currentContent;
        
        if (file_put_contents($htaccessFile, $currentContent)) {
            logOutput("Added PHP 8.2 directive to .htaccess");
        } else {
            logError("Failed to update .htaccess");
        }
    }
    
    // Show current .htaccess content
    echo "<div class='step'>";
    echo "<h3>Current .htaccess content:</h3>";
    echo "<pre>" . htmlspecialchars(file_get_contents($htaccessFile)) . "</pre>";
    echo "</div>";
}

echo "<hr>";
echo "<h2>✅ PHP Version Fix Complete</h2>";
echo "<p><strong>Next steps:</strong></p>";
echo "<ol>";
echo "<li>Refresh your website to see if the error is resolved</li>";
echo "<li>If the error persists, you may need to check cPanel's PHP Selector to ensure PHP 8.2 is available</li>";
echo "<li>Delete this file (fix-php-version.php) after use</li>";
echo "</ol>";

?>

    <hr>
    <p><strong>⚠️  SECURITY WARNING:</strong> Delete this file (fix-php-version.php) immediately after use!</p>
</div>
</body>
</html>
