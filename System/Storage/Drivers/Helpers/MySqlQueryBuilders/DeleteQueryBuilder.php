<?php

namespace System\Storage\Drivers\Helpers\MySqlQueryBuilders;

class DeleteQueryBuilder extends QueryBuilderCore
{
    
    protected function buildQuery()
    {
        $query = "DELETE FROM {$this->tableName}";
        $criteria = $this->attr(QueryBuilderInterface::PARAMETER_CRITERIA);

        if ($criteria) {
            $query .= " WHERE " . $this->buildCriteria($criteria);
        }

        return $query;
    }

    private function buildCriteria(array $criteria)
    {
        if (false === isset($criteria['where'])) {
            $criteria['where'] = [];
        }

        if (false === isset($criteria['where_not'])) {
            $criteria['where_not'] = [];
        }

        foreach ($criteria['where'] as $field => $value) {
            $where[] = "{$field} = '{$value}'";
        }

        foreach ($criteria['where_not'] as $field => $value) {
            $where[] = "{$field} != '{$value}'";
        }

        return implode(' AND ', $where);
    }

    public function getSupportedAttributes()
    {
        return [
            QueryBuilderInterface::PARAMETER_CRITERIA => 'array',
        ];
    }
}