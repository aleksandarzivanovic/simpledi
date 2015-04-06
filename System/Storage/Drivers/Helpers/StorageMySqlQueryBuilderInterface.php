<?php

namespace System\Storage\Drivers\Helpers;


interface StorageMySqlQueryBuilderInterface {
    const QUERY_TYPE_SELECT = 'select';
    const QUERY_TYPE_INSERT = 'insert';
    const QUERY_TYPE_UPDATE = 'update';
    const QUERY_TYPE_DELETE = 'delete';
}