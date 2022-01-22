<?php

namespace App\DataBase\MySQL\Model;

use App\Services\HTTPService;

use DateTime;

class BaseModel
{
    private $databaseConfig;
    protected static  $DBConnection;
    protected string $query;
    protected $model;
    public bool $terminateOnError = true;
    protected \mysqli_stmt $statement;
    public function __construct(protected string $table)
    {
        $this->databaseConfig = require "Config/database.php";
        self::$DBConnection = new \mysqli($this->databaseConfig["ip"], $this->databaseConfig["username"], $this->databaseConfig["password"], $this->databaseConfig["database"]);
        self::$DBConnection->set_charset("utf8");
    }

    protected function select(string $selection): self
    {
        $this->query = " SELECT $selection FROM $this->table ";
        return $this;
    }

    /**
     * @param string|array $columns - string of columns names without parentheses or an array of columns names
     */
    protected function insert(array|string $columns): self
    {
        $columnsStr = is_string($columns) ? $columns : implode(',', $columns);
        $valuesCount = substr_count($columnsStr, ",");
        $quMarks = "";
        for ($i = 0; $i <= $valuesCount; $i++) $quMarks .= "?,";
        $quMarks = trim($quMarks, ",");
        $this->query = " INSERT INTO $this->table ($columnsStr) VALUES ($quMarks) ";
        return $this;
    }

    protected function where(string $condition): self
    {
        $this->query .= " WHERE " . $condition;
        return $this;
    }

    protected function and(string $condition): self
    {
        $this->query .= " AND " . $condition;
        return $this;
    }

    protected function or(string $condition): self
    {
        $this->query .= " OR " . $condition;
        return $this;
    }

    protected function limit(int $limit, int $offset = 0): self
    {
        $this->query = " LIMIT $offset,$limit ";
        return $this;
    }

    protected function orderBy(string $order, string $sortOrder = "DESC"): self
    {
        $this->query .= " ORDER BY $order $sortOrder ";
        return $this;
    }

    /**
     * @param string $types - type of bound values
     * @param array $params - an array of arrays , which inside arrays are values to be executed in prepared statement
     */
    protected function execute(string $types = "", array $params = []): self
    {
        try {
            if ($statement = self::$DBConnection->prepare($this->query)) {
                if (count($params) > 0) {
                    foreach ($params as $paramArray) {
                        if (!$statement->bind_param($types, ...$paramArray)) throw new \Error();
                        if (!$statement->execute()) throw new \Error(); //* execute for each set of values
                    }
                } else if (!$statement->execute()) throw new \Error(); //* if there are no parameters
                $this->statement = $statement;
            } else throw new \Error();
            return $this;
        } catch (\Throwable $th) {
            return $this->checkConnection();
        }
    }

    protected function get_result(): \mysqli_result|false
    {
        if ($this->statement) return $this->statement->get_result();
        else return false;
    }

    protected function fetch_all(int $mode = MYSQLI_ASSOC): array|bool
    {
        $results = $this->get_result();
        if ($results === false) return $this->checkConnection();
        return $results->fetch_all($mode);
    }

    //* since query is being replace when query-initializing methods (such as select,insert & ...) are used .
    //? this method is probably useless
    protected function clearQuery(): self
    {
        $this->query = "";
        return $this;
    }

    protected function returnStatement(): \mysqli_stmt
    {
        return $this->statement;
    }

    /**
     * if `Model.terminateOnError` is true , will end the script with a HTTP Response. (code:503)
     */
    protected function checkConnection()
    {
        if (self::$DBConnection->errno) {
            if ($this->terminateOnError)
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
