<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Load mime_content_type polyfill if fileinfo is not available
require __DIR__.'/../bootstrap/mime-polyfill.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

// Disable image optimizers if fileinfo is not available (after app is created)
if (!extension_loaded('fileinfo')) {
    $app->make('config')->set('media-library.image_optimizers', []);
}

$app->handleRequest(Request::capture());
