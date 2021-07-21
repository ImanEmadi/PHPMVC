<?php

namespace App\Utility;

class StorageManager
{
    public function getCookie($name)
    {
        return $_COOKIE[$name] ?? null;
    }

    public function getSession($name)
    {
        return $_SESSION[$name] ?? null;
    }

    /**
     * @param string $name
     * @param string $value
     * @param int $expire
     * @return bool
     */
    public function createCookie($name, $value, $expire = 0)
    {
        return setcookie($name, $value, [
            'expires' => $expire ?: time() + (86400 * 30),
            'path' => '/',
            'domain' => DOMAIN,
            'secure' => true,
            'httponly' => true,
            'samesite' => 'none'
        ]);
    }

    public function createSession($name, $value, $replace = true)
    {
        if (isset($_SESSION[$name]) && !$replace)
            return true;
        $_SESSION[$name] = $value;
        return true;
    }

    public function deleteCookie($name)
    {
        return setcookie($name, '', time() - 1);
    }

    public function deleteSession($name) // unset can not be used inside a function as it only unset it in local scope
    {
        if (isset($_SESSION[$name]))
            $_SESSION[$name] = null;
        return true;
    }
}
