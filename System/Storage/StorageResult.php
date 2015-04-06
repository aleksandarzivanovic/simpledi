<?php

namespace System\Storage;

class StorageResult extends \ArrayObject implements StorageResultInterface {

    public function __construct(array $result)
    {
        parent::__construct($result);
    }

    public function getField($field)
    {
        if (false == $this[$field]) {
            throw new \RuntimeException("Unknown field '{$field}' in result'");
        }

        return $this[$field];
    }
}