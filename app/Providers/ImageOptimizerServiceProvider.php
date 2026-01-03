<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ImageOptimizerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Patch Spatie\ImageOptimizer\Image class to use polyfill
        if (!extension_loaded('fileinfo')) {
            // Define mime_content_type in Spatie\ImageOptimizer namespace if not exists
            if (!function_exists('Spatie\ImageOptimizer\mime_content_type')) {
                eval('
                    namespace Spatie\ImageOptimizer {
                        function mime_content_type($filename) {
                            if (!file_exists($filename)) {
                                return false;
                            }
                            
                            $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                            $mimeTypes = [
                                "jpg" => "image/jpeg",
                                "jpeg" => "image/jpeg",
                                "png" => "image/png",
                                "gif" => "image/gif",
                                "webp" => "image/webp",
                                "svg" => "image/svg+xml",
                                "bmp" => "image/bmp",
                                "ico" => "image/x-icon",
                                "tiff" => "image/tiff",
                                "tif" => "image/tiff",
                                "avif" => "image/avif",
                                "pdf" => "application/pdf",
                            ];
                            
                            return $mimeTypes[$extension] ?? "application/octet-stream";
                        }
                    }
                ');
            }
        }
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}

