<?php
/**
 * Created by: Gregory Benner.
 * Date: 8/21/13
 */

echo 'Test Page!';
$spreadshirtItems = new SpreadshirtItems($database, $config);
$spreadshirtItems->getNewItems();

Debug::dump($_SERVER);

echo $request->getQueryString();