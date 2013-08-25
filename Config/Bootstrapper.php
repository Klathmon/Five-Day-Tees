<?php
/**
 * Created by: Gregory Benner.
 * Date: 8/22/13
 */
ob_start();
if (session_id() == '') {
    session_start();
}

/** Set the current working directory to the public root */
chdir(__DIR__ . '/../');

set_include_path('Includes/');

/** Include and construct the ConfigParser */
require('ConfigParser.php');
$config = new ConfigParser('Config/development.ini');

/** Include Debug.php if we are in development */
if ($config->getMode() == 'DEV') {
    include('Library/Debug.php');
}

/** Setup Timezone, Autoloader, and Error Reporting */
date_default_timezone_set('America/New_York');
include('Autoloader.php');
spl_autoload_register('Autoloader');
if ($config->getMode() == 'DEV') {
    error_reporting(E_ALL);
    Debug::setEmail('greg@fivedaytees.com');
} else {
    error_reporting(0);
}

$database = new PDO("mysql:host={$config->getDatabaseHost()};dbname={$config->getDatabaseName()}",
    $config->getDatabaseUsername(), $config->getDatabasePassword());
$database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

/** @var Query $query Parse the URL into it's parts */
$query = new Query();