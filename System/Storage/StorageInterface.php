<?php

namespace System\Storage;

use System\Storage\Drivers\StorageDriverInterface;

interface StorageInterface extends StorageCRUDInterface {

    /**
     * @param StorageDriverInterface $driver
     * @return StorageInterface|$this
     */
    public function setDriver(StorageDriverInterface $driver);
}