<?php

namespace App\DataBase\MySQL\Repository;

use App\DataBase\MySQL\Model\MockModel;
use App\DataBase\MySQL\Repository\BaseRepository;


class MockRepository extends BaseRepository
{
    protected static MockModel $model;

    public function __construct()
    {
        self::$model = new MockModel("mock_table");
    }

    public static function getPassedUsers()
    {
        return self::$model->getByMinNumber(80);
    }

    public static function saveNewUsers(array $users)
    {
        return self::$model->insertMockData($users);
    }
}
