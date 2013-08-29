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

    /**
     * Returns the item's current price based on it's category
     *
     * @param \Entity\Item $item
     *
     * @return string
     */
    public function getItemCurrentPrice($item)
    {
        $currentPrice = $item->getRetail();

        switch ($item->getCategory()) {
            case 'Level1':
                $currentPrice += $this->getLevel1();
                break;
            case 'Level2':
                $currentPrice += $this->getLevel2();
                break;
            case 'Level3':
                $currentPrice += $this->getLevel3();
                break;
            case 'Vault':
            case 'Default':
            case 'Queue':
            default:
                $currentPrice = '999.99';
                break;
        }

        return $currentPrice;
    }

    /**
     * This will return the item's "Category"
     * Return Values can be:
     * Queue    In the queue. (Treat items in this category as non-existent to the user)
     * Disabled Items not ready to be sold yet, but can be viewed by the user
     * Level1   The current day's/time's featured item
     * Level2   In the current featured period, but not the featured item
     * Level3   In the store, the highest price items
     * Vault    Similar to disabled, When a item's total sold is >= the sales limit
     *
     * @param \Entity\Item $item
     *
     * @return string
     */
    public function getItemCategory($item)
    {
        $dates       = $this->getFeaturedDates();
        $displayDate = $item->getDisplayDate()->format('z');

        $oldestDate = $dates[0]->format('z');
        $olderDate  = $dates[1]->format('z');
        $newerDate  = $dates[2]->format('z');
        $newestDate = $dates[3]->format('z');

        if ($item->getTotalSold() >= $item->getSalesLimit()) {
            $category = 'Vault';
        } elseif ($displayDate <= $oldestDate) {
            $category = 'Level3';
        } elseif ($displayDate > $newerDate) {
            $category = 'Disabled';
        } elseif ($displayDate >= $olderDate && $displayDate < $newerDate) {
            $category = 'Level1';
        } elseif ($displayDate <= $newerDate && $displayDate < $newestDate) {
            $category = 'Level2';
        } elseif ($displayDate >= $newestDate) {
            $category = 'Queue';
        }

        return $category;

    }

    /**
     * Returns the past and future dates for the Featured page
     * These dates are the dividers for all the categories and are used in a ton of places.
     *
     * @return DateTime[]
     */
    public function getFeaturedDates()
    {
        $daysApart   = $this->getDaysApart();
        $currentDate = DateTime::createFromFormat('U', time())->modify('-' . $daysApart * 3 . ' days');

        $oldestDate = clone $currentDate;
        $olderDate  = clone $currentDate->modify('+' . $daysApart * 2 . ' days');
        $newerDate  = clone $currentDate->modify('+' . $daysApart * 1 . ' days');
        $newestDate = clone $currentDate->modify('+' . $daysApart * 2 . ' days')->modify('+1 day');

        return [$oldestDate, $olderDate, $newerDate, $newestDate];
    }
}