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

/** Include the DataCache Class */
require('Library/DataCache.php');

/** Include and construct the ConfigParser */
require('ConfigParser.php');
$config = new ConfigParser('Config/config.ini');

/** Include Debug.php if we are in development */
if ($config->get('DEBUG', 'DEBUGGING')) {
    include('Library/Debug.php');
    Debug::setEmail('greg@fivedaytees.com');
}

/** Setup Timezone, Autoloader, and Error Reporting */
date_default_timezone_set('America/New_York');
include('Autoloader.php');
spl_autoload_register('Autoloader');
if ($config->get('DEBUG', 'SHOW_ERRORS')) {
    error_reporting(E_ALL);
} else {
    error_reporting(0);
}


$database = new PDO(
    "mysql:host={$config->get('DATABASE', 'HOST')};dbname={$config->get('DATABASE', 'NAME')}",
    $config->get('DATABASE', 'USERNAME'), $config->get('DATABASE', 'PASSWORD')
);
$database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


require('Library/Smarty/3.1.15/Smarty.class.php');