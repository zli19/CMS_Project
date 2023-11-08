<?php

class DBConnection
{
    private string $DB_DSN = 'mysql:host=localhost;dbname=molijuninn;charset=utf8';
    private string $DB_USER = 'root';
    private string $DB_PASS = '';
    private PDO $db;

    function __construct()
    {
        try {
            // Try creating new PDO connection to MySQL.
            $this->db = new PDO($this->DB_DSN, $this->DB_USER, $this->DB_PASS);
            //,array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
        } catch (PDOException $e) {
            print "Error: " . $e->getMessage();
            die(); // Force execution to stop on errors.
            // When deploying to production you should handle this
            // situation more gracefully. ¯\_(ツ)_/¯
        }
    }

    function queryArrayByBindingParams(string $query, array $keyValuePairs)
    {
        try {
            $statement = $this->db->prepare($query);
            $statement->execute($keyValuePairs);
            $resultArray = $statement->fetch();
            return $resultArray;
        } catch (PDOException $e) {
            exit($e->getMessage());
        }
    }

    function queryObjectsByBindingParams(string $query, array $keyValuePairs, string $className)
    {
        try {
            $statement = $this->db->prepare($query);
            $statement->execute($keyValuePairs);
            $statement->setFetchMode(PDO::FETCH_CLASS, $className);
            $objectArray = $statement->fetchAll();
            return $objectArray;
        } catch (PDOException $e) {
            exit($e->getMessage());
        }
    }

    function queryObjectByAttribute(string $columnName, mixed $value, string $className)
    {
        $tableName = strtolower($className) . 's';
        $query = "SELECT * FROM {$tableName} WHERE {$columnName} = :{$columnName}";
        try {
            $statement = $this->db->prepare($query);
            $statement->execute(["{$columnName}" => $value]);
            $statement->setFetchMode(PDO::FETCH_CLASS, "{$className}");
            $object = $statement->fetch();
            return $object;
        } catch (PDOException $e) {
            exit($e->getMessage());
        }
    }

    function insertObject(array $keyValuePairs, string $className,)
    {
        $tableName = strtolower($className) . 's';
        // String interpolation: to form the column names in the query.
        $keys = array_reduce(array_keys($keyValuePairs), function ($carry, $item) {
            if ($carry) {
                $carry .= ', ';
            }
            $carry .= $item;
            return $carry;
        }, '');
        // String interpolation: to form the string inside values();
        $values = array_reduce(array_keys($keyValuePairs), function ($carry, $item) {
            if ($carry) {
                $carry .= ', ';
            }
            $carry .= ':' . $item;
            return $carry;
        }, '');

        $query = "INSERT INTO {$tableName}({$keys}) values({$values})";

        try {
            $statement = $this->db->prepare($query);
            $result = $statement->execute($keyValuePairs);
            return $result;
        } catch (PDOException $e) {
            exit($e->getMessage());
        }
    }

    function updateObjectByAttribute(string $columnName, mixed $value, array $setKeyValuePairs, string $className)
    {
        $tableName = strtolower($className) . 's';
        $setString = '';
        foreach ($setKeyValuePairs as $key => $value) {
            $setString .= $key . ' = ' . $value . ', ';
        }
        $setString = substr($setString, 0, -2);

        $query = "UPDATE {$tableName} SET {$setString} WHERE {$columnName} = :{$columnName}";
        try {
            $statement = $this->db->prepare($query);
            $result = $statement->execute(["{$columnName}" => $value]);
            return $result;
        } catch (PDOException $e) {
            exit($e->getMessage());
        }
    }
}