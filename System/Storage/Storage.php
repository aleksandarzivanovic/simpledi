<?php

namespace System\Storage;

use System\Storage\Drivers\StorageDriverInterface;

class Storage implements StorageInterface {

    /** @var StorageDriverInterface */
    private $driver;

    /**
     * 
     * @param StorageDriverInterface $driver
     * @return Storage
     */
    public function __construct(StorageDriverInterface $driver) {
        $this->driver = $driver;

        return $this;
    }

    /**
     * 
     * @return StorageDriverInterface
     */
    public function getDriver() {
        return $this->driver;
    }

    /**
     * 
     * @param string $table
     * @param array $criteria
     * @param array $orderBy
     * @param int $offset
     * @param int $limit
     * @return array
     */
    public function get($table, array $criteria, array $orderBy = null, $offset = null, $limit = null) {
        return $this->driver->get($table, $criteria, $orderBy, $offset, $limit);
    }

    /**
     * 
     * @param string $table
     * @param array $criteria
     * @param array $orderBy
     * @param type $offset
     * @param type $limit
     * @return array
     */
    public function getAll($table, array $criteria, array $orderBy = null, $offset = null, $limit = null) {
        return $this->driver->getAll($table, $criteria, $orderBy, $offset, $limit);
    }

    /**
     * 
     * @param string $table
     * @param array $values
     * @return array
     */
    public function insert($table, array $values) {
        return $this->driver->insert($table, $values);
    }

    /**
     * 
     * @param string $table
     * @param array $values
     * @return array
     */
    public function insertAll($table, array $values) {
        return $this->driver->insertAll($table, $values);
    }

}
