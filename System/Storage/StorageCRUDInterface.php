<?php

namespace System\Storage;


interface StorageCRUDInterface {
    /**
     * @param array $criteria
     * @param array $order
     * @param int $offset
     * @param int $limit
     * @return StorageResultInterface result
     */
    public function get(array $criteria, array $order, $offset, $limit);

    /**
     * @param array $criteria
     * @param array $order
     * @param int $offset
     * @param int $limit
     * @return StorageResultInterface[] array of results
     */
    public function getAll(array $criteria, array $order, $offset, $limit);

    /**
     * @param array $data
     * @return int Insert id or affected rows
     */
    public function insert(array $data);

    /**
     * @param array $data
     * @return int[]|int array of inserted ids or number of affected rows
     */
    public function insertAll(array $data);

    /**
     * @param array $data
     * @return int number of affected rows
     */
    public function update(array $data);

    /**
     * @param array $data
     * @return int number of affected rows
     */
    public function updateAll(array $data);

    /**
     * @param array $data
     * @return int number of deleted rows
     */
    public function delete(array $data);

}