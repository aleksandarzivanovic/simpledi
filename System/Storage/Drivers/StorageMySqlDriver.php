<?php

namespace System\Storage\Drivers;


use System\Storage\StorageResultInterface;

class StorageMySqlDriver implements StorageMySqlDriverInterface {

    private $tableName;

    /**
     * @param array $criteria
     * @param array $order
     * @param int $offset
     * @param int $limit
     * @return StorageResultInterface result
     */
    public function get(array $criteria, array $order, $offset, $limit)
    {
        // TODO: Implement get() method.
    }

    /**
     * @param array $criteria
     * @param array $order
     * @param int $offset
     * @param int $limit
     * @return StorageResultInterface[] array of results
     */
    public function getAll(array $criteria, array $order, $offset, $limit)
    {
        // TODO: Implement getAll() method.
    }

    /**
     * @param array $data
     * @return int Insert id or affected rows
     */
    public function insert(array $data)
    {
        // TODO: Implement insert() method.
    }

    /**
     * @param array $data
     * @return int[]|int array of inserted ids or number of affected rows
     */
    public function insertAll(array $data)
    {
        // TODO: Implement insertAll() method.
    }

    /**
     * @param array $data
     * @return int number of affected rows
     */
    public function update(array $data)
    {
        // TODO: Implement update() method.
    }

    /**
     * @param array $data
     * @return int number of affected rows
     */
    public function updateAll(array $data)
    {
        // TODO: Implement updateAll() method.
    }

    /**
     * @param array $data
     * @return int number of deleted rows
     */
    public function delete(array $data)
    {
        // TODO: Implement delete() method.
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'mysql';
    }

    /**
     * @param string $tableName
     * @return StorageMySqlDriverInterface|$this
     */
    public function setTableName($tableName)
    {
        if (empty($tableName)) {
            throw new \RuntimeException('Table name may not be empty');
        }

        $this->tableName = $tableName;

        return $this;
    }
}