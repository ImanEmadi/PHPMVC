<?php

namespace App\Utility;

class Loader
{

    /**
     * returns the array at Customs/values.php
     * @param string $index  - an index of values to be returned                    
     * by default , entire array is returned
     * @return mixed,null
     */
    public static function loadValues($index = '')
    {
        $values = include 'Customs/Values.php';
        if ($index === '')
            return $values;
        return $values[$index] ?? null;
    }
}
