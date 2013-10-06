<?php
/**
 * Created by: Gregory Benner.
 * Date: 8/21/13
 */

$timerStart = microtime(true);

/** Setup Bootstrapper */
require('Config/Bootstrapper.php');


/** @var RequestParser $request Parse the URL into it's parts */
$request = new RequestParser($_SERVER['REQUEST_URI']);

switch ($request->get(0)) {
    case 'Test':
        $controller = 'Test';
        break;
    case '':
    case 'Featured':
    case 'Store':
    case 'Vault':
        $controller = 'Page';
        break;
    case 'Admin':
    case 'Viewport':
    case 'Item':
    case 'FAQ':
    case 'Contact':
    case 'Cart':
    case 'Checkout':
    case 'SpreadShirt':
        $controller = $request->get(0);
        break;
    case 'robots.txt':
        $controller = 'Robotstxt';
        break;
    default:
        $controller = '404';
}

require("Controllers/$controller.php");


if ($config->get('DEBUG', 'DEBUGGING')) {
    echo '// Script took ' . (microtime(true) - $timerStart) . ' Seconds to run //<br/>'.
    (memory_get_peak_usage() / 1048576) . " MB of memory used";
}

die(); //Farewell cruel world!