<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
    <title>{$title} - {$config->getSiteName()}</title>

    {foreach $cssSheets as $cssURL}
        <link href="{$cssURL}" rel="stylesheet" type="text/css"/>
    {/foreach}

    <!--[if lt IE 9]>
    <script defer src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
    <![endif]-->
    <!--[if gte IE 9]><!-->
    <script defer src="//ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>
    <!--<![endif]-->

    {foreach $javascripts as $scriptURL}
        <script defer src="{$scriptURL}"></script>
    {/foreach}
</head>
<body>
