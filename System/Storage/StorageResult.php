<?php

namespace System\Storage;

class StorageResult extends \ArrayObject implements StorageResultInterface
{
    public function __construct($result)
    {
        $data = [];

        if ($result instanceof \mysqli_result) {
            $data = $result->fetch_assoc();
        }

        parent::__construct($data);
    }

    public function getField($field)
    {
        $array = $this->getArrayCopy();

        if (false == isset($array[$field])) {
            throw new \RuntimeException("Unknown field '{$field}' in result'");
        }

        return $array[$field];
    }
}
