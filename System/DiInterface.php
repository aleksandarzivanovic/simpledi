<?php

namespace System;

interface DiInterface {
    /**
     * @param $class
     * @param bool $singleton
     * @return mixed
     */
    public function get($class, $singleton);

    /**
     * @return Di
     */
    public function reload();
}