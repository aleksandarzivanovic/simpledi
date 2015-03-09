<?php

namespace System\Http\Request\Method;

class Method implements MethodInterface
{
    /** @var string */
    private $current = MethodInterface::METHOD_NONE;

    /** @var array */
    private $_acceptedMethdos = array(
        MethodInterface::METHOD_GET => true,
        MethodInterface::METHOD_POST => true,
        MethodInterface::METHOD_PUT => true,
        MethodInterface::METHOD_DELETE => true,
    );

    /**
     *
     * @param string $method
     */
    public function __construct($method)
    {
        $this->factory($method);
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->current;
    }

    /**
     * @param callable $callback
     * @param array $arguments
     * @return bool|mixed
     */
    public function isGet(callable $callback = null, array $arguments = array())
    {
        return $this->isType(MethodInterface::METHOD_GET, $callback, $arguments);
    }

    /**
     * @param callable $callback
     * @param array $arguments
     * @return bool|mixed
     */
    public function isPost(callable $callback = null, array $arguments = array())
    {
        return $this->isType(MethodInterface::METHOD_POST, $callback, $arguments);
    }

    /**
     * @param callable $callback
     * @param array $arguments
     * @return bool|mixed
     */
    public function isPut(callable $callback = null, array $arguments = array())
    {
        return $this->isType(MethodInterface::METHOD_PUT, $callback, $arguments);
    }

    /**
     * @param callable $callback
     * @param array $arguments
     * @return bool|mixed
     */
    public function isDelete(callable $callback = null, array $arguments = array())
    {
        return $this->isType(MethodInterface::METHOD_DELETE, $callback, $arguments);
    }

    /**
     *
     * @param string $type
     * @param callable $callback
     * @param array $arguments
     * @return bool|mixed
     */
    private function isType($type, callable $callback = null, array $arguments = array())
    {
        if ($type != $this->current) {
            return false;
        } elseif (is_callable($callback)) {
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
    private function factory($method)
    {
        if (true == empty($method)) {
            throw new \RuntimeException('Method may not be empty');
        }

        if (false == isset($this->_acceptedMethdos[$method])) {
            throw new \RuntimeException("Invalid method {$method}");
        }

        $this->current = $method;
    }

    /**
     * @param type $method
     * @return \System\Http\Request\Method\Method
     */
    public function setMethod($method)
    {
        $this->factory($method);

        return $this;
    }
}
