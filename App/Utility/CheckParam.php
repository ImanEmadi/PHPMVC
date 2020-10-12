<?php

namespace App\Utility;

class CheckParam
{

    public static function validateInt($number, $sanitize = false)
    {
        $number = $sanitize ? filter_var($number, FILTER_SANITIZE_NUMBER_INT) : $number;
        return filter_var($number, FILTER_VALIDATE_INT); // returns the number on true , else returns false
    }

    public static function validateFloat($number, $sanitize = false)
    {
        $number = $sanitize ? filter_var($number, FILTER_SANITIZE_NUMBER_FLOAT) : $number;
        return filter_var($number, FILTER_VALIDATE_FLOAT); // returns the number on true , else returns false
    }



    public static function checkNumber($numbers)
    {
        if (is_array($numbers)) {
            foreach ($numbers as  $number) {
                if (!is_numeric($number))
                    return false;
            }
            return true;
        }
        return is_numeric($numbers);
    }


    public static function checkEmail($email)
    {
        // $email = filter_var($email , FILTER_SANITIZE_EMAIL);
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    public static function isPhoneNumber($number)
    {
        return !(preg_match('/[^0123456789\+\-\s]/i', $number) === 1);
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

    public static function stringInRange($string, $max, $min = 1, $trim = true)
    {
        $strLen = mb_strlen($trim ? trim($string) : $string);
        return is_string($string) ? ($strLen <= $max && $strLen >= $min) : false;
    }

    public static function checkStringsGroupInRange($stringsParams) // validate the length of a group of strings as 2D array
    {
        foreach ($stringsParams as $errorMessage => $param) {
            if (!self::stringInRange($param[0], $param[1], $param[2] ?? 1, true))
                return $errorMessage;
        }
        return true;
    }

    public static function checkParamsStatus($params)
    {
        foreach ($params as $key => $param) {
            if (!$param->status)
                return $key;
        }
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


    // customs 

    public static function degreesAreValid($degrees)
    {
        //  beta
        $allowedDegrees = [
            'هیچ کدام',
            'سیکل',
            'دیپلم',
            'لیسانس',
            'فوق لیسانس',
            'دکتری',
            'فوق دکتری',
        ];

        if (is_array($degrees)) {
            foreach ($degrees as  $degree) {
                if (!in_array($degree, $allowedDegrees)) {
                    return false;
                }
            }
            return true;
        } else
            return in_array($degrees, $allowedDegrees);
    }

    public static function genderIsValid($genders)
    {
        $allowedGenders = ['آقا', 'خانم'];
        if (is_array($genders)) {
            foreach ($genders as  $gender) {
                if (!in_array($gender, $allowedGenders))
                    return false;
            }
            return true;
        } else
            return in_array($genders, $allowedGenders);
    }

    public static function jobTimeIsValid($jobTimes)
    {
        $allowedWorkTime = [
            'تمام وقت',
            'نیمه وقت',
        ];
        if (is_array($jobTimes)) {
            foreach ($jobTimes as  $jobTime) {
                if (!in_array($jobTime, $allowedWorkTime))
                    return false;
            }
            return true;
        } else
            return in_array($jobTimes, $allowedWorkTime);
    }

    public static function jobStatusIsValid($jobStatus)
    {
        $allowedJobStatus = [
            'hidden',
            'visible',
            'disabled'
        ];
        if (is_array($jobStatus)) {
            foreach ($jobStatus as  $jobTime) {
                if (!in_array($jobTime, $allowedJobStatus))
                    return false;
            }
            return true;
        } else
            return in_array($jobStatus, $allowedJobStatus);
    }
    public static function jobTypeIsValid($jobTypes)
    {
        $allowedJobTypes = [
            'programming',
            'administrative',
            'normal'
        ];
        if (is_array($jobTypes)) {
            foreach ($jobTypes as  $jobTime) {
                if (!in_array($jobTime, $allowedJobTypes))
                    return false;
            }
            return true;
        } else
            return in_array($jobTypes, $allowedJobTypes);
    }
}
