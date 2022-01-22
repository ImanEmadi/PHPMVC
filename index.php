<?php
if (session_status() !== PHP_SESSION_ACTIVE)  // BETA : no session destroy atm
    session_start();

ini_set('allow_url_fopen', 'on');
ini_set('allow_url_include', 'on');
ini_set('upload_max_filesize', '1M');
ini_set('max_file_uploads', '2');
ini_set('post_max_size', '10M');
// security privacy
ini_set('register_globals', "0");

#  ENVIRONMENT = PRODUCTION | DEVELOPMENT
// define("ENVIRONMENT", "PRODUCTION");
define("ENVIRONMENT", "DEVELOPMENT");

set_error_handler(
    function ($errno, $errStr, $errFile, $errLine, $errContext) {
        $errorObj = (object) [];
        $errorObj->status = 0;
        $errorObj->response = "Internal server error";
        if (ENVIRONMENT === 'DEVELOPMENT')
            $errorObj->errorInfo = [
                "errorNumber" => $errno,
                "errorMessage" => $errStr,
                "errorFile" => $errFile,
                "errorLine" => $errLine,
            ];
        header("HTTP/1.0 500");
        header("Content-Type: application/json");
        die(json_encode($errorObj));
    },
    ENVIRONMENT === 'DEVELOPMENT' ? E_ALL | E_STRICT : E_ERROR | E_COMPILE_ERROR
);


$allowedOrigins = [
    'http://localhost:3000',
];

if (isset($_SERVER['HTTP_ORIGIN']) && in_array($_SERVER['HTTP_ORIGIN'], $allowedOrigins))
    $origin = $_SERVER['HTTP_ORIGIN'];
else
    $origin = "";

header("Access-Control-Allow-Origin: $origin"); // while setting cookie via CORS , asterisk must be avoided
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept ,contentType,enctype,X-Custom-Header"); // with Credentials set to true , asterisk is no longer acceptable
header("Access-Control-Allow-methods: POST,OPTIONS,GET"); // OPTIONS is used for preflight requests ( CORS requests)
header('Access-Control-Allow-Credentials: true');
if (PHP_VERSION_ID < 70400) // ^7.4
    throw new Error("API unavailable - cfg.php.ver");
// die(phpinfo());
require_once "Config/constants.php";
require_once "Config/messages.php";
require_once "Bootstrap/AutoLoad.php";
require __DIR__ . '/vendor/autoload.php';
date_default_timezone_set(TIMEZONE);
App\Services\Router\Router::start();
