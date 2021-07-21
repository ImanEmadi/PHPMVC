<?php

namespace App\Controller;

use App\Utility\CheckParam;
use App\Utility\GetParam;

//  methods that different controllers may have in common
class BaseController
{
    protected static function getSliceParameter()
    {
        $sliceObj = GetParam::getParam('slice');
        if (!$sliceObj->status || !CheckParam::validateInt($sliceObj->param))
            $sliceNum = 1;
        else
            $sliceNum = $sliceObj->param;
        return $sliceNum;
    }
}
