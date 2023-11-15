<?php

class Token
{
    public int $token_id;
    public int $user_id;
    public string $token_no;
    public int $is_expired;
    public string $expiry_date;

    function insertToken()
    {
        require_once('./db/DBConnection.php');

        $db = new DBConnection();

        $result = $db->insertObject(['user_id' => $this->user_id, 'token_no' => $this->token_no, 'expiry_date' => $this->expiry_date], 'Token');
        return $result;
    }

    function markAsExpired()
    {
        require_once('./db/DBConnection.php');

        $db = new DBConnection();
        $result = $db->updateObjectByAttribute('token_id', $this->token_id, ['is_expired' => 1], 'Token');
        return $result;
    }

    static function queryTokenByTokenNo(string $token)
    {
        require_once('./db/DBConnection.php');

        $db = new DBConnection();

        $token = $db->queryObjectByAttribute('token_no', $token, 'Token');
        return $token;
    }
}
