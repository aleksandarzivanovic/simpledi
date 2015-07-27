<?php

namespace System\Storage\Drivers\Helpers\MySqlQueryBuilders;

class SelectQueryBuilder extends QueryBuilderCore
{


    /** @var array */
    private $criteria = [];

    /** @var array */
    private $fields = [];

    /** @var array */
    private $order = [];

    /** @var null|int */
    private $limit = null;

    /** @var int */
    private $offset = 0;

    /**
     * @return string
     */
    protected function buildQuery()
    {
        $this->prepareQueryBuilder(
                $this->attr(QueryBuilderInterface::PARAMETER_CRITERIA),
                $this->attr(QueryBuilderInterface::PARAMETER_FIELDS),
                $this->attr(QueryBuilderInterface::PARAMETER_ORDER),
                $this->attr(QueryBuilderInterface::PARAMETER_LIMIT),
                $this->attr(QueryBuilderInterface::PARAMETER_OFFSET)
            );

        $builtCriteria = $this->buildCriteria();
        $builtOrder = $this->buildOrder();
        $builtFields = $this->buildFields();

        $query = "SELECT {$builtFields} FROM `{$this->tableName}`";

        if (false == empty($builtCriteria)) {
            $query .= " WHERE {$builtCriteria}";
        }

        if (false == empty($builtOrder)) {
            $query .= " ORDER BY {$builtOrder}";
        }

        if ($this->limit) {
            $query .= " LIMIT {$this->offset}, {$this->limit}";
        }

        return $query;
    }

    /**
     * @param array $criteria
     * @param array $fields
     * @param array $order
     * @param null $limit
     * @param int $offset
     */
    private function prepareQueryBuilder(array $criteria = [], array $fields = [], array $order = [], $limit = null, $offset = 0)
    {
        $this->setCriteria($criteria);
        $this->setFields($fields);
        $this->setOrder($order);

        $this->limit = $limit;
        $this->offset = $offset;
    }

    /**
     * @return string
     */
    private function buildOrder()
    {
        if (empty ($this->order)) {
            return '';
        }

        $order = [];

        foreach ($this->order as $field => $direction) {
            $direction = strtoupper($direction);

            if (strcmp($direction, 'ASC') && strcmp($direction, 'DESC')) {
                throw new \RuntimeException("Unknown order direction '{$direction}'");
            }

            $order[] = "`{$field}` {$direction}";
        }

        return implode(', ', $order);
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
     * @param array $order
     * @return string
     */
    private function setOrder(array $order = [])
    {
        $this->order = $order;

        return $this;
    }

    public function getSupportedAttributes()
    {
        return [
            QueryBuilderInterface::PARAMETER_CRITERIA => 'array',
            QueryBuilderInterface::PARAMETER_FIELDS => 'array',
            QueryBuilderInterface::PARAMETER_LIMIT => 'number',
            QueryBuilderInterface::PARAMETER_OFFSET => 'number',
            QueryBuilderInterface::PARAMETER_ORDER => 'array',
        ];
    }

}