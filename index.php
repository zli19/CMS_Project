<?php

session_start();

require('./utils/Auth.php');
$auth = new Auth();
$isLoggedIn = $auth->isLoggedIn();

if ($_POST && $isLoggedIn) {

    header("Location: newRoom.php");
    exit;
}
require('./models/Room.php');

$orderBy = [];
if (!empty($_GET['orderBy'])) {
    $orderBy = ['name' => $_GET['orderBy']];
}
$rooms = Room::queryRoomsOrderBy($orderBy);

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Molijun Inn</title>
    <link rel="stylesheet" href="main.css">
</head>

<body class="box-border bg-gray-100">
    <header class="flex justify-center py-4">
        <nav class="flex justify-between w-4/5 max-w-5xl">
            <a href="./index.php" style="text-decoration: none; color: black;">
                <h1 class="text-2xl font-bold">Molijun Inn</h1>
            </a>
            <ul>
                <?php if ($isLoggedIn) : ?>
                    <li><span class="mx-4"><?= $_SESSION['user_name'] ?></span><a class="btn" href="./logout.php?location=<?= urlencode($_SERVER['REQUEST_URI']) ?>">Sign out</a></li>
                <?php else : ?>
                    <li><a href="./login.php" class="btn">sign in</a></li>
                <?php endif ?>
            </ul>
        </nav>
    </header>
    <main>
        <pre><?= var_dump($_GET) ?></pre>
        <section class="flex justify-center px-4 py-8">
            <div class="w-4/5 max-w-5xl lg:h-96 lg:grid lg:grid-cols-6 lg:gap-4">
                <div class="lg:col-span-3 bg-slate-400"></div>
                <div class="lg:col-span-1">
                    <div class="bg-slate-400 w-48 h-32"></div>
                    <div class="bg-slate-400 w-48 h-32"></div>
                    <div class="bg-slate-400 w-48 h-32"></div>
                </div>
                <div class="lg:col-span-2"></div>
            </div>
        </section>
        <section class="flex justify-center px-4 py-8">
            <ul class="w-4/5 max-w-5xl">
                <!-- display only when user is a administrator -->
                <?php if ($isLoggedIn && $_SESSION['discriminator'] === 'admin') : ?>
                    <li class="mb-4">
                        <div class="mb-4"><button id="create" class="btn edit">Create</button></div>
                    </li>
                <?php endif ?>
                <?php foreach ($rooms as $room) : ?>
                    <a href="./room.php?id=<?= $room->room_id ?>">
                        <li class="grid grid-cols-4 mb-4 bg-white rounded h-20"><?= $room->room_name ?></li>
                    </a>
                <?php endforeach ?>
            </ul>
        </section>
    </main>
</body>

</html>