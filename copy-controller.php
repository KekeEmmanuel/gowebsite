<?php
/**
 * Copy TourPackageController from Repository to Laravel
 * 
 * Upload this file to public_html and run it via browser:
 * https://www.gotzsafari.com/copy-controller.php
 * 
 * IMPORTANT: Delete this file after use!
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

$homeDir = '/home/gotzsafari';
$repoPath = $homeDir . '/repositories/gowebsitelaravel';
$laravelPath = $homeDir . '/laravel';
$controllerFile = 'app/Http/Controllers/Admin/TourPackageController.php';
$sourcePath = $repoPath . '/' . $controllerFile;
$targetPath = $laravelPath . '/' . $controllerFile;

?>
<!DOCTYPE html>
<html>
<head>
    <title>Copy TourPackageController</title>
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
    <h1>📋 Copy TourPackageController</h1>
    <p>Copying controller from repository to Laravel directory...</p>
    <hr>

<?php

function logOutput($message, $type = 'info') {
    $class = $type === 'error' ? 'error' : ($type === 'success' ? 'success' : 'info');
    echo "<div class='$class'>$message</div>";
    flush();
}

// Check if repository directory exists
if (!is_dir($repoPath)) {
    logOutput("❌ Repository directory not found: $repoPath", 'error');
    exit;
}

logOutput("✓ Repository directory found", 'success');

// Check if source file exists
if (!file_exists($sourcePath)) {
    logOutput("❌ Source file not found: $sourcePath", 'error');
    echo "<p><strong>Note:</strong> Make sure you've pulled the latest changes from Git first.</p>";
    exit;
}

logOutput("✓ Source file found in repository", 'success');

// Get source file info
$sourceSize = filesize($sourcePath);
$sourceModified = date('Y-m-d H:i:s', filemtime($sourcePath));
logOutput("ℹ Source file size: " . number_format($sourceSize) . " bytes", 'info');
logOutput("ℹ Source file modified: $sourceModified", 'info');

// Check if target directory exists
$targetDir = dirname($targetPath);
if (!is_dir($targetDir)) {
    logOutput("Creating target directory: $targetDir", 'info');
    if (!mkdir($targetDir, 0755, true)) {
        logOutput("❌ Failed to create target directory", 'error');
        exit;
    }
    logOutput("✓ Target directory created", 'success');
} else {
    logOutput("✓ Target directory exists", 'success');
}

// Backup existing file if it exists
if (file_exists($targetPath)) {
    $backupPath = $targetPath . '.backup.' . date('Y-m-d_H-i-s');
    if (copy($targetPath, $backupPath)) {
        logOutput("✓ Backed up existing controller to: " . basename($backupPath), 'success');
    } else {
        logOutput("⚠ Could not backup existing controller (continuing anyway)", 'info');
    }
}

// Read source file
$fileContent = file_get_contents($sourcePath);
if ($fileContent === false) {
    logOutput("❌ Failed to read source file", 'error');
    exit;
}

logOutput("✓ Read source file (" . number_format(strlen($fileContent)) . " bytes)", 'success');

// Write to target
if (file_put_contents($targetPath, $fileContent) === false) {
    logOutput("❌ Failed to write to target file", 'error');
    exit;
}

logOutput("✓ Controller copied successfully!", 'success');

// Set permissions
chmod($targetPath, 0644);
logOutput("✓ Set file permissions (0644)", 'success');

// Verify the file was written correctly
$verifyContent = file_get_contents($targetPath);
if ($verifyContent === $fileContent) {
    logOutput("✓ Verified: File content matches", 'success');
} else {
    logOutput("⚠ Warning: File content verification failed (file may be corrupted)", 'error');
}

// Check if the controller has error handling
if (strpos($fileContent, 'try {') !== false && strpos($fileContent, 'hasMedia') !== false) {
    logOutput("✓ Error handling code detected in controller", 'success');
} else {
    logOutput("⚠ Warning: Error handling code not found (file may be outdated)", 'info');
}

// Show file info
$fileInfo = [
    'Source' => $sourcePath,
    'Target' => $targetPath,
    'Size' => number_format(filesize($targetPath)) . ' bytes',
    'Permissions' => substr(sprintf('%o', fileperms($targetPath)), -4),
    'Modified' => date('Y-m-d H:i:s', filemtime($targetPath)),
];

echo "<div class='info'>";
echo "<h3>File Information:</h3>";
echo "<pre>";
foreach ($fileInfo as $key => $value) {
    echo "$key: $value\n";
}
echo "</pre>";
echo "</div>";

// Clear Laravel caches
logOutput("Clearing Laravel caches...", 'info');

$phpPath = '';
$phpPaths = [
    '/opt/cpanel/ea-php81/root/usr/bin/php',
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

if (!empty($phpPath)) {
    $commands = [
        'cache:clear' => 'Clearing application cache',
        'config:clear' => 'Clearing configuration cache',
        'route:clear' => 'Clearing route cache',
    ];
    
    foreach ($commands as $command => $description) {
        $fullCommand = "cd $laravelPath && $phpPath artisan $command 2>&1";
        $output = [];
        $returnVar = 0;
        exec($fullCommand, $output, $returnVar);
        
        if ($returnVar === 0) {
            logOutput("✓ $description", 'success');
        } else {
            logOutput("⚠ $description failed", 'info');
        }
    }
} else {
    logOutput("⚠ PHP not found - could not clear caches (you may need to clear them manually)", 'info');
}

?>

    <hr>
    <div class="info">
        <h3>✅ Copy Complete!</h3>
        <p><strong>Next Steps:</strong></p>
        <ol>
            <li>Test the tour packages page: <a href="/admin/tour-packages" style="color: #4CAF50;">/admin/tour-packages</a></li>
            <li>The page should now load without 500 errors</li>
            <li>Images may not display until fileinfo extension is enabled</li>
            <li><strong>Delete this script file for security!</strong></li>
        </ol>
    </div>

    <div class="error">
        <p><strong>⚠ Security Note:</strong> Please delete this file (copy-controller.php) from your server after use!</p>
    </div>
</body>
</html>

