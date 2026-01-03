<?php
/**
 * Check storage symlink and media files
 * Upload to public_html and run: https://www.gotzsafari.com/check-storage.php
 */

$homeDir = '/home/gotzsafari';
$laravelPath = $homeDir . '/laravel';
$publicHtml = $homeDir . '/public_html';

?>
<!DOCTYPE html>
<html>
<head>
    <title>Check Storage</title>
    <style>
        body { font-family: Arial; max-width: 900px; margin: 50px auto; padding: 20px; background: #1a1a1a; color: #fff; }
        .success { color: #4CAF50; padding: 10px; background: #2a2a2a; margin: 5px 0; }
        .error { color: #f44336; padding: 10px; background: #2a2a2a; margin: 5px 0; }
        .info { color: #2196F3; padding: 10px; background: #2a2a2a; margin: 5px 0; }
        .warning { color: #FFD700; padding: 10px; background: #2a2a2a; margin: 5px 0; }
        h1 { color: #4CAF50; }
        pre { background: #000; padding: 10px; overflow-x: auto; font-size: 12px; }
    </style>
</head>
<body>
    <h1>🔍 Storage & Media Library Check</h1>

<?php

// Step 1: Check storage symlink
echo "<div class='info'><strong>Step 1:</strong> Check storage symlink</div>";
$storageLink = $publicHtml . '/storage';
$storageTarget = $laravelPath . '/storage/app/public';

if (is_link($storageLink)) {
    $linkTarget = readlink($storageLink);
    if ($linkTarget === $storageTarget || realpath($storageLink) === realpath($storageTarget)) {
        echo "<div class='success'>✓ Storage symlink exists and points to: $linkTarget</div>";
    } else {
        echo "<div class='error'>❌ Storage symlink points to wrong location: $linkTarget</div>";
        echo "<div class='info'>Expected: $storageTarget</div>";
    }
} else {
    echo "<div class='error'>❌ Storage symlink does NOT exist at: $storageLink</div>";
    echo "<div class='warning'>This is likely why images aren't displaying!</div>";
    
    // Try to create it
    echo "<div class='info'>Attempting to create symlink...</div>";
    if (symlink($storageTarget, $storageLink)) {
        echo "<div class='success'>✓ Symlink created successfully!</div>";
    } else {
        echo "<div class='error'>❌ Failed to create symlink. You may need to run: php artisan storage:link</div>";
    }
}

// Step 2: Check media directory
echo "<div class='info'><strong>Step 2:</strong> Check media directory</div>";
$mediaDir = $laravelPath . '/storage/app/public/media';
if (is_dir($mediaDir)) {
    echo "<div class='success'>✓ Media directory exists: $mediaDir</div>";
    
    // Count files
    $files = glob($mediaDir . '/*/*/*');
    $fileCount = count($files);
    echo "<div class='info'>Found $fileCount media files</div>";
    
    // Show first few files
    if ($fileCount > 0) {
        echo "<div class='info'>Sample files:</div>";
        echo "<pre>";
        foreach (array_slice($files, 0, 5) as $file) {
            $relative = str_replace($laravelPath . '/storage/app/public/', '', $file);
            echo "$relative (" . filesize($file) . " bytes)\n";
        }
        echo "</pre>";
    }
} else {
    echo "<div class='error'>❌ Media directory does NOT exist: $mediaDir</div>";
}

// Step 3: Check if files are accessible via web
echo "<div class='info'><strong>Step 3:</strong> Check file accessibility</div>";
if ($fileCount > 0) {
    $sampleFile = $files[0];
    $relativePath = str_replace($laravelPath . '/storage/app/public/', '', $sampleFile);
    $webPath = '/storage/' . $relativePath;
    $fullPath = $publicHtml . $webPath;
    
    if (file_exists($fullPath) || is_link($fullPath)) {
        echo "<div class='success'>✓ File accessible via web: $webPath</div>";
    } else {
        echo "<div class='error'>❌ File NOT accessible via web: $webPath</div>";
        echo "<div class='info'>Full path checked: $fullPath</div>";
    }
}

// Step 4: Check media library config
echo "<div class='info'><strong>Step 4:</strong> Check media library configuration</div>";
require_once $laravelPath . '/vendor/autoload.php';
$app = require_once $laravelPath . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$diskName = config('media-library.disk_name');
$disk = \Illuminate\Support\Facades\Storage::disk($diskName);
$root = $disk->getDriver()->getAdapter()->getPathPrefix();

echo "<div class='info'>Media library disk: <code>$diskName</code></div>";
echo "<div class='info'>Disk root: <code>$root</code></div>";

// Step 5: Check a specific package's media
echo "<div class='info'><strong>Step 5:</strong> Check TourPackage media</div>";
try {
    $package = \App\Models\TourPackage::first();
    if ($package) {
        echo "<div class='info'>Checking package ID: {$package->id} ({$package->title})</div>";
        
        if ($package->hasMedia('hero')) {
            $media = $package->getFirstMedia('hero');
            $url = $media->getUrl();
            $path = $media->getPath();
            
            echo "<div class='success'>✓ Package has hero media</div>";
            echo "<div class='info'>Media URL: <code>$url</code></div>";
            echo "<div class='info'>Media Path: <code>$path</code></div>";
            echo "<div class='info'>File exists: " . (file_exists($path) ? '✓ YES' : '❌ NO') . "</div>";
            
            // Check if accessible via web
            $relativeUrl = str_replace(config('app.url'), '', $url);
            $webPath = $publicHtml . $relativeUrl;
            echo "<div class='info'>Web path: <code>$webPath</code></div>";
            echo "<div class='info'>Web accessible: " . (file_exists($webPath) || is_link($webPath) ? '✓ YES' : '❌ NO') . "</div>";
        } else {
            echo "<div class='warning'>⚠ Package has no hero media</div>";
        }
    } else {
        echo "<div class='warning'>⚠ No tour packages found</div>";
    }
} catch (\Exception $e) {
    echo "<div class='error'>❌ Error checking package: " . $e->getMessage() . "</div>";
}

echo "<div class='success'><strong>✅ Check complete!</strong></div>";
?>

</body>
</html>

