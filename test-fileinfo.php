<?php
/**
 * Test fileinfo Extension
 * 
 * Upload this to public_html and access via browser
 * DELETE after checking!
 */

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>fileinfo Extension Test</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 600px; margin: 50px auto; padding: 20px; }
        .success { color: green; font-weight: bold; font-size: 18px; padding: 20px; background: #e8f5e9; border-left: 4px solid green; }
        .error { color: red; font-weight: bold; font-size: 18px; padding: 20px; background: #ffebee; border-left: 4px solid red; }
        .info { background: #e3f2fd; padding: 15px; border-left: 4px solid #2196F3; margin: 20px 0; }
    </style>
</head>
<body>
    <h1>PHP fileinfo Extension Test</h1>
    
    <?php
    if (extension_loaded('fileinfo')) {
        echo '<div class="success">✓ fileinfo extension is ENABLED</div>';
        echo '<div class="info">';
        echo '<p><strong>Extension Info:</strong></p>';
        echo '<ul>';
        echo '<li>Extension loaded: Yes</li>';
        echo '<li>finfo class available: ' . (class_exists('finfo') ? 'Yes' : 'No') . '</li>';
        echo '<li>mime_content_type() available: ' . (function_exists('mime_content_type') ? 'Yes' : 'No') . '</li>';
        echo '</ul>';
        echo '</div>';
    } else {
        echo '<div class="error">✗ fileinfo extension is NOT ENABLED</div>';
        echo '<div class="info">';
        echo '<p><strong>To enable:</strong></p>';
        echo '<ol>';
        echo '<li>Go to cPanel → Select PHP Version</li>';
        echo '<li>Select PHP 8.2</li>';
        echo '<li>Click "Extensions" tab</li>';
        echo '<li>Check "fileinfo" to enable it</li>';
        echo '<li>Click "Save"</li>';
        echo '</ol>';
        echo '</div>';
    }
    ?>
    
    <hr>
    <p><strong>⚠ Security Note:</strong> Delete this file after checking!</p>
</body>
</html>

