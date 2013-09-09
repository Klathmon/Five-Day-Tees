<?php
/**
 * Created by: Gregory Benner.
 * Date: 8/25/13
 */

$settings       = new Settings($database, $config);
$itemsFactory   = new \Factory\Item($database, $settings);
$designFactory  = new \Factory\Design($database);
$articleFactory = new \Factory\Article($database);
$productFactory = new \Factory\Product($database);
$couponsMapper  = new \Mapper\Coupon($database);


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    switch ($_POST['Command']) {
        case 'SaveGlobals':
            $settings->setStartingDisplayDate(DateTime::createFromFormat('Y-m-d', $_POST['StartingDisplayDate']));
            $settings->setRetail(new Currency(Sanitize::preserveGivenCharacters($_POST['Retail'], '1234567890.')));
            $settings->setSalesLimit(Sanitize::cleanInteger($_POST['SalesLimit']));
            $settings->setDaysApart(Sanitize::cleanInteger($_POST['DaysApart']));
            $settings->setLevel1(new Currency(Sanitize::preserveGivenCharacters($_POST['Level1'], '1234567890.')));
            $settings->setLevel2(new Currency(Sanitize::preserveGivenCharacters($_POST['Level2'], '1234567890.')));
            $settings->setLevel3(new Currency(Sanitize::preserveGivenCharacters($_POST['Level3'], '1234567890.')));
            $settings->setCartCallout($_POST['CartCallout']);
            $settings->persistSelf();
            $response['status']  = 'OK';
            $response['message'] = 'Saved!';
            break;
        case 'SaveItem':
            $design  = $designFactory->getByID(Sanitize::cleanInteger($_POST['designID']));
            $article = $articleFactory->getByID(Sanitize::cleanInteger($_POST['articleID']));
            
            $design->setName($_POST['name']);
            $article->setDescription($_POST['description']);
            $article->setBaseRetail(new Currency(Sanitize::preserveGivenCharacters($_POST['baseRetail'], '1234567890.')));
            $design->setDisplayDate(DateTime::createFromFormat('Y-m-d', $_POST['displayDate']));
            $design->setVotes(Sanitize::cleanInteger($_POST['votes']));
            $article->setNumberSold(Sanitize::cleanInteger($_POST['numberSold']));
            $design->setSalesLimit(Sanitize::cleanInteger($_POST['salesLimit']));
            
            $designFactory->persist($design);
            $articleFactory->persist($article);
            
            $response['status']  = 'OK';
            $response['message'] = 'Item Saved!';
            break;
        case 'DeleteItem':
            $article = $articleFactory->getByID(Sanitize::cleanInteger($_POST['articleID']));
            $articleFactory->delete($article);
            
            $response['status']  = 'OK';
            $response['message'] = 'Item Deleted!';
            break;
        case 'GetNewItems':
            $spreadshirt = new SpreadshirtItems($database, $config);
            $spreadshirt->getNewItems();
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
                $database->query('DELETE FROM Article');
                $database->query('DELETE FROM Design');
                $database->query('DELETE FROM Product');

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
    $layout->assign('retail', $settings->getRetail()->getDecimal());
    $layout->assign('salesLimit', $settings->getSalesLimit());
    $layout->assign('daysApart', $settings->getDaysApart());
    $layout->assign('level1', $settings->getLevel1()->getDecimal());
    $layout->assign('level2', $settings->getLevel2()->getDecimal());
    $layout->assign('level3', $settings->getLevel3()->getDecimal());
    $layout->assign('cartCallout', $settings->getCartCallout());
    $layout->assign('coupons', $couponsMapper->listAll());


    $items = $itemsFactory->getAll();

    foreach ($items as $item) {
        $design = $item->getDesign();
        foreach ($item->getArticles() as $article) {
            $product        = $item->getProduct($article->getProductID());
            $displayItems[] = [
                'designID'       => $design->getID(),
                'articleID'      => $article->getID(),
                'productID'      => $product->getID(),
                'name'           => $design->getName(),
                'type'           => $product->getType(),
                'description'    => $article->getDescription(),
                'cost'           => $product->getCost()->getDecimal(),
                'baseRetail'     => $article->getBaseRetail()->getDecimal(),
                'displayDate'    => $design->getDisplayDate(),
                'votes'          => $design->getVotes(),
                'numberSold'     => $article->getNumberSold(),
                'salesLimit'     => $design->getSalesLimit(),
                'sizesAvailable' => $product->getSizesAvailable()
            ];
        }
    }

    $layout->assign('items', $displayItems);

    $layout->output();
}