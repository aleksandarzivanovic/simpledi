<?php

namespace System\Storage\Drivers\Helpers\MySqlQueryBuilders;

class SelectQueryBuilder
{

    /** @var string */
    private $tableName;

    /** @var array */
    private $criteria = [];

    /** @var array */
    private $fields = [];

    /** @var null|int */
    private $limit = null;

    /** @var int */
    private $offset = 0;

    /**
     * @param $tableName
     * @param array $criteria
     * @param array $fields
     * @param null $limit
     * @param int $offset
     * @return string
     */
    public function buildQuery($tableName, array $criteria = [], array $fields = [], $limit = null, $offset = 0)
    {
        $this->prepareQueryBuilder($tableName, $criteria, $fields, $limit, $offset);

        $builtCriteria = $this->buildCriteria();
        $builtFields = $this->buildFields();

        $query = "SELECT {$builtFields} FROM `{$this->tableName}`";

        if (false == empty($builtCriteria)) {
            $query .= " WHERE {$builtCriteria}";
        }

        if ($this->limit) {
            $query .= " LIMIT {$this->offset}, {$this->limit}";
        }

        return $query;
    }

    /**
     * @param $tableName
     * @param array $criteria
     * @param array $fields
     * @param null $limit
     * @param int $offset
     */
    private function prepareQueryBuilder($tableName, array $criteria = [], array $fields = [], $limit = null, $offset = 0)
    {
        $this->setTableName($tableName);
        $this->setCriteria($criteria);
        $this->setFields($fields);

        $this->limit = $limit;
        $this->offset = $offset;
    }

    /**
     * @return string
     */
    private function buildCriteria()
    {
        $criteria = '';

        if (false == empty($this->criteria['where'])) {
            $where = [];
            foreach ($this->criteria['where'] as $column => $value) {
                $where[] = $this->buildSingleCriteria($column, $value);
            }

            $criteria .= implode(' AND ', $where);
        }

        if (false == empty($this->criteria['where_not'])) {
            $whereNot = [];
            foreach ($this->criteria['where_not'] as $column => $value) {
                $whereNot[] = $this->buildSingleCriteria($column, $value, '!=');
            }

            $criteria .= ' AND ' . implode(' AND ', $whereNot);
        }

        return $criteria;
    }

    /**
     * @param string|int|null $column
     * @param string|int|null $value
     * @param string $operator
     * @return string
     */
    private function buildSingleCriteria($column, $value, $operator = '=')
    {
        return "`{$column}` {$operator} '$value'";
    }

    /**
     * @return string
     */
    private function buildFields()
    {
        $fields = '*';

        if (false == empty($this->fields)) {
            $fields = implode(',', $this->fields);
        }

        return $fields;
    }

    /**
     * @param array $criteria
     */
    private function setCriteria(array $criteria = [])
    {
        $this->criteria = $criteria;
    }

    /**
     * @param array $fields
     */
    private function setFields(array $fields = [])
    {
        $this->fields = $fields;
    }

    /**
     * @param $tableName
     */
    private function setTableName($tableName)
    {
        if (empty($tableName)) {
            throw new \RuntimeException('Table name may not be empty.');
        }

        $this->tableName = $tableName;
    }
}