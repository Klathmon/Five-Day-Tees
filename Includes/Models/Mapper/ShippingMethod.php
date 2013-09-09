<?php
/**
 * Created by: Gregory Benner.
 * Date: 9/4/13
 */

namespace Mapper;

use Entity\Entity, \PDO;

class ShippingMethod implements Mapper
{
    /** @var PDO */
    private $database;

    public function __construct(PDO $database)
    {
        $this->database = $database;
    }

    public function getByID($ID)
    {
        $statement = $this->database->prepare('SELECT * FROM ShippingMethods WHERE ID=:ID');

        $statement->bindValue(':ID', $ID, PDO::PARAM_INT);
        $statement->execute();

        return $this->createEntity($statement->fetch(PDO::FETCH_ASSOC));
    }

    public function getByName($name)
    {
        $statement = $this->database->prepare('SELECT * FROM ShippingMethods WHERE Name=:Name');

        $statement->bindValue(':Name', $name, PDO::PARAM_INT);
        $statement->execute();

        return $this->createEntity($statement->fetch(PDO::FETCH_ASSOC));
    }

    public function listAll($start = 0, $stop = null)
    {
        $statement = $this->database->prepare('SELECT * FROM ShippingMethods');

        $statement->execute();

        $array = false;

        foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $shippingMethod) {
            $array[] = $this->createEntity($shippingMethod);
        }

        return $array;
    }

    public function listAllEnabled()
    {
        $statement = $this->database->prepare('SELECT * FROM ShippingMethods WHERE Disable <> 1');

        $statement->execute();

        $array = false;

        foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $shippingMethod) {
            $array[] = $this->createEntity($shippingMethod);
        }

        return $array;
    }

    public function delete(Entity $entity)
    {
        $statement = $this->database->prepare('DELETE FROM ShippingMethods WHERE ID=:ID');

        $statement->bindValue(':ID', $entity->getID(), PDO::PARAM_INT);
        $statement->execute();
    }

    public function persist(Entity $entity)
    {

        /** @var \Entity\ShippingMethod $entity */

        $sql
            = <<<SQL
INSERT INTO ShippingMethods
  SET
    ID=:ID,
    DaysLow=:DaysLow,
    DaysHigh=:DaysHigh,
    Tier1Cost=:Tier1Cost,
    Tier2Cost=:Tier2Cost,
    Tier3Cost=:Tier3Cost,
    Tier1PriceLimit=:Tier1PriceLimit,
    Tier2PriceLimit=:Tier2PriceLimit,
    Tier3PriceLimit=:Tier3PriceLimit,
    Disable=:Disable
  ON DUPLICATE KEY UPDATE
    DaysLow=:DaysLow,
    DaysHigh=:DaysHigh,
    Tier1Cost=:Tier1Cost,
    Tier2Cost=:Tier2Cost,
    Tier3Cost=:Tier3Cost,
    Tier1PriceLimit=:Tier1PriceLimit,
    Tier2PriceLimit=:Tier2PriceLimit,
    Tier3PriceLimit=:Tier3PriceLimit,
    Disable=:Disable
SQL;

        $statement = $this->database->prepare($sql);

        $statement->bindValue(':ID', $entity->getID());
        $statement->bindValue(':DaysLow', $entity->getDaysLow());
        $statement->bindValue(':DaysHigh', $entity->getDaysHigh());
        $statement->bindValue(':Tier1Cost', $entity->getTier1Cost());
        $statement->bindValue(':Tier2Cost', $entity->getTier2Cost());
        $statement->bindValue(':Tier3Cost', $entity->getTier3Cost());
        $statement->bindValue(':Tier1PriceLimit', $entity->getTier1PriceLimit());
        $statement->bindValue(':Tier2PriceLimit', $entity->getTier2PriceLimit());
        $statement->bindValue(':Tier3PriceLimit', $entity->getTier3PriceLimit());
        $statement->bindValue(':Disable', $entity->getDisabled());

        $statement->execute();
    }

    private function createEntity($data)
    {
        if ($data === false) {
            $entity = false;
        } else {

            $entity = new \Entity\ShippingMethod($data['ID']);

            $entity->setName($data['Name']);
            $entity->setDaysLow($data['DaysLow']);
            $entity->setDaysHigh($data['DaysHigh']);
            $entity->setTier1Cost($data['Tier1Cost']);
            $entity->setTier2Cost($data['Tier2Cost']);
            $entity->setTier3Cost($data['Tier3Cost']);
            $entity->setTier1PriceLimit($data['Tier1PriceLimit']);
            $entity->setTier2PriceLimit($data['Tier2PriceLimit']);
            $entity->setTier3PriceLimit($data['Tier3PriceLimit']);
        }

        return $entity;
    }
}