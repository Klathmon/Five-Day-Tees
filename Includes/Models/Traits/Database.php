<?php
/**
 * Created by: Gregory Benner.
 * Date: 10/3/13
 */

namespace Traits;

use Abstracts\Entity;
use PDO;
use PDOStatement;

/**
 * @property string $namespace
 * @property string $identifier
 * @property \PDO   $database
 */
trait Database
{
    public function getByIDFromDatabase($ID, $passThru = null)
    {
        $statement = $this->database->prepare('SELECT * FROM ' . $this->namespace . ' WHERE ' . $this->identifier . '=:ID LIMIT 1');

        $statement->bindValue(':ID', $ID);

        $statement->execute();

        return $this->executeAndParse($statement, $passThru)[0];
    }

    public function getAllFromDatabase()
    {
        $statement = $this->database->prepare('SELECT * FROM ' . $this->namespace);

        $statement->execute();

        return $this->executeAndParse($statement);
    }

    public function deleteFromDatabase($entity)
    {
        /** @var \Abstracts\Entity $entity */
        
        $statement = $this->database->prepare('DELETE FROM ' . $this->namespace . ' WHERE ' . $this->identifier . '=:ID LIMIT 1');

        $statement->bindValue(':ID', $entity->getID());

        $statement->execute();
    }

    public function persistToDatabase($entity)
    {
        /** @var object $this */
        $array = $this->convertObjectToArray($entity);

        $setValues = '';

        foreach ($array as $name => $value) {
            $setValues .= $name . '=:' . $name;

            end($array);
            if ($name !== key($array)) {
                $setValues .= ', ';
            }
        }


        $sql = 'INSERT INTO ' . $this->namespace . ' SET ' . $setValues . ' ON DUPLICATE KEY UPDATE ' . $setValues;

        $statement = $this->database->prepare($sql);

        foreach ($array as $key => $value) {
            $statement->bindValue(':' . $key, $value);
        }

        $statement->execute();

        return $this->database->lastInsertId();
    }

    protected function executeAndParse($statement, $passThru = null)
    {
        /** @var PDOStatement $statement */
        
        $statement->execute();

        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);

        if ($rows === false || $rows == []) {
            throw new \Exception('No data in returned array');
        }

        return $this->parseDatabaseResult($rows, $passThru);
    }

    protected function parseDatabaseResult($array, $passThru = null)
    {
        foreach ($array as $row) {
            /** @var object $this */
            $returnArray[] = $this->convertArrayToObject($row, $passThru);
        }

        return $returnArray;
    }
}