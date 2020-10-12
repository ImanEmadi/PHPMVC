<?php
function autoload($path)
{
    $path .= ".php";
    $path = str_replace("\\", "/", $path); // for linux use
    if (is_readable($path) && file_exists($path)) {
        include $path;
    } else {
        echo "either the path is not readable or doesn't exist (ERR_autoload) , tried to load : $path";
    }
}

spl_autoload_register("autoload");
