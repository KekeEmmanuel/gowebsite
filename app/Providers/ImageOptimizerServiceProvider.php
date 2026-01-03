<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ImageOptimizerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     * 
     * This provider ensures mime_content_type() exists in the Spatie\ImageOptimizer namespace
     * when the fileinfo extension is not available.
     */
    public function register(): void
    {
        // Ensure the namespace function exists before any Image class is instantiated
        if (!extension_loaded('fileinfo')) {
            $this->defineNamespaceFunction();
        }
    }

    /**
     * Define mime_content_type() in Spatie\ImageOptimizer namespace
     */
    protected function defineNamespaceFunction(): void
    {
        // Check if function already exists by trying to call it
        $functionExists = false;
        try {
            if (function_exists('Spatie\ImageOptimizer\mime_content_type')) {
                $functionExists = true;
            }
        } catch (\Throwable $e) {
            // Function doesn't exist, continue
        }
        
        if (!$functionExists) {
            // Define the function in the namespace using eval
            // This must be done before any Image class is instantiated
            eval('
                namespace Spatie\ImageOptimizer {
                    if (!function_exists("mime_content_type")) {
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
                }
            ');
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

