<?php
/**
 * Fix PHP Handler Conflict - Ensure PHP 8.2 is used
 * Upload to public_html and run: https://www.gotzsafari.com/fix-php-handler-conflict.php
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

$htaccessPath = '/home/gotzsafari/public_html/.htaccess';

?>
<!DOCTYPE html>
<html>
<head>
    <title>Fix PHP Handler Conflict</title>
    <style>
        body { font-family: Arial; max-width: 800px; margin: 50px auto; padding: 20px; background: #1a1a1a; color: #fff; }
        .success { color: #4CAF50; padding: 10px; background: #2a2a2a; margin: 5px 0; }
        .error { color: #f44336; padding: 10px; background: #2a2a2a; margin: 5px 0; }
        .info { color: #2196F3; padding: 10px; background: #2a2a2a; margin: 5px 0; }
        .warning { color: #FF9800; padding: 10px; background: #2a2a2a; margin: 5px 0; }
        h1 { color: #4CAF50; }
        pre { background: #000; padding: 10px; overflow-x: auto; }
    </style>
</head>
<body>
    <h1>🔧 Fix PHP Handler Conflict</h1>

<?php
if (!file_exists($htaccessPath)) {
    echo "<div class='error'>❌ .htaccess file not found</div>";
    exit;
}

$content = file_get_contents($htaccessPath);
$originalContent = $content;

echo "<div class='info'><strong>Step 1:</strong> Reading current .htaccess</div>";
echo "<div class='success'>✓ File read successfully</div>";

// Check for cPanel handler
$hasCpanelHandler = preg_match('/# php -- BEGIN cPanel-generated handler.*?# php -- END cPanel-generated handler/s', $content, $matches);
$cpanelHandler = $matches[0] ?? '';

if ($hasCpanelHandler) {
    echo "<div class='warning'>⚠️ Found cPanel-generated handler (this might override PHP 8.2)</div>";
    
    // Check if it's using PHP 8.1
    if (strpos($cpanelHandler, 'ea-php81') !== false) {
        echo "<div class='warning'>⚠️ cPanel handler is using PHP 8.1 - updating to PHP 8.2</div>";
        
        // Replace PHP 8.1 with PHP 8.2 in cPanel handler
        $content = preg_replace(
            '/# php -- BEGIN cPanel-generated handler.*?AddHandler application\/x-httpd-ea-php81.*?# php -- END cPanel-generated handler/s',
            "# php -- BEGIN cPanel-generated handler, do not edit\n# Set the \"ea-php82\" package as the default \"PHP\" programming language.\n<IfModule mime_module>\n  AddHandler application/x-httpd-ea-php82 .php .php8 .phtml\n</IfModule>\n# php -- END cPanel-generated handler, do not edit",
            $content
        );
        
        echo "<div class='success'>✓ Updated cPanel handler to PHP 8.2</div>";
    } else {
        echo "<div class='info'>✓ cPanel handler is already using PHP 8.2 or different version</div>";
    }
} else {
    echo "<div class='info'>ℹ No cPanel handler found (this is fine)</div>";
}

// Ensure PHP 8.2 handler is at the top
if (strpos($content, 'AddHandler application/x-httpd-ea-php82') === false) {
    echo "<div class='warning'>⚠️ PHP 8.2 handler missing - adding it</div>";
    $content = "# Force PHP 8.2\nAddHandler application/x-httpd-ea-php82 .php\n\n" . $content;
    echo "<div class='success'>✓ Added PHP 8.2 handler at top</div>";
} else {
    // Make sure it's at the very top
    if (strpos($content, '# Force PHP 8.2') !== 0) {
        echo "<div class='info'>ℹ Moving PHP 8.2 handler to top</div>";
        // Remove existing handler
        $content = preg_replace('/^# Force PHP 8\.2\s*\nAddHandler.*\n\n?/m', '', $content);
        // Add at top
        $content = "# Force PHP 8.2\nAddHandler application/x-httpd-ea-php82 .php\n\n" . $content;
        echo "<div class='success'>✓ PHP 8.2 handler moved to top</div>";
    } else {
        echo "<div class='success'>✓ PHP 8.2 handler is already at top</div>";
    }
}

// Remove any other PHP handlers (except cPanel one)
$content = preg_replace('/^AddHandler application\/x-httpd-ea-php(?!82).*\n/m', '', $content);

// Backup before writing
$backupPath = $htaccessPath . '.backup.' . date('Y-m-d_H-i-s');
copy($htaccessPath, $backupPath);
echo "<div class='success'>✓ Backup created: " . basename($backupPath) . "</div>";

// Write updated content
if ($content !== $originalContent) {
    if (file_put_contents($htaccessPath, $content)) {
        echo "<div class='success'>✓ .htaccess updated successfully</div>";
        
        echo "<div class='info'><strong>Updated .htaccess:</strong></div>";
        echo "<pre>" . htmlspecialchars($content) . "</pre>";
        
        echo "<div class='success'><strong>✅ Done! PHP 8.2 handler conflict fixed.</strong></div>";
        echo "<div class='warning'>⚠️ <strong>Important:</strong> Refresh this page to see if PHP version changed. If still 8.1, the server might need to restart Apache or there's a higher-level configuration overriding it.</div>";
    } else {
        echo "<div class='error'>❌ Failed to write .htaccess. Check file permissions.</div>";
    }
} else {
    echo "<div class='info'>✓ .htaccess is already correctly configured</div>";
}

// Show current PHP version
echo "<div class='info'><strong>Current PHP Version:</strong> " . phpversion() . "</div>";
echo "<div class='warning'>⚠️ If this still shows 8.1, try:</div>";
echo "<div class='info'>1. Wait 1-2 minutes for Apache to reload</div>";
echo "<div class='info'>2. Clear browser cache</div>";
echo "<div class='info'>3. Contact your hosting provider - they may need to restart Apache or there's a server-level override</div>";
?>

</body>
</html>
