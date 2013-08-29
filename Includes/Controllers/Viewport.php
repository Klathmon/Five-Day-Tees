<?php
/**
 * Created by: Gregory Benner.
 * Date: 8/28/13
 */

$settings    = new Settings($database, $config);
$itemsMapper = new \Mapper\Item($database, $config);

$items = $itemsMapper->getByName($settings->decodeName($query->get(1)));

if ($items !== false && $items[0]->getCategory() != 'Queue') {
    //We are good! shirts are okay to display!
    $template = new FDTSmarty($config, 'Viewport.tpl');

    $template->assign('settings', $settings);

    /* If there is no default ID set, set it to the first shirt's ID */
    $defaultID = (!is_null($query->get(2)) ? Sanitize::cleanInteger($query->get(2)) : $items[0]->getID());

    foreach ($items as $item) {
        if ($item->getID() == $defaultID) {
            $template->assign('primaryItem', $item);
        } else {
            $template->assign('secondaryItem', $item);
        }
    }

    $template->output();

} else {
    die('error.');
}