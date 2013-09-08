<?php
/**
 * Created by: Gregory Benner.
 * Date: 8/31/13
 */

namespace Mapper;

use Entity\Entity, \PDO;

class Coupon implements Mapper
{
    /** @var \PDO */
    private $database;

    public function __construct(PDO $database)
    {
        $this->database = $database;
    }

    public function getByID($ID)
    {

        $sql
            = <<<SQL
SELECT Coupons.Code, Coupons.Percent, Coupons.Amount, Coupons.UsesRemaining FROM Coupons WHERE Code=:ID LIMIT 1
SQL;

        $statement = $this->database->prepare($sql);

        $statement->bindValue(':ID', $ID, PDO::PARAM_STR);

        $statement->execute();

        return $this->createEntity($statement->fetch(PDO::FETCH_ASSOC));
    }

    public function getByCode($code)
    {
        return $this->getByID($code);
    }

    public function listAll($start = null, $stop = null)
    {

        $sql
            = <<<SQL
SELECT Coupons.Code, Coupons.Percent, Coupons.Amount, Coupons.UsesRemaining FROM Coupons
SQL;

        $statement = $this->database->prepare($sql);

        $statement->execute();

        $array = [];

        foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $entityData) {
            $array[] = $this->createEntity($entityData);
        }

        return $array;
    }

    public function delete(Entity $entity)
    {
        $ID = $entity->getID();

        $sql
            = <<<SQL
DELETE FROM Coupons WHERE Code = :ID LIMIT 1
SQL;

        $statement = $this->database->prepare($sql);

        $statement->bindValue(':ID', $ID, PDO::PARAM_STR);

        $statement->execute();
    }

    public function persist(Entity $entity)
    {
        /** @var \Entity\Coupon $entity */

        $sql
            = <<<SQL
INSERT INTO Coupons
  SET
    Code=:Code,
    Percent=:Percent,
    Amount=:Amount,
    UsesRemaining=:UsesRemaining
  ON DUPLICATE KEY UPDATE
    Code=:Code,
    Percent=:Percent,
    Amount=:Amount,
    UsesRemaining=:UsesRemaining
SQL;

        $statement = $this->database->prepare($sql);

        $statement->bindValue(':Code', $entity->getID(), PDO::PARAM_STR);
        $statement->bindValue(':Percent', $entity->isPercent(), PDO::PARAM_BOOL);
        $statement->bindValue(':Amount', $entity->getAmount(), PDO::PARAM_STR);
        $statement->bindValue(':UsesRemaining', $entity->getUsesRemaining(), PDO::PARAM_INT);

        $statement->execute();
    }

    private function createEntity($data)
    {

        $entity = new \Entity\Coupon($data['Code']);

        if ($data['Percent'] == true) {
            $entity->makePercent();
        } else {
            $entity->makeFlatAmount();
        }

        $entity->setAmount($data['Amount']);
        $entity->setUsesRemaining($data['UsesRemaining']);

        return $entity;
    }
}