<?php
/**
 * Deploy Build Folder
 * Run this script via browser: https://www.gotzsafari.com/deploy-build.php
 * IMPORTANT: Delete this file after use!
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

$homeDir = '/home/gotzsafari';
$publicHtmlPath = $homeDir . '/public_html';
$laravelPath = $homeDir . '/laravel';
$buildZipPath = $publicHtmlPath . '/build.zip';

function logOutput($message) {
    echo "<p style='color: #4CAF50;'>✓ $message</p>";
    flush();
}

function logError($message) {
    echo "<p style='color: #f44336;'>✗ $message</p>";
    flush();
}

function logInfo($message) {
    echo "<p style='color: #2196F3;'>ℹ $message</p>";
    flush();
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Deploy Build</title>
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
    <h1>📦 Deploy Build Folder</h1>
    <p>Deploying build assets at <?php echo date('Y-m-d H:i:s'); ?></p>
    <hr>

<?php

// Check if build.zip exists
if (!file_exists($buildZipPath)) {
    logError("build.zip not found at: $buildZipPath");
    logInfo("Please upload build.zip to public_html first");
    echo "</div></body></html>";
    exit;
}

logOutput("Found build.zip (" . round(filesize($buildZipPath) / 1024, 2) . " KB)");

// Extract build.zip to a temporary location
$tempExtractPath = $publicHtmlPath . '/build_temp';
if (is_dir($tempExtractPath)) {
    // Remove existing temp directory
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($tempExtractPath, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );
    foreach ($files as $file) {
        $file->isDir() ? rmdir($file->getRealPath()) : unlink($file->getRealPath());
    }
    rmdir($tempExtractPath);
}

mkdir($tempExtractPath, 0755, true);

logInfo("Extracting build.zip...");

$zip = new ZipArchive();
if ($zip->open($buildZipPath) === TRUE) {
    $zip->extractTo($tempExtractPath);
    $zip->close();
    logOutput("Extracted build.zip successfully");
} else {
    logError("Failed to open build.zip");
    echo "</div></body></html>";
    exit;
}

// Check if build folder exists in extracted files
$extractedBuildPath = $tempExtractPath . '/build';
if (!is_dir($extractedBuildPath)) {
    logError("Build folder not found in extracted files");
    echo "</div></body></html>";
    exit;
}

// Backup existing build folder if it exists
$targetBuildPath = $laravelPath . '/public/build';
if (is_dir($targetBuildPath)) {
    $backupPath = $targetBuildPath . '.backup.' . date('Y-m-d_H-i-s');
    if (rename($targetBuildPath, $backupPath)) {
        logOutput("Backed up existing build folder to: " . basename($backupPath));
    } else {
        logError("Failed to backup existing build folder");
    }
}

// Copy build folder to Laravel public directory
logInfo("Copying build folder to Laravel public directory...");

function copyDirectory($source, $target) {
    if (!is_dir($source)) {
        return false;
    }
    
    if (!is_dir($target)) {
        mkdir($target, 0755, true);
    }
    
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($source, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );
    
    $copied = 0;
    foreach ($files as $file) {
        $targetPath = $target . DIRECTORY_SEPARATOR . $files->getSubPathName();
        
        if ($file->isDir()) {
            if (!is_dir($targetPath)) {
                mkdir($targetPath, 0755, true);
            }
        } else {
            if (copy($file->getRealPath(), $targetPath)) {
                $copied++;
            }
        }
    }
    
    return $copied;
}

$copiedFiles = copyDirectory($extractedBuildPath, $targetBuildPath);

if ($copiedFiles > 0) {
    logOutput("Copied $copiedFiles files to Laravel public/build");
} else {
    logError("Failed to copy build files");
    echo "</div></body></html>";
    exit;
}

// Set permissions
chmod($targetBuildPath, 0755);
$assetsPath = $targetBuildPath . '/assets';
if (is_dir($assetsPath)) {
    chmod($assetsPath, 0755);
}

logOutput("Set appropriate permissions on build folder");

// Create symlink in public_html if it doesn't exist
$publicHtmlBuildPath = $publicHtmlPath . '/build';
if (!is_link($publicHtmlBuildPath) && !is_dir($publicHtmlBuildPath)) {
    if (symlink($targetBuildPath, $publicHtmlBuildPath)) {
        logOutput("Created symlink: public_html/build -> laravel/public/build");
    } else {
        logError("Failed to create symlink (this is okay if build is accessible directly)");
    }
}

// Clean up temp directory
$files = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($tempExtractPath, RecursiveDirectoryIterator::SKIP_DOTS),
    RecursiveIteratorIterator::CHILD_FIRST
);
foreach ($files as $file) {
    $file->isDir() ? rmdir($file->getRealPath()) : unlink($file->getRealPath());
}
rmdir($tempExtractPath);
logOutput("Cleaned up temporary files");

echo "<hr>";
echo "<h2>✅ Deployment Summary</h2>";
echo "<div class='step'>";
echo "<strong>Completed:</strong><br>";
echo "✓ Extracted build.zip<br>";
echo "✓ Copied $copiedFiles files to Laravel public/build<br>";
echo "✓ Set permissions<br>";
echo "✓ Created symlink (if needed)<br>";
echo "</div>";

echo "<div class='step'>";
echo "<h3>⚠️  SECURITY WARNING:</h3>";
echo "<p><strong>Delete this file (deploy-build.php) immediately after use!</strong></p>";
echo "</div>";

?>

</div>
</body>
</html>

