<?php
/**
 * Upload Limits Diagnostic Script
 * 
 * Upload this file to your server's public_html directory and access it via browser
 * to check current PHP upload limits.
 * 
 * DELETE THIS FILE after checking for security!
 */

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Upload Limits Check</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; }
        .success { color: green; font-weight: bold; }
        .warning { color: orange; font-weight: bold; }
        .error { color: red; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #f2f2f2; }
        .recommended { background-color: #e8f5e9; }
    </style>
</head>
<body>
    <h1>PHP Upload Limits Diagnostic</h1>
    
    <h2>Current PHP Configuration</h2>
    <table>
        <tr>
            <th>Setting</th>
            <th>Current Value</th>
            <th>Recommended</th>
            <th>Status</th>
        </tr>
        <?php
        $settings = [
            'upload_max_filesize' => ['current' => ini_get('upload_max_filesize'), 'recommended' => '50M'],
            'post_max_size' => ['current' => ini_get('post_max_size'), 'recommended' => '60M'],
            'max_execution_time' => ['current' => ini_get('max_execution_time'), 'recommended' => '300'],
            'max_input_time' => ['current' => ini_get('max_input_time'), 'recommended' => '300'],
            'memory_limit' => ['current' => ini_get('memory_limit'), 'recommended' => '256M'],
        ];
        
        function convertToBytes($value) {
            $value = trim($value);
            $last = strtolower($value[strlen($value)-1]);
            $value = (int)$value;
            switch($last) {
                case 'g': $value *= 1024;
                case 'm': $value *= 1024;
                case 'k': $value *= 1024;
            }
            return $value;
        }
        
        foreach ($settings as $key => $data) {
            $currentBytes = convertToBytes($data['current']);
            $recommendedBytes = convertToBytes($data['recommended']);
            
            $status = $currentBytes >= $recommendedBytes ? 'success' : 'warning';
            $statusText = $currentBytes >= $recommendedBytes ? '✓ OK' : '⚠ Needs Increase';
            
            echo "<tr class='$status'>";
            echo "<td><strong>$key</strong></td>";
            echo "<td>{$data['current']}</td>";
            echo "<td>{$data['recommended']}</td>";
            echo "<td class='$status'>$statusText</td>";
            echo "</tr>";
        }
        ?>
    </table>
    
    <h2>PHP Info</h2>
    <p><strong>PHP Version:</strong> <?php echo phpversion(); ?></p>
    <p><strong>Loaded php.ini:</strong> <?php echo php_ini_loaded_file(); ?></p>
    <p><strong>Additional .ini files:</strong> <?php echo php_ini_scanned_files() ?: 'None'; ?></p>
    
    <h2>Configuration Files Check</h2>
    <ul>
        <li>
            <strong>.user.ini in public_html:</strong> 
            <?php echo file_exists(__DIR__ . '/.user.ini') ? '<span class="success">✓ Found</span>' : '<span class="error">✗ Not Found</span>'; ?>
        </li>
        <li>
            <strong>.htaccess in public_html:</strong> 
            <?php echo file_exists(__DIR__ . '/.htaccess') ? '<span class="success">✓ Found</span>' : '<span class="error">✗ Not Found</span>'; ?>
        </li>
    </ul>
    
    <h2>Recommendations</h2>
    <ol>
        <li><strong>Create .user.ini file</strong> in your public_html directory with the recommended values above</li>
        <li><strong>Update .htaccess</strong> to include PHP value directives (if .user.ini doesn't work)</li>
        <li><strong>Check cPanel PHP Settings</strong> - Go to "Select PHP Version" → "Options" and set the values there</li>
        <li><strong>Contact your hosting provider</strong> if limits cannot be increased via .user.ini or .htaccess</li>
    </ol>
    
    <h2>Test Upload</h2>
    <form method="POST" enctype="multipart/form-data">
        <p>
            <label>Select a test file (max 50MB):</label><br>
            <input type="file" name="test_file" accept="image/*">
        </p>
        <p>
            <button type="submit">Test Upload</button>
        </p>
    </form>
    
    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['test_file'])) {
        $file = $_FILES['test_file'];
        echo "<h3>Upload Test Result</h3>";
        
        if ($file['error'] === UPLOAD_ERR_OK) {
            echo "<p class='success'>✓ Upload successful! File size: " . number_format($file['size'] / 1024 / 1024, 2) . " MB</p>";
            // Clean up test file
            if (file_exists($file['tmp_name'])) {
                unlink($file['tmp_name']);
            }
        } else {
            $errors = [
                UPLOAD_ERR_INI_SIZE => 'File exceeds upload_max_filesize',
                UPLOAD_ERR_FORM_SIZE => 'File exceeds MAX_FILE_SIZE',
                UPLOAD_ERR_PARTIAL => 'File was only partially uploaded',
                UPLOAD_ERR_NO_FILE => 'No file was uploaded',
                UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder',
                UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
                UPLOAD_ERR_EXTENSION => 'A PHP extension stopped the file upload',
            ];
            $errorMsg = $errors[$file['error']] ?? 'Unknown error';
            echo "<p class='error'>✗ Upload failed: $errorMsg (Error code: {$file['error']})</p>";
        }
    }
    ?>
    
    <hr>
    <p><strong>⚠ Security Note:</strong> Delete this file after checking your limits!</p>
</body>
</html>

