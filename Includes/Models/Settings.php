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
 * **NOTE** Because of the Infrequent nature of "Sets" to this class, each setter should implement it's own storage to the database
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
     * @var array The main array of data from the database
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
        $mysqlDate = $data->format('Y-m-d');

        $this->data['StartingDisplayDate'] = $mysqlDate;

        $statement = $this->database->prepare('UPDATE Settings SET StartingDisplayDate = :DisplayDate WHERE Mode = :Mode');

        $statement->bindValue(':DisplayDate', $mysqlDate, PDO::PARAM_STR);
        $statement->bindValue(':Mode', $this->config->getMode(), PDO::PARAM_STR);
        $statement->execute();
    }

    /**
     * Returns the Default retail price
     *
     * @return mixed
     */
    public function getRetail()
    {
        return $this->data['Retail'];
    }

    /**
     * Set the default retail price
     *
     * @param $retail
     */
    public function setRetail($retail)
    {
        $statement = $this->database->prepare('UPDATE Settings SET Retail = :Retail WHERE Mode = :Mode');

        $statement->bindValue(':Retail', $retail, PDO::PARAM_STR);
        $statement->bindValue(':Mode', $this->config->getMode(), PDO::PARAM_STR);
        $statement->execute();
    }

    public function getSalesLimit()
    {
        return $this->data['SalesLimit'];
    }

    public function setSalesLimit($salesLimit)
    {

        $statement = $this->database->prepare('UPDATE Settings SET SalesLimit = :SalesLimit WHERE Mode = :Mode');

        $statement->bindValue(':SalesLimit', $salesLimit, PDO::PARAM_INT);
        $statement->bindValue(':Mode', $this->config->getMode(), PDO::PARAM_STR);
        $statement->execute();
    }

    public function getDaysApart()
    {
        return $this->data['DaysApart'];
    }


    public function setDaysApart($daysApart)
    {

        $statement = $this->database->prepare('UPDATE Settings SET DaysApart = :DaysApart WHERE Mode = :Mode');

        $statement->bindValue(':DaysApart', $daysApart, PDO::PARAM_INT);
        $statement->bindValue(':Mode', $this->config->getMode(), PDO::PARAM_STR);
        $statement->execute();
    }
}