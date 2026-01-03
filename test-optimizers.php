<?php
/**
 * Test Image Optimizers Configuration
 * 
 * Upload to public_html and visit: https://www.gotzsafari.com/test-optimizers.php
 * DELETE after checking!
 */

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

?>
<!DOCTYPE html>
<html>
<head>
    <title>Test Image Optimizers Config</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; background: #1a1a1a; color: #fff; }
        .success { color: #4CAF50; padding: 15px; background: #2a2a2a; border-left: 4px solid #4CAF50; margin: 10px 0; }
        .error { color: #f44336; padding: 15px; background: #2a2a2a; border-left: 4px solid #f44336; margin: 10px 0; }
        .info { color: #2196F3; padding: 15px; background: #2a2a2a; border-left: 4px solid #2196F3; margin: 10px 0; }
        pre { background: #1a1a1a; padding: 10px; overflow-x: auto; font-size: 12px; border: 1px solid #444; }
        h1 { color: #4CAF50; }
    </style>
</head>
<body>
    <h1>🔍 Test Image Optimizers Configuration</h1>
    <hr>

<?php

$fileinfoEnabled = extension_loaded('fileinfo');
$optimizers = config('media-library.image_optimizers');

echo "<div class='info'>";
echo "<h3>Current Status:</h3>";
echo "<p><strong>fileinfo extension:</strong> " . ($fileinfoEnabled ? "✅ Enabled" : "❌ Not Enabled") . "</p>";
echo "<p><strong>Image optimizers count:</strong> " . count($optimizers) . "</p>";
echo "</div>";

if (empty($optimizers)) {
    echo "<div class='success'>";
    echo "<h3>✅ Optimizers are Disabled</h3>";
    echo "<p>Image optimizers are correctly disabled. The Media Library should work without fileinfo.</p>";
    echo "</div>";
} else {
    echo "<div class='error'>";
    echo "<h3>❌ Optimizers are Still Enabled</h3>";
    echo "<p>The optimizers array is not empty. This means the config override is not working.</p>";
    echo "<p><strong>Optimizers found:</strong></p>";
    echo "<pre>";
    print_r(array_keys($optimizers));
    echo "</pre>";
    echo "</div>";
}

// Test if mime_content_type exists in Spatie namespace
if (function_exists('Spatie\ImageOptimizer\mime_content_type')) {
    echo "<div class='success'>";
    echo "<p>✅ mime_content_type() exists in Spatie\\ImageOptimizer namespace</p>";
    echo "</div>";
} else {
    echo "<div class='error'>";
    echo "<p>❌ mime_content_type() does NOT exist in Spatie\\ImageOptimizer namespace</p>";
    echo "</div>";
}

// Test global mime_content_type
if (function_exists('mime_content_type')) {
    echo "<div class='success'>";
    echo "<p>✅ mime_content_type() exists in global namespace</p>";
    echo "</div>";
} else {
    echo "<div class='error'>";
    echo "<p>❌ mime_content_type() does NOT exist in global namespace</p>";
    echo "</div>";
}

?>

    <hr>
    <div class="error">
        <p><strong>⚠ Security Note:</strong> Delete this file (test-optimizers.php) after checking!</p>
    </div>
</body>
</html>

