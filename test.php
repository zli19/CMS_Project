<?php
require_once('./db/DBConnection.php');
$db = new DBConnection();
require_once('./models/Image.php');

$images = Image::getNumberOfImagesByAttribute('room_id', 1);
var_dump($images);
