<?php

namespace System\Form;

class Form
{
    const METHOD_POST = INPUT_POST;
    const METHOD_GET = INPUT_GET;

    /** @var string */
    private $name;

    /** @var array */
    private $fields = [];

    /** @var array */
    private $validationFields = [];

    /** @var bool */
    private $error = false;

    /** @var array */
    private $attr = [];

    /** @var array */
    private $method = [];

    /** @var \stdClass */
    private $model;

    private $snippets = array(
        'input' => '{errors}<input name="{name}" type="{type}" value="{value}" {required} {attr} />',
        'textarea' => '{errors}<textarea name="{name}" {attr} {required}>{value}</textarea>',
    );

    /**
     * @param $name
     * @param array $fields
     * @param int $method
     * @throws \RuntimeException
     */
    public function __construct($name, array $fields, $method = self::METHOD_POST)
    {
        if (empty($name) || false === is_string($name)) {
            throw new \RuntimeException('Form name must be defined and must be string.');
        }

        $this->name = $name;

        foreach ($fields as $field => $value) {
            if (empty($value['type'])) {
                $value['type'] = 'text';
            }

            if (empty($value['collection'])) {
                $this->fields[$field] = $value;
            } else {
                $this->fields[$field] = $value;
                $this->fields[$field]['fields'][] = $value;
            }
        }

        if (self::METHOD_GET === $method) {
            $this->method['type'] = 'get';
        } else {
            $this->method['type'] = 'post';
        }

        $this->method['identifier'] = $method;
    }

    /**
     * @param string $field
     * @return null|string
     */
    public function getFieldValue($field)
    {
        return isset($this->validationFields[$field]) ? $this->validationFields[$field] : null;
    }

    /**
     * @param string $field
     * @return null|string
     */
    public function getDefaultFieldValue($field)
    {
        return isset($this->fields[$field]) ? $this->fields[$field] : null;
    }

    /**
     * @param bool $createModel
     * @return bool
     */
    public function validate($createModel = true)
    {
        $input = filter_input_array(INPUT_POST);
        $this->validationFields = isset($input["form_{$this->name}"]) ? $input["form_{$this->name}"] : [];

        if (empty($this->validationFields)) {
            return false;
        }

        foreach ($this->validationFields as $field => $value) {
            if (false === isset($this->fields[$field])) {
                continue;
            }

            if (is_array($value)) {
                $f = $this->fields[$field];

                foreach ($value as $key => $v) {
                    $this->fields[$field]['fields'][$key] = $f;
                    $this->fields[$field]['fields'][$key]['value'] = $v;
                }
            } else {
                $this->fields[$field]['value'] = $value;
            }
        }

        foreach ($this->fields as $field => $data) {
            $this->validateField($field, $data);
        }

        if (false === $this->error && $createModel) {
            $this->createModel();
        }

        return false === $this->error;
    }

    /**
     * @return \stdClass
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @return string
     */
    public function open()
    {
        return "<form method=\"{$this->method['type']}\" name=\"form_{$this->name}\">";
    }

    /**
     * @return string
     */
    public function close()
    {
        return '</form>';
    }

    /**
     * @param null $field
     * @return string
     */
    public function render($field = null)
    {
        if ($field) {
            return $this->renderField($field);
        } else {
            return $this->renderAll();
        }
    }

    /**
     * @param $field
     * @return mixed|string
     */
    private function renderField($field)
    {
        if (false === isset($this->fields[$field])) {
            return '';
        }

        $data = $this->fields[$field];
        $code = '';

        if (empty($data['collection'])) {
            if (false === isset($data['type'])) {
                $data['type'] = $this->fields[$field]['type'] = 'text';
            }

            $code = $this->generateFieldCode($field, $data);
        } else if (false === empty($this->fields[$field]['fields'])) {
            foreach ($this->fields[$field]['fields'] as $index => $collectionField) {
                $code .= $this->generateFieldCode($field, $collectionField);
            }

        }

        return $code;
    }

    /**
     * @return string
     */
    private function renderAll()
    {
        $fields = [];

        foreach ($this->fields as $field => $data) {
            $fields[$field] = $this->renderField($field);
        }

        return $this->open() . implode('<br />',  $fields) . $this->close();
    }

    /**
     * @param string $field
     * @param array $data
     * @return string
     */
    private function generateFieldCode($field, array $data)
    {
        $snippet = $data['type'] == 'textarea' ? $this->snippets['textarea'] : $this->snippets['input'];

        $replaces = [];

        $this->setReplaceRequired($replaces, $data);
        $this->setReplaceValue($replaces, $data);
        $this->setReplaceAttr($replaces, $data);
        $this->setReplaceErrors($replaces, $data, $field);

        $replaces['{name}'] = "form_{$this->name}[{$field}]";

        if (false === empty($data['collection'])) {
            $replaces['{name}'] .= '[]';
        }

        $replaces['{type}'] = $data['type'];

        return str_replace(array_keys($replaces), array_values($replaces), $snippet);
    }

