<?php

namespace System\Http\Request;

use System\Http\Method\MethodInterface;

class Request extends RequestFactory implements RequestInterface {

    /** @var MethodInterface */
    protected $method;

    public function __construct(MethodInterface $method) {
        parent::__construct();
        $this->method = $method;
    }

    public function getHeader($header) {
        
    }

    public function getHeaders() {
        
    }

    public function getMethod() {
        return $this->method;
    }

}
