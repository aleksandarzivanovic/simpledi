<?php

class TestClass {

    public function __construct(AnotherTestClass $anotherTestClass) {
	var_dump('TestClass: ', $anotherTestClass);
    }

}

class AnotherTestClass {

    public function __construct() {
	var_dump('AnotherTestClass: Loaded');
    }

}

class MainClass {

    public $test;

    public function tests() {
	echo 'TEST JE: ' . $this->test;
    }

    public function __construct($test, TestClass $testClass, AnotherTestClass $anotherTestClass) {
	var_dump('MainClass: ', $test);
	var_dump('MainClass: ', $testClass);
	var_dump('MainClass: ', $anotherTestClass);
    }

}

include 'System/Di/DiInterface.php';
include 'System/Di/Di.php';

$di = System\Di\Di::getInstance();
$di2 = System\Di\Di::getInstance();

$m = $di->get('MainClass');

