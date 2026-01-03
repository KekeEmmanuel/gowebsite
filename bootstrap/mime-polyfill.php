<?php
/**
 * Polyfill for mime_content_type() when fileinfo extension is not available
 * 
 * This file should be loaded early in the application bootstrap
 */

if (!function_exists('mime_content_type') && !extension_loaded('fileinfo')) {
    // Define in global namespace
    function mime_content_type($filename) {
        if (!file_exists($filename)) {
            return false;
        }
        
        // Use file extension to determine MIME type
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        $mimeTypes = [
            // Images
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
            'svg' => 'image/svg+xml',
            'bmp' => 'image/bmp',
            'ico' => 'image/x-icon',
            'tiff' => 'image/tiff',
            'tif' => 'image/tiff',
            'avif' => 'image/avif',
            
            // Documents
            'pdf' => 'application/pdf',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'xls' => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'ppt' => 'application/vnd.ms-powerpoint',
            'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            
            // Text
            'txt' => 'text/plain',
            'html' => 'text/html',
            'htm' => 'text/html',
            'css' => 'text/css',
            'js' => 'application/javascript',
            'json' => 'application/json',
            'xml' => 'application/xml',
            
            // Archives
            'zip' => 'application/zip',
            'rar' => 'application/x-rar-compressed',
            'tar' => 'application/x-tar',
            'gz' => 'application/gzip',
            
            // Video
            'mp4' => 'video/mp4',
            'avi' => 'video/x-msvideo',
            'mov' => 'video/quicktime',
            'wmv' => 'video/x-ms-wmv',
            'flv' => 'video/x-flv',
            'webm' => 'video/webm',
            
            // Audio
            'mp3' => 'audio/mpeg',
            'wav' => 'audio/wav',
            'ogg' => 'audio/ogg',
            'm4a' => 'audio/mp4',
        ];
        
        return $mimeTypes[$extension] ?? 'application/octet-stream';
    }
}

// Define in Spatie\ImageOptimizer namespace using eval to avoid namespace declaration issues
// Always define if fileinfo is not loaded - define it unconditionally (no function_exists check)
if (!extension_loaded('fileinfo')) {
    // Use eval to define function in the namespace
    // This must be done before any Image class tries to use it
    // CRITICAL: This must be defined BEFORE any Conversion class is instantiated
    // Don't use function_exists check inside eval - just define it
    eval('
        namespace Spatie\ImageOptimizer {
            function mime_content_type($filename) {
                // Call the global function (which we defined above)
                return \\mime_content_type($filename);
            }
        }
    ');
}
