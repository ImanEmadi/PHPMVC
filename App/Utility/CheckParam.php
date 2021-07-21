<?php

namespace App\Utility;

class CheckParam
{

    /**
     * @return int,false
     */
    public static function validateInt($number, $sanitize = false)
    {
        if (!is_numeric($number))
            return false;
        $number =  ($sanitize ? filter_var($number, FILTER_SANITIZE_NUMBER_INT) : $number);
        return filter_var($number, FILTER_VALIDATE_INT); // returns the number on true , else returns false
    }

    public static function validateFloat($number, $sanitize = false)
    {
        $number = $sanitize ? filter_var($number, FILTER_SANITIZE_NUMBER_FLOAT) : $number;
        return filter_var($number, FILTER_VALIDATE_FLOAT); // returns the number on true , else returns false
    }


    public static function validateURL($url, $sanitize = false)
    {
        $url = $sanitize ? filter_var($url, FILTER_SANITIZE_NUMBER_FLOAT) : $url;
        return filter_var($url, FILTER_VALIDATE_URL);
    }

    /**
     * @param array,integer $numbers
     */
    public static function checkNumber($numbers)
    {
        if (is_array($numbers)) {
            foreach ($numbers as  $number)
                if (!is_numeric($number))
                    return false;
            return true;
        }
        return is_numeric($numbers);
    }

    public static function checkEmail($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    public static function isPhoneFormat($number)
    {
        return !(preg_match('/[^0123456789\+\-\s]/i', $number) === 1);
    }

    public static function isValidPhoneNumber($number)
    {
        return is_numeric($number) && (mb_strlen($number) < 14);
    }

    public static function stringHasSpecialChars($string)
    {
        if (htmlspecialchars_decode($string) !== $string || htmlspecialchars($string) !== $string) {
            return true;
        }

        $invalidChars = ['/', '\\', '<', '>', '!', '#', '$', '%', '^', '&', '*', '(', ')', '+', '=', '-', '_', '.', '?', ',', '"', '\''];

        foreach ($invalidChars as $index => $char) {
            if (strpos($string, $char) > -1) {
                return true;
            }
        }
        return false;
    }

    public static function XSSDetector($string)
    {
        $script_link_detection_pattern = "/<(script|link)(.*)(src|href)(.*)(?(?=\/>)\/>|(>(.)*<\/(script|link)>))/ismxU";
        $anchor_xss_href_detection_pattern = "/<a(.*)(\"|\')javascript:.*(\".*|\'.*)(\".*|\'.*)(\"|\')>(.*)<\/a>/ismxU";
        $pregReplaceCallback = function ($match) use (&$pregReplaceCallback) {
            foreach ($match[0] as $key => $value) {
                return is_array($value) ? $pregReplaceCallback($value) : htmlspecialchars($value, ENT_QUOTES);
            }
        };

        $string = preg_replace_callback($script_link_detection_pattern, $pregReplaceCallback, $string);
        $string = preg_replace_callback($anchor_xss_href_detection_pattern, $pregReplaceCallback, $string);
        return $string;
    }

    /**
     * @param string $string 
     */
    public static function stringInRange($string,  $max,  $min = 0,  $trim = true, $encoding = 'utf8')
    {
        if (!is_string($string))
            return false;
        $strLen = mb_strlen($trim ? trim($string) : $string, $encoding);
        return ($strLen <= $max && $strLen >= $min);
    }

    public static function checkStringsGroupInRange($stringsParams) // validate the length of a group of strings as 2D array
    {
        foreach ($stringsParams as $errorMessage => $param) {
            if (!self::stringInRange($param[0], $param[1], $param[2] ?? 1, true))
                return $errorMessage;
        }
        return true;
    }

    /**
     * returns the key of param with status 0 in the array
     * @return int,bool 
     */
    public static function checkParamsStatus($params)
    {
        foreach ($params as $key => $param)
            if (!$param->status)
                return $key;
        return true;
    }

    public static function areArrays(array $arrays)
    {
        foreach ($arrays as $array) {
            if (!is_array($array))
                return false;
        }
        return true;
    }
}
