<?php

function Autoloader($className)
{
    $className = ltrim($className, '\\');
    $fileName  = 'Models/';

    /** @noinspection PhpAssignmentInConditionInspection */
    if ($lastNsPos = strripos($className, '\\')) {
        $namespace = substr($className, 0, $lastNsPos);
        $className = substr($className, $lastNsPos + 1);
        $fileName .= str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
    }
    $fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';

    if (is_file(get_include_path() . $fileName)) {
        include $fileName;
    } else {
        switch ($className) {
            default:
                //throw new Exception('Class ' . $className . ' cannot be loaded!');
        }
    }
}