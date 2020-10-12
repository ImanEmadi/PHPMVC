<?php

namespace App\Utility;

class Recaptcha
{

    public static function verifyToken($token, $serverKey = SERVER_KEY)
    {
        $data = http_build_query([
            'secret' => $serverKey,
            'response' => $token
        ]);
        $context = stream_context_create([
            'http' => [
                'method'  => 'POST',
                'header'  => 'Content-Type: application/x-www-form-urlencoded',
                'content' => $data
            ],
        ]);
        $captchaResult = json_decode(file_get_contents("https://www.google.com/recaptcha/api/siteverify", false, $context));
        return $captchaResult->success ?? false;
    }
}
