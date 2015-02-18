<?php

namespace System\Http\Request;

use System\Http\Method\MethodInterface;

class Request implements RequestInterface {

    /** @var MethodInterface */
    protected $method;
    
    /** @var RequestFactory */
    protected $requestFactory;


    public function __construct(RequestFactory $requestFactory, MethodInterface $method) {
        $this->method = $method;
        $this->requestFactory = $requestFactory;
    }

    public function getHeader($header) {
        
    }

    public function getHeaders() {
        
    }

    public function getMethod() {
        return $this->method;
    }

}
