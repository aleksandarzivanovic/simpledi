<?php

namespace System\Config;

interface ConfigInterface {

    /**
     * @param string $path
     */
    public function loadConfig($path);

    /**
     * 
     * @param string $key
     * @return mixed
     */
    public function get($key);

    /**
     * 
     * @return string|null
     */
    public function getJson();

    /**
     * 
     * @return array|null
     */
    public function getArray();
}
