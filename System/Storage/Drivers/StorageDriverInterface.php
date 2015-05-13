<?php

namespace System\Storage\Drivers;

use System\Storage\StorageCRUDInterface;

interface StorageDriverInterface extends StorageCRUDInterface
{
    /**
     * @return string
     */
    public function getName();

    public function prepare();
}
