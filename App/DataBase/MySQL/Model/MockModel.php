<?php

namespace App\DataBase\MySQL\Model;

use App\DataBase\MySQL\Model\BaseModel;

class MockModel extends BaseModel
{

    public function getByMinNumber(int $min = 50)
    {
        return $this
            ->select("id,name,numberrange")
            ->where(" numberrange >= ?")
            ->orderBy("numberrange")
            ->execute("d", [[$min]])
            ->fetch_all();
    }

    public function insertMockData(array $values)
    {
        return $this->insert("name,phone,email,address")
            ->execute("ssss", [...$values])
            ->fetch_all();
    }
}
