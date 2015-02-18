<?php
//error_reporting(E_ALL);
//ini_set('display-errors', true);
//ini_set('display_errors', true);

include 'System/Di/DiInterface.php';
include 'System/Di/Di.php';

include 'System/Http/Request/RequestInterface.php';
include 'System/Http/Request/Request.php';



$di = System\Di\Di::getInstance();

var_dump($di->get('system.http.request'));
