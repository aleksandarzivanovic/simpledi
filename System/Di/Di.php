<?php

namespace System\Di;

use System\Parameters\ParametersInterface;

class Di implements DiInterface {

    /** @var Di */
    private static $instance;

    /** @var  array() */
    protected $loadedInstances = array();

    /** @var array */
    protected $_diContainer = array();

    /**
     *
     * @throws \RuntimeException
     */
    protected function __construct() {
        $this->loadedInstances = array();

        try {
            $containerJson = file_get_contents('Config/data/di_container.json');
            $this->_diContainer = json_decode($containerJson, true);
        } catch (\Exception $ex) {
            throw new \RuntimeException('Error while parsing .json data', 1, $ex);
        }
    }

    /**
     * @return Di
     */
    public static function getInstance() {
        if (false == static::$instance) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    /**
     * @return Di
     */
    public function reload() {
        static::$instance = false;

        return static::getInstance();
    }

    /**
     * @param string $class
     * @param bool $singleton
     * @param array $customArgs
     * @return object
     */
    public function get($class, $singleton = true, array $customArgs = array()) {
        if ($singleton && isset($this->loadedInstances[$class])) {
            return $this->loadedInstances[$class];
        }

        $alias = $class;
        $loadDependencies = false;

        if (false == empty($this->_diContainer[$class]) && is_array($this->_diContainer)) {
            $loadDependencies = true;
            $class = $this->_diContainer[$alias]['class'];
        }

        $classReflection = $this->validateClassAndGetReflection($class);

        $instance = $this->instanceClass(
                $classReflection, $alias, $singleton, $loadDependencies, $customArgs
        );

        return $instance;
    }

    /**
     * @param  string $class
     * @return string|null
     */
    public function getClassAlias($class) {
        foreach ($this->_diContainer as $alias => $classDefinition) {
            if ($class == $classDefinition['class']) {
                return $alias;
            }
        }

        return null;
    }

    /**
     * @param \ReflectionClass $classReflection
     * @param $alias
     * @param bool $singleton
     * @param bool $loadDependencies
     * @param array $customArgs
     * @return object
     */
    private function instanceClass(
    \ReflectionClass $classReflection, $alias, $singleton = true, $loadDependencies = true, array $customArgs = array()
    ) {
        $arguments = array();

        if ($loadDependencies && empty($customArgs)) {
            $arguments = $this->loadDependencies(
                    $alias, $singleton
            );
        } else if (false == empty($customArgs)){
            $arguments = $customArgs;
        }

        $instance = $classReflection->newInstanceArgs($arguments);

        if ($singleton) {
            $this->loadedInstances[$alias] = $instance;
        }

        return $instance;
    }

    /**
     *
     * @param  string $class
     * @return \ReflectionClass
     * @throws \RuntimeException
     */
    private function validateClassAndGetReflection($class) {
        if (true == empty($class)) {
            throw new \RuntimeException("Class name empty");
        }

        if (false == class_exists($class)) {
            throw new \RuntimeException("Class {$class} not found");
        }

        $reflection = new \ReflectionClass($class);

        if (false == $reflection->isInstantiable()) {
            throw new \RuntimeException("Class {$class} is not instantiable");
        }

        return $reflection;
    }

    /**
     *
     * @param  string $class
     * @param  bool   $singleton
     * @return array
     */
    private function loadDependencies($class, $singleton = true) {
        $arguments = array();

        if (false == empty($this->_diContainer[$class]['arguments'])) {
            $arguments = $this->parseArguments($this->_diContainer[$class]['arguments'], $singleton);
        }



        return $arguments;
    }

    /**
     * @param array $arguments
     * @param bool $singleton
     * @return array
     */
    private function parseArguments(array $arguments, $singleton = true)
    {
        $final = [];

        foreach ($arguments as $dependency) {
            $dependency = $this->parseDependency($dependency);

            if (is_array($dependency)) {
                $final[] = $dependency[0];
                continue;
            }

            if ($singleton) {
                $final[] = $this->get($dependency);
            } else {
                $final[] = clone $this->get($dependency, false);
            }
        }

        return $final;
    }

    /**
     * @param  string            $dependency
     * @return string
     * @throws \RuntimeException
     */
    private function parseDependency($dependency) {
        /** @var ParametersInterface $parameters */
        $parameters = self::get('system.parameters');
        $parameters->parseParameter($dependency);

        switch ($parameters->getParameterType()) {
            case ParametersInterface::PARAMETER_TYPE_METHOD:
                $cleanDependency = $this->callMethod($parameters->getParameter(), $parameters->getParameterMethod());
                break;
            case ParametersInterface::PARAMETER_TYPE_CLASS:
                $cleanDependency = $parameters->getParameter();
                $classAlias = $this->getClassAlias($cleanDependency);

                if ($classAlias) {
                    $cleanDependency = $classAlias;
                }

                break;
            case ParametersInterface::PARAMETER_TYPE_STRING:
                $cleanDependency = array($parameters->getParameter());
                break;
            case ParametersInterface::PARAMETER_TYPE_ALIAS:
                $alias = $parameters->getParameter();

                if (false == isset($this->_diContainer[$alias])) {
                    throw new \RuntimeException("Class alias {$alias} not found");
                }

                $cleanDependency = $alias;
                break;
            default:
                throw new \RuntimeException('Unknown parameter ' . $dependency);
        }

        return $cleanDependency;
    }

    /**
     * @param string $class
     * @param string $method
     * @return array
     */
    private function callMethod($class, $method) {
        $object = $this->get($class);

        return array(call_user_func(array($object, $method)));
    }

}
