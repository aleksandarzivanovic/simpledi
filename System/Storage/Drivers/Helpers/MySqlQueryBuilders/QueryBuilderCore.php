<?php

namespace System\Storage\Drivers\Helpers\MySqlQueryBuilders;

abstract class QueryBuilderCore implements QueryBuilderInterface {
    
    const TYPE_ARRAY = 'array';
    const TYPE_NUMBER = 'number';
    
    /** @var string */
    protected $tableName;
    
    /** @var array */
    protected $attributes;

    /**
     * 
     * @param string $tableName
     * 
     * @return $this
     * @throws \RuntimeException
     */
    public function setTable($tableName)
    {
        if (empty($tableName)) {
            throw new \RuntimeException('Table name may not be empty.');
        }
        
        $this->tableName = $tableName;
        
        return $this;
    }
   
    /**
     * 
     * @param array $options
     * @return string
     * @throws \RuntimeException
     */
    public function build(array $options)
    {
        $supported = $this->getSupportedAttributes();
        
        foreach($options as $key => $value) {
            if (false == isset($supported[$key])) {
                throw new \RuntimeException(
                        "Unsupported attribute \"{$key}\", supported are \"" . implode(',', array_keys($supported)) . '\"'
                    );
            }

            $this->validateAttribute($value, $supported[$key]);
        }
        
        $this->attributes = $options;
                
        return $this->buildQuery();
    }
    
    /**
     * 
     * @param string $attribute
     * @return mixed
     */
    protected function attr($attribute)
    {
        return (isset($this->attributes[$attribute]) ? $this->attributes[$attribute] : null);
    }
    
    /**
     * 
     * @param mixed $value
     * @param string $type
     * @throws \RuntimeException
     */
    private function validateAttribute($value, $type)
    {
        $error = false;
        
        switch ($type) {
            case static::TYPE_ARRAY:
                $error = false == is_array($value) && false === is_null($value);
                break;
            case static::TYPE_NUMBER:
                $error = false == is_numeric($value) && false === is_null($value);
                break;
        }
        
        if ($error) {
            throw new \RuntimeException("Invalid type");
        }
    }
    
    protected abstract function buildQuery();
        
    public abstract function getSupportedAttributes();
    
}
