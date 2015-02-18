<?php

namespace System\Http\Method;

interface MethodInterface
{
    /**
     * @return string
     */
    public function getMethod();

    /**
     * @param  callable   $callback
     * @param  array      $arguments
     * @return bool|mixed
     */
    public function isGet(callable $callback = null, array $arguments = array());

    /**
     * @param  callable   $callback
     * @param  array      $arguments
     * @return bool|mixed
     */
    public function isPost(callable $callback = null, array $arguments = array());

    /**
     * @param  callable   $callback
     * @param  array      $arguments
     * @return bool|mixed
     */
    public function isPut(callable $callback = null, array $arguments = array());

    /**
     * @param  callable   $callback
     * @param  array      $arguments
     * @return bool|mixed
     */
    public function isDelete(callable $callback = null, array $arguments = array());
}
