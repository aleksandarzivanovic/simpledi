<?php

session_start();

use System\Application\App;

error_reporting(E_ERROR | E_WARNING);
ini_set('display_errors', true);
spl_autoload_register(function ($class) {
    $file = str_replace('\\', '/', $class).'.php';

    if (is_file($file)) {
        require_once $file;
    } else {
        throw new \RuntimeException("File not found {$file}");
    }
});

App::init();
