<?php

namespace System\Form\Validators;

use System\Form\FormValidator;

class Numeric extends FormValidator
{
    /**
     * @return bool
     */
    public function validate()
    {
        return is_numeric($this->value);
    }

    /**
     * @return string
     */
    public function getErrorMessage()
    {
        return 'Field must be numeric';
    }
}