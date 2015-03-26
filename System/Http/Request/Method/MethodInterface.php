<?php

namespace System\Http\Request\Method;

interface MethodInterface
{
    const METHOD_GET = 'GET';
    const METHOD_POST = 'POST';
    const METHOD_PUT = 'PUT';
    const METHOD_DELETE = 'DELETE';
    const METHOD_NONE = 'NONE';

    /**
     * @return string
     */
    public function getMethod();

    /**
     * @param callable $callback
     * @param array    $arguments
     *
     * @return bool|mixed
     */
    public function isGet(callable $callback = null, array $arguments = array());

    /**
     * @param callable $callback
     * @param array    $arguments
     *
     * @return bool|mixed
     */
    public function isPost(callable $callback = null, array $arguments = array());

    /**
     * @param callable $callback
     * @param array    $arguments
     *
     * @return bool|mixed
     */
    public function isPut(callable $callback = null, array $arguments = array());

    /**
     * @param callable $callback
     * @param array    $arguments
     *
     * @return bool|mixed
     */
    public function isDelete(callable $callback = null, array $arguments = array());

    /**
     * @param string $method
     *
     * @return MethodInterface|$this
     */
    public function setMethod($method);
}
