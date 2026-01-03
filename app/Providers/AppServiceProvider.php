<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use League\Flysystem\Local\LocalFilesystemAdapter;
use League\Flysystem\UnixVisibility\PortableVisibilityConverter;
use League\MimeTypeDetection\ExtensionMimeTypeDetector;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Polyfill mime_content_type() if fileinfo extension is not available
        // This allows Spatie Image Optimizer to work without fileinfo
        if (!function_exists('mime_content_type') && !extension_loaded('fileinfo')) {
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
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);
        
        // Force HTTPS URLs in production
        if (config('app.env') === 'production' || request()->secure() || request()->header('X-Forwarded-Proto') === 'https') {
            URL::forceScheme('https');
        }

        // Configure Flysystem to use ExtensionMimeTypeDetector instead of FinfoMimeTypeDetector
        // This avoids requiring the fileinfo PHP extension
        if (!extension_loaded('fileinfo')) {
            // Override the 'local' disk configuration
            Storage::extend('local', function ($app, $config) {
                $adapter = new LocalFilesystemAdapter(
                    $config['root'],
                    PortableVisibilityConverter::fromArray($config['permissions'] ?? []),
                    $config['lock'] ?? LOCK_EX,
                    $config['linkHandling'] ?? LocalFilesystemAdapter::DISALLOW_LINKS,
                    new ExtensionMimeTypeDetector() // Use extension-based detection instead of fileinfo
                );

                return new \Illuminate\Filesystem\FilesystemAdapter(
                    new \League\Flysystem\Filesystem($adapter, $config),
                    $adapter,
                    $config
                );
            });

            // Override the 'public' disk configuration
            Storage::extend('public', function ($app, $config) {
                $adapter = new LocalFilesystemAdapter(
                    $config['root'],
                    PortableVisibilityConverter::fromArray($config['permissions'] ?? []),
                    $config['lock'] ?? LOCK_EX,
                    $config['linkHandling'] ?? LocalFilesystemAdapter::DISALLOW_LINKS,
                    new ExtensionMimeTypeDetector() // Use extension-based detection instead of fileinfo
                );

                return new \Illuminate\Filesystem\FilesystemAdapter(
                    new \League\Flysystem\Filesystem($adapter, $config),
                    $adapter,
                    $config
                );
            });
        }
    }
}
