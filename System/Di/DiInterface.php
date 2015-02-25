<?php

namespace System\Di;

interface DiInterface
{
    /**
     * @param  string $class
     * @return object
     */
    public function get($class);

    /**
     * @return Di
     */
    public function reload();
}
