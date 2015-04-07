<?php

namespace System\Storage\Drivers\Helpers;

class StorageMySqlQueryBuilder implements StorageMySqlQueryBuilderInterface {

    /** @var array */
    private $selectFields;

    /** @var array */
    private $criteria;

    /** @var string */
    private $query;

    /** @var string */
    private $queryType;

    /**
     * @param string $tableName
     * @return StorageMySqlQueryBuilder|$this
     */
    public function __construct($tableName) {
        if (empty($tableName)) {
            throw new \RuntimeException('Table name may not be empty');
        }

        $this->tableName = trim($tableName, '`');

        return $this;
    }

    public function select(array $fields) {
        $this->queryType = self::QUERY_TYPE_SELECT;

        foreach ($fields as $index => $field) {
            if (is_array($field)) {
                $fields[$index] = '`' . trim($field[0], '`') . '` as ' . trim($field[1], '`');
            } else {
                $fields[$index] = '`' . trim($field, '`') . '`';
            }
        }

        $this->selectFields = $fields;
        return $this;
    }

    public function where(array $criteria) {
        foreach ($criteria as $column => $value) {
            $this->criteria['where'][$column] = $this->cleanCriteria($value);
        }

        return $this;
    }

    public function whereNot(array $criteria) {
        foreach ($criteria as $column => $value) {
            $this->criteria['where_not'][$column] = $this->cleanCriteria($value);
        }

        return $this;
    }

    private function cleanCriteria($criteria) {
        if (is_numeric($criteria)) {
            if (false !== strpos($criteria, '.')) {
                $criteria = floatval($criteria);
            } else {
                $criteria = intval($criteria);
            }
        } else if (is_array($criteria)) {
            $criteria = json_encode($criteria);
        } else if (is_string($criteria)) {
            $criteria = mysqli_real_escape_string($criteria);
        }

        return $criteria;
    }

    private function buildFields() {
        $fields = '*';

        if (false == empty($this->selectFields)) {
            $fields = '`' . implode('`,`', $this->selectFields) . '`';
        }

        return $fields;
    }

    private function buildCriteria() {
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

    private function buildSingleCriteria($column, $value, $operator = '=') {
        return "`{$column}` {$operator} '$value'";
    }

    public function build($table, $limit = 0, $offset = 0) {
        $criteria = $this->buildCriteria();

        switch ($this->queryType) {

            case self::QUERY_TYPE_SELECT:
                $fields = $this->buildFields();
                $this->query = "SELECT {$fields} FROM `{$table}`";

                if ($criteria) {
                    $this->query .= " WHERE {$criteria}";
                }

                if ($limit || $offset) {
                    $this->query .= " LIMIT {$offset}, {$limit}";
                }

                break;
            case self::QUERY_TYPE_INSERT:
                break;
            case self::QUERY_TYPE_UPDATE:
                break;
            case self::QUERY_TYPE_DELETE:
                break;
            default:
                throw new \RuntimeException("Unknown query type '{$this->queryType}'");
        }

        $this->query .= ';';
    }

}
