<?php

namespace System\Config;

class Config implements ConfigInterface {

    private $dir;
    private $json;
    private $array;

    public function __construct($configDirectory) {
        $this->dir = trim($configDirectory, '/');
    }

    /**
     * @param string $path
     */
    public function loadConfig($path) {
        $this->generatePath($path);
        $this->fetchJson($path);
        $this->validateJson($path);
    }

    /**
     * 
     * @param string $key
     * @return mixed
     */
    public function get($key) {
        if (false == empty($this->array[$key])) {
            return $this->array[$key];
        }
    }

    /**
     * 
     * @return string|null
     */
    public function getJson() {
        return $this->json;
    }

    /**
     * 
     * @return array|null
     */
    public function getArray() {
        return $this->array;
    }

    private function fetchJson($path) {
        $this->json = file_get_contents($path);
        $this->array = json_decode($this->json, true);
    }

    private function validateJson($path) {
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException("Invalid JSON in path {$path}");
        }
    }

    /**
     * 
     * @param string $path
     * @return string
     */
    private function generatePath(&$path) {
        $path = $this->dir . '/' . trim($path, '/');

        return $path;
    }

}
