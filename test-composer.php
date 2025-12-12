<?php
/**
 * Test Composer Installation
 * Run this script via browser: https://www.gotzsafari.com/test-composer.php
 */

$laravelPath = '/home/gotzsafari/laravel';
$phpPath = '/usr/local/bin/php';
$composerPath = "$phpPath $laravelPath/composer.phar";

?>
<!DOCTYPE html>
<html>
<head>
    <title>Test Composer Installation</title>
    <style>
        body { font-family: monospace; padding: 20px; background: #1a1a1a; color: #fff; }
        .container { max-width: 900px; margin: 0 auto; }
        h1 { color: #4CAF50; }
        .step { background: #2a2a2a; padding: 15px; margin: 10px 0; border-left: 4px solid #4CAF50; }
        pre { background: #1a1a1a; padding: 10px; overflow-x: auto; color: #0f0; }
        .error { color: #f00; }
    </style>
</head>
<body>
<div class="container">
    <h1>🧪 Test Composer Installation</h1>
    <hr>

<?php

chdir($laravelPath);

echo "<div class='step'>";
echo "<h3>1. Checking vendor directory before</h3>";
if (is_dir("$laravelPath/vendor")) {
    echo "<p style='color: green;'>✓ vendor directory exists</p>";
} else {
    echo "<p style='color: red;'>✗ vendor directory does NOT exist</p>";
}
echo "</div>";

echo "<div class='step'>";
echo "<h3>2. Testing composer.phar directly</h3>";
$testCmd = "$composerPath --version 2>&1";
echo "<p>Testing: <code>$testCmd</code></p>";
exec($testCmd, $testOutput, $testReturn);
echo "<p>Exit code: $testReturn</p>";
if (!empty($testOutput)) {
    echo "<pre>" . htmlspecialchars(implode("\n", $testOutput)) . "</pre>";
} else {
    echo "<p class='error'>No output from composer --version</p>";
}
echo "</div>";

echo "<div class='step'>";
echo "<h3>3. Running composer install with passthru</h3>";
$cmd = "cd $laravelPath && $composerPath install --no-dev --optimize-autoloader 2>&1";
echo "<p>Command: <code>$cmd</code></p>";
echo "<p>Running with passthru...</p>";
echo "<pre style='max-height: 400px; overflow-y: auto;'>";
ob_start();
passthru($cmd, $returnVar);
$passthruOutput = ob_get_clean();
echo htmlspecialchars($passthruOutput);
echo "</pre>";
echo "<p>Exit code: <strong>$returnVar</strong></p>";
echo "</div>";

echo "<div class='step'>";
echo "<h3>4. Alternative: Using proc_open for better output capture</h3>";
$descriptorspec = array(
    0 => array("pipe", "r"),
    1 => array("pipe", "w"),
    2 => array("pipe", "w")
);
$process = proc_open($cmd, $descriptorspec, $pipes, $laravelPath);
if (is_resource($process)) {
    fclose($pipes[0]);
    $stdout = stream_get_contents($pipes[1]);
    $stderr = stream_get_contents($pipes[2]);
    fclose($pipes[1]);
    fclose($pipes[2]);
    $procReturn = proc_close($process);
    
    echo "<p>Exit code: <strong>$procReturn</strong></p>";
    if (!empty($stdout)) {
        echo "<h4>STDOUT:</h4><pre style='max-height: 300px; overflow-y: auto;'>" . htmlspecialchars($stdout) . "</pre>";
    }
    if (!empty($stderr)) {
        echo "<h4>STDERR:</h4><pre style='max-height: 300px; overflow-y: auto; color: #f00;'>" . htmlspecialchars($stderr) . "</pre>";
    }
    if (empty($stdout) && empty($stderr)) {
        echo "<p class='error'>No output from proc_open either!</p>";
    }
} else {
    echo "<p class='error'>Failed to open process</p>";
}
echo "</div>";

echo "<div class='step'>";
echo "<h3>5. Checking vendor directory after</h3>";
if (is_dir("$laravelPath/vendor")) {
    echo "<p style='color: green;'>✓ vendor directory exists</p>";
    $autoload = "$laravelPath/vendor/autoload.php";
    if (file_exists($autoload)) {
        echo "<p style='color: green;'>✓ vendor/autoload.php exists</p>";
    } else {
        echo "<p class='error'>✗ vendor/autoload.php does NOT exist</p>";
    }
} else {
    echo "<p class='error'>✗ vendor directory still does NOT exist</p>";
    echo "<p class='error'>Composer install did not create the vendor directory!</p>";
}
echo "</div>";

echo "<div class='step'>";
echo "<h3>6. Checking composer.phar file</h3>";
$composerPhar = "$laravelPath/composer.phar";
if (file_exists($composerPhar)) {
    echo "<p style='color: green;'>✓ composer.phar exists</p>";
    echo "<p>File size: " . filesize($composerPhar) . " bytes</p>";
    echo "<p>File permissions: " . substr(sprintf('%o', fileperms($composerPhar)), -4) . "</p>";
    echo "<p>Is readable: " . (is_readable($composerPhar) ? "Yes" : "No") . "</p>";
    echo "<p>Is executable: " . (is_executable($composerPhar) ? "Yes" : "No") . "</p>";
    
    // Check if it's a valid ZIP file (PHAR files are ZIP archives)
    $zip = new ZipArchive();
    if ($zip->open($composerPhar) === TRUE) {
        echo "<p style='color: green;'>✓ composer.phar is a valid ZIP/PHAR archive</p>";
        echo "<p>Files in archive: " . $zip->numFiles . "</p>";
        $zip->close();
    } else {
        echo "<p class='error'>✗ composer.phar is NOT a valid ZIP/PHAR archive</p>";
        echo "<p class='error'>The file may be corrupted or incomplete</p>";
    }
    
    // Check first few bytes
    $handle = fopen($composerPhar, 'r');
    $firstBytes = fread($handle, 100);
    fclose($handle);
    echo "<p>First 100 bytes (hex): " . bin2hex($firstBytes) . "</p>";
    if (strpos($firstBytes, 'PK') === 0) {
        echo "<p style='color: green;'>✓ File starts with ZIP signature (PK)</p>";
    } else {
        echo "<p class='error'>✗ File does NOT start with ZIP signature</p>";
    }
} else {
    echo "<p class='error'>✗ composer.phar does not exist</p>";
}
echo "</div>";

echo "<div class='step'>";
echo "<h3>7. Testing direct PHP execution</h3>";
$testPhp = "$phpPath -r 'echo \"PHP is working\\n\";' 2>&1";
echo "<p>Testing PHP: <code>$testPhp</code></p>";
exec($testPhp, $phpTest, $phpReturn);
if (!empty($phpTest)) {
    echo "<pre>" . htmlspecialchars(implode("\n", $phpTest)) . "</pre>";
} else {
    echo "<p class='error'>No output from PHP test</p>";
}
echo "</div>";

echo "<div class='step'>";
echo "<h3>8. Checking composer.json</h3>";
$composerJson = "$laravelPath/composer.json";
if (file_exists($composerJson)) {
    echo "<p style='color: green;'>✓ composer.json exists</p>";
    $jsonContent = file_get_contents($composerJson);
    $json = json_decode($jsonContent, true);
    if ($json) {
        echo "<p>JSON is valid</p>";
        if (isset($json['require'])) {
            echo "<p>Dependencies found: " . count($json['require']) . " packages</p>";
        }
    } else {
        echo "<p class='error'>✗ composer.json is not valid JSON</p>";
    }
} else {
    echo "<p class='error'>✗ composer.json does not exist</p>";
}
echo "</div>";

?>

</div>
</body>
</html>

