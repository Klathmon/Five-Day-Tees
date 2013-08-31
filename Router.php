<?php
/**
 * Created by: Gregory Benner.
 * Date: 8/21/13
 */

/** Setup Bootstrapper */
require('Config/Bootstrapper.php');

switch ($query->get(0)) {
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
        $controller = $query->get(0);
        break;
    case '':
        $controller = 'Featured';
        break;
    default:
        $controller = '404';
}


require("Controllers/$controller.php");

die(); //Farewell cruel world!