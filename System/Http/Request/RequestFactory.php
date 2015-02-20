<?php

namespace System\Http\Request;

use System\Http\Request\Method\MethodInterface;

class RequestFactory {

    /** @var array|null */
    protected $get;

    /** @var array|null */
    protected $post;

    /** @var array|null */
    protected $files;

    /** @var array|null */
    protected $server;

    /** @var array|null */
    protected $cookie;

    /** @var array|null */
    protected $env;

    public function __construct() {
        $this->get = filter_input_array(INPUT_GET);
        $this->post = filter_input_array(INPUT_POST);
        $this->cookie = filter_input_array(INPUT_COOKIE);
        $this->env = filter_input_array(INPUT_ENV);
        $this->server = filter_input_array(INPUT_SERVER);
        $this->files = $_FILES;
    }
    
    /**
     * @return string
     */
    public function getRequestMethod() {
        return isset($this->server['REQUEST_METHOD']) ? $this->server['REQUEST_METHOD'] : MethodInterface::METHOD_NONE;
    }
}
