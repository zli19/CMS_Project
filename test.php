<?php
require('./models/Review.php');
require_once('./db/DBConnection.php');

$db = new DBConnection();
$query = 'SELECT r.review_id, r.user_id, u.user_name, r.room_id, r.review_content, r.star_rating, r.created_at, re.reply_id, re.reply_content FROM reviews r JOIN users u ON r.user_id = u.user_id LEFT JOIN replies re ON r.review_id = re.review_id WHERE r.room_id = :room_id';
$keyValuePairs = ['room_id' => 2];

$query .= " ORDER BY :order_by DESC";
$keyValuePairs['order_by'] = 'star_rating';

$objs = $db->queryObjectsByBindingParams($query, $keyValuePairs, 'Review');


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
print_r(array_map(function ($item) {
    return $item->star_rating;
}, $objs));
