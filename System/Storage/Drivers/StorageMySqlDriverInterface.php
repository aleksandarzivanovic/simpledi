<?php

namespace System\Storage\Drivers;

interface StorageMySqlDriverInterface extends StorageDriverInterface
{
    /**
     * @param  string                 $tableName
     * @return StorageInterface|$this
     */
    public function setTableName($tableName);
}
