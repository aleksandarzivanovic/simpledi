<?php

namespace System\Cache\Helper;

class CacheHelper extends \ArrayObject {

    protected function loadCacheFiles(array $files) {
        foreach ($files as $path) {
            if (file_exists($path)) {
                $this->loadOriginal($path);
                $info = pathinfo($path);
                $dir = $info['dirname'];
                $path = str_replace(['/', '\\', '-', ':', '\'', '"', $dir], '_', $path);
                $this->createaCache($dir, $path);
                $this->loadCacheFile($dir, $path);
            }
        }
    }

    private function loadOriginal($path) {
        $this->selfClean($path, 'file');
        $this[$path]['file'] = file_get_contents($path);
    }

    private function createaCache($dir, $path) {
        $this->selfClean($path, 'object');
        if (FALSE == file_exists("Cache/{$dir}/{$path}")) {
            file_put_contents("Cache/{$path}", '');
        }
    }

    private function loadCacheFile($fileName) {
        $this[$fileName]['cache'] = file_get_contents("Cache/{$fileName}");
    }

    private function selfClean($path, $key) {
        if (false == is_array($this[$path])) {
            $this[$path][$key] = [];
        }
    }

}
