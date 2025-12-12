<?php
/**
 * Build Upload Helper Script
 * 
 * This script helps upload the build folder to the server.
 * 
 * INSTRUCTIONS:
 * 1. Upload this file to: /home/gotzsafari/public_html/upload-build.php
 * 2. Access it via: https://www.gotzsafari.com/upload-build.php
 * 3. Upload your build.zip file using the form below
 * 4. The script will extract it to the correct location
 * 5. DELETE THIS FILE after use for security!
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);
set_time_limit(300);

$laravelPath = '/home/gotzsafari/laravel';
$publicPath = $laravelPath . '/public';
$buildPath = $publicPath . '/build';
$uploadDir = $publicPath;
$maxFileSize = 10 * 1024 * 1024; // 10MB

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['build_zip'])) {
    $file = $_FILES['build_zip'];
    
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $error = "Upload error: " . $file['error'];
    } elseif ($file['size'] > $maxFileSize) {
        $error = "File too large. Maximum size: " . ($maxFileSize / 1024 / 1024) . " MB";
    } elseif ($file['type'] !== 'application/zip' && $file['type'] !== 'application/x-zip-compressed') {
        $error = "Invalid file type. Please upload a ZIP file.";
    } else {
        $uploadedFile = $uploadDir . '/build.zip';
        
        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $uploadedFile)) {
            // Delete old build folder if exists
            if (is_dir($buildPath)) {
                exec("rm -rf " . escapeshellarg($buildPath));
            }
            
            // Extract ZIP file
            $zip = new ZipArchive();
            if ($zip->open($uploadedFile) === TRUE) {
                $zip->extractTo($publicPath);
                $zip->close();
                
                // Delete ZIP file
                unlink($uploadedFile);
                
                // Set permissions
                exec("chmod -R 755 " . escapeshellarg($buildPath));
                
                $message = "✅ Build folder uploaded and extracted successfully!";
                $message .= "<br>Location: " . htmlspecialchars($buildPath);
                $message .= "<br><br>⚠️ <strong>IMPORTANT:</strong> Delete this file (upload-build.php) for security!";
            } else {
                $error = "Failed to extract ZIP file.";
                if (file_exists($uploadedFile)) {
                    unlink($uploadedFile);
                }
            }
        } else {
            $error = "Failed to move uploaded file.";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Upload Build Folder</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            margin-top: 0;
        }
        .form-group {
            margin: 20px 0;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #555;
        }
        input[type="file"] {
            width: 100%;
            padding: 10px;
            border: 2px dashed #ddd;
            border-radius: 4px;
            background: #fafafa;
        }
        button {
            background: #4CAF50;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            width: 100%;
        }
        button:hover {
            background: #45a049;
        }
        .message {
            padding: 15px;
            border-radius: 4px;
            margin: 20px 0;
        }
        .success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .info {
            background: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
            padding: 15px;
            border-radius: 4px;
            margin: 20px 0;
        }
        code {
            background: #f4f4f4;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: monospace;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>📦 Upload Build Folder</h1>
        
        <?php if ($message): ?>
            <div class="message success">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="message error">
                <strong>Error:</strong> <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <div class="info">
            <strong>📋 Instructions:</strong><br>
            1. Select your <code>build.zip</code> file (from <code>/Users/mdegy/goweb/backend/public/build.zip</code>)<br>
            2. Click "Upload and Extract"<br>
            3. Wait for the upload to complete<br>
            4. <strong>Delete this file</strong> after successful upload for security!
        </div>
        
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="build_zip">Select build.zip file:</label>
                <input type="file" name="build_zip" id="build_zip" accept=".zip" required>
            </div>
            
            <button type="submit">Upload and Extract</button>
        </form>
        
        <div class="info" style="margin-top: 30px;">
            <strong>📍 Current Status:</strong><br>
            Build path: <code><?php echo htmlspecialchars($buildPath); ?></code><br>
            Build exists: <?php echo is_dir($buildPath) ? '✅ Yes' : '❌ No'; ?><br>
            Files in build: <?php echo is_dir($buildPath) ? count(glob($buildPath . '/*')) : '0'; ?>
        </div>
    </div>
</body>
</html>

