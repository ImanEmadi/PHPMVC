<?php

namespace App\Model;

class BaseModel
{
    private $databaseConfig;
    protected static $DBConnection;
    public $table;
    public function __construct($table = '')
    {
        $this->databaseConfig = require "Config/database.php";
        self::$DBConnection = new \mysqli($this->databaseConfig["ip"], $this->databaseConfig["username"], $this->databaseConfig["password"], $this->databaseConfig["database"]);
        self::$DBConnection->set_charset("utf8");
        $this->table = $table;
    }

    public function getAll($order = null)
    {
        $result = self::$DBConnection->query("SELECT * FROM " . $this->table . $order);
        $this->checkConnection();
        return $this->returnResult($result);
    }

    public function count($rows, $condition)
    {
        $result = self::$DBConnection->query("SELECT COUNT($rows) FROM " . $this->table . $condition);
        $this->checkConnection();
        // return $result;
        return $this->returnResult($result);
    }

    public function countAll()
    {
        $result = self::$DBConnection->query("SELECT COUNT(*) FROM " . $this->table);
        $this->checkConnection();
        //return $result;
        return $this->returnResult($result);
    }

    public function select($required = "*", $condition = '', $bindings = [])
    {
        $qu = "SELECT $required FROM $this->table $condition";
        // die($qu);
        if ($stmt = self::$DBConnection->prepare($qu)) {
            if ($condition !== '' && !empty($bindings)) {
                $stmt->bind_param(...$bindings);
            }
            try {
                $stmt->execute();
                return $this->returnResult($stmt->get_result());
            } catch (\Exception $e) {
                $this->checkConnection();
            }
        } else {
            $this->checkConnection();
        }
    }

    public function insert(string $columns, array $valuesArr, $types = null)
    {
        // $valuesArr is an array of arrays , each child array indicates a row of data to be inserted
        $qu = "INSERT INTO $this->table $columns VALUES (";
        $rowsCount = sizeof($valuesArr[0]);
        if ($types === null) { // if user has not defined values type for binding , set all as string
            $typesStr = '';
            for ($i = 1; $i <= $rowsCount; $i++) {
                $typesStr .= 's';
            }
        } else {
            $typesStr = $types;
        }

        for ($i = 1; $i <= $rowsCount; $i++) {
            $qu .= "?,";
        }
        $qu = rtrim($qu, ',') . "); ";
        $stmt = self::$DBConnection->prepare($qu);
        if ($stmt === false) {
            // die($qu);
            return $this->checkConnection();
        }

        try {
            foreach ($valuesArr as  $rowArr) {
                $stmt->bind_param($typesStr, ...$rowArr);
                $stmt->execute();
            }
        } catch (\Exception $e) {
            // throw new Error($e);
            return $this->checkConnection();
        }
        return true;
    }


    public function delete($condition = '', $binding = null)
    {
        $qu = "DELETE FROM $this->table $condition";
        if ($stmt = self::$DBConnection->prepare($qu)) {
            try {
                if ($binding !== null) {
                    $stmt->bind_param(...$binding);
                }
                return $stmt->execute();
            } catch (\Exception $th) {
                return $this->checkConnection();
            }
        } else
            return $this->checkConnection();
    }

    public function update(array $columns, $condition = '', $binding = null)
    {
        $qu = "UPDATE $this->table SET ";
        foreach ($columns as $column) {
            $qu .= " $column = ? , ";
        }
        $qu = rtrim($qu, ', ');
        $qu .= $condition;
        if ($stmt = self::$DBConnection->prepare($qu)) {
            if (!is_null($binding)) {
                try {
                    $stmt->bind_param(...$binding);
                    return $stmt->execute();
                } catch (\Exception $e) {
                    return $this->checkConnection();
                }
            }
        } else
            return $this->checkConnection();
    }

    public function packPreparedResults($results)
    {
        while ($data = $results->fetch_assoc()) {
            $resultArray[] = $data;
            return $this->checkConnection();
        }
        return $resultArray;
    }

    public function checkConnection($terminateOnError = true)
    {
        if (self::$DBConnection->errno) {
            $ConnectionErrObj = (object) [];
            $ConnectionErrObj->status = 0;
            $ConnectionErrObj->response = DB_ERROR;
            // $ConnectionErrObj->response = self::$DBConnection->error;
            if ($terminateOnError) {
                echo json_encode($ConnectionErrObj);
                die();
            } else {
                return false;
            }
            // die(self::$DBConnection->error);
        }
        return true;
    }

    public function returnResult($result)
    {
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
