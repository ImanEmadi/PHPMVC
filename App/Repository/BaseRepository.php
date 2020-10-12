<?php

namespace App\Repository;

class BaseRepository
{

    private static $addSep = 0;

    // public static function mysqli_real_escape_string($var)
    // {
    //     return static::$instance->real_escape_string($var);
    // }

    // public static function addCondition($query, $condition, $sep)
    // {
    //     //  self::$addSep = self::$addSep == 'unset' ? 0 : self::$addSep;
    //     if (self::$addSep == 1) {
    //         $query .= $sep . $condition;
    //     } else {
    //         $query .= $condition;
    //         self::$addSep = 1;
    //     }

    //     return $query;
    // }

    // public static function resetAddSep()
    // {
    //     self::$addSep = 0;
    // }
}
