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
        require('./connect.php');

        $query = 'SELECT * FROM users WHERE email = :email';

        $statement = $db->prepare($query);
        $statement->execute(['email' => $email]);
        $statement->setFetchMode(PDO::FETCH_CLASS, 'User');
        $user = $statement->fetch();
        return $user;
    }

    static function queryUserById(int $id)
    {
        require('./connect.php');

        $query = 'SELECT * FROM users WHERE user_id = :user_id';

        $statement = $db->prepare($query);
        $statement->execute(['user_id' => $id]);
        $statement->setFetchMode(PDO::FETCH_CLASS, 'User');
        $user = $statement->fetch();
        return $user;
    }
}
