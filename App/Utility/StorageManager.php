<?php

namespace App\Utility;

class StorageManager
{
    public function getCookie($name)
    {
        return $_COOKIE[$name] ?? false;
    }

    public function getSession($name)
    {
        return $_SESSION[$name] ?? false;
    }

    public function createCookie($name, $value = '', $expire = 0, $path = "", $domain = "", $secure = false, $httpOnly = true, $raw = true, $urlEncode = true)
    { // UNUSED !
        if ($raw) {
            return setrawcookie($name, $urlEncode ? urlencode($value) : $value, time() +  $expire, $path, $domain, $secure, $httpOnly);
        } else {
            return setcookie($name, $value, $expire, $path, $domain, $secure, $httpOnly);
        }
    }

    public function createSession($name, $value, $replace = true)
    {
        if (isset($_SESSION[$name]) && !$replace)
            return false;
        $_SESSION[$name] = $value;
        return true;
    }

    public function deleteCookie($name)
    {
        return setcookie($name, '', time());
    }

    public function deleteSession($name) // unset can not be used inside a function as it only unset it in local scope
    {
        if (isset($_SESSION[$name])) {
            $_SESSION[$name] = null;
            return true;
        } else
            return false;
    }
}
