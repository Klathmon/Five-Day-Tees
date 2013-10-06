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

        $statement->bindValue(':Mode', $this->config->get('MODE'), PDO::PARAM_STR);

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
    Level3=:Level3,
    CartCallout=:CartCallout
  WHERE 
    Mode=:Mode
  LIMIT 1
SQL
        );

        $statement->bindValue(':Mode', $this->config->get('MODE'), PDO::PARAM_STR);
        $statement->bindValue(':StartingDisplayDate', $this->getStartingDisplayDate()->format('Y-m-d'), PDO::PARAM_STR);
        $statement->bindValue(':Retail', $this->getRetail(), PDO::PARAM_STR);
        $statement->bindValue(':SalesLimit', $this->getSalesLimit(), PDO::PARAM_INT);
        $statement->bindValue(':DaysApart', $this->getDaysApart(), PDO::PARAM_INT);
        $statement->bindValue(':Level1', $this->getLevel1(), PDO::PARAM_STR);
        $statement->bindValue(':Level2', $this->getLevel2(), PDO::PARAM_STR);
        $statement->bindValue(':Level3', $this->getLevel3(), PDO::PARAM_STR);
        $statement->bindValue(':CartCallout', $this->getCartCallout(), PDO::PARAM_STR);
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

    /**
     * @return DateTime
     */
    public function getLastDate()
    {
        $sql = 'SELECT MAX(date) FROM Article';

        $statement = $this->database->query($sql);

        $result = $statement->fetch(PDO::FETCH_NUM)[0];

        if (is_null($result)) {
            return $this->getStartingDisplayDate();
        } else {
            return DateTime::createFromFormat('Y-m-d', $result);
        }
    }

    /**
     * @return Currency
     */
    public function getRetail()
    {
        return Currency::createFromDecimal($this->data['Retail']);
    }

    /**
     * @param Currency $retail
     */
    public function setRetail($retail)
    {
        $this->data['Retail'] = $retail->getDecimal();
    }

    /**
     * @return int
     */
    public function getSalesLimit()
    {
        return $this->data['SalesLimit'];
    }

    /**
     * @param int $salesLimit
     */
    public function setSalesLimit($salesLimit)
    {
        $this->data['SalesLimit'] = $salesLimit;
    }

    /**
     * @return int
     */
    public function getDaysApart()
    {
        return $this->data['DaysApart'];
    }

    /**
     * @param int $daysApart
     */
    public function setDaysApart($daysApart)
    {
        $this->data['DaysApart'] = $daysApart;
    }

    /**
     * @return Currency
     */
    public function getLevel1()
    {
        return Currency::createFromDecimal($this->data['Level1']);
    }

    /**
     * @param Currency $amount
     */
    public function setLevel1($amount)
    {
        $this->data['Level1'] = $amount->getDecimal();
    }

    /**
     * @return Currency
     */
    public function getLevel2()
    {
        return Currency::createFromDecimal($this->data['Level2']);
    }

    /**
     * @param Currency $amount
     */
    public function setLevel2($amount)
    {
        $this->data['Level2'] = $amount->getDecimal();
    }

    /**
     * @return Currency
     */
    public function getLevel3()
    {
        return Currency::createFromDecimal($this->data['Level3']);
    }

    /**
     * @param Currency $amount
     */
    public function setLevel3($amount)
    {
        $this->data['Level3'] = $amount->getDecimal();
    }

    /**
     * Returns the item's current price based on it's category
     *
     * @param Currency $baseRetail
     * @param string   $category
     *
     * @return Currency
     */
    public function getItemCurrentPrice($baseRetail, $category)
    {
        switch ($category) {
            case 'Level1':
                $currentPrice = Currency::createFromCents($baseRetail->getCents() + $this->getLevel1()->getCents());
                break;
            case 'Level2':
                $currentPrice = Currency::createFromCents($baseRetail->getCents() + $this->getLevel2()->getCents());
                break;
            case 'Level3':
                $currentPrice = Currency::createFromCents($baseRetail->getCents() + $this->getLevel3()->getCents());
                break;
            case 'Vault':
            case 'Default':
            case 'Queue':
            default:
                $currentPrice = Currency::createFromDecimal(999.99);
                break;
        }

        return $currentPrice;
    }

    /**
     * This will return the item's "Category" based on the design and article
     * Return Values can be:
     * Queue    In the queue. (Treat items in this category as non-existent to the user)
     * Disabled Items not ready to be sold yet, but can be viewed by the user
     * Level1   The current day's/time's featured item
     * Level2   In the current featured period, but not the featured item
     * Level3   In the store, the highest price items
     * Vault    Similar to disabled, When a item's total sold is >= the sales limit
     *
     *
     * @param DateTime $displayDate
     * @param int      $totalSold
     * @param int      $salesLimit
     *
     * @return string
     */
    public function getItemCategory($displayDate, $totalSold, $salesLimit)
    {

        $dates       = $this->getFeaturedDates();
        $displayDate = $displayDate->format('z');

        $oldestDate = $dates[0]->format('z');
        $olderDate  = $dates[1]->format('z');
        $newerDate  = $dates[2]->format('z');
        $newestDate = $dates[3]->format('z');


        if ($totalSold >= $salesLimit) {
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

    /**
     * @param string $nameEncoded
     *
     * @return string
     */
    public function decodeName($nameEncoded)
    {
        return urldecode(str_replace('_', ' ', $nameEncoded));
    }

    /**
     * @return string
     */
    public function getCartCallout()
    {
        return $this->data['CartCallout'];
    }

    /**
     * @param string $cartCallout
     */
    public function setCartCallout($cartCallout)
    {
        $this->data['CartCallout'] = $cartCallout;
    }
}