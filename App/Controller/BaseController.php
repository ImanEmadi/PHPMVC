<?php

namespace App\Controller;

use App\Utility\checkUser;

class BaseController
{

    protected static function returnResult($response = "", $status = 0, $redirect = null, ...$args)
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
        // echo json_last_error_msg();
        // if (session_status() !== PHP_SESSION_NONE) { // BETA
        //     session_destroy();
        // }
        die();
    }

    protected static function authorized()
    {
        if (!checkUser::userSignedIn() || !$_SESSION['userCredentials']['rank'] === 'Admin') {
            header("HTTP/1.1 401");
            self::returnResult('ACCESS DENIED');
        }
    }
}
