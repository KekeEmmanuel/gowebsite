<?php
/**
 * Check Homepage Issues
 * Upload to public_html and run: https://www.gotzsafari.com/check-homepage.php
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

$homeDir = '/home/gotzsafari';
$laravelPath = $homeDir . '/laravel';
$publicHtmlPath = $homeDir . '/public_html';

?>
<!DOCTYPE html>
<html>
<head>
    <title>Check Homepage Issues</title>
    <style>
        body { font-family: Arial; max-width: 900px; margin: 50px auto; padding: 20px; background: #1a1a1a; color: #fff; }
        .success { color: #4CAF50; padding: 10px; background: #2a2a2a; margin: 5px 0; border-left: 4px solid #4CAF50; }
        .error { color: #f44336; padding: 10px; background: #2a2a2a; margin: 5px 0; border-left: 4px solid #f44336; }
        .info { color: #2196F3; padding: 10px; background: #2a2a2a; margin: 5px 0; border-left: 4px solid #2196F3; }
        .warning { color: #FF9800; padding: 10px; background: #2a2a2a; margin: 5px 0; border-left: 4px solid #FF9800; }
        h1 { color: #4CAF50; }
        h2 { color: #4CAF50; margin-top: 30px; }
        pre { background: #000; padding: 10px; overflow-x: auto; font-size: 12px; }
        code { background: #2a2a2a; padding: 2px 6px; border-radius: 3px; }
    </style>
</head>
<body>
    <h1>🔍 Homepage Diagnostic</h1>

<?php

// Check 1: Marketing view exists
echo "<h2>1. Marketing View File</h2>";
$marketingView = $laravelPath . '/resources/views/marketing.blade.php';
if (file_exists($marketingView)) {
    echo "<div class='success'>✓ Marketing view exists: $marketingView</div>";
    $viewContent = file_get_contents($marketingView);
    if (strpos($viewContent, '@vite') !== false) {
        echo "<div class='info'>✓ View uses @vite directive</div>";
    } else {
        echo "<div class='error'>❌ View doesn't use @vite directive</div>";
    }
} else {
    echo "<div class='error'>❌ Marketing view NOT found: $marketingView</div>";
}

// Check 2: Build directory and symlink
echo "<h2>2. Build Assets</h2>";
$buildSymlink = $publicHtmlPath . '/build';
$buildTarget = $laravelPath . '/public/build';

if (is_link($buildSymlink)) {
    $linkTarget = readlink($buildSymlink);
    echo "<div class='success'>✓ Build symlink exists: $buildSymlink → $linkTarget</div>";
    
    if ($linkTarget === $buildTarget || $linkTarget === '../laravel/public/build') {
        echo "<div class='success'>✓ Symlink points to correct location</div>";
    } else {
        echo "<div class='warning'>⚠️ Symlink points to: $linkTarget (expected: $buildTarget)</div>";
    }
} else {
    echo "<div class='error'>❌ Build symlink NOT found: $buildSymlink</div>";
    echo "<div class='info'>💡 Run fix-build-assets.php to create the symlink</div>";
}

if (is_dir($buildTarget)) {
    echo "<div class='success'>✓ Build directory exists: $buildTarget</div>";
    
    // Check for manifest
    $manifest = $buildTarget . '/.vite/manifest.json';
    if (file_exists($manifest)) {
        echo "<div class='success'>✓ Vite manifest exists</div>";
        $manifestData = json_decode(file_get_contents($manifest), true);
        if ($manifestData) {
            echo "<div class='info'>✓ Manifest contains " . count($manifestData) . " entries</div>";
        }
    } else {
        echo "<div class='error'>❌ Vite manifest NOT found: $manifest</div>";
        echo "<div class='warning'>⚠️ You may need to run: npm run build</div>";
    }
} else {
    echo "<div class='error'>❌ Build directory NOT found: $buildTarget</div>";
    echo "<div class='warning'>⚠️ You need to build the frontend assets</div>";
}

// Check 3: Vite config
echo "<h2>3. Vite Configuration</h2>";
$viteConfig = $laravelPath . '/vite.config.js';
if (file_exists($viteConfig)) {
    echo "<div class='success'>✓ Vite config exists</div>";
    $configContent = file_get_contents($viteConfig);
    if (strpos($configContent, 'resources/marketing/main.ts') !== false) {
        echo "<div class='success'>✓ Config includes marketing entry point</div>";
    }
} else {
    echo "<div class='error'>❌ Vite config NOT found</div>";
}

// Check 4: Marketing entry point
echo "<h2>4. Marketing Entry Point</h2>";
$marketingMain = $laravelPath . '/resources/marketing/main.ts';
if (file_exists($marketingMain)) {
    echo "<div class='success'>✓ Marketing main.ts exists</div>";
} else {
    echo "<div class='error'>❌ Marketing main.ts NOT found: $marketingMain</div>";
}

// Check 5: Test route
echo "<h2>5. Route Test</h2>";
echo "<div class='info'>Testing if Laravel can serve the homepage route...</div>";

// Try to bootstrap Laravel and test the route
try {
    require $laravelPath . '/vendor/autoload.php';
    $app = require_once $laravelPath . '/bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    
    $request = Illuminate\Http\Request::create('/', 'GET');
    $response = $kernel->handle($request);
    
    if ($response->getStatusCode() === 200) {
        echo "<div class='success'>✓ Route returns 200 OK</div>";
        $content = $response->getContent();
        if (strpos($content, 'marketing-app') !== false) {
            echo "<div class='success'>✓ Response contains marketing-app div</div>";
        } else {
            echo "<div class='warning'>⚠️ Response doesn't contain marketing-app div</div>";
        }
        if (strpos($content, '@vite') !== false || strpos($content, '/build/') !== false) {
            echo "<div class='success'>✓ Response contains Vite asset references</div>";
        } else {
            echo "<div class='warning'>⚠️ Response doesn't contain Vite asset references</div>";
        }
    } else {
        echo "<div class='error'>❌ Route returns status: " . $response->getStatusCode() . "</div>";
    }
} catch (Exception $e) {
    echo "<div class='error'>❌ Error testing route: " . $e->getMessage() . "</div>";
}

// Check 6: Laravel logs for errors
echo "<h2>6. Recent Laravel Errors</h2>";
$laravelLog = $laravelPath . '/storage/logs/laravel.log';
if (file_exists($laravelLog)) {
    $logSize = filesize($laravelLog);
    echo "<div class='info'>Log file size: " . number_format($logSize / 1024, 2) . " KB</div>";
    
    // Read last 50 lines
    $lines = file($laravelLog);
    $recentLines = array_slice($lines, -50);
    $recentErrors = array_filter($recentLines, function($line) {
        return stripos($line, 'error') !== false || stripos($line, 'exception') !== false;
    });
    
    if (count($recentErrors) > 0) {
        echo "<div class='warning'>⚠️ Found " . count($recentErrors) . " recent error lines:</div>";
        echo "<pre>" . htmlspecialchars(implode('', array_slice($recentErrors, -10))) . "</pre>";
    } else {
        echo "<div class='success'>✓ No recent errors in log</div>";
    }
} else {
    echo "<div class='warning'>⚠️ Laravel log file not found</div>";
}

// Summary
echo "<hr>";
echo "<h2>📋 Summary & Recommendations</h2>";
echo "<div class='info'>";
echo "<ol>";
echo "<li>If build symlink is missing, run: <code>fix-build-assets.php</code></li>";
echo "<li>If build directory is missing, run: <code>npm run build</code> in the Laravel directory</li>";
echo "<li>If Vite manifest is missing, rebuild assets</li>";
echo "<li>Check browser console for JavaScript errors</li>";
echo "<li>Check if <code>/build/assets/</code> URLs are accessible</li>";
echo "</ol>";
echo "</div>";

?>

</body>
</html>
