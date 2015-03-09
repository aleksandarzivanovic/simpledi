<?php

use System\Application\App;

error_reporting(E_ALL | E_ERROR | E_NOTICE | E_WARNING);
ini_set('display_errors', TRUE);
spl_autoload_register(function($class) {
	$file = str_replace('\\', '/', $class) . '.php';
	require_once $file;
});

function loadFiles($path)
{
    $path = rtrim($path, '/');
    $contents = scandir($path);

    $di = \System\Di\Di::getInstance();
    $router = $di->get('system.router');

    foreach ($contents as $content) {
        if ($content == '.' || $content == '..') {
            continue;
        }

        $router->clearRoutes();
        $fullPath = $path . '/' . $content;
        $fileName = explode('.', $content);
        array_shift($fileName);

        if ('php' === $fileName[0]) {
            include $fullPath;
        } else if (is_dir($fullPath)) {
            loadFiles($fullPath);
            return;
        }

        foreach ($router->getRoutes()['GET']as $r) {
            var_dump('SADASDASDAD', $r);
        }
    }
}

$di = \System\Di\Di::getInstance();

if (isset ($_GET['build_router'])) {
    loadFiles('Controllers/ResponseControllers/');
    /** @var \System\Router\RouterInterface $router */
    $router = $di->get('system.router');
    var_dump($router->getRoutes());
}

App::init();