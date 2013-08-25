<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
    <title>{$title} - {$config->getSiteName()}</title>

    {foreach $cssSheets as $cssURL}
        <link href="{$cssURL}" rel="stylesheet" type="text/css"/>
    {/foreach}

    {* This is a Box-Sizing polyfill for IE5.5 6 and 7 *}
    {* I use Box-Sizing:border-box heavily, so this makes layout easier *}
    <style>
        * {
            box-sizing: border-box;
            -webkit-box-sizing: border-box;
            -moz-box-sizing: border-box;
            *behavior: url("{$const.STATIC_URL}js/polyfills/boxsizing.htc");
        }
    </style>

    <!--[if lt IE 9]>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
    {*
    <script>window.jQuery || document.write('<script src="js/jquery/jquery-1.10.2.min.js"><\/script>')</script> *}
    <![endif]-->
    <!--[if gte IE 9]><!-->
    <script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>
    {* <script>window.jQuery || document.write('<script src="js/jquery/jquery-2.0.3.min.js"><\/script>')</script> *}
    <!--<![endif]-->

    {foreach $javascripts as $scriptURL}
        <script defer src="{$scriptURL}"></script>
    {/foreach}
</head>
<body>
