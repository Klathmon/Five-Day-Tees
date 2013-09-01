<?php
/**
 * Created by: Gregory Benner.
 * Date: 8/21/13
 */

echo 'Test Page!';

$database->query('DELETE FROM Items');
$database->query('DELETE FROM ItemsCommon');

$spreadshirtItems = new SpreadshirtItems($database, $config);
$spreadshirtItems->getNewItems();
echo "DONE!";