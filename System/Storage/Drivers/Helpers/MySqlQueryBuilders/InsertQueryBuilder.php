<?php

namespace System\Storage\Drivers\Helpers\MySqlQueryBuilders;


class InsertQueryBuilder extends QueryBuilderCore
{
    /**
     * @return string
     */
    protected function buildQuery()
    {
        $query = "INSERT INTO `{$this->tableName}` ";
        
        $values = $this->attr(QueryBuilderInterface::PARAMETER_VALUES);
        $multiple = $this->attr(QueryBuilderInterface::PARAMETER_MULTIPLE);
        
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

    public function getSupportedAttributes() {
        return [
            QueryBuilderInterface::PARAMETER_VALUES => 'array',
            QueryBuilderInterface::PARAMETER_MULTIPLE => 'array',
        ];
    }

}