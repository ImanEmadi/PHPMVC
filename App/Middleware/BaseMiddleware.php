<?php


namespace App\Middleware;

class BaseMiddleware
{
    protected static function returnResult($response, $status = 0, $redirect = null, ...$args)
    {
        $result = (object) [];
        $result->status = $status;
        $result->response = $response;
        $result->redirect = $redirect;
        foreach ($args as $index => $arg) {
            $propName = "p_" . ($index + 4); // custom extra parameters to be sent to client
            $result->$propName = $arg;
        }

        echo json_encode($result);
        die();
    }
}
