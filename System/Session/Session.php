<?php

namespace System\Session;

class Session implements SessionInterface
{
    /** @var array */
    private $values = [];

    /** @var array */
    private $getOneTimeValues = [];

    /** @var array */
    private $setOneTimeValues = [];

    public function __construct()
    {
        $this->populateSessions();
    }

    /**
     * @param string $name
     * @param mixed  $value
     *
     * @return SessionInterface|$this
     */
    public function add($name, $value = null)
    {
        if (is_array($name)) {
            $this->addMore($name);
        } else {
            $this->values[$name] = $value;
        }

        return $this;
    }

    /**
     * @param array $values
     *
     * @return $this
     */
    private function addMore(array $values)
    {
        foreach ($values as $name => $value) {
            $this->values[$name] = $value;
        }
    }

    /**
     * @param string $name
     *
     * @return mixed
     */
    public function get($name)
    {
        if (is_array($name)) {
            return $this->getMore($name);
        }

        return $this->values[$name];
    }

    /**
     * @param array $names
     *
     * @return array
     */
    private function getMore(array $names)
    {
        $values = [];

        foreach ($names as $name) {
            $values[] = $this->values[$name];
        }

        return $values;
    }

    /**
     * @return array
     */
    public function getAll()
    {
        return $this->values;
    }

    /**
     * @param string $name
     *
     * @return mixed
     */
    public function getOneTime($name)
    {
        if (is_array($name)) {
            return $this->getMoreOneTime($name);
        }

        if (false == empty($this->getOneTimeValues[$name])) {
            return $this->getOneTimeValues[$name];
        } elseif (false == empty($this->setOneTimeValues[$name])) {
            return $this->setOneTimeValues[$name];
        }
    }

    /**
     * @param array $names
     *
     * @return array
     */
    private function getMoreOneTime(array $names)
    {
        $values = [];

        foreach ($names as $name) {
            $value = $this->getOneTime($name);
            if ($value) {
                $values[] = $value;
            }
        }

        return $values;
    }

    /**
     * @param string $name
     * @param string $value
     *
     * @return SessionInterface
     */
    public function addOneTime($name, $value = null)
    {
        if (is_array($name)) {
            $this->addMoreOneTime($name);
        } else {
            $this->setOneTimeValues[$name] = $value;
        }

        return $this;
    }

    /**
     * @param array $values
     */
    private function addMoreOneTime(array $values)
    {
        foreach ($values as $name => $value) {
            $this->addOneTime($name, $value);
        }
    }

    /**
     * @return array
     */
    public function getAllOneTime()
    {
        return array_merge($this->getOneTimeValues, $this->setOneTimeValues);
    }

    private function populateSessions()
    {
        if (false == empty($_SESSION[self::ONE_TIME])) {
            foreach ($_SESSION[self::ONE_TIME] as $name => $value) {
                $this->getOneTimeValues[$name] = $value;
            }

            $_SESSION[self::ONE_TIME] = [];
        }

        if (false == empty($_SESSION[self::NORMAL])) {
            foreach ($_SESSION[self::NORMAL] as $name => $value) {
                $this->values[$name] = $value;
            }
        }
    }

    public function persistSession()
    {
        $_SESSION = [];

        foreach ($this->setOneTimeValues as $name => $value) {
            $_SESSION[self::ONE_TIME][$name] = $value;
        }

        foreach ($this->values as $name => $value) {
            $_SESSION[self::NORMAL][$name] = $value;
        }
    }
}
