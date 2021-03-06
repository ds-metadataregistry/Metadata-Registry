<?php

define('SF_ROOT_DIR', realpath(dirname(__FILE__) . '/..'));
define('SF_APP', 'frontend');

//initialize composer through laravel bootstrap
require_once SF_ROOT_DIR . DIRECTORY_SEPARATOR . 'bootstrap/autoload.php';

//fire up Laravel
$app = require_once SF_ROOT_DIR . DIRECTORY_SEPARATOR . 'bootstrap/app.php';

define('SF_ENVIRONMENT', env('APP_ENV', 'prod'));
define('SF_DEBUG', env('APP_DEBUG', 'false'));

//we may use this in future to prescreen URLs to bypass laravel processing
$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

/** @var \Illuminate\Contracts\Http\Kernel $kernel */
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

//let laravel handle the response
$response = $kernel->handle($request = Illuminate\Http\Request::capture());
$response->send();

$kernel->terminate($request, $response);
