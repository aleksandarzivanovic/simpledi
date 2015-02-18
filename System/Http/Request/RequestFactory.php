<?php

namespace System\Http\Request;

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
    protected $session;

    /** @var array|null */
    protected $env;

    /** @var array|null */
    protected $request;

    public function __construct() {
        $this->get = filter_input_array(INPUT_GET);
        $this->post = filter_input_array(INPUT_POST);
        $this->cookie = filter_input_array(INPUT_COOKIE);
        $this->env = filter_input_array(INPUT_ENV);
        $this->request = filter_input_array(INPUT_REQUEST);
        $this->session = filter_input_array(INPUT_SESSION);
        $this->server = filter_input_array(INPUT_SERVER);
        $this->files = $_FILES;
    }    
}
