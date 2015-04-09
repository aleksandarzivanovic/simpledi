<?php

namespace System\Di;

interface DiInterface
{
    /**
     * @param string $class
     * @param array  $customArguments
     *
     * @return object
     */
    public function getDefault($class, array $customArguments);

    /**
     * @param string $class
     * @param array  $customArguments
     *
     * @return object
     */
    public function getShared($class, array $customArguments);

    /**
     * @return Di
     */
    public function reload();
}
