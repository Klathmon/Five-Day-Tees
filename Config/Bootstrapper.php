<?php
/**
 * Created by: Gregory Benner.
 * Date: 8/22/13
 */

/** Start Output Buffering */
ob_start();


/** Set the current working directory to the public root */
chdir(__DIR__ . '/../');

set_include_path('Includes/');


/** Include and construct the ConfigParser */
require('ConfigParser.php');
$config = new ConfigParser('Config/config.ini');

/** Include Debug.php if we are in development */
if ($config->debugModeOn()) {
    include('Library/Debug.php');
    Debug::setEmail('greg@fivedaytees.com');
}

/** Setup Timezone, Autoloader, and Error Reporting */
date_default_timezone_set('America/New_York');
include('Autoloader.php');
spl_autoload_register('Autoloader');
if ($config->showErrors()) {
    error_reporting(E_ALL);
} else {
    error_reporting(0);
}


$database = new PDO(
    "mysql:host={$config->getDatabaseHost()};dbname={$config->getDatabaseName()}",
    $config->getDatabaseUsername(), $config->getDatabasePassword()
);
$database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);