<?php
/**
 * Sync Routes File - Copy latest routes/web.php from Git to Laravel
 * Run this script via browser: https://www.gotzsafari.com/sync-routes.php
 * IMPORTANT: Delete this file after use!
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

$gitRepoPath = '/home/gotzsafari/repositories/gowebsitelaravel';
$laravelPath = '/home/gotzsafari/laravel';

function logOutput($message) {
    echo "<p style='color: green;'>✓ $message</p>";
    flush();
}

function logError($message) {
    echo "<p style='color: red;'>✗ $message</p>";
    flush();
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Sync Routes File</title>
    <style>
        body { font-family: monospace; padding: 20px; background: #1a1a1a; color: #fff; }
        .container { max-width: 900px; margin: 0 auto; }
        h1 { color: #4CAF50; }
        .step { background: #2a2a2a; padding: 15px; margin: 10px 0; border-left: 4px solid #4CAF50; }
        pre { background: #1a1a1a; padding: 10px; overflow-x: auto; font-size: 11px; }
    </style>
</head>
<body>
<div class="container">
    <h1>🔄 Sync Routes File</h1>
    <p>Syncing routes file at <?php echo date('Y-m-d H:i:s'); ?></p>
    <hr>

<?php

// Check if Git repo exists
if (!is_dir($gitRepoPath)) {
    logError("Git repository not found at $gitRepoPath");
    echo "</div></body></html>";
    exit;
}

// Check if Laravel directory exists
if (!is_dir($laravelPath)) {
    logError("Laravel directory not found at $laravelPath");
    echo "</div></body></html>";
    exit;
}

$sourceFile = "$gitRepoPath/routes/web.php";
$targetFile = "$laravelPath/routes/web.php";

// Check if source file exists
if (!file_exists($sourceFile)) {
    logError("Source routes file not found at $sourceFile");
    echo "</div></body></html>";
    exit;
}

// Backup existing file
if (file_exists($targetFile)) {
    $backupFile = "$targetFile.backup." . date('Y-m-d_H-i-s');
    if (copy($targetFile, $backupFile)) {
        logOutput("Backed up existing routes file to: " . basename($backupFile));
    } else {
        logError("Failed to backup existing routes file");
    }
}

// Copy the file
if (copy($sourceFile, $targetFile)) {
    logOutput("Successfully copied routes/web.php from Git repository");
    
    // Check if contact-messages route is in the file
    $content = file_get_contents($targetFile);
    if (strpos($content, 'contact-messages') !== false) {
        logOutput("Verified: contact-messages route found in routes file");
    } else {
        logError("WARNING: contact-messages route NOT found in routes file!");
    }
    
    // Show a snippet of the routes file
    echo "<div class='step'>";
    echo "<h3>Routes File Content (snippet):</h3>";
    $lines = explode("\n", $content);
    $contactLines = [];
    $inContactSection = false;
    foreach ($lines as $i => $line) {
        if (strpos($line, 'contact-messages') !== false || strpos($line, 'contact-channels') !== false || strpos($line, 'contact-quick-facts') !== false) {
            $inContactSection = true;
            $start = max(0, $i - 2);
            $end = min(count($lines), $i + 3);
            for ($j = $start; $j < $end; $j++) {
                $contactLines[] = ($j + 1) . ": " . $lines[$j];
            }
            break;
        }
    }
    if (!empty($contactLines)) {
        echo "<pre>" . htmlspecialchars(implode("\n", $contactLines)) . "</pre>";
    } else {
        echo "<pre>" . htmlspecialchars(substr($content, 0, 500)) . "...</pre>";
    }
    echo "</div>";
    
} else {
    logError("Failed to copy routes file");
    echo "</div></body></html>";
    exit;
}

// Clear route cache
logOutput("Clearing route cache...");
$phpPath = '';
$phpPaths = [
    '/opt/cpanel/ea-php82/root/usr/bin/php',
    '/opt/cpanel/ea-php83/root/usr/bin/php',
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

$command = "cd $laravelPath && $phpPath artisan route:clear 2>&1";
$output = [];
$returnVar = 0;
exec($command, $output, $returnVar);

if ($returnVar === 0) {
    logOutput("Route cache cleared successfully");
} else {
    logError("Failed to clear route cache");
    echo "<pre>" . htmlspecialchars(implode("\n", $output)) . "</pre>";
}

echo "<hr>";
echo "<h2>Sync Summary</h2>";
echo "<div class='step'>";
echo "<strong>Completed:</strong><br>";
echo "✓ Routes file synced from Git repository<br>";
echo "✓ Route cache cleared<br>";
echo "</div>";

echo "<div class='step'>";
echo "<h3>⚠️  SECURITY WARNING:</h3>";
echo "<p><strong>Delete this file (sync-routes.php) immediately after use!</strong></p>";
echo "</div>";

?>

</div>
</body>
</html>

