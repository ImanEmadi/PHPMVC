<?php

namespace App\Utility;


class Generator
{

    public static function generateRandomHex($length = 30)
    {
        return bin2hex(random_bytes($length));
    }

    public static function generateRandomString($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++)
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        return $randomString;
    }

    public static function generateString($length = 10, $format = 'hex_int', $separator = '_')
    {
        $date = new \DateTime();
        switch ($format) {
            case 'hex_ts':
                return self::generateRandomHex($length) . $separator . $date->getTimestamp();
            case 'hex_int':
                return self::generateRandomHex($length) . $separator . random_int(100000000, 999999999);
            case 'hex_bcrypt':
                return password_hash(self::generateRandomHex($length), PASSWORD_BCRYPT);
            case 'hex_bcrypt_origin':
                $origin = self::generateRandomHex($length);
                return [$origin, password_hash($origin, PASSWORD_BCRYPT)];
            case 'hex':
                return bin2hex(random_bytes($length));
            case 'rnd_str_ts':
                return self::generateRandomString($length) . $separator . $date->getTimestamp();
            case 'rnd_str_int':
                return self::generateRandomString($length) . $separator . random_int(100000000, 999999999);
            default:
                return false;
        }
    }
}
