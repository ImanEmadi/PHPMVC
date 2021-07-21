<?php

namespace App\Utility;

class resultType
{
    public $status;
    public $response;
    public $param;
}

class GetParam
{
    protected static function returnResult($status = false, $response = '', $param = null): resultType
    {
        $result = new resultType;
        $result->status = $status;
        $result->response = $response;
        $result->param = $param;
        return $result;
    }

    /**
     * @param string $name
     * @param string $method
     */
    public static function getParam($name,  $method = 'POST', $EncodeSpecialChars = true): resultType
    {
        $method = strtoupper($method);
        if ($method == 'POST')
            $requestMethodArray = $_POST;
        elseif ($method == 'GET')
            $requestMethodArray = $_GET;
        else
            return self::returnResult(false, "unExpected method");

        if (isset($requestMethodArray[$name])) {
            $param = $requestMethodArray[$name];
            if (is_string($param)) {
                if ($EncodeSpecialChars)
                    $param = htmlspecialchars($param, ENT_QUOTES | ENT_HTML5);
                $param = stripslashes($param);
                $param = trim($param);
            }
        } else
            return self::returnResult(false, "missing $name");
        return self::returnResult(true, "param received", $param);
    }
}
