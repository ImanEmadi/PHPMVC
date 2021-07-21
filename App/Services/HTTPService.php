<?php

namespace App\Services;

class HTTPService
{
    /**
     * @param mixed $response
     * @param int $status
     * @param int $responseCode
     * @param array $headers - array of headers to be set, each header as an array including the header params
     */
    public static function respond(
        $response = "",
        $responseCode = 200,
        $status = 0,
        $headers = [["Content-Type: application/json"]],
        ...$args
    ) {
        $result = (object) [];
        $result->status = $status;
        $result->response = $response;
        foreach ($args as $index => $arg) {
            $propName = "p_" . ($index + 5); // custom extra parameters to be sent to client
            $result->$propName = $arg;
        }
        foreach ($headers as $key => $header)
            header(...$header);
        http_response_code($responseCode);
        echo json_encode($result);
        die();
    }

    public static function request($url, $dataArr = [], $headers = ['Content-Type: application/x-www-form-urlencoded'], $method = 'POST')
    {
        $dataQuery = http_build_query($dataArr);
        $context = stream_context_create([
            'http' => [
                'method'  => $method,
                'header'  => $headers,
                'content' => $dataQuery
            ],
        ]);

        return file_get_contents($url, false, $context);
    }
}
