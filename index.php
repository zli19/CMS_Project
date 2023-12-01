<?php

session_start();

require('./utils/Auth.php');
$auth = new Auth();
$isLoggedIn = $auth->isLoggedIn();

if ($_POST) {
    require('./processPost.php');

    $result = handlePostFromIndex();

    $_SESSION['message'] = $result ? 'Success!' : 'Failure.';

    header("Location: index.php");
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
    <link rel="stylesheet" href="./main.css">
</head>

<body class="box-border bg-gray-100">
    <header class="flex justify-center py-4">
        <nav class="flex justify-between w-4/5 max-w-5xl">
            <div>
                <ul>
                    <li class="inline-block mr-4">
                        <a href=" ./index.php" style="text-decoration: none; color: black;">
                            <h1 class="text-2xl font-bold">Molijun Inn</h1>
                        </a>
                    </li>
                    <li class="inline-block font-mono  mr-4 hover:text-green-800 hover:underline">
                        <a href="./reviews.php">Reviews</a>
                    </li>
                </ul>
            </div>
            <ul>
                <?php if ($isLoggedIn) :
                    if ($_SESSION['discriminator'] === 'admin') : ?>
                        <li class="inline-block btn-secondary text-sm"><a href="./admin.php">Edit Users</a></li>
                    <?php endif ?>
                    <li class="inline-block font-mono text-sm ml-6"><span><?= $_SESSION['user_name'] ?></span></li>
                    <li class="inline-block btn text-sm"><a href="./logout.php?location=<?= urlencode($_SERVER['REQUEST_URI']) ?>">Sign out</a></li>
                <?php else : ?>
                    <li class="inline-block text-sm"><a href="./login.php" class="btn">sign in</a></li>
                <?php endif ?>
            </ul>
        </nav>
    </header>
    <main>
        <?php if (isset($_SESSION['message'])) : ?>
            <script>
                alert('<?= $_SESSION['message'] ?>')
            </script>
        <?php
            unset($_SESSION['message']);
        endif ?>
        <section class="flex justify-center px-4 py-8 bg-white">
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
                    <li class="mb-4 grid grid-cols-4">
                        <div class="col-span-1"><button id="create" class="btn edit">Create</button></div>
                        <div id="createForm" class="col-span-3 hidden">
                            <form method='post' enctype='multipart/form-data'>
                                <input class="mb-2 w-full border border-gray-300 rounded" type="text" placeholder="Provide a name for the room..." name="room_name">
                                <textarea class="w-full border border-gray-300 rounded" name="description" rows="5" placeholder="Write a description for the room..."></textarea>
                                <div>Pictures go here...</div>
                                <input type='file' name='images[]' id='image' multiple>
                                <input type="submit" class="btn" name="insert" value="submit" />
                            </form>
                        </div>
                    </li>
                <?php endif ?>
                <?php foreach ($rooms as $room) :
                    $stat = Room::queryRoomStatById($room->room_id) ?>
                    <a href="./rooms.php?id=<?= $room->room_id ?>">
                        <li class="px-4 py-4 grid grid-cols-4 mb-4 bg-white rounded shadow hover:shadow-md">
                            <div class="col-span-1 font-bold"><?= $room->room_name ?></div>
                            <div class="col-span-1">
                                <div id="avg">
                                    <?php if ($stat && $stat['total'] > 0) : ?>
                                        <?php for ($i = 0; $i < floor($stat['avg']); $i++) : ?>
                                            <img class="inline-block py-2" src="./images/star_16.png" alt="star">
                                        <?php endfor ?>
                                        <?php if (floor($stat['avg']) - $stat['avg'] < 0) : ?>
                                            <img class="inline-block py-2" src="./images/half_star_16.png" alt="star">
                                        <?php endif ?>
                                        <?= $stat['avg'] ?><span> out of 5</span>
                                    <?php else : ?>
                                        <span>Null</span>
                                    <?php endif ?>
                                </div>
                                <div class="text-gray-400 text-sm" id="stars"><?= $stat['total'] ?> ratings</div>
                            </div>
                            <div class="col-span-2 text-sm">
                                <?php if (strlen($room->description) > 195) : ?>
                                    <?= substr($room->description, 0, 195) . '...' ?>
                                <?php else : ?>
                                    <?= $room->description ?>
                                <?php endif ?>
                            </div>
                        </li>
                    </a>
                <?php endforeach ?>
            </ul>
        </section>
    </main>
    <script src="./js/room.js"></script>
</body>

</html>