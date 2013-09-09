<?php
/**
 * Created by: Gregory Benner.
 * Date: 9/8/13
 */

namespace Factory;

use PDO;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;

abstract class FactoryBase
{
    /** @var PDO */
    protected $database;
    /** @var string */
    protected $className;
    /** @var string */
    protected $objectNamespace = '\\Object\\';

    public function __construct(PDO $database)
    {
        $this->database  = $database;
        $fullClassName   = explode('\\', get_class($this));
        $this->className = end($fullClassName);
    }

    /**
     * Returns one object from the given ID
     *
     * @param int $ID
     *
     * @throws \Exception
     *
     * @return object
     */
    public function getByID($ID)
    {
        $statement = $this->database->prepare('SELECT * FROM ' . $this->className . ' WHERE ID=:ID LIMIT 1');
        $statement->bindValue(':ID', $ID, PDO::PARAM_INT);
        $statement->execute();

        $array = $statement->fetch(PDO::FETCH_ASSOC);

        if ($array === false) {
            throw new \Exception('No object with that ID exists in the database');
        } else {
            $object = $this->convertArrayToObject($array);
        }

        return $object;
    }

    /**
     * Deletes the object from the database
     *
     * @param object $object
     *
     */
    public function delete($object)
    {

        $sql = "DELETE FROM {$this->className} WHERE ID=:ID";

        $statement = $this->database->prepare($sql);
        $statement->bindValue(':ID', $object->getID(), PDO::PARAM_INT);
        $statement->execute();
    }

    /**
     * Saves an object to the database in it's entirety, returns the entities ID for linking purposes
     *
     * @param object $object
     *
     * @return int
     */
    public function persist($object)
    {
        $array     = $this->convertObjectToArray($object);
        $setValues = '';

        foreach ($array as $name => $value) {
            $setValues .= $name . '=:' . $name;

            end($array);
            if ($name !== key($array)) {
                $setValues .= ', ';
            }
        }

        $sql = 'INSERT INTO ' . $this->className . ' SET ' . $setValues . ' ON DUPLICATE KEY UPDATE ' . $setValues;

        $statement = $this->database->prepare($sql);

        foreach ($array as $key => $value) {
            $statement->bindValue(':' . $key, $value);
        }

        $statement->execute();

        $ID = $object->getID();
        if ($ID === null) {
            //Set the ID of the object to it's new value from the database
            $ID         = (int)$this->database->lastInsertID();
            $reflection = new \ReflectionClass($object);
            $propertyID = $reflection->getProperty('ID');
            $propertyID->setAccessible(true);
            $propertyID->setValue($object, $ID);
        }

        return $ID;
    }

    /**
     * Converts an object to an array of the objects protected properties
     * *NOTE* Override this function in each child class to convert data from PHP classes to MySQL datatypes
     *
     * @param object $object
     *
     * @return array
     */
    protected function convertObjectToArray($object)
    {
        $reflection = new ReflectionClass($object);

        foreach ($reflection->getProperties(ReflectionProperty::IS_PROTECTED) as $property) {
            $property->setAccessible(true);

            $name         = $property->getName();
            $value        = $property->getValue($object);
            $array[$name] = $value;
        }

        return $array;
    }

    /**
     * Converts an array full of protected properties to an object
     * *NOTE* Override this function in each child class to convert data from MySQL datatypes to PHP classes
     *
     * @param array $array
     *
     * @return object
     */
    protected function convertArrayToObject($array)
    {
        $reflection = new ReflectionClass($this->objectNamespace . $this->className);
        $object     = $reflection->newInstance();

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