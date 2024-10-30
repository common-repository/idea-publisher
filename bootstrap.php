<?php

set_include_path(implode(DIRECTORY_SEPARATOR, array(dirname(__FILE__) . '/lib', get_include_path())));

spl_autoload_extensions(".php");
spl_autoload_register(
    function ($sClassName) {
        if (preg_match('/IdeaPublisher/', $sClassName)) {
            include_once str_replace('\\', "/", $sClassName) . ".php";
        }
    }
);

require_once 'polyfill.php';
