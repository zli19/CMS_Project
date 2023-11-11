<?php

class Reply
{
    public int $reply_id;
    public int $review_id;
    public string $content;

    static function insertReplyByBindingParams(array $keyValuePairs)
    {
        require_once('./db/DBConnection.php');

        $db = new DBConnection();
        $result = $db->insertObject($keyValuePairs, 'Replie');
        return $result;
    }
}
