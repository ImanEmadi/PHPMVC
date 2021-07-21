<?php

namespace App\Validations;

use App\Utility\CheckParam;

class UserValidation
{

    /**
     * @param string $fullName
     * @return bool|string
     */
    public static function isValidFullName($fullName)
    {
        $strLen = mb_strlen($fullName);
        if ($strLen < 4 || $strLen > 100)
            return FULLNAME_OUT_RANGE;
        preg_match("#[\\\/\<\>\!]#i", $fullName, $matches);
        if (sizeof($matches) > 0)
            return INVALID_FULLNAME;
        return true;
    }
    /**
     * @param string $phoneNum
     * @return bool|string
     */
    public static function isValidPhoneNumber($phoneNum)
    {
        if (CheckParam::isValidPhoneNumber($phoneNum))
            return true;
        return INVALID_PHONE_NUMBER;
    }

    /**
     * @param string $role
     * @return bool|string
     */
    public static function isValidUserRole($role)
    {
        if (in_array($role, ['admin', 'user', 'suspended']))
            return true;
        return INVALID_ROLE;
    }
}
