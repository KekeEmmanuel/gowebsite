<?php
/**
 * Simple MIME Test - Upload to public_html
 * Access: https://www.gotzsafari.com/test-mime-simple.php
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

$laravelPath = '/home/gotzsafari/laravel';
$polyfillPath = $laravelPath . '/bootstrap/mime-polyfill.php';

?>
<!DOCTYPE html>
<html>
<head>
    <title>MIME Test</title>
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
    <h1>🧪 MIME Polyfill Test</h1>

<?php

echo "<div class='info'><strong>Step 1:</strong> Check fileinfo extension</div>";
$fileinfoLoaded = extension_loaded('fileinfo');
echo "<div class='" . ($fileinfoLoaded ? 'success' : 'info') . "'>fileinfo: " . ($fileinfoLoaded ? 'ENABLED' : 'NOT ENABLED') . "</div>";

echo "<div class='info'><strong>Step 2:</strong> Load polyfill</div>";
if (file_exists($polyfillPath)) {
    require $polyfillPath;
    echo "<div class='success'>✓ Polyfill loaded from: $polyfillPath</div>";
} else {
    echo "<div class='error'>❌ Polyfill NOT found at: $polyfillPath</div>";
    exit;
}

echo "<div class='info'><strong>Step 3:</strong> Test global mime_content_type()</div>";
$testFile = __FILE__;
if (function_exists('mime_content_type')) {
    try {
        $mime = mime_content_type($testFile);
        echo "<div class='success'>✓ Global function works: <code>$mime</code></div>";
    } catch (\Throwable $e) {
        echo "<div class='error'>❌ Global function failed: " . $e->getMessage() . "</div>";
    }
} else {
    echo "<div class='error'>❌ Global function does not exist</div>";
}

echo "<div class='info'><strong>Step 4:</strong> Test namespace mime_content_type()</div>";
try {
    $namespaceMime = call_user_func('Spatie\ImageOptimizer\mime_content_type', $testFile);
    echo "<div class='success'>✓ Namespace function works: <code>$namespaceMime</code></div>";
} catch (\Throwable $e) {
    echo "<div class='error'>❌ Namespace function failed: " . $e->getMessage() . "</div>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

echo "<div class='info'><strong>Step 5:</strong> Test Image class</div>";
try {
    require_once $laravelPath . '/vendor/autoload.php';
    $image = new \Spatie\ImageOptimizer\Image($testFile);
    $imageMime = $image->mime();
    echo "<div class='success'>✓ Image class works: <code>$imageMime</code></div>";
} catch (\Throwable $e) {
    echo "<div class='error'>❌ Image class failed: " . $e->getMessage() . "</div>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

?>

</body>
</html>

