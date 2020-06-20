<?php

function autoload($className)
{
    if (!defined('PROJECT')) {
        define('PROJECT', '.');
    }

    $className = preg_replace('/^' . PROJECT . '/', '', $className);
    $className = str_replace('\\', '/', $className);
    $className = str_replace('_', '/', $className);

    if (is_file(__DIR__ . '/' . $className . '.php')) {
        require_once(__DIR__ . '//' . $className . '.php');
    }

    if (is_file(__DIR__ . '/../src/' . $className . '.php')) {
        require_once(__DIR__ . '/../src/' . $className . '.php');
    }

}

spl_autoload_register('autoload');