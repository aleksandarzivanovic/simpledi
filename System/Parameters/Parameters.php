<?php

namespace System\Parameters;


class Parameters implements ParametersInterface {

    /** @var int */
    private $parameterType = self::PARAMETER_TYPE_NONE;

    /** @var string */
    private $parameter;

    /** @var string */
    private $parameterMethod;

    /** @var array */
    public static $parameterPlaceholder = [
        '@' => self::PARAMETER_TYPE_CLASS,
        '#' => self::PARAMETER_TYPE_STRING,
        '::' => self::PARAMETER_TYPE_METHOD,
    ];

    /**
     * @return int
     */
    public function getParameterType()
    {
        return $this->parameterType;
    }

    /**
     * @return string
     */
    public function getParameter()
    {
        return $this->parameter;
    }

    /**
     * @return string
     */
    public function getParameterMethod()
    {
        return $this->parameterMethod;
    }

    /**
     * @param string $parameter
     */
    public function parseParameter($parameter)
    {
        $this->clearParameter();

        if (empty($parameter)) {
            throw new \RuntimeException('Trying to parse empty parameter');
        }

        $this->parseParameterType($parameter);

        if (self::PARAMETER_TYPE_NONE == $this->parameterType) {
            throw new \RuntimeException("Invalid parameter {$parameter}");
        } else if (self::PARAMETER_TYPE_METHOD == $this->parameterType) {
            $this->parseParameterMethod($parameter);
        } else {
            $this->parameter = $this->cleanParameterPlaceholder($parameter);
        }
    }

    /**
     * @param string $parameter
     */
    private function parseParameterMethod($parameter)
    {
        $classMethod = explode('::', $parameter);

        if (empty($classMethod[0])) {
            throw new \RuntimeException('Parameter is empty.');
        }

        $this->parameter = $this->cleanParameterPlaceholder($classMethod[0]);

        $this->parameterMethod = empty($classMethod[1]) ? null : $classMethod[1];
    }

    /**
     * @param string $parameter
     */
    private function parseParameterType($parameter)
    {
        $this->parameter = self::PARAMETER_TYPE_NONE;

        foreach (self::$parameterPlaceholder as $placeholder => $parameterType) {
            if (false !== strpos($parameter, $placeholder)) {
                $this->parameterType = $parameterType;
                return;
            }
        }

        if ($parameter) {
            $this->parameterType = self::PARAMETER_TYPE_ALIAS;
        }

    }

    private function clearParameter()
    {
        $this->parameter = self::PARAMETER_TYPE_NONE;
        $this->parameterMethod = null;
    }

    /**
     * @param $parameter
     * @return string
     */
    private function cleanParameterPlaceholder($parameter)
    {
        return str_replace(array_keys(self::$parameterPlaceholder), '', $parameter);
    }
}