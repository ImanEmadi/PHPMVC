<?php

namespace App\Utility;


class Generator
{

    public static function generateRandomHex($length = 30)
    {
        return bin2hex(random_bytes($length));
    }

    public static function generateRandomString($length = 10, $format = 'hex_int')
    {
        switch ($format) {
            case 'hex_int':
                $date = new \DateTime();
                return self::generateRandomHex($length) . "_" . $date->getTimestamp();
            case 'hex_bcrypt':
                return password_hash(self::generateRandomHex($length), PASSWORD_BCRYPT);
            case 'hex_bcrypt_origin':
                $origin = self::generateRandomHex($length);
                return [$origin, password_hash($origin, PASSWORD_BCRYPT)];
            case 'hex':
                return bin2hex(random_bytes($length));
            case 'rnd_str':
                $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                $charactersLength = strlen($characters);
                $randomString = '';
                for ($i = 0; $i < $length; $i++) {
                    $randomString .= $characters[rand(0, $charactersLength - 1)];
                }
                return $randomString;
            default:
                return false;
        }
    }
}
