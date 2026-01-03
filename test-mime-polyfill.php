<?php
/**
 * Test script to verify mime_content_type polyfill works
 * 
 * Upload to public_html and access via browser:
 * https://www.gotzsafari.com/test-mime-polyfill.php
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

?>
<!DOCTYPE html>
<html>
<head>
    <title>Test mime_content_type Polyfill</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 900px; margin: 50px auto; padding: 20px; background: #1a1a1a; color: #fff; }
        .success { color: #4CAF50; padding: 15px; background: #2a2a2a; border-left: 4px solid #4CAF50; margin: 10px 0; }
        .error { color: #f44336; padding: 15px; background: #2a2a2a; border-left: 4px solid #f44336; margin: 10px 0; }
        .info { color: #2196F3; padding: 15px; background: #2a2a2a; border-left: 4px solid #2196F3; margin: 10px 0; }
        pre { background: #1a1a1a; padding: 10px; overflow-x: auto; font-size: 12px; border: 1px solid #444; }
        h1 { color: #4CAF50; }
    </style>
</head>
<body>
    <h1>🧪 Test mime_content_type Polyfill</h1>

<?php

echo "<div class='info'>";
echo "<h2>Step 1: Check fileinfo extension</h2>";
$fileinfoLoaded = extension_loaded('fileinfo');
echo "<p>fileinfo extension loaded: " . ($fileinfoLoaded ? "<span style='color: #4CAF50;'>YES</span>" : "<span style='color: #f44336;'>NO</span>") . "</p>";
echo "</div>";

echo "<div class='info'>";
echo "<h2>Step 2: Load polyfill</h2>";
$polyfillPath = __DIR__ . '/bootstrap/mime-polyfill.php';
if (file_exists($polyfillPath)) {
    require $polyfillPath;
    echo "<p style='color: #4CAF50;'>✓ Polyfill loaded from: $polyfillPath</p>";
} else {
    echo "<p style='color: #f44336;'>❌ Polyfill not found at: $polyfillPath</p>";
    echo "<p>Trying alternative path...</p>";
    $altPath = dirname(__DIR__) . '/laravel/bootstrap/mime-polyfill.php';
    if (file_exists($altPath)) {
        require $altPath;
        echo "<p style='color: #4CAF50;'>✓ Polyfill loaded from: $altPath</p>";
    } else {
        echo "<p style='color: #f44336;'>❌ Polyfill not found at alternative path either</p>";
    }
}
echo "</div>";

echo "<div class='info'>";
echo "<h2>Step 3: Test global mime_content_type()</h2>";
$testFile = __FILE__;
if (function_exists('mime_content_type')) {
    try {
        $mime = mime_content_type($testFile);
        echo "<p style='color: #4CAF50;'>✓ Global mime_content_type() works</p>";
        echo "<p>Result for test file: <code>$mime</code></p>";
    } catch (\Throwable $e) {
        echo "<p style='color: #f44336;'>❌ Global mime_content_type() failed: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p style='color: #f44336;'>❌ Global mime_content_type() function does not exist</p>";
}
echo "</div>";

echo "<div class='info'>";
echo "<h2>Step 4: Test namespace mime_content_type()</h2>";
try {
    // Try to call the namespace function
    $namespaceFunction = 'Spatie\ImageOptimizer\mime_content_type';
    if (function_exists($namespaceFunction)) {
        $mime = $namespaceFunction($testFile);
        echo "<p style='color: #4CAF50;'>✓ Namespace mime_content_type() exists and works</p>";
        echo "<p>Result for test file: <code>$mime</code></p>";
    } else {
        // Try calling it directly (function_exists might not work for namespaced functions)
        try {
            $mime = call_user_func('Spatie\ImageOptimizer\mime_content_type', $testFile);
            echo "<p style='color: #4CAF50;'>✓ Namespace mime_content_type() works (via call_user_func)</p>";
            echo "<p>Result for test file: <code>$mime</code></p>";
        } catch (\Throwable $e) {
            echo "<p style='color: #f44336;'>❌ Namespace mime_content_type() does not exist or failed</p>";
            echo "<p>Error: " . $e->getMessage() . "</p>";
        }
    }
} catch (\Throwable $e) {
    echo "<p style='color: #f44336;'>❌ Error testing namespace function: " . $e->getMessage() . "</p>";
}
echo "</div>";

echo "<div class='info'>";
echo "<h2>Step 5: Test Image class instantiation</h2>";
try {
    // Try to create an Image object (this is what fails in production)
    require_once __DIR__ . '/vendor/autoload.php';
    
    $imagePath = __FILE__;
    $image = new \Spatie\ImageOptimizer\Image($imagePath);
    echo "<p style='color: #4CAF50;'>✓ Image class instantiated successfully</p>";
    
    try {
        $mime = $image->mime();
        echo "<p style='color: #4CAF50;'>✓ Image->mime() method works</p>";
        echo "<p>Result: <code>$mime</code></p>";
    } catch (\Throwable $e) {
        echo "<p style='color: #f44336;'>❌ Image->mime() failed: " . $e->getMessage() . "</p>";
        echo "<pre>" . $e->getTraceAsString() . "</pre>";
    }
} catch (\Throwable $e) {
    echo "<p style='color: #f44336;'>❌ Failed to instantiate Image class: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
echo "</div>";

echo "<div class='info'>";
echo "<h2>Step 6: Check if ImageOptimizerServiceProvider is registered</h2>";
try {
    require_once __DIR__ . '/vendor/autoload.php';
    $app = require_once __DIR__ . '/bootstrap/app.php';
    $providers = $app->getLoadedProviders();
    $imageOptimizerProvider = 'App\Providers\ImageOptimizerServiceProvider';
    if (isset($providers[$imageOptimizerProvider])) {
        echo "<p style='color: #4CAF50;'>✓ ImageOptimizerServiceProvider is registered</p>";
    } else {
        echo "<p style='color: #f44336;'>❌ ImageOptimizerServiceProvider is NOT registered</p>";
        echo "<p>Registered providers:</p>";
        echo "<pre>" . print_r(array_keys($providers), true) . "</pre>";
    }
} catch (\Throwable $e) {
    echo "<p style='color: #f44336;'>❌ Error checking providers: " . $e->getMessage() . "</p>";
}
echo "</div>";

?>

    <div class="error">
        <p><strong>⚠ Security Note:</strong> Please delete this file after testing!</p>
    </div>
</body>
</html>

