<?php

namespace System\Storage\Drivers\Helpers\MySqlQueryBuilders;

interface QueryBuilderInterface {

    const PARAMETER_VALUES = 'values';
    const PARAMETER_MULTIPLE = 'multiple';
    const PARAMETER_CRITERIA = 'criteria';
    const PARAMETER_FIELDS = 'fields';
    const PARAMETER_LIMIT = 'limit';
    const PARAMETER_OFFSET = 'offset';
    const PARAMETER_ORDER = 'order';

    public function getSupportedAttributes();
}
