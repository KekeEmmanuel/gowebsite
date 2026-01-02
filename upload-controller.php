<?php
/**
 * Upload TourPackageController to Server
 * 
 * Upload this file to public_html and run it via browser:
 * https://www.gotzsafari.com/upload-controller.php
 * 
 * IMPORTANT: Delete this file after use!
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

$homeDir = '/home/gotzsafari';
$repoPath = $homeDir . '/repositories/gowebsitelaravel';
$laravelPath = $homeDir . '/laravel';
$controllerPath = $laravelPath . '/app/Http/Controllers/Admin/TourPackageController.php';
$sourceControllerPath = $repoPath . '/app/Http/Controllers/Admin/TourPackageController.php';

?>
<!DOCTYPE html>
<html>
<head>
    <title>Upload TourPackageController</title>
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
    <h1>📤 Upload TourPackageController</h1>
    <p>Uploading updated controller to server...</p>
    <hr>

<?php

function logOutput($message, $type = 'info') {
    $class = $type === 'error' ? 'error' : ($type === 'success' ? 'success' : 'info');
    echo "<div class='$class'>$message</div>";
    flush();
}

// Check if repository exists
if (!is_dir($repoPath)) {
    logOutput("❌ Repository directory not found: $repoPath", 'error');
    echo "<p><strong>Note:</strong> Make sure the Git repository is set up correctly.</p>";
    exit;
}

// Check if source controller file exists in repository
if (!file_exists($sourceControllerPath)) {
    logOutput("❌ Controller file not found in repository: $sourceControllerPath", 'error');
    echo "<p><strong>Note:</strong> Make sure you've committed and pushed the updated controller to Git, then pulled on the server.</p>";
    exit;
}

logOutput("✓ Source controller file found in repository", 'success');

// Check if Laravel directory exists
if (!is_dir($laravelPath)) {
    logOutput("❌ Laravel directory not found: $laravelPath", 'error');
    exit;
}

logOutput("✓ Laravel directory found: $laravelPath", 'success');

// Check if controller directory exists
$controllerDir = dirname($controllerPath);
if (!is_dir($controllerDir)) {
    logOutput("Creating controller directory: $controllerDir", 'info');
    if (!mkdir($controllerDir, 0755, true)) {
        logOutput("❌ Failed to create controller directory", 'error');
        exit;
    }
    logOutput("✓ Controller directory created", 'success');
} else {
    logOutput("✓ Controller directory exists", 'success');
}

// Read controller file from repository
$controllerContent = file_get_contents($sourceControllerPath);
if ($controllerContent === false) {
    logOutput("❌ Failed to read controller file from repository", 'error');
    exit;
}

logOutput("✓ Read controller file from repository (" . number_format(strlen($controllerContent)) . " bytes)", 'success');

// Backup existing controller if it exists
if (file_exists($controllerPath)) {
    $backupPath = $controllerPath . '.backup.' . date('Y-m-d_H-i-s');
    if (copy($controllerPath, $backupPath)) {
        logOutput("✓ Backed up existing controller to: " . basename($backupPath), 'success');
    } else {
        logOutput("⚠ Could not backup existing controller (continuing anyway)", 'info');
    }
}

// Write controller to server
if (file_put_contents($controllerPath, $controllerContent) === false) {
    logOutput("❌ Failed to write controller to server", 'error');
    exit;
}

logOutput("✓ Controller uploaded successfully!", 'success');

// Set permissions
chmod($controllerPath, 0644);
logOutput("✓ Set file permissions (0644)", 'success');

// Verify the file was written correctly
$verifyContent = file_get_contents($controllerPath);
if ($verifyContent === $controllerContent) {
    logOutput("✓ Verified: File content matches", 'success');
} else {
    logOutput("⚠ Warning: File content verification failed (file may be corrupted)", 'error');
}

// Show file info
$fileInfo = [
    'Path' => $controllerPath,
    'Size' => number_format(filesize($controllerPath)) . ' bytes',
    'Permissions' => substr(sprintf('%o', fileperms($controllerPath)), -4),
    'Modified' => date('Y-m-d H:i:s', filemtime($controllerPath)),
];

echo "<div class='info'>";
echo "<h3>File Information:</h3>";
echo "<pre>";
foreach ($fileInfo as $key => $value) {
    echo "$key: $value\n";
}
echo "</pre>";
echo "</div>";

// Check if the controller has our error handling
if (strpos($controllerContent, 'try {') !== false && strpos($controllerContent, 'hasMedia') !== false) {
    logOutput("✓ Error handling code detected in controller", 'success');
} else {
    logOutput("⚠ Warning: Error handling code not found (file may be outdated)", 'error');
}

?>

    <hr>
    <div class="info">
        <h3>✅ Upload Complete!</h3>
        <p><strong>Next Steps:</strong></p>
        <ol>
            <li>Test the tour packages page: <a href="/admin/tour-packages" style="color: #4CAF50;">/admin/tour-packages</a></li>
            <li>The page should now load without 500 errors</li>
            <li>Images may not display until fileinfo extension is enabled</li>
            <li><strong>Delete this script file for security!</strong></li>
        </ol>
    </div>

    <div class="error">
        <p><strong>⚠ Security Note:</strong> Please delete this file (upload-controller.php) from your server after use!</p>
    </div>
</body>
</html>

