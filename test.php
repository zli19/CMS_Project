<?php

require('./db/DBConnection.php');
$db = new DBConnection();

$result = $db->updateObjectByAttribute('token_no', 20, ['is_expired' => 1], 'Token');

// $columnName = 'token_id';
// $value = 20;


// $DB_DSN = 'mysql:host=localhost;dbname=molijuninn;charset=utf8';
// $DB_USER = 'root';
// $DB_PASS = '';
// $db = new PDO($DB_DSN, $DB_USER, $DB_PASS);
// $query = "UPDATE tokens SET is_expired = 0 WHERE {$columnName} = :{$columnName}";


// try {
//     $statement = $db->prepare($query);
//     $result = $statement->execute(["{$columnName}" => $value]);
// } catch (PDOException $e) {
//     exit($e->getMessage());
// }

var_dump($result);
