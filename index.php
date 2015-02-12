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

include 'D:\\php\\di\\System\\Di.php';

$di = System\Di::instance();
$di2 = System\Di::instance();

$m = $di->get('MainClass');

