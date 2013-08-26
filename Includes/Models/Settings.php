<?php
/**
 * Created by: Gregory Benner.
 * Date: 8/25/13
 */
/**
 * Class Settings
 *
 * This is the Settings class that deals with Getting Global/Defaults from the database for use in various places.
 *
 * **NOTE** Because of the Infrequent nature of "Sets" to this class, i provided a persistSelf() method
 */
class Settings
{
    /**
     * @var PDO
     */
    private $database;
    /**
     * @var ConfigParser
     */
    private $config;

    /**
     * @var array The array of data from the database
     */
    private $data;

    /**
     * @param PDO          $database
     * @param ConfigParser $config
     */
    public function __construct($database, $config)
    {
        $this->database = $database;
        $this->config   = $config;

        $statement = $this->database->prepare('SELECT * FROM Settings WHERE Mode = :Mode LIMIT 1');

        $statement->bindValue(':Mode', $this->config->getMode(), PDO::PARAM_STR);

        $statement->execute();

        $this->data = $statement->fetch(PDO::FETCH_ASSOC);
    }

    public function persistSelf()
    {
        $statement = $this->database->prepare(
            <<<SQL
            UPDATE Settings
  SET
    StartingDisplayDate=:StartingDisplayDate,
    Retail=:Retail,
    SalesLimit=:SalesLimit,
    DaysApart=:DaysApart,
    Level1=:Level1,
    Level2=:Level2,
    Level3=:Level3
  WHERE 
    Mode=:Mode
  LIMIT 1
SQL
        );

        $statement->bindValue(':Mode', $this->config->getMode(), PDO::PARAM_STR);
        $statement->bindValue(':StartingDisplayDate', $this->getStartingDisplayDate()->format('Y-m-d'), PDO::PARAM_STR);
        $statement->bindValue(':Retail', $this->getRetail(), PDO::PARAM_STR);
        $statement->bindValue(':SalesLimit', $this->getSalesLimit(), PDO::PARAM_INT);
        $statement->bindValue(':DaysApart', $this->getDaysApart(), PDO::PARAM_INT);
        $statement->bindValue(':Level1', $this->getLevel1(), PDO::PARAM_STR);
        $statement->bindValue(':Level2', $this->getLevel2(), PDO::PARAM_STR);
        $statement->bindValue(':Level3', $this->getLevel3(), PDO::PARAM_STR);
        $statement->execute();
    }

    /**
     * Gets the Starting display date for when there is nothing in the Items/ItemsCommon tables
     *
     * @return DateTime
     */
    public function getStartingDisplayDate()
    {
        return DateTime::createFromFormat('Y-m-d', $this->data['StartingDisplayDate']);
    }

    /**
     * Sets the Starting display date for when there is nothing in the Items/ItemsCommon tables
     *
     * @param DateTime $data
     */
    public function setStartingDisplayDate($data)
    {
        $this->data['StartingDisplayDate'] = $data->format('Y-m-d');;
    }

    public function getRetail()
    {
        return $this->data['Retail'];
    }

    public function setRetail($retail)
    {
        $this->data['Retail'] = $retail;
    }

    public function getSalesLimit()
    {
        return $this->data['SalesLimit'];
    }

    public function setSalesLimit($salesLimit)
    {
        $this->data['SalesLimit'] = $salesLimit;
    }

    public function getDaysApart()
    {
        return $this->data['DaysApart'];
    }

    public function setDaysApart($daysApart)
    {
        $this->data['DaysApart'] = $daysApart;
    }

    public function getLevel1()
    {
        return $this->data['Level1'];
    }

    public function setLevel1($amount)
    {
        $this->data['Level1'] = $amount;
    }

    public function getLevel2()
    {
        return $this->data['Level2'];
    }

    public function setLevel2($amount)
    {
        $this->data['Level2'] = $amount;
    }

    public function getLevel3()
    {
        return $this->data['Level3'];
    }

    public function setLevel3($amount)
    {
        $this->data['Level3'] = $amount;
    }
}