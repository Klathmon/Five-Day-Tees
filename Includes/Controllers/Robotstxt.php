<?php
/**
 * Created by: Gregory Benner.
 * Date: 9/6/13
 */

$output = 'User-agent: *'. PHP_EOL;

if($config->getMode() != 'LIVE'){
    $output .= 'Disallow: /' . PHP_EOL;
}else{
    $output .= 'Disallow: ' . PHP_EOL;
    $output .= 'Disallow: /Admin/' . PHP_EOL;
    $output .= 'Disallow: /Checkout/' . PHP_EOL;
    $output .= 'Disallow: /PayPal/' . PHP_EOL;
    $output .= 'Disallow: /SpreadShirt/' . PHP_EOL;
}

header('Content-type: text/plain; charset=UTF-8');
echo $output;