<?php

namespace System\Storage\Drivers;

interface StorageMySqlDriverInterface extends StorageDriverInterface
{
    /**
     * @param  string                 $tableName
     * @return StorageMySqlDriverInterface|$this
     */
    public function setTableName($tableName);
}
