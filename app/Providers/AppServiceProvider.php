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
        // CRITICAL: Define namespace function FIRST, before any vendor code runs
        if (!extension_loaded('fileinfo')) {
            // Define the namespace function immediately using eval
            // This must happen before ANY Spatie\ImageOptimizer code is loaded
            if (!function_exists('Spatie\ImageOptimizer\mime_content_type')) {
                eval('
                    namespace Spatie\ImageOptimizer {
                        function mime_content_type($filename) {
                            if (!file_exists($filename)) {
                                return false;
                            }
                            $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                            $mimeTypes = [
                                "jpg" => "image/jpeg", "jpeg" => "image/jpeg", "png" => "image/png",
                                "gif" => "image/gif", "webp" => "image/webp", "svg" => "image/svg+xml",
                                "bmp" => "image/bmp", "ico" => "image/x-icon", "tiff" => "image/tiff",
                                "tif" => "image/tiff", "avif" => "image/avif", "pdf" => "application/pdf",
                            ];
                            return $mimeTypes[$extension] ?? "application/octet-stream";
                        }
                    }
                ');
            }
            
            // Also define global function if not exists
            if (!function_exists('mime_content_type')) {
                eval('
                    function mime_content_type($filename) {
                        return \\Spatie\\ImageOptimizer\\mime_content_type($filename);
                    }
                ');
            }
            
            // Disable image optimizers completely
            config(['media-library.image_optimizers' => []]);
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
