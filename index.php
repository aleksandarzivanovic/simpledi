<?php

session_start();

use System\Application\App;

error_reporting(E_ALL | E_ERROR | E_NOTICE | E_WARNING);
ini_set('display_errors', true);
spl_autoload_register(function ($class) {
    $file = str_replace('\\', '/', $class).'.php';

    if (is_file($file)) {
        require_once $file;
    } else {
        throw new \RuntimeException("File not found {$file}");
    }
});

/* @var $template \System\Template\Template */
$template = \System\Di\Di::getInstance()->get('system.template');
$template->load('./test.html');

$template->render([
    'test' => ['coa' => 'JO JO JO'],
    'sta' => ['sad_da_radim' => 'NE ZNAM!'],
]);

App::init();
