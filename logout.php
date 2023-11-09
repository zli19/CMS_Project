<?php
session_start();

require('./auth.php');
$auth = new Auth();
$auth->clearCookieAndToken();
session_destroy();
if (!empty($_GET['location'])) {
    header("Location: {$_GET['location']}");
} else {
    header('Location: index.php');
}
