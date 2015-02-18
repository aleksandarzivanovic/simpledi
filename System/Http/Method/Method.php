<?php

namespace System\Http\Method;

class Method implements MethodInterface {

    const GET = 'GET';
    const POST = 'POST';
    const PUT = 'PUT';
    const DELETE = 'DELETE';
    const NONE = 'NONE';

    /** @var string */
    private $currentMethod = self::NONE;

    /** @var array */
    private $_acceptedMethdos = array(
        self::GET => true,
        self::POST => true,
        self::PUT => true,
        self::DELETE => true,
    );

    /**
     * 
     * @param string $method
     */
    public function __construct($method = self::GET) {
        $this->factory($method);
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
        return $this->isType(static::GET, $callback, $arguments);
    }

    /**
     * @param callable $callback
     * @param array $arguments
     * @return bool|mixed
     */
    public function isPost(callable $callback = null, array $arguments = array()) {
        return $this->isType(static::POST, $callback, $arguments);
    }

    /**
     * @param callable $callback
     * @param array $arguments
     * @return bool|mixed
     */
    public function isPut(callable $callback = null, array $arguments = array()) {
        return $this->isType(static::PUT, $callback, $arguments);
    }

    /**
     * @param callable $callback
     * @param array $arguments
     * @return bool|mixed
     */
    public function isDelete(callable $callback = null, array $arguments = array()) {
        return $this->isType(static::DELETE, $callback, $arguments);
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

        if (false == isset($this->_acceptedMethdos)) {
            throw new \RuntimeException("Invalid method {$method}");
        }

        $this->currentMethod = $method;
    }

}
