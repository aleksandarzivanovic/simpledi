<?php

namespace System\Session;

interface SessionInterface
{
    const ONE_TIME  =   'onetime';
    const NORMAL    =   'normal';

    /**
     * @param string|array $name
     * @param mixed $value
     *
     * @return SessionInterface
     */
    public function add($name, $value = null);

    /**
     * @param string|array $name
     *
     * @return mixed
     */
    public function get($name);

    /**
     * @return array
     */
    public function getAll();

    /**
     * @param string $name
     *
     * @return mixed
     */
    public function getOneTime($name);

    /**
     * @param string $name
     * @param string $value
     *
     * @return mixed
     */
    public function addOneTime($name, $value = null);

    /**
     * @return array
     */
    public function getAllOneTime();

    public function persistSession();
}
