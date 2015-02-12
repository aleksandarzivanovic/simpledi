<?php

namespace System;

class Di {

    /** @var System\Di */
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
	try {
	    $this->loadedInstances = array();
	    $json = file_get_contents('Config/data/di_container.json');
	    $this->_diContainer = json_decode($json, true);
	} catch (\Exception $ex) {
	    throw new \RuntimeException('di_container.json failed to parse', 1, $ex);
	}
    }

    /**
     * @return \Di
     */
    public static function instance() {
	if (false == self::$instance) {
	    self::$instance = new self;
	}

	return self::$instance;
    }

    /**
     * @return \Di|$this
     */
    public function reload() {
	self::$instance = false;

	return self::instance();
    }

    /**
     * 
     * @param string $class
     * @param bool $singleton
     * @return mixed
     */
    public function get($class, $singleton = true) {
	if ($singleton && isset($this->loadedInstances[$class])) {
	    return $this->loadedInstances[$class];
	}

	$classReflection = $this->validateClass($class);
	$loadDependencies = true;

	if (empty($this->_diContainer[$class]) || false == is_array($this->_diContainer)) {
	    $loadDependencies = false;
	}

	$instance = $this->instanceClass(
		$classReflection, $singleton, $loadDependencies
	);

	return $instance;
    }

    /**
     * 
     * @param ReflectionClass $classReflection
     * @param bool $singleton
     * @param bool $loadDependencies
     * @return mixed
     */
    private function instanceClass(
    \ReflectionClass $classReflection, $singleton = true, $loadDependencies = true
    ) {
	$class = $classReflection->getName();
	$arguments = array();

	if ($loadDependencies) {
	    $arguments = $this->loadDependencies(
		    $class, $singleton
	    );
	}

	$instance = $classReflection->newInstanceArgs($arguments);

	if ($singleton) {
	    $this->loadedInstances[$class] = $instance;
	}

	return $instance;
    }

    /**
     * 
     * @param string $class
     * @param bool $singleton
     * @return array
     */
    private function loadDependencies($class, $singleton = true) {
	$arguments = array();

	foreach ($this->_diContainer[$class] as $dependency) {
	    if (0 !== strpos($dependency, '@')) {
		$arguments[] = $dependency;
		continue;
	    }

	    $dependency = substr($dependency, 1);

	    if ($singleton) {
		$arguments[] = $this->get($dependency);
	    } else {
		$arguments[] = clone $this->get($dependency, false);
	    }
	}

	return $arguments;
    }

    /**
     * 
     * @param string $class
     * @return \ReflectionClass
     * @throws \RuntimeException
     */
    private function validateClass($class) {
	if (true == empty($class)) {
	    throw new \RuntimeException("Invalid class name");
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

}
