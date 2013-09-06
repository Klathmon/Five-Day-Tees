<?php
/**
 * Created by: Gregory Benner.
 * Date: 8/25/13
 */

$settings      = new Settings($database, $config);
$itemsMapper   = new \Mapper\Item($database, $config);
$couponsMapper = new \Mapper\Coupon($database);

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
            $settings->setCartCallout($_POST['CartCallout']);
            $settings->persistSelf();
            $response['status']  = 'OK';
            $response['message'] = 'Saved!';
            break;
        case 'SaveItem':
            $item = $itemsMapper->getByID(Sanitize::cleanInteger($_POST['ID']));
            $item->setName($_POST['Name']);
            $item->setDescription($_POST['Description']);
            $item->setRetail(Sanitize::preserveGivenCharacters($_POST['Retail'], '1234567890.'));
            $item->setDisplayDate(DateTime::createFromFormat('Y-m-d', $_POST['DisplayDate']));
            $item->setVotes(Sanitize::cleanInteger($_POST['Votes']));
            $item->setNumberSold(Sanitize::cleanInteger($_POST['Sold']));
            $item->setSalesLimit(Sanitize::cleanInteger($_POST['SalesLimit']));
            $itemsMapper->persist($item);
            $response['status']  = 'OK';
            $response['message'] = 'Item Saved!';
            break;
        case 'DeleteItem':
            $item = $itemsMapper->getByID(Sanitize::cleanInteger($_POST['ID']));
            $itemsMapper->delete($item);
            $response['status']  = 'OK';
            $response['message'] = 'Item Deleted!';
            break;
        case 'GetNewItems':
            $spreadshirt = new SpreadshirtItems($database, $config);
            $spreadshirt->getNewItems();
            unset($spreadshirt);
            $response['status']  = 'OK';
            $response['command'] = 'refreshPage';
            break;
        case 'AddCoupon':
            $code          = Sanitize::cleanAlphaNumeric($_POST['Code']);
            $isPercent     = ($_POST['IsPercent'] == 'true' ? true : false);
            $amount        = Sanitize::preserveGivenCharacters($_POST['Amount'], '1234567890.-');
            $usesRemaining = Sanitize::cleanInteger($_POST['UsesRemaining']);

            $coupon = new \Entity\Coupon($code);
            if ($isPercent) {
                $coupon->makePercent();
            } else {
                $coupon->makeFlatAmount();
            }
            $coupon->setAmount($amount);
            $coupon->setUsesRemaining($usesRemaining);

            $couponsMapper->persist($coupon);

            $response['status']  = 'OK';
            $response['command'] = 'refreshPage';
            break;
        case 'UpdateCoupon':
            $code          = Sanitize::cleanAlphaNumeric($_POST['Code']);
            $isPercent     = ($_POST['IsPercent'] == 'true' ? true : false);
            $amount        = Sanitize::preserveGivenCharacters($_POST['Amount'], '1234567890.-');
            $usesRemaining = Sanitize::cleanInteger($_POST['UsesRemaining']);

            $coupon = $couponsMapper->getByCode($code);
            if ($isPercent) {
                $coupon->makePercent();
            } else {
                $coupon->makeFlatAmount();
            }
            $coupon->setAmount($amount);
            $coupon->setUsesRemaining($usesRemaining);

            $couponsMapper->persist($coupon);

            $response['status']  = 'OK';
            $response['message'] = 'Coupon Saved!';
            break;
        case 'DeleteCoupon':
            $code = Sanitize::cleanAlphaNumeric($_POST['Code']);

            $coupon = $couponsMapper->getByCode($code);

            $couponsMapper->delete($coupon);

            $response['status']  = 'OK';
            $response['command'] = 'refreshPage';
            break;
        case 'ReloadAllItems':
            if ($config->getMode() == 'DEV') {
                $database->query('DELETE FROM Items');
                $database->query('DELETE FROM ItemsCommon');

                $spreadshirtItems = new SpreadshirtItems($database, $config);
                $spreadshirtItems->getNewItems();
            }
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
    $layout->assign('cartCallout', $settings->getCartCallout());
    $layout->assign('coupons', $couponsMapper->listAll());


    $layout->assign('items', $itemsMapper->listAll());

    $layout->output();
}