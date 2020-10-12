<?php

namespace App\utility;

use App\Utility\CheckParam;

class GetParam
{

    protected static function returnResult($status = false, $response = '', $param = null)
    {
        $result = (object) [];
        $result->status = $status;
        $result->response = $response;
        $result->param = $param;
        return $result;
    }



    public static function getParam($name,  $method = 'POST', $encodeHTML = false)
    {

        $method = strtoupper($method);
        if ($method == 'POST') {
            $requestMethodArray = $_POST;
        } elseif ($method == 'GET') {
            $requestMethodArray = $_GET;
        } else {
            return self::returnResult(false, "unExpected method");
        }


        if (isset($requestMethodArray[$name])) {
            if (!is_array($requestMethodArray[$name]) && !CheckParam::checkNumber($requestMethodArray[$name])) {
                $param = htmlspecialchars($requestMethodArray[$name]);
                $param = stripslashes($param);
                $param = trim($param);
                $param = $encodeHTML ? htmlentities($param, ENT_HTML5, 'UTF-8') : $param;
            } else {
                $param = $requestMethodArray[$name];
            }
        } else {
            return self::returnResult(false, "missing $name");
        }
        return self::returnResult(true, "param received", isset($param) ? $param : null);
    }
}
