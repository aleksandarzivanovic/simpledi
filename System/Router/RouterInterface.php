<?php

namespace System\Router;

interface RouterInterface {

	/**
	 * 
	 * @param type $route
	 * @param \System\Router\MethodInterface $method
	 * @param \System\Router\callable $callback
	 * @return RouterInterface|$this
	 * @throws \RuntimeException
	 */
	public function add($route, $method, callable $callback);

	/**
	 * 
	 * @return RouterInterface|null
	 * @throws \RuntimeException
	 */
	public function run();

	/**
	 * @return RouterInterface|null
	 * @throws \RuntimeException
	 */
	public function loadController();

    /**
     * @return array
     */
    public function getRoutes();
}
