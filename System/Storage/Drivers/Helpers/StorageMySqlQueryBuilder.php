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
    public function __construct($tableName)
    {
        if (empty($tableName)) {
            throw new \RuntimeException('Table name may not be empty');
        }

        $this->tableName = trim($tableName, '`');

        return $this;
    }

    public function select(array $fields)
    {
        $this->queryType = self::QUERY_TYPE_SELECT;

        foreach($fields as $index => $field) {
            $fields[$index] = trim($field, '`');
        }

        $this->selectFields = $fields;

    }

    public function where(array $criteria)
    {
        $this->criteria = $criteria;
    }

    public function build()
    {
        switch ($this->queryType) {
            case self::QUERY_TYPE_SELECT:
                $this->query = 'SELECT `' . implode('`,`', $this->selectFields) . ' FROM `' . $this->tableName . '`';
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
    }
}