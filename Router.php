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
    case 'Admin':
    case 'Featured':
    case 'Store':
    case 'Vault':
    case 'Viewport':
    case 'Item':
    case 'FAQ':
    case 'Contact':
    case 'Cart':
    case 'Checkout':
        $controller = $request->get(0);
        break;
    case '':
        $controller = 'Featured';
        break;
    default:
        $controller = '404';
}


require("Controllers/$controller.php");

if ($config->debugModeOn()) {
    echo '*DEBUG* Script took ' . (microtime(true) - $timerStart) . ' Seconds to run *DEBUG*';
}

die(); //Farewell cruel world!