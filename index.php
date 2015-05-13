<?php

session_start();

error_reporting(E_ALL);

use System\Application\App;

ini_set('display_errors', true);
spl_autoload_register(function ($class) {
    $file = str_replace('\\', '/', $class).'.php';

    if (is_file($file)) {
        require_once $file;
    } else {
        throw new \RuntimeException("File not found {$file}");
    }
});

//App::init();
/** @var \System\Storage\StorageInterface $storage */
$storage = \System\Di\Di::getInstance()->getShared('system.storage');
$driver = new \System\Storage\Drivers\StorageMySqlDriver();
$driver->setTableName('test_table');
$storage->setDriver($driver);
$s = $storage->get(['id' => 2, 'name' => 'coa'], [], 0, [['id', 'identifikacioni_broj'], ['name', 'ime'], 'dummy']);
var_dump($s->getField('dummy'));
