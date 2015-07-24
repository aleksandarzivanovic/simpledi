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
     * @param string $name
     * @return $this
     */
    public function setRepository($name) {
        $this->driver->setRepository($name);

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
     * @param array $criteria
     * @param array $order
     * @param array $fields
     * @return StorageResultInterface[]
     */
    public function getAll(array $criteria, array $order = [], array $fields = [])
    {
        $results = $this->driver->getAll($criteria, $order, $fields);
        
        foreach ($results as $result) {
            $this->validateResult($result, 'getAll');
        }

        return $results;
    }

    /**
     * @param array $data
     * @return int
     */
    public function insert(array $data)
    {
        return $this->driver->insert($data);
    }

    /**
     * @param array $fields
     * @param array $values
     * @return int|\int[]
     */
    public function insertAll(array $fields, array $values)
    {
        return $this->driver->insertAll($fields, $values);
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
     * @param array $data
     * @param array $notData
     * @return int
     */
    public function delete(array $data = [], array $notData = [])
    {
        return $this->driver->delete($data, $notData);
    }

    private function validateResult($result, $method)
    {
        if (false == $result instanceof StorageResult) {
            $driverName = $this->driver->getName();
            throw new \RuntimeException("Driver '{$driverName}' method {$method} should return 'StorageResult'");
        }
    }

    /**
     * 
     * @return string
     */
    public function getQuery() {
        return $this->driver->getQuery();
    }

}
