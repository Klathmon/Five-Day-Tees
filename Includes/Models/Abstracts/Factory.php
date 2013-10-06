<?php
/**
 * Created by: Gregory Benner.
 * Date: 10/3/13
 */

namespace Abstracts;

use PDO;
use ReflectionClass;
use ReflectionProperty;

abstract class Factory
{
    /** @var PDO */
    protected $database;
    /** @var string */
    protected $className = 'Entity';
    /** @var string */
    protected $namespace;
    /** @var string */
    protected $identifier;

    public function __construct(PDO $database)
    {
        $this->database   = $database;
        $fullClassName    = explode('\\', get_class($this));
        end($fullClassName);
        $this->namespace  = prev($fullClassName);
        $this->identifier = strtolower($this->namespace) . 'ID';
    }

    public function createFromData($array)
    {
        return $this->convertArrayToObject($array);
    }

    public function persistToData($entity)
    {
        return $this->convertObjectToArray($entity);
    }

    protected function convertObjectToArray($entity)
    {
        $reflection = new ReflectionClass($entity);

        foreach ($reflection->getProperties(ReflectionProperty::IS_PROTECTED) as $property) {
            $property->setAccessible(true);

            $name         = $property->getName();
            $value        = $property->getValue($entity);
            $array[$name] = $value;
        }

        return $array;
    }
    
    protected function convertArrayToObject($array)
    {
        $reflection = new ReflectionClass('\\' . $this->namespace . '\\' . $this->className);
        $object     = $reflection->newInstanceWithoutConstructor();

        foreach ($array as $name => $value) {
            if ($reflection->hasProperty($name)) {
                $property = $reflection->getProperty($name);
                $property->setAccessible(true);
                $property->setValue($object, $value);
            }
        }

        return $object;
    }
}