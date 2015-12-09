<?php

namespace System\Form;

abstract class FormValidator
{

    /** @var string */
    protected $value;

    /** @var Form */
    protected $form;

    /** @var string */
    public $errorMessage;

    /**
     * 
     * @param string $errorMessage
     */
    public function __construct($errorMessage = null)
    {
        $this->setErrorMessage($errorMessage);
    }

    /**
     * @param string $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * @param Form $form
     */
    public function setForm(Form $form)
    {
        $this->form = $form;
    }

    /**
     * 
     * @param string $message
     */
    public function setErrorMessage($message)
    {
        $this->errorMessage = $message;
    }

    /**
     * @return bool
     */
    public abstract function validate();

    /**
     * @return string
     */
    public abstract function getErrorMessage();
}
