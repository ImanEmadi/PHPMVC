<?php

namespace App\Services;

use Firebase\JWT\JWT;

class JWTService
{
    /**
     * uses HS256 algorithm to encrypt the payload
     * @param object,array $payload
     * @return string
     */
    public static function encode($payload)
    {
        try {
            return JWT::encode($payload, SECRET_KEY, 'HS256');
        } catch (\Throwable $th) {
            return false;
        }
    }

    /**
     * @param  string $payload
     * @return object
     */
    public static function decode($payload)
    {
        try {
            return JWT::decode($payload, SECRET_KEY, ['HS256']);
        } catch (\Throwable $th) {
            return false;
        }
    }
}
