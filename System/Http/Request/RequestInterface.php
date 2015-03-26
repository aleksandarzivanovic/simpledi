<?php

namespace System\Http\Request;

use System\Http\Request\Method\MethodInterface;

interface RequestInterface
{
    /**
     * @return array
     */
    public function getHeaders();

    /**
     * @param string $header
     *
     * @return string|null
     */
    public function getHeader($header);

    /**
     * @return MethodInterface
     */
    public function getMethod();

    /**
     * @param string          $data
     * @param MethodInterface $method
     *
     * @return string|array|object|null
     */
    public function getRequestData($data, MethodInterface $method);

    /**
     * @param MethodInterface $method
     *
     * @return array
     */
    public function getRequestDataArray(MethodInterface $method);
}
