<?php

class Room
{
    public int $room_id;
    public string $room_name;
    public string $description;
    public ?int $image_id;
    public ?string $path;

    static function queryRoomById(int $id)
    {
        require_once('./db/DBConnection.php');

        $db = new DBConnection();

        $room = $db->queryObjectByAttribute('room_id', $id, 'Room');
        return $room;
    }

    // returns the number of reviews 'total' and 'avg' as the average star rating of the specified room
    static function queryRoomStatById(int $id)
    {
        require_once('./db/DBConnection.php');

        $db = new DBConnection();

        $query = 'SELECT COUNT(1) AS total, ROUND(AVG(r.star_rating),1) AS avg FROM reviews r WHERE r.room_id = :room_id';
        $keyValuePairs = ['room_id' => $id];
        $resultArray = $db->queryArrayByBindingParams($query, $keyValuePairs);
        return $resultArray;
    }

    static function queryRoomsOrderBy(array $orderBy)
    {
        require_once('./db/DBConnection.php');

        $db = new DBConnection();
        $query = 'SELECT * FROM rooms';
        if (!empty($orderBy['name'])) {
            $query .= " ORDER BY {$orderBy['name']} DESC";
        }
        $objs = $db->queryObjectsByBindingParams($query, [], 'Room');
        return $objs;
    }

    function updateRoom()
    {
        require_once('./db/DBConnection.php');

        $db = new DBConnection();

        $keyValuePairs = ['room_name' => $this->room_name, 'description' => $this->description];
        $result = $db->updateObjectByAttribute('room_id', $this->room_id, $setKeyValuePairs, 'Room');
        return $result;
    }

    function deleteRoom()
    {
        require_once('./db/DBConnection.php');

        $db = new DBConnection();
        $result = $db->deleteObjectByAttribute('room_id', $this->room_id, 'Room');
        return $result;
    }
}
