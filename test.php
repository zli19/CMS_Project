<?php

require('./db/DBConnection.php');
$db = new DBConnection();

$result = $db->updateObjectByAttribute('review_id', 20, ['is_expired' => 1,], 'Token');

$columnName = 'token_id';
$value = 20;


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


// $num = "5";
// $non_num = '5b';

// $numVar = filter_var($num, FILTER_VALIDATE_INT);
// $non_numVar = filter_var($non_num, FILTER_VALIDATE_INT);

// var_dump($numVar);
var_dump($non_numVar);
