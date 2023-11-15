<?php

class User
{
    public int $user_id;
    public string $user_name;
    public string $email;
    public string $password;
    public string $discriminator;

    static function queryUserByEmail(string $email)
    {
        require_once('./db/DBConnection.php');

        $db = new DBConnection();

        $user = $db->queryObjectByAttribute('email', $email, 'User');
        return $user;
    }

    static function queryUserById(int $id)
    {
        require_once('./db/DBConnection.php');

        $db = new DBConnection();

        $user = $db->queryObjectByAttribute('user_id', $id, 'User');
        return $user;
    }

    function insertUser()
    {
        require_once('./db/DBConnection.php');

        $db = new DBConnection();

        $result = $db->insertObject(
            [
                'user_name' => $this->user_name,
                'email' => $this->email,
                'password' => $this->password,
                'discriminator' => $this->discriminator
            ],
            'User'
        );
        return $result;
    }
}
