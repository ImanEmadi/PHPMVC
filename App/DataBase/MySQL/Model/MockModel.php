<?php

namespace App\DataBase\MySQL\Model;

use App\DataBase\MySQL\Model\BaseModel;

class MockModel extends BaseModel
{

    /**
     * select mock data
     */
    public function getByMinNumber(int $min = 50)
    {
        return $this
            ->select("id,name,numberrange")
            ->where(" numberrange >= ?")
            ->orderBy("numberrange")
            ->limit(5)
            ->execute("d", [[$min]])
            ->fetch_all();
    }

    public function insertMockData(array $values)
    {
        return $this->insert("name,phone,email,address")
            ->execute("ssss", [...$values])
            ->fetch_all();
    }

    /**
     * @param string $ids - separate IDs with ,
     */
    public function deleteMockDataById(string $ids)
    {
        //! weird example 
        $commasCount = substr_count($ids, ",");
        $quMarks = "";
        for ($i = 0; $i <= $commasCount; $i++) $quMarks .= "?,";
        $quMarks = trim($quMarks, ",");
        return $this
            ->delete()
            ->where(" id IN ($quMarks) ")
            ->execute(str_replace(["?,", "?"], "s", $quMarks), [explode(",", $ids)])
            ->fetch_all();
    }

    public function updateUsersNumberrangeById(array $uIDs,  array $newRanges)
    {
        return $this->update(['numberrange'])
            ->where("id = ?")
            ->execute(
                "dd",
                array_map(function ($id, $newRange) {
                    return [$newRange, $id]; //* in the same order of value-condition
                }, $uIDs, $newRanges)
            )
            ->fetch_all();
    }
}
