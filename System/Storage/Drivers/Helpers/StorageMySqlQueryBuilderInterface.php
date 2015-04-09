<?php

namespace System\Storage\Drivers\Helpers;

interface StorageMySqlQueryBuilderInterface
{
    const QUERY_TYPE_SELECT = 'select';
    const QUERY_TYPE_INSERT = 'insert';
    const QUERY_TYPE_UPDATE = 'update';
    const QUERY_TYPE_DELETE = 'delete';

    /**
     * @param  string                                  $tableName
     * @return StorageMySqlQueryBuilderInterface|$this
     */
    public function from($tableName);

    /**
     * @param  array                                   $fields
     * @return StorageMySqlQueryBuilderInterface|$this
     */
    public function select(array $fields = []);

    /**
     * @param  array                                   $criteria
     * @return StorageMySqlQueryBuilderInterface|$this
     */
    public function where(array $criteria);

    /**
     * @param  array                                   $criteria
     * @return StorageMySqlQueryBuilderInterface|$this
     */
    public function whereNot(array $criteria);

    /**
     * @param  int                                     $limit
     * @return StorageMySqlQueryBuilderInterface|$this
     */
    public function setLimit($limit);

    /**
     * @param  int                                     $offset
     * @return StorageMySqlQueryBuilderInterface|$this
     */
    public function setOffset($offset);

    /**
     * @return StorageMySqlQueryBuilderInterface|$this
     */
    public function build();

    /**
     * @return string
     */
    public function getQuery();
}
