<?php

namespace System\Http\Request;

use System\Http\Method\MethodInterface;

interface RequestInterface {

    /**
     * @return array
     */
    public function getHeaders();

    /**
     * @param string $header
     * @return string|null
     */
    public function getHeader($header);

    /**
     * @return MethodInterface
     */
    public function getMethod();
}
