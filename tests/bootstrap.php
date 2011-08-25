<?php

function tracksAutoloader($className)
{
    $file = __DIR__
        .DIRECTORY_SEPARATOR
        .'..'
        .DIRECTORY_SEPARATOR
        .'library'
        .DIRECTORY_SEPARATOR
        .str_replace('\\', DIRECTORY_SEPARATOR, $className)
        .'.php';

    if (file_exists($file)) {
        include $file;
    }
}

spl_autoload_register('tracksAutoloader');
