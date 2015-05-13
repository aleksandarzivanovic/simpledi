<?php

namespace System\Storage;

use System\Storage\Drivers\StorageDriverInterface;

class Storage implements StorageInterface
{
    /** @var StorageDriverInterface */
    private $driver;

    /**
     * @param  StorageDriverInterface $driver
     * @return StorageInterface|$this
     */
    public function setDriver(StorageDriverInterface $driver)
    {
        $name = $driver->getName();

        if (empty($name)) {
            $class = get_class($driver);
            throw new \RuntimeException("Driver name for '{$class}' may not be empty.");
        }

        $this->driver = $driver;
        $this->driver->prepare();

        return $this;
    }

    /**
     * @param  array                  $criteria
     * @param  array                  $order
     * @param  int                    $offset
     * @param  array                  $fields
     * @return StorageResultInterface result
     */
    public function get(array $criteria, array $order = [], $offset = 0, array $fields = [])
    {
        $result = $this->driver->get($criteria, $order, $offset, $fields);
        $this->validateResult($result, 'get');

        return $result;
    }

    /**
     * @param  array                    $criteria
     * @param  array                    $order
     * @param  int                      $offset
     * @param  int                      $limit
     * @param  array                    $fields
     * @return StorageResultInterface[] array of results
     */
    public function getAll(array $criteria, array $order = [], $limit = null, $offset = 0, array $fields = [])
    {
        $results = $this->driver->getAll($criteria, $order, $offset, $limit);
        foreach ($results as $result) {
            $this->validateResult($result, 'getAll');
        }

        return $results;
    }

    /**
     * @param  array $data
     * @return int   Insert id or affected rows
     */
    public function insert(array $data)
    {
        return $this->driver->insert($data);
    }

    /**
     * @param  array     $data
     * @return int[]|int array of inserted ids or number of affected rows
     */
    public function insertAll(array $data)
    {
        return $this->driver->insertAll($data);
    }

    /**
     * @param  array $data
     * @return int   number of affected rows
     */
    public function update(array $data)
    {
        return $this->driver->update($data);
    }

    /**
     * @param  array $data
     * @return int   number of affected rows
     */
    public function updateAll(array $data)
    {
        return $this->driver->updateAll($data);
    }

    /**
     * @param  array $data
     * @return int   number of deleted rows
     */
    public function delete(array $data)
    {
        return $this->driver->delete($data);
    }

    private function validateResult($result, $method)
    {
        if (false == $result instanceof StorageResult) {
            $driverName = $this->driver->getName();
            throw new \RuntimeException("Driver '{$driverName}' method {$method} should return 'StorageResult'");
        }
    }
}
