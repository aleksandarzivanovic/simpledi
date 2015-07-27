<?php

namespace System\Storage;

use System\Storage\Drivers\StorageDriverInterface;

interface StorageInterface extends StorageCRUDInterface
{
    /**
     * @param  StorageDriverInterface $driver
     * @return StorageInterface|$this
     */
    public function setDriver(StorageDriverInterface $driver);
    
    /**
     * 
     * @param string $name
     * @return StorageInterface
     */
    public function setRepository($name);
    
    /**
     * @return string
     */
    public function getQuery();
}
