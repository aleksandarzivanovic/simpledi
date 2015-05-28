<?php

namespace System\Storage\Drivers\Helpers\MySqlQueryBuilders;


class InsertQueryBuilder
{
    /** @var string */
    private $tableName;

    /**
     * @param string $tableName
     * @param array $values
     * @param array $multiple
     * @return string
     */
    public function buildQuery($tableName, array $values, array $multiple = [])
    {
        $this->setTableName($tableName);

        return $this->buildValues($values, $multiple);
    }

    private function buildValues(array $values, array $multiple = [])
    {
        $query = "INSERT INTO `{$this->tableName}` ";

        if (empty($multiple)) {
            $query .= $this->buildValuesForSingle($values);
        } else {
            $query .= $this->buildValuesForMultiple($values, $multiple);
        }

        return $query;
    }

    private function buildValuesForSingle(array $values)
    {
        $insertValues = [];

        foreach ($values as $field => $value) {
            $insertValues[] = "`{$field}` = '{$value}'";
        }

        return 'SET ' . implode(',', $insertValues);
    }

    private function buildValuesForMultiple(array $fields, array $data)
    {
        $values = [];

        foreach ($data as $value) {
            $values[] = "'" . implode("','", $value) . "'";
        }

        return '(`' . implode('`,`', $fields) . '`) VALUES (' . implode('),(', $values) . ')';
    }

    private function setTableName($tableName)
    {
        if (empty($tableName)) {
            throw new \RuntimeException('Table name may not be empty.');
        }

        $this->tableName = $tableName;
    }
}