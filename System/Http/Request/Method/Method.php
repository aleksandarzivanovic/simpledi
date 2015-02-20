<?php

namespace System\Http\Request\Method;

use System\Http\Request\RequestFactory;

class Method implements MethodInterface {

    /** @var string */
    private $currentMethod = MethodInterface::METHOD_NONE;

    /** @var array */
    private $_acceptedMethdos = array(
        MethodInterface::METHOD_GET => true,
        MethodInterface::METHOD_POST => true,
        MethodInterface::METHOD_PUT => true,
        MethodInterface::METHOD_DELETE => true,
    );

    /**
     * 
     * @param RequestFactory $requestFactory
     */
    public function __construct(RequestFactory $requestFactory) {
        $this->factory($requestFactory->getRequestMethod());
    }

    /**
     * @return string
     */
    public function getMethod() {
        return $this->currentMethod;
    }

    /**
     * @param callable $callback
     * @param array $arguments
     * @return bool|mixed
     */
    public function isGet(callable $callback = null, array $arguments = array()) {
        return $this->isType(MethodInterface::METHOD_GET, $callback, $arguments);
    }

    /**
     * @param callable $callback
     * @param array $arguments
     * @return bool|mixed
     */
    public function isPost(callable $callback = null, array $arguments = array()) {
        return $this->isType(MethodInterface::METHOD_POST, $callback, $arguments);
    }

    /**
     * @param callable $callback
     * @param array $arguments
     * @return bool|mixed
     */
    public function isPut(callable $callback = null, array $arguments = array()) {
        return $this->isType(MethodInterface::METHOD_PUT, $callback, $arguments);
    }

    /**
     * @param callable $callback
     * @param array $arguments
     * @return bool|mixed
     */
    public function isDelete(callable $callback = null, array $arguments = array()) {
        return $this->isType(MethodInterface::METHOD_DELETE, $callback, $arguments);
    }

    /**
     * 
     * @param string $type
     * @param callable $callback
     * @param array $arguments
     * @return bool|mixed
     */
    private function isType($type, callable $callback = null, array $arguments = array()) {
        if ($type != $this->currentMethod) {
            return false;
        } else if (is_callable($callback)) {
            $closure = new \ReflectionFunction($callback);
            return $closure->invokeArgs($arguments);
        } else {
            return true;
        }
    }

    /**
     * @param string $method
     * @throws \RuntimeException
     */
    private function factory($method) {
        if (true == empty($method)) {
            throw new \RuntimeException('Method may not be empty');
        }

        if (false == isset($this->_acceptedMethdos[$method])) {
            throw new \RuntimeException("Invalid method {$method}");
        }

        $this->currentMethod = $method;
    }

}