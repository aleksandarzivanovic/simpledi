<?php

namespace System\Application;

use System\Di\Di;
use System\Http\Request\Method\MethodInterface;
use System\Router\RouterInterface;

class App {

	/** @var bool */
	private static $initialized;

	public static function init() {
		if (false == static::$initialized) {
			Di::getInstance()->get('system.router')->loadController()->run();
			static::$initialized = true;
		}
	}

	/**
	 * 
	 * @param string $route
	 * @param callable $callback
	 */
	public static function get($route, callable $callback) {
		Di::getInstance()->get('system.router')->add($route, MethodInterface::METHOD_GET, $callback);
	}

	/**
	 * 
	 * @param string $route
	 * @param callable $callback
	 */
	public static function post($route, callable $callback) {
		Di::getInstance()->get('system.router')->add($route, MethodInterface::METHOD_POST, $callback);
	}

	/**
	 * 
	 * @param string $route
	 * @param callable $callback
	 */
	public static function put($route, callable $callback) {
		Di::getInstance()->get('system.router')->add($route, MethodInterface::METHOD_PUT, $callback);
	}

	/**
	 * 
	 * @param string $route
	 * @param callable $callback
	 */
	public static function delete($route, callable $callback) {
		/* @var $router RouterInterface */
		$router = Di::getInstance()->get('system.router');

		$router->add($route, MethodInterface::METHOD_DELETE, $callback);
	}

}
