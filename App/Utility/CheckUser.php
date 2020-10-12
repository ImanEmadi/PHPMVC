<?php

namespace App\Utility;

use App\Repository\usersRepository;


class checkUser
{

    static public function userSignedIn()
    { // checks whether user is signedIn or not
        if (isset($_SESSION['userCredentials']) && !empty($_SESSION['userCredentials'])) {
            return true;
        }
        if (isset($_COOKIE['auth']) && !empty($_COOKIE['auth'])) {
            $usersInstance = new usersRepository();
            $decodedCookie = json_decode($_COOKIE['auth']);
            if ($decodedCookie === null)
                return false; // if data is modified by user and is no longer a valid JSON
            $fetchedUser = $usersInstance::findByID($decodedCookie->id);
            if (empty($fetchedUser) || !is_array($fetchedUser) || sizeof($fetchedUser) !== 1)
                return false;
            else {
                $decodedUserIden = json_decode($fetchedUser[0]['identifier'], true);
                if (is_null($decodedUserIden) || !is_array($decodedUserIden))
                    return false;
                foreach ($decodedUserIden as $key => $value) {
                    if (password_verify($decodedCookie->identifier, $value)) {
                        $_SESSION['userCredentials'] = $fetchedUser[0];
                        return true;
                    }
                }
            }
            return false;
        }
        return false;
    }


    static public function logoutUser()
    {
        if (isset($_COOKIE['auth']) && !empty($_COOKIE['auth'])) {
            $usersInstance = new usersRepository();
            $decodedCookie = json_decode($_COOKIE['auth']);
            if ($decodedCookie !== null) {
                $fetchedUser = $usersInstance::findByID($decodedCookie->id);
                if (!empty($fetchedUser) && is_array($fetchedUser) && sizeof($fetchedUser) == 1) {
                    $decodedUserIden = json_decode($fetchedUser[0]['identifier'], true);
                    if ($decodedUserIden !== null) {
                        foreach ($decodedUserIden as $key => $value) {
                            if (password_verify($decodedCookie->identifier, $value)) {
                                unset($decodedUserIden[$key]);
                                $decodedUserIden = array_values($decodedUserIden);
                                break;
                            }
                        }
                        $usersInstance::updateIdentifier(json_encode($decodedUserIden), $decodedCookie->id);
                    }
                } else {
                    // $decodedUserIden = json_decode($fetchedUser[0]['identifier'], true); // ????
                }
                // $decodedCookie = json_decode($_COOKIE['auth']);
                // $usersInstance = new usersRepository();
                // $usersInstance::updateIdentifier('', $decodedCookie->id);
                setrawcookie('auth', '', time() - 1);
            }
            if (isset($_SESSION['userCredentials']))
                unset($_SESSION['userCredentials']);
        }
    }
}
