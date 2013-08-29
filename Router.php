<?php
/**
 * Created by: Gregory Benner.
 * Date: 8/21/13
 */

/** Setup Bootstrapper */
require('Config/Bootstrapper.php');


switch ($query->get(0)) {
    case 'test':
        $controller = 'Test';
        break;
    case 'admin':
        $controller = 'Admin';
        break;
    case '':
    case 'featured':
        $controller = 'Featured';
        break;
    case 'store':
        $controller = 'Store';
        break;
    case 'vault':
        $controller = 'Vault';
        break;
    case 'viewport':
        $controller = 'Viewport';
        break;
    default:
        $controller = '404';
}


require("Controllers/$controller.php");

die(); //Farewell cruel world!