<?php

namespace System\Storage\Drivers\Helpers;

use System\Storage\Drivers\Helpers\MySqlQueryBuilders\DeleteQueryBuilder;
use System\Storage\Drivers\Helpers\MySqlQueryBuilders\InsertQueryBuilder;
use System\Storage\Drivers\Helpers\MySqlQueryBuilders\QueryBuilderInterface;
use System\Storage\Drivers\Helpers\MySqlQueryBuilders\SelectQueryBuilder;

class StorageMySqlQueryBuilder implements StorageMySqlQueryBuilderInterface
{
    /** @var string */
    private $tableName;

    /** @var array */
    private $selectFields;

    /** @var array */
    private $criteria;

    /** @var array */
    private $values;

    /** @var array */
    private $multiple = [];

    /** @var string */
    private $query;

    /** @var int */
    private $limit = 0;

    /** @var int */
    private $offset = 0;

    /** @var array */
    private $order = [];

    /** @var string */
    private $queryType;

    /** @var \mysqli */
    private $db;

    /**
     * @param  \mysqli $db
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
        return $this->setTable($tableName);
    }

    /**
     * @param string $tableName
     * @return StorageMySqlQueryBuilder
     */
    public function into($tableName)
    {
        return $this->setTable($tableName);
    }

    private function setTable($tableName)
    {
        if (empty($tableName)) {
            throw new \RuntimeException('Table name may not be empty');
        }

        $this->tableName = trim($tableName, '`');

        return $this;
    }

    public function select(array $fields = [])
    {
        $this->queryType = StorageMySqlQueryBuilderInterface::QUERY_TYPE_SELECT;

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

    public function insert(array $values, array $multiple = [])
    {
        $this->queryType = StorageMySqlQueryBuilderInterface::QUERY_TYPE_INSERT;

        $this->values = $values;
        $this->multiple = $multiple;

        return $this;
    }

    public function delete(array $data = [], array $notData = [])
    {
        if (false === empty($data)) {
            $this->where($data);
        }

        if (false === empty($notData)) {
            $this->whereNot($notData);
        }

        $this->queryType = StorageMySqlQueryBuilderInterface::QUERY_TYPE_DELETE;

        return $this;
    }

    public function where(array $criteria)
    {
        $this->criteria['where'] = [];



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
     * @param array $order
     * @return $this
     */
    public function setOrder(array $order = [])
    {
        $this->order = $order;

        return $this;
    }

    /**
     * @param  int $limit
     * @return StorageMySqlQueryBuilderInterface|$this
     */
    public function setLimit($limit)
    {
        $this->limit = intval($limit);

        return $this;
    }

    /**
     * @param  int $offset
     * @return StorageMySqlQueryBuilderInterface|$this
     */
    public function setOffset($offset)
    {
        $this->offset = intval($offset);

        return $this;
    }

    /**
     * @return $this
     */
    public function build()
    {
        $this->validateTableName();

        switch ($this->queryType) {
            case StorageMySqlQueryBuilderInterface::QUERY_TYPE_SELECT:
                $this->query = $this->buildSelectQuery();
                break;
            case StorageMySqlQueryBuilderInterface::QUERY_TYPE_INSERT:
                $this->query = $this->buildInsertQuery();
                break;
            case StorageMySqlQueryBuilderInterface::QUERY_TYPE_UPDATE:
                break;
            case StorageMySqlQueryBuilderInterface::QUERY_TYPE_DELETE:
                $this->query = $this->buildDeleteQuery();
                break;
            default:
                throw new \RuntimeException("Unknown query type '{$this->queryType}'");
        }

        $this->query .= ';';

        return $this;
    }

    /**
     * @return string
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * @return string
     */
    private function buildSelectQuery()
    {
        $queryBuilder = new SelectQueryBuilder();
        $queryBuilder->setTable($this->tableName);
        $query = $queryBuilder->build([
            QueryBuilderInterface::PARAMETER_CRITERIA => $this->criteria,
            QueryBuilderInterface::PARAMETER_FIELDS => $this->selectFields,
            QueryBuilderInterface::PARAMETER_ORDER => $this->order,
            QueryBuilderInterface::PARAMETER_LIMIT => $this->limit,
            QueryBuilderInterface::PARAMETER_OFFSET => $this->offset,
        ]);
        
        return $query;
    }

    /**
     * 
     * @return string
     */
    private function buildInsertQuery()
    {
        $queryBuilder = new InsertQueryBuilder();
        $queryBuilder->setTable($this->tableName);
        $query = $queryBuilder->build([
            QueryBuilderInterface::PARAMETER_VALUES  => $this->values,
            QueryBuilderInterface::PARAMETER_MULTIPLE => $this->multiple,
        ]);
        
        return $query;
    }

    /**
     * @return string
     */
    private function buildDeleteQuery()
    {
        $queryBuilder = new DeleteQueryBuilder();
        $queryBuilder->setTable($this->tableName);
        $query = $queryBuilder->build([
            QueryBuilderInterface::PARAMETER_CRITERIA => $this->criteria,
        ]);

        return $query;
    }

    /**
     * @throws \RuntimeException
     */
    private function validateTableName()
    {
        if (empty($this->tableName)) {
            throw new \RuntimeException('Table name may not be empty');
        }
    }
}
