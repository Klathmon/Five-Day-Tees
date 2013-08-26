<?php
/**
 * Created by: Gregory Benner.
 * Date: 8/25/13
 */

$settings    = new Settings($database, $config);
$itemsMapper = new \Mapper\Item($database, $config);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    switch ($_POST['Command']) {
        case 'SaveGlobals':
            $settings->setStartingDisplayDate(DateTime::createFromFormat('Y-m-d', $_POST['StartingDisplayDate']));
            $settings->setRetail(Sanitize::preserveGivenCharacters($_POST['Retail'], '1234567890.'));
            $settings->setSalesLimit(Sanitize::cleanInteger($_POST['SalesLimit']));
            $settings->setDaysApart(Sanitize::cleanInteger($_POST['DaysApart']));
            $settings->setLevel1(Sanitize::preserveGivenCharacters($_POST['Level1'], '1234567890.'));
            $settings->setLevel2(Sanitize::preserveGivenCharacters($_POST['Level2'], '1234567890.'));
            $settings->setLevel3(Sanitize::preserveGivenCharacters($_POST['Level3'], '1234567890.'));
            $settings->persistSelf();
            $response['status'] = 'OK';
            break;
        default:
            $response['status']  = 'ERROR';
            $response['message'] = 'Unknown Command.';
            break;
    }
    echo json_encode($response);
} else {
    $layout = new Layout($config, 'Admin.tpl', 'Admin Section');

    $layout->assign('startingDisplayDate', $settings->getStartingDisplayDate());
    $layout->assign('retail', $settings->getRetail());
    $layout->assign('salesLimit', $settings->getSalesLimit());
    $layout->assign('daysApart', $settings->getDaysApart());
    $layout->assign('level1', $settings->getLevel1());
    $layout->assign('level2', $settings->getLevel2());
    $layout->assign('level3', $settings->getLevel3());

    $layout->assign('items', $itemsMapper->listAll());

    $layout->output();
}