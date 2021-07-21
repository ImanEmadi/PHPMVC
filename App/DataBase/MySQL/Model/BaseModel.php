<?php

namespace App\DataBase\MySQL\Model;

use App\Services\HTTPService;
use DateTime;

class BaseModel
{
    // methods in Models that extend this BaseModel , and start with _ , mean they are for non-user use 
    // such as loading data for admin or in-code usage
    private $databaseConfig;
    protected static $DBConnection;
    protected $table;
    public function __construct($table = '')
    {
        $this->databaseConfig = require "Config/database.php";
        self::$DBConnection = new \mysqli($this->databaseConfig["ip"], $this->databaseConfig["username"], $this->databaseConfig["password"], $this->databaseConfig["database"]);
        self::$DBConnection->set_charset("utf8");
        $this->table = $table;
    }

    protected function getAll($order = null)
    {
        $result = self::$DBConnection->query("SELECT * FROM " . $this->table . $order);
        $this->checkConnection();
        return $this->returnResult($result);
    }

    /**
     * @param string $row
     * @param string $condition
     */
    protected function count($row, $condition = "", $bindings = [])
    {
        $qu = "SELECT COUNT($row) FROM " . $this->table . $condition;
        if ($stmt = self::$DBConnection->prepare($qu)) {
            try {
                if (!empty($bindings))
                    $stmt->bind_param(...$bindings);
                $stmt->execute();
                return $this->returnResult($stmt->get_result());
            } catch (\Throwable $th) {
                $this->checkConnection();
            }
        } else {
            $this->checkConnection();
        }
        // $result = self::$DBConnection->query("SELECT COUNT($row) FROM " . $this->table . $condition);
        // $this->checkConnection();;
        // return $this->returnResult($result);
    }

    protected function countAll()
    {
        $result = self::$DBConnection->query("SELECT COUNT(*) FROM " . $this->table);
        $this->checkConnection();
        //return $result;
        return $this->returnResult($result);
    }

    protected function select($required = "*", $condition = '', $bindings = [])
    {
        $qu = "SELECT $required FROM $this->table $condition";
        // die($qu);
        if ($stmt = self::$DBConnection->prepare($qu)) {
            try {
                if ($condition !== '' && !empty($bindings))
                    $stmt->bind_param(...$bindings);
                $stmt->execute();
                return $this->returnResult($stmt->get_result());
            } catch (\Exception $e) {
                $this->checkConnection();
            }
        } else {
            $this->checkConnection();
        }
    }

    /**
     * @param string $columns  columns to insert data in
     * @param array $valuesArr   an array of arrays , each child array indicates a row of data to be inserted
     * @param array $types   inserted data binding types 
     */
    protected function insert($columns,  $valuesArr, $types = null)
    {
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
        // die($qu);
        $stmt = self::$DBConnection->prepare($qu);
        if ($stmt === false)
            return $this->checkConnection();

        try {
            foreach ($valuesArr as  $rowArr) {
                $stmt->bind_param($typesStr, ...$rowArr);
                $stmt->execute();
                if (!$this->checkConnection(false))
                    return false;
            }
        } catch (\Error $e) {
            // throw new Error($e);
            return $this->checkConnection();
        }
        return true;
    }

    protected function delete($condition = '', $binding = null)
    {
        $qu = "DELETE FROM $this->table $condition";
        if ($stmt = self::$DBConnection->prepare($qu)) {
            try {
                if ($binding !== null)
                    $stmt->bind_param(...$binding);

                return $stmt->execute();
            } catch (\Exception $th) {
                return $this->checkConnection();
            }
        } else
            return $this->checkConnection();
    }

    protected function update(array $columns, $condition = '', array $binding)
    {
        $qu = "UPDATE $this->table SET ";
        foreach ($columns as $column) {
            $qu .= " $column = ? , ";
        }
        $qu = rtrim($qu, ', ');
        $qu .= $condition;
        if ($stmt = self::$DBConnection->prepare($qu)) {
            try {
                $stmt->bind_param(...$binding);
                return $stmt->execute();
            } catch (\Exception $e) {
                return $this->checkConnection();
            }
        } else
            return $this->checkConnection();
    }

    protected function packPreparedResults($results)
    {
        while ($data = $results->fetch_assoc()) {
            $resultArray[] = $data;
            return $this->checkConnection();
        }
        return $resultArray;
    }

    protected function checkConnection($terminateOnError = true)
    {
        if (self::$DBConnection->errno) {
            if ($terminateOnError)
                HTTPService::respond(ENVIRONMENT === 'DEVELOPMENT' ? self::$DBConnection->error : DB_ERROR, 503);
            else
                return false;
        }
        return true;
    }

    protected function epoch_timestamp()
    {
        $date = new DateTime();
        return $date->getTimestamp();
    }

    protected function returnResult($result)
    {
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
