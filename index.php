<?php
if (session_status() !== PHP_SESSION_ACTIVE) { // BETA : session destroy in baseController
    session_start();
}
// necessary for google reCaptcha
ini_set('allow_url_fopen', 'on');
ini_set('allow_url_include', 'on');
set_error_handler(
    function ($errno, $errStr, $errFile, $errLine, $errContext) {
        $errorObj = (object) [];
        $errorObj->status = 0;
        $errorObj->response = "Internal server error";
        // $errorObj->errorInfo = [
        //     "errorNumber" => $errno,
        //     "errorMessage" => $errStr,
        //     "errorFile" => $errFile,
        //     "errorLine" => $errLine,
        // ];
        header("HTTP/1.0 500");
        die(json_encode($errorObj));
    },
    E_ALL
);
// #BETA : exception handling according to async Ajax requests from front-end

// http://localhost:3000    

$allowedOrigins = [
    'http://localhost:3000',
];
if (isset($_SERVER['HTTP_ORIGIN']) && in_array($_SERVER['HTTP_ORIGIN'], $allowedOrigins)) {
    $origin = $_SERVER['HTTP_ORIGIN'];
} else {
    $origin = "";
}

header("Access-Control-Allow-Origin: $origin"); // while setting cookie via CORS , asterisk must be avoided
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept ,contentType,enctype,X-Custom-Header"); // with Credentials set to true , asterisk is no longer acceptable
header("Access-Control-Allow-methods: POST,OPTIONS,GET"); // OPTIONS is used for preflight requests
header('Access-Control-Allow-Credentials: true');
require_once "Config/constants.php";
require_once "Bootstrap/autoload.php";
date_default_timezone_set(TIMEZONE);
App\Services\Router\Router::start();
