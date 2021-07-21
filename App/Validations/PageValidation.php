<?php


namespace App\Validations;

use App\Utility\CheckParam;

class PageValidation
{

    /**
     ** if a new type is considered in front-end , it must also be added here 
     * @param string $field
     * @param string $type
     * @return bool
     */
    public static function validateField($field, $type)
    {
        if (!is_string($field))
            return false;

        if (mb_strlen($field) === 0)
            return true;

        switch ($type) {
            case 'text':
                return CheckParam::stringInRange($field, 150);
            case 'longtext':
                return CheckParam::stringInRange($field, 1500);
            case 'tel':
            case 'wa':
                return CheckParam::checkNumber($field) !== false;
            case 'tlg':
                return CheckParam::stringInRange($field, 50);
            case 'map':
                // abstract
                return true;
            case 'link':
                return CheckParam::validateURL($field) !== false;
            case 'yt':
                // preg_match("/.*(youtube\.com\/).+/", $field, $matches);
                // return CheckParam::validateURL($field) !== false && sizeof($matches) !== 0;
            case 'apt':
                // preg_match("/.*(aparat\.com\/).+/", $field, $matches);
                // return CheckParam::validateURL($field) !== false && sizeof($matches) !== 0;
                return CheckParam::stringInRange($field, 300);
            default:
                return false;
        }
    }

    /**
     * * NOTE that this pageID property is different with id property of pages 
     * @param string $pageID
     */
    public static function validatePageID($pageID)
    {
        if (!is_string($pageID))
            return false;
        preg_match("/(([a-zA-Z0-9])|((?!<\_)\_(?!\_))){5,50}/i", $pageID, $matches);
        return $pageID === ($matches[0] ?? "");
    }

    public static function isValidPageGroupField($string)
    {
        preg_match('/(map|longText|text|tel|wa|tlg|map|link|yt|apt)\:.*\S/im', $string, $matches);
        return $string === ($matches[0] ?? "");
    }

    public static function validatePGParams($groupName, $fields)
    {
        if (!CheckParam::stringInRange($groupName, 50, 2))
            return GROUP_NAME_INVALID_LENGTH;
        if (!(is_string($groupName) && is_array($fields)))
            return INVALID_PG_PARAMS;
        if (sizeof($fields) === 0)
            return EMPTY_PG_ARRAY;
        return true;
    }

    public static function isValidFieldID($fieldID)
    {
        preg_match("/([a-z0-9]+)/i", $fieldID, $matches);
        return $fieldID === ($matches[0] ?? "");
    }

    public static function isValidPageStatus($status)
    {
        return in_array($status, ['active', 'hidden', 'awaiting']);
    }
}
