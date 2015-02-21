<?php

error_reporting(E_ALL | E_ERROR | E_NOTICE | E_WARNING);
ini_set('display_errors', TRUE);
spl_autoload_register(function($class) {
	$file = str_replace('\\', '/', $class) . '.php';
	require_once $file;
});

$di = System\Di\Di::getInstance();

/* @var $request System\Http\Request\Request */
$request = $di->get('system.http.request');
$method = $request->getMethod();

// closures of isGet/Post/Put/Delete will always have Reqest as first and Response as second argument
// all other argumets are optional and they depends on second parameter of isGet/Post/Put/Delete method
$method->isGet(function($request, $name, $email) {

	/* @var $method \System\Http\Request\Method\MethodInterface */
	// using false parameter to avoid executing of $method->isPost/Put/Delete closure (non-singleton)
	$method = System\Di\Di::getInstance()->get('system.http.request.method', false);
	$method->setMethod(\System\Http\Request\Method\MethodInterface::METHOD_GET);
	
	$page = $request->getRequestData('page', $method);
	
	$method->setMethod(\System\Http\Request\Method\MethodInterface::METHOD_POST);
	$postData = $request->getRequestDataArray($method);

	$array = array(
		'page' => $page,
		'name' => $name,
		'email' => $email,
		'this-should-be-null' => $postData,
	);

	var_dump($array);
}, array(
	'name',
	'ema@il.com',
));

$method->isPost(function($request) {
	var_dump('POST');
});