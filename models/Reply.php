<?php

class Reply
{
    public int $reply_id;
    public int $review_id;
    public string $reply_content;

    function insertReply()
    {
        require_once('./db/DBConnection.php');

        $db = new DBConnection();
        $keyValuePairs = ['review_id' => $this->review_id, 'reply_content' => $this->reply_content];
        $result = $db->insertObject($keyValuePairs, 'Replie');
        return $result;
    }
}
