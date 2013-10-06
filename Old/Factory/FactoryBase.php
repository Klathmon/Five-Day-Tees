<?php
/**
 * Created by: Gregory Benner.
 * Date: 9/8/13
 */

namespace Factory;

use PDO;
use PDOStatement;
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
     * Returns all of the objects from the database.
     *
     * @return object[]
     * @throws \Exception
     */
    public function getAll()
    {
        $rows = $this->database->query('SELECT * FROM ' . $this->className)->fetchAll(PDO::FETCH_ASSOC);

        return $this->parseSQLResult($rows);
    }

    /**
     * Create a new object. This object will not exist in the database until persisted
     *
     * @param array $array
     *
     * @return object
     */
    public function create($array)
    {
        return $this->convertArrayToObject($array);
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
     * Persists an object to the database
     * 
     * @param object $object
     */
    public function persist($object)
    {
        $this->save($this->convertObjectToArray($object));
        return $this->database->lastInsertID();
    }

    /**
     * "Upserts" an object into the database.
     * 
     * @param array $array The array of columns to upsert
     */
    public function save($array)
    {
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
    }

    /**
     * Converts an object's protected properties to an array
     *
     * @param object $object
     *
     * @return array
     */
    public function convertObjectToArray($object)
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
    
    public function convertArrayToObject($array)
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

    /**
     * @param PDOStatement $statement
     *
     * @return \object[]
     */
    protected function executeAndParse($statement)
    {
        $statement->execute();
        
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
        
        return $this->parseSQLResult($rows);
    }

    /**
     * Converts an array of SQL results to an object
     *
     * @param array $array
     *
     * @throws \Exception
     * @return object[]
     */
    protected function parseSQLResult($array)
    {
        if ($array === false || $array == []) {
            throw new \Exception('No data in returned array');
        }
        
        foreach($array as $row){
            $returnArray[] = $this->convertArrayToObject($row);
        }

        return $returnArray;
    }
}