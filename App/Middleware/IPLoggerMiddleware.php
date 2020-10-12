<?php 

namespace App\Middleware;

class ipLoggerMiddleware {

    public function __construct()
    {
        $logpath = "Logs/iplogs.txt";
            $day = (int) date('d');
            if ($day == 1) {
                file_put_contents($logpath,'');
            }
    }

    public static function logip(){
        $logpath = "Logs/iplogs.txt";
        $userip = $_SERVER['REMOTE_ADDR'];
        
        $APIresponse = file_get_contents('http://ip-api.com/json/' . $userip);
        $responseObj =  json_decode($APIresponse);
        if ( $responseObj->status === 'fail') {
            $logRecord = "$responseObj->status | $responseObj->message | $responseObj->query" ;
            file_put_contents($logpath, $logRecord . PHP_EOL ,FILE_APPEND);
        } elseif ($responseObj->status === 'success') {
            $logRecord = "$responseObj->status | $responseObj->query | $responseObj->region | $responseObj->country | $responseObj->city | isp" ;
            file_put_contents($logpath, $logRecord . PHP_EOL ,FILE_APPEND);
        }
    }
}