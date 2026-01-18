<?php
/**
 * Set PHP 8.2 in .htaccess (when cPanel doesn't allow changes)
 * Upload to public_html and run: https://www.gotzsafari.com/set-php-version.php
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

$htaccessPath = '/home/gotzsafari/public_html/.htaccess';

?>
<!DOCTYPE html>
<html>
<head>
    <title>Set PHP 8.2 in .htaccess</title>
    <style>
        body { font-family: Arial; max-width: 800px; margin: 50px auto; padding: 20px; background: #1a1a1a; color: #fff; }
        .success { color: #4CAF50; padding: 10px; background: #2a2a2a; margin: 5px 0; }
        .error { color: #f44336; padding: 10px; background: #2a2a2a; margin: 5px 0; }
        .info { color: #2196F3; padding: 10px; background: #2a2a2a; margin: 5px 0; }
        h1 { color: #4CAF50; }
        pre { background: #000; padding: 10px; overflow-x: auto; }
    </style>
</head>
<body>
    <h1>🔧 Set PHP 8.2 in .htaccess</h1>

<?php
if (!file_exists($htaccessPath)) {
    echo "<div class='error'>❌ .htaccess file not found</div>";
    exit;
}

// Read current .htaccess
$content = file_get_contents($htaccessPath);
$originalContent = $content;

echo "<div class='info'><strong>Step 1:</strong> Reading current .htaccess</div>";
echo "<div class='success'>✓ File read successfully</div>";

// Check if PHP 8.2 handler already exists
if (strpos($content, 'AddHandler application/x-httpd-ea-php82') !== false) {
    echo "<div class='info'>✓ PHP 8.2 handler already exists in .htaccess</div>";
} else {
    echo "<div class='info'><strong>Step 2:</strong> Adding PHP 8.2 handler</div>";
    
    // Remove any existing PHP handlers at the top
    $content = preg_replace('/^# Force PHP.*\nAddHandler.*\n\n?/m', '', $content);
    $content = preg_replace('/^AddHandler application\/x-httpd-ea-php\d+.*\n\n?/m', '', $content);
    
    // Add PHP 8.2 handler at the very top
    $phpHandler = "# Force PHP 8.2\nAddHandler application/x-httpd-ea-php82 .php\n\n";
    $content = $phpHandler . $content;
    
    echo "<div class='success'>✓ PHP 8.2 handler added</div>";
}

// Backup before writing
$backupPath = $htaccessPath . '.backup.' . date('Y-m-d_H-i-s');
copy($htaccessPath, $backupPath);
echo "<div class='success'>✓ Backup created: " . basename($backupPath) . "</div>";

// Write updated content
if (file_put_contents($htaccessPath, $content)) {
    echo "<div class='success'>✓ .htaccess updated successfully</div>";
    
    echo "<div class='info'><strong>Updated .htaccess:</strong></div>";
    echo "<pre>" . htmlspecialchars($content) . "</pre>";
    
    echo "<div class='success'><strong>✅ Done! PHP 8.2 is now set in .htaccess</strong></div>";
    echo "<div class='info'>⚠️ <strong>Test your site now:</strong> <a href='/' style='color: #4CAF50;'>Visit Homepage</a></div>";
} else {
    echo "<div class='error'>❌ Failed to write .htaccess. Check file permissions.</div>";
}

// Show current PHP version
echo "<div class='info'><strong>Current PHP Version:</strong> " . phpversion() . "</div>";
?>

</body>
</html>
