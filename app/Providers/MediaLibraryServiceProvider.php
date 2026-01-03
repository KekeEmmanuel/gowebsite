<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Spatie\MediaLibrary\Conversions\Conversion;

class MediaLibraryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     * 
     * This provider ensures the namespace function exists before any Conversion
     * class is instantiated, preventing mime_content_type() errors.
     */
    public function register(): void
    {
        // Ensure namespace function exists BEFORE any Conversion class is loaded
        if (!extension_loaded('fileinfo')) {
            // Define the namespace function immediately
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
            
            // Disable optimizers in config
            config(['media-library.image_optimizers' => []]);
        }
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Ensure namespace function exists in boot() as well (for queued jobs)
        if (!extension_loaded('fileinfo') && !function_exists('Spatie\ImageOptimizer\mime_content_type')) {
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
    }
}

