<?php

namespace System\Storage\Drivers;

use System\Storage\Drivers\Helpers\StorageMySqlQueryBuilder;
use System\Storage\Drivers\Helpers\StorageMySqlQueryBuilderInterface;
use System\Storage\StorageResult;
use System\Storage\StorageResultInterface;

class StorageMySqlDriver implements StorageDriverInterface
{
    /** @var string */
    private $tableName;

    /** @var \mysqli */
    private $db;

    /** @var StorageMySqlQueryBuilderInterface */
    private $queryBuilder;

    /**
     * @param array $criteria
     * @param array $order
     * @param int $offset
     * @param array $fields
     * @return StorageResult
     */
    public function get(array $criteria, array $order = [], $offset = 0, array $fields = [])
    {
        $query = $this->queryBuilder
            ->select($fields)
            ->from($this->tableName)
            ->where($criteria)
            ->setOrder($order)
            ->setLimit(1)
            ->setOffset($offset)
            ->build()
            ->getQuery();

        if (empty($query)) {
            throw new \RuntimeException('Query builder failed to build query.');
        }

        return new StorageResult($this->db->query($query));
    }

    /**
     * @param array $criteria
     * @param array $order
     * @param array $fields
     * @return StorageResult
     */
    public function getAll(array $criteria, array $order = [], array $fields = [])
    {
        $query = $this->queryBuilder
                ->select($fields)
                ->from($this->tableName)
                ->where($criteria)
                ->setOrder($order)
                ->build()
                ->getQuery();
        
        if (empty($query)) {
            throw new \RuntimeException('Query builder failed to build query.');
        }
        
        return new StorageResult($this->db->query($query));
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function insert(array $data)
    {
        $query = $this->queryBuilder->insert($data)->into($this->tableName)->build()->getQuery();
        
        $this->db->query($query);

        return $this->db->insert_id;
    }

    /**
     * @param array $fields
     * @param array $values
     * @return int[]|int number of affected rows
     */
    public function insertAll(array $fields, array $values)
    {
        $query = $this->queryBuilder->insert($fields, $values)->into($this->tableName)->build()->getQuery();
        $this->db->query($query);

        return $this->db->affected_rows;
    }

    /**
     * @param  array $data
     * @return int   number of affected rows
     */
    public function update(array $data)
    {
        // TODO: Implement update() method.
    }

    /**
     * @param  array $data
     * @return int   number of affected rows
     */
    public function updateAll(array $data)
    {
        // TODO: Implement updateAll() method.
    }

    /**
     * @param  array $data
     * @return int   number of deleted rows
     */
    public function delete(array $data, array $not = [])
    {
        $query = $this->queryBuilder->delete($data, $not)->from($this->tableName)->build()->getQuery();
        $this->db->query($query);

        return $this->db->affected_rows;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'mysql';
    }

    public function prepare()
    {
        $parameters = $this->getParameters();

        if (false == isset($parameters[$this->getName()])) {
            throw new \RuntimeException("Unknown driver " . $this->getName());
        }

        $this->connect($parameters[$this->getName()]);
    }

    /**
     *
     * @param  array $parameters
     * @throws \RuntimeException
     */
    private function connect(array $parameters)
    {
        $this->db = new \mysqli($parameters['host'], $parameters['user'], $parameters['password'], $parameters['database'], $parameters['port']);

        if ($this->db->errno) {
            throw new \RuntimeException("Unable to connect to database. Reason[{$this->db->errno}]:{$this->db->error}");
        }

        $this->queryBuilder = new StorageMySqlQueryBuilder($this->db);
    }

    /**
     * @return array
     * @throws \RuntimeException
     */
    private function getParameters()
    {
        if (false == file_exists("Config/data/storage.json")) {
            throw new \RuntimeException('Unable to load storage.json file');
        }

        $json = file_get_contents("Config/data/storage.json");
        $data = json_decode($json, true);

        if (json_last_error() != JSON_ERROR_NONE) {
            throw new \RuntimeException('Invalid json storage.json');
        }

        return $data;
    }

    public function setRepository($name) {
        if (empty($name)) {
            throw new \RuntimeException('Table name may not be empty');
        }

        $this->tableName = $name;

        return $this;
    }

    public function getQuery() {
        return $this->queryBuilder->getQuery();
    }

}
