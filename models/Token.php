<?php

class Token
{
    public int $token_id;
    public int $user_id;
    public string $token_no;
    public int $is_expired;
    public string $expiry_date;

    static function queryTokenByTokenNO(string $token)
    {
        require_once('./db/DBConnection.php');

        $db = new DBConnection();

        $token = $db->queryObjectByAttribute('token_no', $token, 'Token');
        return $token;
    }

    static function insert($token)
    {
        require_once('./db/DBConnection.php');

        $db = new DBConnection();

        $result = $db->insertObject(['user_id' => $token->user_id, 'token_no' => $token->token_no, 'expiry_date' => $token->expiry_date], 'Token');
        return $result;
    }

    static function markAsExpiredById(int $id)
    {
        require_once('./db/DBConnection.php');

        $db = new DBConnection();

        $result = $db->updateObjectByAttribute('token_id', $id, ['is_expired' => 1], 'Token');
        return $result;
    }
}
