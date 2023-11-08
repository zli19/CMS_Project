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
        require('./connect.php');

        $query = 'SELECT * FROM tokens WHERE token_no = :token_no';

        $statement = $db->prepare($query);
        $statement->execute(['token_no' => $token]);
        $statement->setFetchMode(PDO::FETCH_CLASS, 'Token');
        $token = $statement->fetch();
        return $token;
    }

    static function insert($token)
    {
        require('./connect.php');

        $query = 'INSERT INTO tokens(user_id, token_no, expiry_date) values(:user_id, :token_no, :expiry_date)';

        $statement = $db->prepare($query);
        $result = $statement->execute(['user_id' => $token->user_id, 'token_no' => $token->token_no, 'expiry_date' => $token->expiry_date]);
        return $result;
    }

    static function markAsExpiredById(int $id)
    {
        require('./connect.php');

        $query = 'UPDATE tokens SET is_expired = 1 WHERE token_id = :token_id';

        $statement = $db->prepare($query);
        $result = $statement->execute(['token_id' => $id]);
        return $result;
    }
}
