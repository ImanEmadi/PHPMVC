<?php
function autoload($path)
{
    $path .= ".php";
    $path = str_replace("\\", "/", $path); // for linux use
    if (is_readable($path) && file_exists($path))
        include $path;
    else
        echo "ERR_AUTOLOAD " . (ENVIRONMENT === 'DEVELOPMENT' ? " : Tried to load : $path" : "");
}

spl_autoload_register("autoload");
