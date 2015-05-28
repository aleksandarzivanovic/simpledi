<?php

namespace System\Storage\Drivers\Helpers;

class StorageMySqlQueryBuilder implements StorageMySqlQueryBuilderInterface
{
    /** @var string */
    private $tableName;

    /** @var array */
    private $selectFields;

    /** @var array */
    private $criteria;

    /** @var string */
    private $query;

    /** @var int */
    private $limit = 0;

    /** @var int */
    private $offset = 0;

    /** @var string */
    private $queryType;

    /** @var \mysqli */
    private $db;

    /**
     * @param  \mysqli                        $db
     * @return StorageMySqlQueryBuilder|$this
     */
    public function __construct(\mysqli $db)
    {
        $this->db = $db;

        return $this;
    }

    /**
     * @param string $tableName
     *
     * @return StorageMySqlQueryBuilderInterface|$this
     */
    public function from($tableName)
    {
        if (empty($tableName)) {
            throw new \RuntimeException('Table name may not be empty');
        }

        $this->tableName = trim($tableName, '`');

        return $this;
    }

    public function select(array $fields = [])
    {
        $this->queryType = self::QUERY_TYPE_SELECT;

        foreach ($fields as $index => $field) {
            if (is_array($field)) {
                $fields[$index] = '`'.trim($field[0], '`').'` as '.trim($field[1], '`');
            } else {
                $fields[$index] = '`'.trim($field, '`').'`';
            }
        }

        $this->selectFields = $fields;

        return $this;
    }

    public function where(array $criteria)
    {
        foreach ($criteria as $column => $value) {
            $this->criteria['where'][$column] = $this->cleanCriteria($value);
        }

        return $this;
    }

    public function whereNot(array $criteria)
    {
        foreach ($criteria as $column => $value) {
            $this->criteria['where_not'][$column] = $this->cleanCriteria($value);
        }

        return $this;
    }

    private function cleanCriteria($criteria)
    {
        if (is_numeric($criteria)) {
            if (false !== strpos($criteria, '.')) {
                $criteria = floatval($criteria);
            } else {
                $criteria = intval($criteria);
            }
        } elseif (is_array($criteria)) {
            $criteria = json_encode($criteria);
        } elseif (is_string($criteria)) {
            $criteria = $this->db->escape_string($criteria);
        }

        return $criteria;
    }

    /**
     * @param  int                                     $limit
     * @return StorageMySqlQueryBuilderInterface|$this
     */
    public function setLimit($limit)
    {
        $this->limit = intval($limit);

        return $this;
    }

    /**
     * @param  int                                     $offset
     * @return StorageMySqlQueryBuilderInterface|$this
     */
    public function setOffset($offset)
    {
        $this->offset = intval($offset);

        return $this;
    }

    private function buildFields()
    {
        $fields = '*';

        if (false == empty($this->selectFields)) {
            $fields = implode(',', $this->selectFields);
        }

        return $fields;
    }

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

            $criteria .= ' AND '.implode(' AND ', $whereNot);
        }

        return $criteria;
    }

    private function buildSingleCriteria($column, $value, $operator = '=')
    {
        return "`{$column}` {$operator} '$value'";
    }

    public function build($limit = 0, $offset = 0)
    {
        switch ($this->queryType) {

            case self::QUERY_TYPE_SELECT:
                $this->query = $this->buildSelectQuery();
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

        return $this;
    }

    public function getQuery()
    {
        return $this->query;
    }

    private function buildSelectQuery()
    {
        $this->validateTableName();
        $fields = $this->buildFields();

        $query = "SELECT {$fields} FROM `{$this->tableName}`";

        $criteria = $this->buildCriteria();
        if (false == empty($criteria)) {
            $query .= " WHERE {$criteria}";
        }

        if ($this->limit) {
            $query .= " LIMIT {$this->offset}, {$this->limit}";
        }

        return $query;
    }

    private function validateTableName()
    {
        if (empty($this->tableName)) {
            throw new \RuntimeException('Table name may not be empty');
        }
    }
}
