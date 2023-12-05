<?php
require_once('./db/DBConnection.php');
class Room
{
    public int $room_id;
    public string $room_name;
    public string $description;

    static function queryRoomById(int $id)
    {
        $db = new DBConnection();

        $room = $db->queryObjectsByAttribute('room_id', $id, 'Room')[0];
        return $room;
    }

    // returns the number of reviews 'total' and 'avg' as the average star rating of the specified room
    static function queryRoomStatById(int $id)
    {
        $db = new DBConnection();

        $query = 'SELECT COUNT(1) AS total, ROUND(AVG(r.star_rating),1) AS avg FROM reviews r WHERE r.room_id = :room_id';
        $keyValuePairs = ['room_id' => $id];
        $resultArray = $db->queryArrayByBindingParams($query, $keyValuePairs);
        return $resultArray;
    }

    static function queryRoomsOrderBy(array $orderBy)
    {
        $db = new DBConnection();
        $query = 'SELECT * FROM rooms';
        if (!empty($orderBy['name'])) {
            $query .= " ORDER BY {$orderBy['name']} DESC";
        }
        $objs = $db->queryObjectsByBindingParams($query, [], 'Room');
        return $objs;
    }

    function insertRoom()
    {
        $db = new DBConnection();

        $KeyValuePairs = ['room_name' => $this->room_name, 'description' => $this->description];
        $result = $db->insertObject($KeyValuePairs, 'Room');
        return $result;
    }

    function updateRoom()
    {
        $db = new DBConnection();

        $setKeyValuePairs = ['room_name' => $this->room_name, 'description' => $this->description];
        $result = $db->updateObjectByAttribute('room_id', $this->room_id, $setKeyValuePairs, 'Room');
        return $result;
    }

    function deleteRoom()
    {
        $db = new DBConnection();
        $result = $db->deleteObjectByAttribute('room_id', $this->room_id, 'Room');
        return $result;
    }

    function removeRoomAndItsImages()
    {
        require_once('./models/Image.php');

        $images = Image::getImagesByAttribute('room_id', $this->room_id);
        // Since images table is set to 'ON DELETE CASCADE', there's no need to delete images record individually.
        // but still need to remove image files.
        $result = $this->deleteRoom();
        if ($result && !empty($images)) {
            $return = true;
            foreach ($images as $image) {
                $r = unlink(realpath($image->path));
                if (!$r && $return) {
                    $return = false;
                }
            }
            return $return;
        } else {
            return $result;
        }
    }
}
