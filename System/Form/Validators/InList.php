<?php

namespace System\Form\Validators;

use System\Form\FormValidator;

class InList extends FormValidator
{
    /** @var array */
    private $items = [];

    /** @var bool */
    private $strict = false;

    public function __construct(array $list, $strict = false)
    {
        $this->items = $list;
        $this->strict = $strict;
    }

    /**
     * @return bool
     */
    public function validate()
    {
        return in_array($this->value, $this->items, $this->strict);
    }

    /**
     * @return string
     */
    public function getErrorMessage()
    {
        return "{$this->value} not found ind list";
    }
}