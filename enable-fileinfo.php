<?php
/**
 * Attempt to Enable fileinfo Extension
 * 
 * Upload this file to public_html and run it via browser:
 * https://www.gotzsafari.com/enable-fileinfo.php
 * 
 * NOTE: This script will attempt to enable fileinfo, but it may fail
 * if you don't have sufficient permissions. In that case, contact your hosting provider.
 * 
 * IMPORTANT: Delete this file after use!
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

?>
<!DOCTYPE html>
<html>
<head>
    <title>Enable fileinfo Extension</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 900px; margin: 50px auto; padding: 20px; background: #1a1a1a; color: #fff; }
        .success { color: #4CAF50; padding: 15px; background: #2a2a2a; border-left: 4px solid #4CAF50; margin: 10px 0; }
        .error { color: #f44336; padding: 15px; background: #2a2a2a; border-left: 4px solid #f44336; margin: 10px 0; }
        .warning { color: #FFD700; padding: 15px; background: #2a2a2a; border-left: 4px solid #FFD700; margin: 10px 0; }
        .info { color: #2196F3; padding: 15px; background: #2a2a2a; border-left: 4px solid #2196F3; margin: 10px 0; }
        pre { background: #1a1a1a; padding: 10px; overflow-x: auto; font-size: 12px; border: 1px solid #444; }
        h1 { color: #4CAF50; }
        code { background: #2a2a2a; padding: 2px 6px; border-radius: 3px; }
    </style>
</head>
<body>
    <h1>🔧 Enable fileinfo Extension</h1>
    <p>Checking current status and attempting to enable fileinfo...</p>
    <hr>

<?php

function logOutput($message, $type = 'info') {
    $class = $type === 'error' ? 'error' : ($type === 'warning' ? 'warning' : ($type === 'success' ? 'success' : 'info'));
    echo "<div class='$class'>$message</div>";
    flush();
}

// Check current PHP version
$phpVersion = phpversion();
logOutput("ℹ Current PHP version: <strong>$phpVersion</strong>", 'info');

// Check if fileinfo is already enabled
if (extension_loaded('fileinfo')) {
    logOutput("✅ fileinfo extension is ALREADY ENABLED!", 'success');
    echo "<p>No action needed. The extension is working correctly.</p>";
    exit;
}

logOutput("❌ fileinfo extension is NOT enabled", 'error');

// Find PHP configuration files
$homeDir = '/home/gotzsafari';
$phpIniPaths = [];
$phpVersionShort = substr($phpVersion, 0, 3); // e.g., "8.1"

// Common PHP INI locations for cPanel
$possiblePaths = [
    "$homeDir/.user.ini",
    "$homeDir/public_html/.user.ini",
    "/opt/cpanel/ea-php{$phpVersionShort}/root/etc/php.ini",
    "/opt/cpanel/ea-php{$phpVersionShort}/root/etc/php.d/fileinfo.ini",
    php_ini_loaded_file(),
    php_ini_scanned_files(),
];

logOutput("🔍 Searching for PHP configuration files...", 'info');

$foundIniFiles = [];
foreach ($possiblePaths as $path) {
    if (is_string($path) && file_exists($path)) {
        $foundIniFiles[] = $path;
        logOutput("✓ Found: $path", 'success');
    } elseif (is_string($path) && $path) {
        // php_ini_scanned_files() returns a string
        $scanned = explode(',', $path);
        foreach ($scanned as $scannedFile) {
            $scannedFile = trim($scannedFile);
            if ($scannedFile && file_exists($scannedFile)) {
                $foundIniFiles[] = $scannedFile;
                logOutput("✓ Found: $scannedFile", 'success');
            }
        }
    }
}

// Check loaded php.ini
$loadedIni = php_ini_loaded_file();
if ($loadedIni) {
    logOutput("ℹ Loaded php.ini: <code>$loadedIni</code>", 'info');
}

// Check scanned directories
$scannedDirs = php_ini_scanned_files();
if ($scannedDirs) {
    logOutput("ℹ Scanned directories: <code>$scannedDirs</code>", 'info');
}

// Attempt to find and enable fileinfo
logOutput("🔧 Attempting to enable fileinfo extension...", 'info');

$enabled = false;
$attempts = [];

// Method 1: Try to enable via ini_set (won't work for extensions, but let's try)
$attempts[] = "Method 1: ini_set() - This won't work for extensions";

// Method 2: Try to modify .user.ini
$userIniPath = "$homeDir/public_html/.user.ini";
if (file_exists($userIniPath) && is_writable($userIniPath)) {
    $iniContent = file_get_contents($userIniPath);
    if (strpos($iniContent, 'extension=fileinfo') === false && 
        strpos($iniContent, 'extension=fileinfo.so') === false) {
        $iniContent .= "\n; Enable fileinfo extension\n";
        $iniContent .= "extension=fileinfo\n";
        
        if (file_put_contents($userIniPath, $iniContent)) {
            logOutput("✓ Added fileinfo to .user.ini", 'success');
            $attempts[] = "Method 2: Modified .user.ini - Changes may take effect after PHP restart";
        } else {
            logOutput("❌ Failed to write to .user.ini", 'error');
            $attempts[] = "Method 2: Failed to modify .user.ini";
        }
    } else {
        logOutput("ℹ fileinfo already mentioned in .user.ini", 'info');
        $attempts[] = "Method 2: fileinfo already in .user.ini";
    }
} else {
    if (!file_exists($userIniPath)) {
        // Try to create it
        $iniContent = "; PHP Configuration\n";
        $iniContent .= "extension=fileinfo\n";
        if (file_put_contents($userIniPath, $iniContent)) {
            logOutput("✓ Created .user.ini with fileinfo extension", 'success');
            $attempts[] = "Method 2: Created .user.ini";
        } else {
            logOutput("❌ Cannot create .user.ini (permission denied)", 'error');
            $attempts[] = "Method 2: Cannot create .user.ini";
        }
    } else {
        logOutput("❌ .user.ini exists but is not writable", 'error');
        $attempts[] = "Method 2: .user.ini not writable";
    }
}

// Method 3: Try to find and modify php.ini
$phpIniFile = php_ini_loaded_file();
if ($phpIniFile && is_writable($phpIniFile)) {
    $iniContent = file_get_contents($phpIniFile);
    if (strpos($iniContent, ';extension=fileinfo') !== false) {
        // Uncomment it
        $iniContent = str_replace(';extension=fileinfo', 'extension=fileinfo', $iniContent);
        if (file_put_contents($phpIniFile, $iniContent)) {
            logOutput("✓ Uncommented fileinfo in php.ini", 'success');
            $attempts[] = "Method 3: Modified php.ini";
        } else {
            logOutput("❌ Failed to write to php.ini", 'error');
            $attempts[] = "Method 3: Failed to modify php.ini";
        }
    } else {
        logOutput("ℹ fileinfo not found or already enabled in php.ini", 'info');
        $attempts[] = "Method 3: php.ini check completed";
    }
} else {
    if ($phpIniFile) {
        logOutput("❌ php.ini is not writable: $phpIniFile", 'error');
        $attempts[] = "Method 3: php.ini not writable";
    } else {
        logOutput("ℹ php.ini file not found", 'info');
        $attempts[] = "Method 3: php.ini not found";
    }
}

// Check if fileinfo.so exists
logOutput("🔍 Checking for fileinfo extension file...", 'info');
$extensionPaths = [
    "/opt/cpanel/ea-php{$phpVersionShort}/root/usr/lib64/php/modules/fileinfo.so",
    "/opt/cpanel/ea-php{$phpVersionShort}/root/usr/lib/php/modules/fileinfo.so",
    "/usr/lib64/php/modules/fileinfo.so",
    "/usr/lib/php/modules/fileinfo.so",
];

$extensionFound = false;
foreach ($extensionPaths as $extPath) {
    if (file_exists($extPath)) {
        logOutput("✓ Found fileinfo.so at: <code>$extPath</code>", 'success');
        $extensionFound = true;
        break;
    }
}

if (!$extensionFound) {
    logOutput("❌ fileinfo.so not found in common locations", 'error');
}

// Summary
echo "<hr>";
echo "<h2>📋 Summary</h2>";

echo "<div class='info'>";
echo "<h3>Attempts Made:</h3>";
echo "<ul>";
foreach ($attempts as $attempt) {
    echo "<li>$attempt</li>";
}
echo "</ul>";
echo "</div>";

// Check again if it's enabled
if (extension_loaded('fileinfo')) {
    logOutput("✅ SUCCESS! fileinfo extension is now enabled!", 'success');
} else {
    logOutput("❌ fileinfo extension is still not enabled", 'error');
    
    echo "<div class='warning'>";
    echo "<h3>⚠️ Manual Steps Required</h3>";
    echo "<p>This script cannot enable the extension automatically. You need to:</p>";
    echo "<ol>";
    echo "<li><strong>Contact your hosting provider</strong> and ask them to enable the <code>fileinfo</code> extension for PHP $phpVersionShort</li>";
    echo "<li>Or, if you have cPanel access, go to <strong>Select PHP Version → Extensions</strong> and enable fileinfo</li>";
    echo "<li>After enabling, PHP may need to be restarted (usually automatic in cPanel)</li>";
    echo "</ol>";
    echo "</div>";
    
    echo "<div class='info'>";
    echo "<h3>📧 Message for Hosting Provider:</h3>";
    echo "<pre style='background: #2a2a2a; padding: 15px; border-radius: 4px;'>";
    echo "Hello,\n\n";
    echo "I need the PHP fileinfo extension enabled for my account (gotzsafari.com).\n\n";
    echo "Current PHP Version: PHP $phpVersionShort\n";
    echo "Extension Needed: fileinfo\n\n";
    echo "This extension is required by Laravel's Media Library to detect MIME types of uploaded files.\n\n";
    echo "Please enable the fileinfo extension for PHP $phpVersionShort on my account.\n\n";
    echo "Thank you!";
    echo "</pre>";
    echo "</div>";
}

// Show current extension status
echo "<div class='info'>";
echo "<h3>Current Extension Status:</h3>";
echo "<pre>";
echo "extension_loaded('fileinfo'): " . (extension_loaded('fileinfo') ? 'true' : 'false') . "\n";
echo "class_exists('finfo'): " . (class_exists('finfo') ? 'true' : 'false') . "\n";
echo "function_exists('mime_content_type'): " . (function_exists('mime_content_type') ? 'true' : 'false') . "\n";
echo "</pre>";
echo "</div>";

?>

    <hr>
    <div class="error">
        <p><strong>⚠ Security Note:</strong> Please delete this file (enable-fileinfo.php) from your server after use!</p>
    </div>
</body>
</html>