    /**
     * @param array &$replaces
     * @param array $data
     */
    private function setReplaceRequired(&$replaces, array $data)
    {
        if (false === empty($data['required'])) {
            $replaces['{required}'] = 'required="required"';
        } else {
            $replaces['{required}'] = '';
        }
    }

    /**
     * @param array &$replaces
     * @param array $data
     */
    private function setReplaceValue(&$replaces, array $data)
    {
        if (false === empty($data['value'])) {
            $replaces['{value}'] = $data['value'];
        } else {
            $replaces['{value}'] = '';
        }
    }

    /**
     * @param array &$replaces
     * @param array $data
     */
    private function setReplaceAttr(&$replaces, array $data)
    {
        if (false === empty($data['attr'])) {
            array_walk($data['attr'], array($this, 'generateAttributes'));
            $replaces['{attr}'] = implode(' ', $this->attr);
        } else {
            $replaces['{attr}'] = '';
        }
    }

    /**
     * @param array &$replaces
     * @param array $data
     * @param string $field
     */
    private function setReplaceErrors(&$replaces, array $data, $field)
    {
        if (false === empty($data['errors'])) {
            $replaces['{errors}'] = $this->generateErrorsCode($field, $data['errors']);
        } else {
            $replaces['{errors}'] = '';
        }
    }

    /**
     * @param string $field
     * @param array $errors
     * @return string
     */
    private function generateErrorsCode($field, array $errors)
    {
        $errors = implode('</li><li>', $errors);
        return "<span class=\"{$field}_errors\"><ul><li>{$errors}</li></ul></span>";
    }

    /**
     * @param string $value
     * @param string $attribute
     */
    private function generateAttributes($value, $attribute) {
        $this->attr[] = "{$attribute}=\"{$value}\"";
    }

    /**
     *
     * Checks for field requirement and runs all validators
     *
     * @param $field
     * @param array $data
     */
    private function validateField($field, array $data)
    {
        if (empty($data['collection'])) {
            if (false === empty($data['required']) && empty($this->validationFields[$field])) {
                $this->error = true;
                $this->fields[$field]['errors'][] = "Field {$field} is required";
            }

            if (false === empty($data['validators'])) {
                $this->runValidators($field, $data['validators']);
            }
        } else if (false === empty($this->fields[$field]['fields'])) {
            foreach ($this->fields[$field]['fields'] as $index => $data) {
                if (false === empty($data['required']) && empty($this->validationFields[$field][$index])) {
                    $this->error = true;
                    $this->fields[$field]['fields'][$index]['errors'][] = "Field {$field} is required";
                }

                if (false === empty($data['validators'])) {
                    $this->runValidators($field, $data['validators'], $index);
                }
            }
        }
    }

    /**
     *
     * Runs all validators for field
     *
     * @param $field
     * @param FormValidator[] $validators
     * @param int|null $index
     */
    private function runValidators($field, array $validators, $index = null)
    {
        $value = $this->validationFields[$field];

        if (false === is_null($index)) {
            $value = $this->validationFields[$field][$index];
        }

        $this->runFieldValidators($field, $value, $validators, $index);
    }

    /**
     * @param $field
     * @param $value
     * @param FormValidator[] $validators
     * @param int|null $index
     */
    private function runFieldValidators($field, $value, array $validators, $index = null)
    {
        foreach ($validators as $validator) {
            if (false === $validator instanceof FormValidator) {
                continue;
            }

            $validator->setValue($value);

            if (false === $validator->validate()) {
                $this->error = true;

                if (is_null($index)) {
                    $this->fields[$field]['errors'][] = $validator->getErrorMessage();
                } else {
                    $this->fields[$field]['fields'][$index]['errors'][] = $validator->getErrorMessage();
                }
            }
        }
    }

    private function createModel()
    {
        $this->model = new \stdClass();

        foreach ($this->fields as $field => $data) {
            if (empty($data['collection'])) {
                $this->model->{$field} = $this->escapeValue($data['value']);
            } else {
                $this->model->{$field} = [];

                foreach ($data['fields'] as $collectionField) {
                    $this->model->{$field}[] = $this->escapeValue($collectionField['value']);
                }
            }
        }

    }

    /**
     * @param $value
     * @return string
     */
    private function escapeValue($value)
    {
        $search = ["\\",  "\x00", "\n",  "\r",  "'",  '"', "\x1a"];
        $replace = ["\\\\","\\0","\\n", "\\r", "\'", '\"', "\\Z"];

        return str_replace($search, $replace, $value);
    }
}