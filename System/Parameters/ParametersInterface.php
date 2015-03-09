<?php

namespace System\Parameters;

interface ParametersInterface
{
    const PARAMETER_TYPE_NONE    =   0;
    const PARAMETER_TYPE_STRING  =   1;
    const PARAMETER_TYPE_CLASS   =   2;
    const PARAMETER_TYPE_ALIAS   =   3;
    const PARAMETER_TYPE_METHOD  =   4;

    /**
     * @return int
     */
    public function getParameterType();

    /**
     * @return string|null
     */
    public function getParameter();

    /**
     * @return string|null
     */
    public function getParameterMethod();

    public function parseParameter($parameter);
}
