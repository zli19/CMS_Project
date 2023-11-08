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

    static function queryRoomStatById(int $id)
    {
        require_once('./db/DBConnection.php');

        $db = new DBConnection();

        $query = 'SELECT COUNT(1) AS total, ROUND(AVG(r.star_rating),1) AS avg FROM reviews r WHERE r.room_id = :room_id';
        $keyValuePairs = ['room_id' => $id];
        $resultArray = $db->queryArrayByBindingParams($query, $keyValuePairs);
        return $resultArray;
    }
}
