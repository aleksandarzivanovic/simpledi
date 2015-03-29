<?php

namespace System\Cache;

use RuntimeException;

class Cache extends Helper\CacheHelper implements CacheInterface {
    /**
     * 
     * @param array $files
     * 
     * @return array
     */
    public function __construct(array $files) {
        parent::__construct($files);
        $this->loadCacheFiles($files);
    }

    /**
     * @param string $file
     * @throws RuntimeException
     */
    public function _getCache($file) {
        if (empty($file) || false == is_string($file)) {
            throw new RuntimeException('File name may not be empty nor array.');
        }
    }

}
