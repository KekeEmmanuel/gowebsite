<?php
/**
 * Pull Latest Changes from Git on Server
 * 
 * Upload this file to public_html and run it via browser:
 * https://www.gotzsafari.com/pull-git.php
 * 
 * IMPORTANT: Delete this file after use!
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

$homeDir = '/home/gotzsafari';
$repoPath = $homeDir . '/repositories/gowebsitelaravel';

?>
<!DOCTYPE html>
<html>
<head>
    <title>Pull Git Updates</title>
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
    <h1>🔄 Pull Git Updates</h1>
    <p>Pulling latest changes from Git repository...</p>
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
    echo "<p><strong>Note:</strong> Make sure the Git repository is set up correctly.</p>";
    exit;
}

logOutput("✓ Repository directory found: $repoPath", 'success');

// Check if .git directory exists
if (!is_dir("$repoPath/.git")) {
    logOutput("❌ Git repository not found in: $repoPath", 'error');
    exit;
}

logOutput("✓ Git repository found", 'success');

// Get current branch
$currentBranch = 'main';
$branchFile = "$repoPath/.git/HEAD";
if (file_exists($branchFile)) {
    $headContent = file_get_contents($branchFile);
    if (preg_match('/ref: refs\/heads\/(.+)/', $headContent, $matches)) {
        $currentBranch = trim($matches[1]);
    }
}

logOutput("ℹ Current branch: <strong>$currentBranch</strong>", 'info');

// Change to repository directory and pull
chdir($repoPath);

// Get current commit before pull
$beforeCommit = '';
exec("git rev-parse HEAD 2>&1", $output, $returnVar);
if ($returnVar === 0 && !empty($output)) {
    $beforeCommit = trim($output[0]);
    logOutput("ℹ Current commit (before pull): <code>" . substr($beforeCommit, 0, 7) . "</code>", 'info');
}

// Fetch first
logOutput("📥 Fetching latest changes from remote...", 'info');
exec("git fetch origin 2>&1", $output, $returnVar);
if ($returnVar === 0) {
    logOutput("✓ Fetch completed", 'success');
    if (!empty($output)) {
        echo "<pre>" . htmlspecialchars(implode("\n", $output)) . "</pre>";
    }
} else {
    logOutput("⚠ Fetch completed with warnings", 'info');
    if (!empty($output)) {
        echo "<pre style='color: #FFD700;'>" . htmlspecialchars(implode("\n", $output)) . "</pre>";
    }
}

// Pull latest changes
logOutput("📥 Pulling latest changes from origin/$currentBranch...", 'info');
exec("git pull origin $currentBranch 2>&1", $output, $returnVar);

if ($returnVar === 0) {
    logOutput("✓ Git pull completed successfully!", 'success');
    
    // Show output
    if (!empty($output)) {
        echo "<pre>" . htmlspecialchars(implode("\n", $output)) . "</pre>";
    }
    
    // Get new commit after pull
    $afterCommit = '';
    exec("git rev-parse HEAD 2>&1", $output2, $returnVar2);
    if ($returnVar2 === 0 && !empty($output2)) {
        $afterCommit = trim($output2[0]);
        if ($afterCommit !== $beforeCommit) {
            logOutput("✓ Repository updated! New commit: <code>" . substr($afterCommit, 0, 7) . "</code>", 'success');
        } else {
            logOutput("ℹ Repository already up to date (no new commits)", 'info');
        }
    }
    
    // Show recent commits
    logOutput("📋 Recent commits:", 'info');
    exec("git log --oneline -5 2>&1", $commits, $returnVar3);
    if ($returnVar3 === 0 && !empty($commits)) {
        echo "<pre style='max-height: 200px; overflow-y: auto;'>";
        foreach ($commits as $commit) {
            echo htmlspecialchars($commit) . "\n";
        }
        echo "</pre>";
    }
    
} else {
    logOutput("❌ Git pull failed", 'error');
    if (!empty($output)) {
        echo "<pre style='color: #f44336;'>" . htmlspecialchars(implode("\n", $output)) . "</pre>";
    }
    echo "<p><strong>Common issues:</strong></p>";
    echo "<ul>";
    echo "<li>Local changes conflict with remote changes - you may need to stash or commit local changes</li>";
    echo "<li>Authentication issues - check Git credentials</li>";
    echo "<li>Network issues - check internet connection</li>";
    echo "</ul>";
    exit;
}

// Check if TourPackageController.php was updated
$controllerPath = "$repoPath/app/Http/Controllers/Admin/TourPackageController.php";
if (file_exists($controllerPath)) {
    $fileTime = filemtime($controllerPath);
    $fileDate = date('Y-m-d H:i:s', $fileTime);
    logOutput("✓ TourPackageController.php found (last modified: $fileDate)", 'success');
    
    // Check if it has error handling
    $content = file_get_contents($controllerPath);
    if (strpos($content, 'try {') !== false && strpos($content, 'hasMedia') !== false) {
        logOutput("✓ Error handling code detected in controller", 'success');
    } else {
        logOutput("⚠ Error handling code not found - file may need to be updated", 'info');
    }
} else {
    logOutput("⚠ TourPackageController.php not found in repository", 'info');
}

?>

    <hr>
    <div class="info">
        <h3>✅ Pull Complete!</h3>
        <p><strong>Next Steps:</strong></p>
        <ol>
            <li>If you used this script, the repository is now updated</li>
            <li>You still need to copy the files from the repository to the Laravel directory</li>
            <li>Use <code>sync-to-server.php</code> to copy files and update Laravel</li>
            <li>Or manually copy: <code>app/Http/Controllers/Admin/TourPackageController.php</code> from repository to Laravel directory</li>
            <li><strong>Delete this script file for security!</strong></li>
        </ol>
    </div>

    <div class="error">
        <p><strong>⚠ Security Note:</strong> Please delete this file (pull-git.php) from your server after use!</p>
    </div>
</body>
</html>

