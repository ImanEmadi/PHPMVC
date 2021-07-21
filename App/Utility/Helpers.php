<?php

namespace App\Utility;

class Helpers
{
    /**
     * @param int $sliceNum
     */
    public static function getStartingIndex($sliceNum)
    {
        return ($sliceNum - 1) * DATA_SLICE_SIZE;
    }
}
