<?php
spl_autoload_register(function($class) {
    $file = str_replace('\\', '/', $class) . '.php';
    require_once $file;
});

$di = System\Di\Di::getInstance();

var_dump($di->get('system.http.request'));
