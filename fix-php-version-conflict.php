<?php
/**
 * Fix PHP version conflict in .htaccess
 * Upload to public_html and run: https://www.gotzsafari.com/fix-php-version-conflict.php
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

$htaccessPath = '/home/gotzsafari/public_html/.htaccess';

?>
<!DOCTYPE html>
<html>
<head>
    <title>Fix PHP Version Conflict</title>
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
    <h1>🔧 Fix PHP Version Conflict</h1>

<?php
if (!file_exists($htaccessPath)) {
    echo "<div class='error'>❌ .htaccess file not found at: $htaccessPath</div>";
    exit;
}

// Read current .htaccess
$content = file_get_contents($htaccessPath);
$originalContent = $content;

echo "<div class='info'><strong>Step 1:</strong> Reading current .htaccess file</div>";
echo "<div class='success'>✓ File read successfully</div>";

// Check if cPanel handler exists and what version it's using
$hasCpanelHandler = preg_match('/# php -- BEGIN cPanel-generated handler.*?# php -- END cPanel-generated handler/s', $content, $matches);
$cpanelHandler = $matches[0] ?? '';

if ($hasCpanelHandler) {
    echo "<div class='info'><strong>Step 2:</strong> Found cPanel-generated handler</div>";
    
    // Check if it's using PHP 8.1
    if (strpos($cpanelHandler, 'ea-php81') !== false) {
        echo "<div class='warning'>⚠️ cPanel handler is using PHP 8.1 - updating to PHP 8.2</div>";
        
        // Replace PHP 8.1 with PHP 8.2 in cPanel handler
        $content = preg_replace(
            '/# php -- BEGIN cPanel-generated handler.*?AddHandler application\/x-httpd-ea-php81.*?# php -- END cPanel-generated handler/s',
            "# php -- BEGIN cPanel-generated handler, do not edit\n# Set the \"ea-php82\" package as the default \"PHP\" programming language.\n<IfModule mime_module>\n  AddHandler application/x-httpd-ea-php82 .php .php8 .phtml\n</IfModule>\n# php -- END cPanel-generated handler, do not edit",
            $content
        );
    } else {
        echo "<div class='success'>✓ cPanel handler is already using PHP 8.2</div>";
    }
} else {
    echo "<div class='warning'>⚠️ No cPanel handler found - adding one for PHP 8.2</div>";
    $content .= "\n\n# php -- BEGIN cPanel-generated handler, do not edit\n# Set the \"ea-php82\" package as the default \"PHP\" programming language.\n<IfModule mime_module>\n  AddHandler application/x-httpd-ea-php82 .php .php8 .phtml\n</IfModule>\n# php -- END cPanel-generated handler, do not edit";
}

// Ensure top handler is PHP 8.2
if (strpos($content, 'AddHandler application/x-httpd-ea-php82') === false || 
    (strpos($content, 'AddHandler application/x-httpd-ea-php81') !== false && strpos($content, 'AddHandler application/x-httpd-ea-php81') < strpos($content, 'AddHandler application/x-httpd-ea-php82'))) {
    echo "<div class='info'><strong>Step 3:</strong> Ensuring top handler uses PHP 8.2</div>";
    
    // Remove any PHP 8.1 handlers at the top
    $content = preg_replace('/^AddHandler application\/x-httpd-ea-php81.*$/m', '', $content);
    
    // Add PHP 8.2 handler at the top if not present
    if (strpos($content, 'AddHandler application/x-httpd-ea-php82') === false) {
        $content = "# Force PHP 8.2\nAddHandler application/x-httpd-ea-php82 .php\n\n" . $content;
    } else {
        // Ensure it's at the top
        $content = preg_replace('/^# Force PHP 8\.2\s*\nAddHandler application\/x-httpd-ea-php82.*$/m', '', $content);
        $content = "# Force PHP 8.2\nAddHandler application/x-httpd-ea-php82 .php\n\n" . $content;
    }
}

// Only write if content changed
if ($content !== $originalContent) {
    echo "<div class='info'><strong>Step 4:</strong> Writing updated .htaccess file</div>";
    
    // Backup original
    $backupPath = $htaccessPath . '.backup.' . date('Y-m-d_H-i-s');
    copy($htaccessPath, $backupPath);
    echo "<div class='success'>✓ Backup created: " . basename($backupPath) . "</div>";
    
    // Write updated content
    if (file_put_contents($htaccessPath, $content)) {
        echo "<div class='success'>✓ .htaccess updated successfully</div>";
        
        // Show what changed
        echo "<div class='info'><strong>Updated .htaccess content:</strong></div>";
        echo "<pre>" . htmlspecialchars($content) . "</pre>";
        
        echo "<div class='success'><strong>✅ Done! PHP version conflict fixed. Both handlers now use PHP 8.2.</strong></div>";
        echo "<div class='info'>⚠️ <strong>Important:</strong> Test your site now. If you still see errors, check the Laravel logs.</div>";
    } else {
        echo "<div class='error'>❌ Failed to write .htaccess file. Check file permissions.</div>";
    }
} else {
    echo "<div class='success'>✓ .htaccess is already correctly configured for PHP 8.2</div>";
}

// Show current PHP version
echo "<div class='info'><strong>Current PHP Version:</strong> " . phpversion() . "</div>";
?>

</body>
</html>
