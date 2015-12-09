<?php


namespace System\Hydrator;

class Hydrator
{

    /**
     * 
     * Hydrates object
     * 
     * @param object|string $object
     * @param array $data
     * @return object
     * @throws RuntimeException
     */
    public static function hydrate($object, array $data = [])
    {
        if (false === is_object($object)) {
            throw new RuntimeException('First parameter of hydrate must be object.');
        }

        if (false === is_object($object)) {
            return null;
        }

        /** @var \ReflectionObject $reflection reflection object of given model */
        $reflection = new \ReflectionObject($object);

        /** @var \ReflectionProperty[] $properties */
        $properties = $reflection->getProperties();

        /**
         * iterate through all fields
         * and fill array for checking
         */
        foreach ($data as $field => $value) {
            $fieldName = preg_replace('/[^A-Za-z]/', '', $field);

            $data[strtolower($fieldName)] = $value;
        }

        /** @var ReflectionProperty $property */
        foreach ($properties as $property) {

            if (false === $property->isPublic()) {
                $property->setAccessible(true);
            }

            $name = preg_replace('/[^A-Za-z]/', '', strtolower($property->getName()));

            if (isset($data[$name])) {
                $property->setValue($object, $data[$name]);
            }
        }

        return $object;
    }

}
