<?php
require_once('./db/DBConnection.php');

class User
{
    public int $user_id;
    public string $user_name;
    public string $email;
    public string $password;
    public string $discriminator;

    static function getAllUsers()
    {
        $db = new DBConnection();
        $query = 'SELECT * FROM users';
        $keyValuePairs = [];
        // if (!empty($options['rating'])) {
        //     $query .= " AND r.star_rating = :star_rating";
        //     $keyValuePairs['star_rating'] = $options['rating'];
        // }
        // if (!empty($options['orderBy'])) {
        //     $query .= " ORDER BY {$options['orderBy']} DESC";
        // }
        $objs = $db->queryObjectsByBindingParams($query, $keyValuePairs, 'User');
        return $objs;
    }

    static function queryUserByEmail(string $email)
    {
        $db = new DBConnection();

        $user = $db->queryObjectsByAttribute('email', $email, 'User')[0];
        return $user;
    }

    static function queryUserById(int $id)
    {
        $db = new DBConnection();

        $user = $db->queryObjectsByAttribute('user_id', $id, 'User')[0];
        return $user;
    }

    function insertUser()
    {
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

    function deleteUser()
    {
        $db = new DBConnection();

        $result = $db->deleteObjectByAttribute('user_id', $this->user_id, 'User');
        return $result;
    }

    function removeUserAndItsImages()
    {
        require_once('./models/Image.php');
        $images = Image::getImagesByAttribute('user_id', $this->user_id);
        $result = $this->deleteUser();
        // Since images table is set to 'ON DELETE CASCADE', there's no need to delete images record individually.
        // but still need to remove image files.
        $result = $this->deleteUser();
        if ($result && !empty($images)) {
            $return = true;
            foreach ($images as $image) {
                $r = $image->removeImageFile();
                if (!$r && $return) {
                    $return = false;
                }
            }
            return $return;
        } else {
            return $result;
        }
    }

    function updateUser()
    {
        $db = new DBConnection();

        $setKeyValuePairs = ['user_name' => $this->user_name, 'email' => $this->email, 'discriminator' => $this->discriminator];
        $result = $db->updateObjectByAttribute('user_id', $this->user_id, $setKeyValuePairs, 'User');
        return $result;
    }
}
