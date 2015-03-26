<?php

namespace System\Router;

use System\Di\Di;
use System\Http\Request\Method\MethodInterface;
use System\Http\Request\RequestInterface;
use System\Http\Response\ResponseInterface;
use System\Template\TemplateInterface;

class Router implements RouterInterface
{
    /** @var array */
    protected $routes = array();

    /** @var RequestInterface */
    private $request;

    /** @var callable */
    private $callback;

    /** @var array */
    private $parameters = [];

    /** @var TemplateInterface */
    private $template;

    /**
     * @param RequestInterface  $request
     * @param TemplateInterface $template
     */
    public function __construct(RequestInterface $request, TemplateInterface $template)
    {
        $this->request = $request;
        $this->template = $template;
    }

    /**
     * @param string   $route
     * @param string   $method
     * @param callable $callback
     *
     * @return RouterInterface|$this
     *
     * @throws \RuntimeException
     */
    public function add($route, $method, callable $callback)
    {
        $parameters = $this->routeParameters($route);
        $regex = $this->routeToRegex($route);

        // validate method
        Di::getInstance()->get('system.http.request.method', false, array($method));

        if (isset($this->routes[$method][$regex])) {
            throw new \RuntimeException(sprintf('Route %s: %s already defined.', $method, $route));
        }

        $this->routes[$method][$regex] = array(
            'response' => $callback,
            'parameters' => $parameters,
        );

        return $this;
    }

    /**
     * @return $this|null
     *
     * @throws \RuntimeException
     */
    public function loadController()
    {
        $map = json_decode(file_get_contents('Config/data/router.json'), true);

        $method = Di::getInstance()->get('system.http.request.method', false, array(MethodInterface::METHOD_GET));
        $requestRoute = trim($this->request->getRequestData('route', $method), '/');

        foreach ($map as $route => $file) {
            $regex = $this->routeToRegex($route);

            if ($this->getRequestParameters($regex, $requestRoute)) {
                if (false == is_file($file)) {
                    throw new \RuntimeException("Response Controller for route {$route} not found");
                }

                require_once $file;
            }
        }

        return $this;
    }

    /**
     * @return \System\Http\Response\ResponseInterface
     *
     * @throws \RuntimeException
     */
    public function run()
    {
        $method = Di::getInstance()->get('system.http.request.method', false, array(MethodInterface::METHOD_GET));
        $route = trim($this->request->getRequestData('route', $method), '/');
        $this->parseRequest($route);

        if (false == is_callable($this->callback)) {
            throw new \RuntimeException("Controller for route {$route} is not callable.");
        }

        return $this->callCallback($this->callback, $this->parameters);
    }

    /**
     * @return array
     */
    public function getRoutes()
    {
        return $this->routes;
    }

    public function clearRoutes()
    {
        $this->routes = [];

        return $this;
    }

    /**
     * @param $route
     */
    private function parseRequest($route)
    {
        $method = Di::getInstance()->get('system.http.request.method')->getMethod();
        $parameters = array();
        $tmpParameters = array();
        $callback = null;
        $lastCount = 999;

        /*
         * The one with least parameters should be called.
         * Why? Imagine you have route /user/{id}/{action} and /user/{id}/delete
         * Without this route /user/{id}/delete will be counted as /user/{id}/{action} if it is registered first
         */
        foreach ($this->routes[$method] as $regex => $value) {
            if ($this->getRequestParameters($regex, $route, $tmpParameters)) {
                $currentCount = count($tmpParameters);

                if ($currentCount < $lastCount) {
                    $lastCount = $currentCount;
                    $callback = $value['response'];
                    $parameters = $tmpParameters;
                    unset($parameters[0]);
                }
            }
        }

        $this->callback = $callback;
        $this->parameters = $this->mapParameters($parameters);
    }

    /**
     * @param callable $callback
     * @param array    $parameters
     *
     * @return ResponseInterface
     *
     * @throws \RuntimeException
     */
    private function callCallback(callable $callback, array $parameters = array())
    {
        $reflection = new \ReflectionFunction($callback);
        $session = Di::getInstance()->get('system.session');
        $response = Di::getInstance()->get('system.http.response');
        array_unshift($parameters, $response);

        /** @var ResponseInterface $return */
        $return = $reflection->invokeArgs($parameters);

        if (false == $return instanceof ResponseInterface) {
            throw new \RuntimeException("Controller return value must be instance of response");
        }

        $return->updateHeaders();
        $session->persistSession();

        return $return;
    }

    /**
     * @param array $parameters
     *
     * @return array
     */
    private function mapParameters(array $parameters)
    {
        $map = array();

        foreach ($parameters as $parameter) {
            $map[] = $parameter[0];
        }

        return $map;
    }

    /**
     * @param $route
     *
     * @return array
     */
    private function routeParameters($route)
    {
        $parameters = [];

        foreach ($this->routeToArray($route) as $data) {
            if (0 === strpos($data, '{')) {
                $parameters[] = str_replace(array('{', '}'), '', $data);
            }
        }

        return $parameters;
    }

    /**
     * @param $route
     *
     * @return string
     */
    private function routeToRegex($route)
    {
        $regex = [];

        foreach ($this->routeToArray($route) as $data) {
            if (0 === strpos($data, '{')) {
                $regex[] = '([a-zA-Z0-9_-]+)';
            } else {
                $regex[] = $data;
            }
        }

        return implode('\/', $regex);
    }

    /**
     * @param $route
     *
     * @return array
     */
    private function routeToArray($route)
    {
        return explode('/', trim($route, '/'));
    }

    /**
     * @param string $regex
     * @param string $route
     * @param array  $matches
     *
     * @return bool
     */
    private function getRequestParameters($regex, $route, array &$matches = array())
    {
        preg_match_all('/'.$regex.'$/', $route, $matches);

        if (false == empty($matches[0])) {
            unset($matches[0]);

            return true;
        }

        return false;
    }
}
