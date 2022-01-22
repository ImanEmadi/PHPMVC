<?php

namespace App\Controller;

use App\Controller\BaseController;
use App\DataBase\MySQL\Repository\MockRepository;
use App\Services\HTTPService;

class HomeController extends BaseController
{
    public static function index()
    {

        $MockRepositoryInstance = new MockRepository();
        $result = $MockRepositoryInstance::getPassedUsers();
        $result2 = $MockRepositoryInstance::saveNewUsers([
            ["Iman", "12545-5645-41", "iman@gmail.com", "IR-TEH"],
            ["Sobhan", "1256465-5645-4", "Sobhan@gmail.com", "IR-TEH"],
            ["Negar", "12545-54543-4", "Negar@gmail.com", "GE-Sach"],
            ["Amir", "176845-5645-2", "Amir@gmail.com", "IR-TEH"],
        ]);

        HTTPService::respond($result2);
    }
}
