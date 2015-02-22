<?php

namespace System\Router;

use System\Di\Di;
use System\Http\Request\Method\MethodInterface;
use System\Http\Request\RequestInterface;

class Router implements RouterInterface {

	/** @var array */
	protected $routes = array();

	/** @var RequestInterface */
	private $request;

	/**
	 * @param RequestInterface $request
	 */
	public function __construct(RequestInterface $request) {
		$this->request = $request;
	}

	/**
	 * @param  string $route
	 * @param  string $method
	 * @param  callable $callback
	 * @throws RuntimeException
	 */
	public function add($route, $method, callable $callback) {
		list($regex, $parameters) = $this->parseRoute($route);

		// filter method parameter
		Di::getInstance()->get('system.http.request.method', false, array($method));

		if (isset($this->routes[$method][$regex])) {
			throw new \RuntimeException(sprintf('Route %s: %s already defined.', $method, $route));
		}

		$this->routes[$method][$regex] = array(
			'response' => $callback,
			'parameters' => $parameters,
		);
	}

	/**
	 *
	 * @return \System\Http\Response\ResponseInterface
	 * @throws \RuntimeException
	 */
	public function run() {
		$requestMethod = $this->request->getMethod()->getMethod();
		$method = Di::getInstance()->get('system.http.request.method', false, array(MethodInterface::METHOD_GET));
		$route = trim($this->request->getRequestData('route', $method), '/');
		$matches = array();
		$callback = null;

		foreach ($this->routes[$requestMethod] as $regex => $value) {
			preg_match_all('/' . $regex . '$/', $route, $matches);

			if (false == empty($matches[0])) {
				$callback = $value['response'];
				unset($matches[0]);
				break;
			}
		}

		if (empty($matches[1][0]) || false == is_callable($callback)) {
			throw new \RuntimeException("Route {$route} not found.");
		}

		$mapped = $this->mapParameters($matches);

		return $this->callCallback($callback, $mapped);
	}

	/**
	 *
	 * @param  callable $callback
	 * @param  array $parameters
	 * @return \System\Http\Response\ResponseInterface
	 * @throws \RuntimeException\
	 */
	private function callCallback(callable $callback, array $parameters = array()) {
		$reflectiton = new \ReflectionFunction($callback);
		$return = $reflectiton->invokeArgs($parameters);

		if (false == $return instanceof \System\Http\Response\ResponseInterface) {
			throw new \RuntimeException("Controller return value must be instance of response");
		}

		return $return;
	}

	/**
	 *
	 * @param  array $parameters
	 * @return array
	 */
	private function mapParameters(array $parameters) {
		$map = array();

		foreach ($parameters as $parameter) {
			$map[] = $parameter[0];
		}

		return $map;
	}

	/**
	 *
	 * @param  string $route
	 * @return array
	 */
	private function parseRoute($route) {
		$routeData = explode('/', trim($route, '/'));
		$regex = array();
		$parameters = array();

		foreach ($routeData as $data) {
			if (0 === strpos($data, '{')) {
				$trim = str_replace(array('{', '}'), '', $data);
				$parameters[] = $trim;
				$regex[] = '([a-zA-Z0-9_-]+)';
			} else {
				$regex[] = $data;
			}
		}

		return array(
			implode('\/', $regex),
			$parameters,
		);
	}

}
