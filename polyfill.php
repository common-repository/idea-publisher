<?php

if(!defined('STDERR')) define('STDERR', fopen('php://stderr', 'wb'));

function debug($message)
{
    fwrite(STDERR, "$message\n");
}