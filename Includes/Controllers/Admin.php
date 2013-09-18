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
$couponsFactory = new \Factory\Coupon($database);


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

            //todo: try-catch to delete product and design as well

            $response['status']  = 'OK';
            $response['message'] = 'Item Deleted!';
            break;
        case 'GetNewItems':
            $spreadshirt = new \SpreadShirt\SpreadShirtItems($database, $config);
            $spreadshirt->getNewItems();
            $response['status']  = 'OK';
            $response['command'] = 'refreshPage';
            break;
        case 'AddCoupon':
            $code          = Sanitize::cleanAlphaNumeric($_POST['Code']);
            $amount        = Currency::createFromDecimal(Sanitize::preserveGivenCharacters($_POST['Amount'], '1234567890.-'));
            $usesRemaining = Sanitize::cleanInteger($_POST['UsesRemaining']);

            $couponsFactory->persist($couponsFactory->create($code, $amount, $usesRemaining));

            $response['status']  = 'OK';
            $response['command'] = 'refreshPage';
            break;
        case 'UpdateCoupon':
            $code          = Sanitize::cleanAlphaNumeric($_POST['Code']);
            $amount        = Currency::createFromDecimal(Sanitize::preserveGivenCharacters($_POST['Amount'], '1234567890.-'));
            $usesRemaining = Sanitize::cleanInteger($_POST['UsesRemaining']);

            $coupon = $couponsFactory->getByCode($code);
            $coupon->setAmount($amount);
            $coupon->setUsesRemaining($usesRemaining);

            $couponsFactory->persist($coupon);

            $response['status']  = 'OK';
            $response['message'] = 'Coupon Saved!';
            break;
        case 'DeleteCoupon':
            $code = Sanitize::cleanAlphaNumeric($_POST['Code']);

            $coupon = $couponsFactory->getByCode($code);

            $couponsFactory->delete($coupon);

            $response['status']  = 'OK';
            $response['command'] = 'refreshPage';
            break;
        case 'ReloadAllItems':
            if ($config->get('MODE') == 'DEV') {
                $database->query('DELETE FROM Article');
                $database->query('DELETE FROM Design');
                $database->query('DELETE FROM Product');

                $spreadshirtItems = new \SpreadShirt\SpreadShirtItems($database, $config);
                $spreadshirtItems->getNewItems();
            }
            break;
        case 'PurgeCache':
            foreach(new DirectoryIterator('Cache/') as $directory){
                /** @var $directory DirectoryIterator */
                if($directory->isDir()  && !$directory->isDot()){
                    foreach(new DirectoryIterator($directory->getPathname()) as $file){
                        /** @var $file DirectoryIterator */
                        if($file->isFile() && $file->getFilename() != '.gitignore' && $file->getFilename() != '.htaccess'){
                            unlink($file->getPathname());
                        }
                    }
                }
            }

            $response['status']  = 'OK';
            $response['command'] = 'refreshPage';
            break;
        default:
            $response['status']  = 'ERROR';
            $response['message'] = 'Unknown Command.';
            break;
    }
    echo json_encode($response);
} else {


    foreach ($couponsFactory->getAll() as $coupon) {
        $coupons[] = [
            'code'          => $coupon->getCode(),
            'amount'        => $coupon->getAmount()->getNiceFormat(),
            'usesRemaining' => $coupon->getUsesRemaining()
        ];
    }

    foreach ($itemsFactory->getAll() as $item) {
        foreach ($item->getArticles() as $article) {
            $product        = $item->getProduct($article->getProductID());
            $displayItems[] = [
                'designID'       => $item->getID(),
                'articleID'      => $article->getID(),
                'productID'      => $product->getID(),
                'name'           => $item->getName(),
                'type'           => $product->getType(),
                'description'    => $article->getDescription(),
                'cost'           => $product->getCost()->getDecimal(),
                'baseRetail'     => $article->getBaseRetail()->getDecimal(),
                'displayDate'    => $item->getDisplayDate(),
                'votes'          => $item->getVotes(),
                'numberSold'     => $article->getNumberSold(),
                'salesLimit'     => $item->getSalesLimit(),
                'sizesAvailable' => $product->getSizesAvailable()
            ];
        }
    }


    $layout = new Layout($config, 'Admin.tpl', 'Admin Section');
    $layout->assign('startingDisplayDate', $settings->getStartingDisplayDate());
    $layout->assign('retail', $settings->getRetail()->getDecimal());
    $layout->assign('salesLimit', $settings->getSalesLimit());
    $layout->assign('daysApart', $settings->getDaysApart());
    $layout->assign('level1', $settings->getLevel1()->getDecimal());
    $layout->assign('level2', $settings->getLevel2()->getDecimal());
    $layout->assign('level3', $settings->getLevel3()->getDecimal());
    $layout->assign('cartCallout', $settings->getCartCallout());
    $layout->assign('coupons', $coupons);
    $layout->assign('items', $displayItems);
    $layout->output();
}