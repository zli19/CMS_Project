<?php
session_start();

require('./utils/Auth.php');
$auth = new Auth();
$auth->logout();

if (!empty($_GET['location'])) {
    header("Location: {$_GET['location']}");
} else {
    header('Location: index.php');
}
