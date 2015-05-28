<?php

namespace System\Storage;

interface StorageCRUDInterface
{
    /**
     * @param  array $criteria
     * @param  array $order
     * @param  int $offset
     * @param  int $limit
     * @param  array $fields
     * @return StorageResultInterface result
     */
    public function get(array $criteria, array $order = [], $offset = 0, array $fields = []);

    /**
     * @param  array $criteria
     * @param  array $order
     * @param  int $offset
     * @param  int $limit
     * @param  array $fields
     * @return StorageResultInterface[] array of results
     */
    public function getAll(array $criteria, array $order = [], $limit = null, $offset = 0, array $fields = []);

    /**
     * @param  array $values
     * @return int   Insert id or affected rows
     */
    public function insert(array $values);

    /**
     * @param  array $fields
     * @param array $values
     * @return int[]|int array of inserted ids or number of affected rows
     */
    public function insertAll(array $fields, array $values);

    /**
     * @param  array $data
     * @return int   number of affected rows
     */
    public function update(array $data);

    /**
     * @param  array $data
     * @return int   number of affected rows
     */
    public function updateAll(array $data);

    /**
     * @param  array $data
     * @return int   number of deleted rows
     */
    public function delete(array $data);
}
