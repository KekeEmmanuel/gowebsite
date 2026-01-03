<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Spatie\ImageOptimizer\OptimizerChainFactory;
use Spatie\ImageOptimizer\OptimizerChain;

class MediaLibraryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     * 
     * This provider patches OptimizerChainFactory to return an empty chain
     * when fileinfo is not available, preventing mime_content_type() errors.
     */
    public function register(): void
    {
        if (!extension_loaded('fileinfo')) {
            // Override OptimizerChainFactory::create() to return empty chain
            // This prevents the Conversion class from trying to use optimizers
            $this->app->bind(
                \Spatie\ImageOptimizer\OptimizerChainFactory::class,
                function ($app) {
                    return new class extends OptimizerChainFactory {
                        public static function create(array $config = []): OptimizerChain
                        {
                            // If fileinfo is not available, return empty chain
                            if (!extension_loaded('fileinfo')) {
                                return new OptimizerChain();
                            }
                            // Otherwise use parent implementation
                            return parent::create($config);
                        }
                    };
                }
            );
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

