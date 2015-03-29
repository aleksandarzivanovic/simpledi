<?php

namespace System\Storage;

use System\Storage\Drivers\StorageDriverInterface;

interface StorageInterface {

    /**
     * @param string $table
     * @param array $criteria
     * @param array $orderBy
     * @param int $offset
     * @param int $limit
     * @return array
     */
    public function get($table, array $criteria, array $orderBy = null, $offset = null, $limit = null);

    /**
     * @param string $table
     * @param array $criteria
     * @param array $orderBy
     * @param int $offset
     * @param int $limit
     * @return array
     */
    public function getAll($table, array $criteria, array $orderBy = null, $offset = null, $limit = null);

    /**
     * @param string $table
     * @param array $values
     * @return int id/number of affected rows
     */
    public function insert($table, array $values);

    /**
     * 
     * @param string $table
     * @param array $values
     */
    public function insertAll($table, array $values);

    /**
     * @return StorageDriverInterface
     */
    public function getDriver();
}
