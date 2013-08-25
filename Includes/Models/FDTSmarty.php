<?php
/**
 * Created by: Gregory Benner.
 * Date: 8/25/13
 */
require('Library/Smarty/3.1.14/Smarty.class.php');

/**
 * Class FDTSmarty
 *
 * This is the "Smarty" class. This one is a bare bones version with no default formatting included
 */
class FDTSmarty extends Smarty
{
    /** @var ConfigParser */
    private $config;
    /** @var string */
    private $template;

    private $cssSheets;
    private $javascripts;

    /**
     * Set the template that you want to display
     *
     * @param ConfigParser $config
     * @param string       $template
     * @param string       $title
     */
    public function __construct($config, $template, $title = '')
    {
        $this->config = $config;

        parent::__construct();

        if ($this->config->getMode() == 'DEV') {
            $this->force_compile = true;
            $this->debugging     = true;
        } else {
            $this->debugging = false;
        }

        $this->setTemplateDir(get_include_path() . 'Views/');
        $this->setCompileDir((string)$this->getTemplateDir()[0] . 'Compiled/');

        $this->compile_check = true;
        $this->caching       = Smarty::CACHING_OFF;
        $this->template      = $template;
        $this->assign('title', $title);

    }

    /**
     * Displays the template with all of it's settings.
     */
    public function output()
    {
        $this->assign('javascripts', $this->javascripts);
        $this->assign('cssSheets', $this->cssSheets);
        $this->assign('config', $this->config);

        header('Content-type: text/html; charset=UTF-8'); //Send the Content-type header and charset.

        parent::display($this->template); //Display the template!
    }

    /**
     * Adds the given Javascript file to the template (in the header)
     *
     * @param mixed $scripts
     */
    public function addJs($scripts)
    {
        foreach ((array)$scripts as $script) {
            $this->javascripts[] = $this->config->getStaticURL() . 'JS/' . $script;
        }
    }

    /**
     * Adds the given CSS Sheets into the template (in the header)
     *
     * @param mixed $sheets
     */
    public function addCss($sheets)
    {
        foreach ((array)$sheets as $sheet) {
            $this->cssSheets[] = $this->config->getStaticURL() . 'CSS/' . $sheet;
        }
    }
}