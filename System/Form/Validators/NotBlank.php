<?php

namespace System\Form\Validators;

use System\Form\FormValidator;

class NotBlank extends FormValidator
{
    /**
     * @return bool
     */
    public function validate()
    {
        return false === empty($this->value);
    }

    /**
     * @return string
     */
    public function getErrorMessage()
    {
        return 'Field may not be empty.';
    }
}