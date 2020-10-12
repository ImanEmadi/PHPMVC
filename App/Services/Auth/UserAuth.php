<?php

namespace App\Services\Auth;

use App\Repository\UsersRepository;
use App\Utility\Generator;
use App\Utility\StorageManager;

class UserAuth
{
    public static function signUser($id)
    {
        $usersInstance = new UsersRepository();
        do {
            $identifier = Generator::generateRandomString(15, 'hex_bcrypt_origin');
            $fetchedUserByIden = $usersInstance::identifierExists($identifier[1]);
        } while (!empty($fetchedUserByIden));

        $signArr = array(
            'id' => $id,
            'identifier' => $identifier[0],
        );
        $userIdens = $usersInstance::getUserIdens($id);
        $decodedCurrentIdens = json_decode($userIdens[0]['identifier'] ?? '[]');
        $decodedCurrentIdens[] = $identifier[1];

        if ($usersInstance::updateUserIdentifier(json_encode($decodedCurrentIdens), $id)) {
            return setrawcookie(
                'sign',
                urlencode(json_encode($signArr)),
                [
                    "expires" => time() + (60 * 60 * 24 * 30 * 12),
                    "path" => "/",
                    "domain" => "salmej.form",
                    "secure" => false,
                    "httponly" => true,
                    "samesite" => "none",
                ]
            );
        } else {
            return false;
        }
    }

    public static function userLoggedIn()
    {
        if (isset($_SESSION['activeUser']) && $_SESSION['activeUser'] !== null) {
            return true;
        }

        $storageManager = new StorageManager();
        if ($signCookie = $storageManager->getCookie('sign')) {
            $userInfo = json_decode($signCookie, true);
            if ($userInfo === null) {
                return false;
            }

            $usersInstance = new UsersRepository();
            $fetchedUser = $usersInstance::findByID($userInfo['id']);
            if ($fetchedUser && !empty($fetchedUser)) {
                $userIdens = $fetchedUser[0]['identifier'] ?? '[]';
                $userIdens = json_decode($userIdens, true);
                if (!is_null($userIdens) && !empty($userIdens)) {
                    foreach ($userIdens as $iden) {
                        if (password_verify($userInfo['identifier'], $iden)) {
                            // login successful
                            $storageManager->createSession('activeUser', $fetchedUser[0]);
                            return true;
                        }
                    }
                    return false;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public static function countLoginDevices()
    {
        $storageManager = new StorageManager();
        $userCookieArr = json_decode($storageManager->getCookie('sign'), true);
        if ($userCookieArr !== null) {
            $userID = $userCookieArr['id'];
            $usersInstance = new UsersRepository();
            if ($fetchedUser = $usersInstance::findByID($userID ?? 0)) {
                if (!is_array($fetchedUser[0]))
                    return false;
                $userIdens = $fetchedUser[0]['identifier'];
                $userIdenArr = json_decode($userIdens, true);
                if ($userIdenArr === null)
                    return false;
                return sizeof($userIdenArr);
            }
        }
        return false;
    }

    public static function signUserOut($mode = 'current')
    {
        $storageManager = new StorageManager();
        $userCookieArr = json_decode($storageManager->getCookie('sign'), true);
        if ($userCookieArr !== null) {
            $userID = $userCookieArr['id'];
            $usersInstance = new UsersRepository();
            if ($fetchedUser = $usersInstance::findByID($userID ?? 0)) {
                if (!is_array($fetchedUser[0])) {
                    return false;
                }
                $userIdens = $fetchedUser[0]['identifier'];
                $userIdenArr = json_decode($userIdens, true);
                if ($userIdenArr === null)
                    return false;
                $userCookieIden = $userCookieArr['identifier'];
                switch ($mode) {
                    case 'current':
                        $storageManager->deleteSession('activeUser');
                        // log out only on this device
                        foreach ($userIdenArr as $index => $iden) {
                            if (password_verify($userCookieIden, $iden)) {
                                unset($userIdenArr[$index]);
                            }
                        }
                        return $usersInstance::updateUserIdentifier(json_encode(array_values($userIdenArr)), $userID);
                    case 'except_current':
                        //  log out from all other devices expect the current
                        foreach ($userIdenArr as $index => $iden) {
                            if (!password_verify($userCookieIden, $iden)) {
                                unset($userIdenArr[$index]);
                            }
                        }
                        return $usersInstance::updateUserIdentifier(json_encode(array_values($userIdenArr)), $userID);
                    case 'all':
                        $storageManager->deleteSession('activeUser');
                        return $usersInstance::updateUserIdentifier(null, $userID);
                    default:
                        return false;
                }
            }
            return false;
        }
        return false;
    }
}
