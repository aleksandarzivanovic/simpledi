<?php

class TestClass {

    public function __construct(AnotherTestClass $anotherTestClass) {
	echo 'TestClass Constructor called.<br />';
    }

}

class AnotherTestClass {

    public function __construct() {
	echo '<br />AnotherTestClass Constructor called.<br />';
    }

}

class MainClass {

    public $test;

    public function __construct($test, TestClass $testClass, AnotherTestClass $anotherTestClass) {
	
    }

}

include 'System/Di/DiInterface.php';
include 'System/Di/Di.php';

$di = System\Di\Di::getInstance();
$di2 = System\Di\Di::getInstance();

$m = $di->get('class.main');
$m1 = $di->get('class.main');

$m1->test = 'Test';
$m->test = 'Singleton Test';

echo '<p>Singleton: <b>' . $m->test . '</b><br />';
echo 'No Singleton: <b>' . $m1->test . '</b><br /></p>';
