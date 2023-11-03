<?php
require_once 'vendor/autoload.php'; // Include the Faker library
require('./connect.php');
$query = 'INSERT INTO users(user_name, email, password, discriminator) values(:user_name,:email,:password,:discriminator)';
$statement = $db->prepare($query);

$faker = Faker\Factory::create();

$users = [];

for ($i = 0; $i < 2; $i++) {
    $username = $faker->userName;
    $email = $faker->email;
    $password = $faker->password;
    $discriminator = "admin";

    $users[] = [
        'username' => $username,
        'email' => $email,
        'password' => $password,
    ];

    $hash = password_hash($password, PASSWORD_DEFAULT);
    $statement->execute(['user_name' => $username, 'email' => $email, 'password' => $hash, 'discriminator' => $discriminator]);
}

// Output the generated users (you can save them to a database or file as needed)
echo '<pre>';
print_r($users);
echo '</pre>';
