<?php

namespace System\Http\Header;

use System\Http\Request\RequestFactory;

class Header implements HeaderInterface {
    
    private $requestFactory;
    
    public function __construct(RequestFactory $requestFactory) {
        $this->requestFactory = $requestFactory;
    }

    /**
     * 
     * @param string $header
     * @return string|array|null
     */
    public function getHeader($header) {
        
    }

    /**
     * @return string
     */
    public function getHeaders() {
        
    }

    /**
     * 
     * @param string $header
     * @param string|array $value
     * @return HeaderInterface|$this
     */
    public function setHeader($header, $value) {
        
    }

}
