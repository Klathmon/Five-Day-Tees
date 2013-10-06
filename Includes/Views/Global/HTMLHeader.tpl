<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
    <title>{$title} - {$config->get('SITE_NAME')}</title>

    {foreach $cssSheets as $cssURL}
        <link href="{$cssURL}" rel="stylesheet" type="text/css"/>
    {/foreach}

    
    <!--[if lt IE 9]>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/{$jQueryVersions[0]}/jquery{$minified}.js"></script>
    <script>window.jQuery || document.write('<script src="/Static/JS/jQuery/jquery-{$jQueryVersions[0]}{$minified}.js"><\/script>')</script>
    <![endif]-->
    <!--[if gte IE 9]><!-->
    <script src="//ajax.googleapis.com/ajax/libs/jquery/{$jQueryVersions[1]}/jquery{$minified}.js"></script>
    <script>window.jQuery || document.write('<script src="/Static/JS/jQuery/jquery-{$jQueryVersions[1]}{$minified}.js"><\/script>')</script>
    <!--<![endif]-->

    {foreach $javascripts as $scriptURL}
        <script defer src="{$scriptURL}"></script>
    {/foreach}
</head>
<body>
