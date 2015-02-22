<?php

namespace System\Di;

class Di implements DiInterface {

    /** @var Di */
    private static $instance;

    /** @var  array() */
    protected $loadedInstances = array();

    /** @var array */
    protected $_diContainer = array();

    /** @var array() */
    private $_placeholders = array(
        '@' => true,
        '%' => true,
        '#' => true,
    );

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
     *
     * @param  string $class
     * @param  bool   $singleton
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

        if (false == empty($this->_diContainer[$alias]['file'])) {
            $file = $this->_diContainer[$alias]['file'];
        } else {
            $file = str_replace('\\', '/', $class) . '.php';
        }

        $classReflection = $this->validateClass($class);

        $instance = $this->instanceClass(
                $classReflection, $alias, $singleton, $loadDependencies, $customArgs
        );

        return $instance;
    }

    /**
     *
     * @param  ReflectionClass $classReflection
     * @param  bool            $singleton
     * @param  bool            $loadDependencies
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
        } else {

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
     * @param  string            $class
     * @return \ReflectionClass
     * @throws \RuntimeException
     */
    private function validateClass($class) {
        if (true == empty($class)) {
            throw new \RuntimeException("Class name empty");
        }

        $reflection = new \ReflectionClass($class);

        if (false == class_exists($class)) {
            throw new \RuntimeException("Class {$class} not found");
        }

        if (false == $reflection->isInstantiable()) {
            throw new \RuntimeException("Class {$class} is not instantiable");
        }

        return $reflection;
    }

    /**
     * @param  string      $class
     * @return string|null
     */
    public function getClassAlias($class) {
        foreach ($this->_diContainer as $alias => $classDefinition) {
            if ($class == $classDefinition['class']) {
                return $alias;
            }
        }

        return;
    }

    /**
     *
     * @param  string $class
     * @param  bool   $singleton
     * @return array
     */
    private function loadDependencies($class, $singleton = true) {
        $arguments = array();

        if (empty($this->_diContainer[$class]['arguments'])) {
            return $arguments;
        }

        foreach ($this->_diContainer[$class]['arguments'] as $dependency) {
            $dependency = $this->parseDependency($dependency);

            if (is_array($dependency)) {
                $arguments[] = $dependency[0];
                continue;
            }

            if ($singleton) {
                $arguments[] = $this->get($dependency);
            } else {
                $arguments[] = clone $this->get($dependency, false);
            }
        }

        return $arguments;
    }

    /**
     * @param  string            $dependency
     * @return string
     * @throws \RuntimeException
     */
    private function parseDependency($dependency) {
        $classMethod = explode('::', $dependency);

        $class = $classMethod[0];
        $method = empty($classMethod[1]) ? null : $classMethod[1];

        $placeholder = $dependency[0];

        if (false == isset($this->_placeholders[$placeholder])) {
            if (false == isset($this->_diContainer[$class])) {
                throw new \RuntimeException("Class alias {$class} not found");
            }

            $class = $classMethod[0];
        } elseif (0 === strpos($dependency, '#')) {
            $class = trim($dependency, '#');

            return array($class);
        } elseif (0 === strpos($dependency, '@')) {
            $class = trim($classMethod[0], '@');
        }

        if (null !== $method) {
            $class = $this->callMethod($class, $method);
        }

        return $class;
    }

    private function callMethod($class, $method) {
        $object = $this->get($class);

        return array(call_user_func(array($object, $method)));
    }

}
