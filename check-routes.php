<?php
/**
 * Check Routes - Verify all routes are registered
 * Run this script via browser: https://www.gotzsafari.com/check-routes.php
 * IMPORTANT: Delete this file after use!
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

$laravelPath = '/home/gotzsafari/laravel';

// Find PHP path
$phpPath = '';
$phpPaths = [
    '/opt/cpanel/ea-php82/root/usr/bin/php',  // PHP 8.2
    '/opt/cpanel/ea-php83/root/usr/bin/php',  // PHP 8.3
    '/usr/local/bin/php',
    '/usr/bin/php',
    'php',
];
foreach ($phpPaths as $path) {
    if ($path === 'php' || file_exists($path)) {
        $phpPath = $path;
        break;
    }
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Check Routes</title>
    <style>
        body { font-family: monospace; padding: 20px; background: #1a1a1a; color: #fff; }
        .container { max-width: 1200px; margin: 0 auto; }
        h1 { color: #4CAF50; }
        .route { background: #2a2a2a; padding: 10px; margin: 5px 0; border-left: 4px solid #4CAF50; }
        .missing { border-left-color: #f44336; }
        pre { background: #1a1a1a; padding: 10px; overflow-x: auto; font-size: 11px; }
    </style>
</head>
<body>
<div class="container">
    <h1>🔍 Check Routes</h1>
    <p>Checking routes at <?php echo date('Y-m-d H:i:s'); ?></p>
    <hr>

<?php

// Check if artisan exists
if (!file_exists("$laravelPath/artisan")) {
    echo "<p style='color: red;'>✗ Laravel artisan file not found at $laravelPath/artisan</p>";
    echo "</div></body></html>";
    exit;
}

// Get all routes
$command = "cd $laravelPath && $phpPath artisan route:list --json";
$output = [];
$returnVar = 0;
exec($command . ' 2>&1', $output, $returnVar);

if ($returnVar === 0 && !empty($output)) {
    $routesJson = json_decode(implode("\n", $output), true);
    
    if ($routesJson) {
        $contactRoutes = array_filter($routesJson, function($route) {
            return strpos($route['name'] ?? '', 'contact') !== false;
        });
        
        echo "<h2>Contact-related Routes:</h2>";
        foreach ($contactRoutes as $route) {
            $name = $route['name'] ?? 'unnamed';
            $uri = $route['uri'] ?? '';
            $methods = is_array($route['methods']) ? implode(', ', $route['methods']) : '';
            echo "<div class='route'>";
            echo "<strong>{$name}</strong><br>";
            echo "URI: {$uri}<br>";
            echo "Methods: {$methods}<br>";
            echo "</div>";
        }
        
        // Check for specific routes
        $requiredRoutes = [
            'admin.contact-messages.index',
            'admin.contact-messages.show',
            'admin.contact-messages.update',
            'admin.contact-messages.destroy',
            'admin.contact-channels.index',
            'admin.contact-quick-facts.index',
        ];
        
        echo "<hr><h2>Required Routes Check:</h2>";
        $routeNames = array_column($routesJson, 'name');
        foreach ($requiredRoutes as $requiredRoute) {
            if (in_array($requiredRoute, $routeNames)) {
                echo "<div class='route'>✓ {$requiredRoute}</div>";
            } else {
                echo "<div class='route missing'>✗ {$requiredRoute} - MISSING!</div>";
            }
        }
    } else {
        echo "<p style='color: red;'>Failed to parse route list JSON</p>";
        echo "<pre>" . htmlspecialchars(implode("\n", $output)) . "</pre>";
    }
} else {
    echo "<p style='color: red;'>Failed to get route list</p>";
    echo "<pre>" . htmlspecialchars(implode("\n", $output)) . "</pre>";
}

?>

</div>
</body>
</html>

