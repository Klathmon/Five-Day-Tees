<?php
/**
 * Created by: Gregory Benner.
 * Date: 8/25/13
 */

/**
 * Class Layout
 *
 * This is my Layout class. This is to be used for all "Full" pages.
 * It includes the Header, footer, and any and all formatting automatically
 */
class Layout
{
    /** @var FDTSmarty The actual FDTSmarty class */
    private $page;

    /**
     * Sets up a new main page
     *
     * @param ConfigParser $config
     * @param string       $template
     * @param string       $title
     */
    public function __construct($config, $template, $title)
    {
        $this->page = new FDTSmarty($config, 'Global/Layout.tpl', $title);

        $this->page->addCss('normalize.css');
        $this->page->addCss('style.css');

        $this->page->addJs('main.js');

        $this->page->assign('mainTemplate', $template);
    }

    public function output()
    {
        $this->page->output();
    }
}