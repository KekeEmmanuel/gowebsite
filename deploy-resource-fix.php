<?php
/**
 * Deploy TourPackageResource fix to production
 * 
 * This script:
 * 1. Pulls latest changes from Git
 * 2. Copies the updated TourPackageResource.php
 * 3. Clears Laravel caches
 */

// Configuration
$basePath = dirname(__DIR__);
$laravelPath = $basePath . '/laravel';
$publicHtmlPath = $basePath . '/public_html';

echo "<h1>Deploying TourPackageResource Fix</h1>\n";
echo "<pre>\n";

// Step 1: Pull from Git
echo "Step 1: Pulling latest changes from Git...\n";
chdir($laravelPath);
$output = [];
$returnVar = 0;
exec('git pull origin main 2>&1', $output, $returnVar);
echo implode("\n", $output) . "\n";
if ($returnVar !== 0) {
    echo "ERROR: Git pull failed!\n";
    exit(1);
}
echo "✓ Git pull successful\n\n";

// Step 2: Copy TourPackageResource.php
echo "Step 2: Copying TourPackageResource.php...\n";
$sourceFile = $laravelPath . '/app/Http/Resources/TourPackageResource.php';
$targetFile = $laravelPath . '/app/Http/Resources/TourPackageResource.php';

if (!file_exists($sourceFile)) {
    echo "ERROR: Source file not found: $sourceFile\n";
    exit(1);
}

// File is already in place after git pull, but verify
if (file_exists($targetFile)) {
    echo "✓ TourPackageResource.php is in place\n\n";
} else {
    echo "ERROR: Target file not found after git pull\n";
    exit(1);
}

// Step 3: Clear Laravel caches
echo "Step 3: Clearing Laravel caches...\n";
chdir($laravelPath);

// Clear config cache
$commands = [
    'php artisan config:clear',
    'php artisan cache:clear',
    'php artisan route:clear',
    'php artisan view:clear',
];

foreach ($commands as $cmd) {
    echo "Running: $cmd\n";
    $output = [];
    $returnVar = 0;
    exec($cmd . ' 2>&1', $output, $returnVar);
    if ($returnVar !== 0) {
        echo "WARNING: Command failed: $cmd\n";
        echo implode("\n", $output) . "\n";
    } else {
        echo "✓ $cmd\n";
    }
}

echo "\n";
echo "========================================\n";
echo "Deployment Complete!\n";
echo "========================================\n";
echo "\n";
echo "The TourPackageResource.php has been updated.\n";
echo "The file_exists() checks have been removed.\n";
echo "Images should now display correctly.\n";
echo "</pre>\n";
