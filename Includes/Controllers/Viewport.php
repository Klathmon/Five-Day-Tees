<?php
/**
 * Created by: Gregory Benner.
 * Date: 8/28/13
 */

$name = $request->get(1);
$ID   = $request->get(2);

$template = new FDTSmarty($config, 'Viewport.tpl', 'Viewport', $name . $ID);


if (!$template->isPageCached()) {
    $settings    = new Settings($database, $config);
    $itemFactory = new \DisplayItem\Factory($database, $settings);

    /* Try to grab the shirt from the database, if it fails, forward the user to /404 */
    try{
        $items = $itemFactory->getByNameFromDatabase($name);

        foreach ($items as $item) {
            if (($ID == '' && $item->getProduct()->getType() == 'male') || $item->getID() == $ID) {
                //Primary item here
                $template->assign('primary', $item);
            } else {
                //Secondary item here
                $template->assign('secondary', $item);
            }
        }
    } catch(Exception $e){
        header('/404');
        die();
    }
}

$template->output();