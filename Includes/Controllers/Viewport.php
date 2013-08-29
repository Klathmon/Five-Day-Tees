<?php
/**
 * Created by: Gregory Benner.
 * Date: 8/28/13
 */

$settings    = new Settings($database, $config);
$itemsMapper = new \Mapper\Item($database, $config);

$item = $itemsMapper->getByID(Sanitize::cleanInteger($query->get(1)));

if ($item !== false && $item->getCategory() != 'Queue') {
    //We are good! shirt is okay to display!
    $template = new FDTSmarty($config, 'Viewport.tpl');

    $template->assign('settings', $settings);
    $template->assign('item', $item);

    $template->output();
} else {
    die('asshat...');
}