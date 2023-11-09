<?php

session_start();

require('./auth.php');
$auth = new Auth();
$isLoggedIn = $auth->isLoggedIn();

if (isset($_GET['id']) && filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

    require('./models/Room.php');

    $room = Room::queryRoomById($id);

    if ($room) {
        // Get the statistic of the room
        $stat = Room::queryRoomStatById($id);

        require('./models/Review.php');
        // code for orderBy goes here
        $orderBy = [];

        $reviews = Review::queryReviewsByRoomIdWithOrderBy($id, $orderBy);
    } else {
        header("Location: index.php");
        exit;
    }
} else {
    header("Location: index.php");
    exit;
}

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
                    <li><span class="mx-2"><?= $_SESSION['user_name'] ?></span><a class="btn" href="./logout.php?location=<?= urlencode($_SERVER['REQUEST_URI']) ?>">Sign out</a></li>
                <?php else : ?>
                    <li><a href="./login.php?location=<?= urlencode($_SERVER['REQUEST_URI']) ?>" class="btn">sign in</a></li>
                <?php endif ?>
            </ul>
        </nav>
    </header>
    <main>
        <pre><?= var_dump($_POST) ?></pre>
        <section class="flex flex-col items-center px-4 py-8">
            <div class="w-4/5 max-w-5xl grid grid-cols-5 gap-8">
                <div class="col-span-3 rounded-md overflow-hidden"><img src="./images/room1.jpg" alt=""></div>
                <div class="col-span-2">
                    <div><span id="avg"><?= $stat['avg'] ?></span> <span id="stars"></span><?= $stat['total'] ?> ratings</div>
                    <div id="room_<?= $room->room_id ?>">
                        <div class="room_info">
                            <div><?= $room->room_name ?></div>
                            <div><?= $room->description ?></div>
                        </div>
                        <?php if ($isLoggedIn && $_SESSION['discriminator'] === 'admin') : ?>
                            <div class="hidden room_form">
                                <form method='post' enctype='multipart/form-data'>
                                    <input type="hidden" name="room_id" value="<?= $room->room_id ?>">
                                    <input class="mb-2 w-full border border-gray-300 rounded" type="text" name="room_name" value="<?= $room->room_name ?>">
                                    <textarea class="w-full border border-gray-300 rounded" name="description" rows="8"><?= $room->description ?></textarea>
                                    <div>Pictures go here...</div>
                                    <input type='file' name='images[]' id='image' multiple>
                                    <input type="submit" class="btn" name="update" value="update" />
                                    <input type="submit" class="btn" name="delete" value="delete" />
                                </form>
                            </div>
                        <?php endif ?>
                    </div>
                </div>
            </div>
            <?php if ($isLoggedIn && $_SESSION['discriminator'] === 'admin') : ?>
                <div class="w-4/5 max-w-5xl flex justify-end">
                    <button type="submit" class="btn edit" onclick="toggleForm('<?= $room->room_id ?>', 'room')">Edit</button>
                </div>
            <?php endif ?>
        </section>
        <section class="flex justify-center px-4 py-8">
            <ul class="w-4/5 max-w-5xl">
                <li class="w-4/5 max-w-5xl flex justify-end">
                    <form method="get">
                        <select name="" id="">
                            <option value=""></option>
                        </select>
                    </form>
                </li>
                <?php if ($isLoggedIn && $_SESSION['discriminator'] === 'customer') : ?>
                    <li class="mb-4">
                        <div class="mb-4"><button id="create" class="btn edit">Create</button></div>
                        <div id="createForm" class="hidden">
                            <div class="grid grid-cols-4">
                                <div class="col-span-1">
                                    <div class="flex justify-start items-end">
                                        <img src="./images/userIcons/default_icon_48.png" alt="">
                                        <h4 class="ml-4"><?= $_SESSION['user_name'] ?></h4>
                                    </div>
                                </div>
                                <div class="col-span-3">
                                    <form method='post' enctype='multipart/form-data' id="form">
                                        <input type="hidden" name="user_id" value="<?= $_SESSION['user_id'] ?>">
                                        <input class="mb-2 w-full border border-gray-300 rounded" type="number" min="1" max="5" placeholder="Give a star rating..." name="star_rating">
                                        <textarea class="w-full border border-gray-300 rounded" name="review_content" rows="5" placeholder="Write a comment about your stay..."></textarea>
                                        <div>Pictures go here...</div>
                                        <input type='file' name='images[]' id='image' multiple>
                                        <input type="submit" class="btn" name="insert" value="submit" />
                                    </form>
                                </div>
                            </div>
                        </div>
                    </li>
                <?php endif ?>
                <?php foreach ($reviews as $review) : ?>
                    <li class="grid grid-cols-4 mb-4">
                        <div class="col-span-1">
                            <div class="flex justify-start items-end">
                                <img src="./images/userIcons/default_icon_48.png" alt="">
                                <h4 class="ml-4"><?= $review->user_name ?></h4>
                            </div>
                            <p><?= $review->get_formatted_datetime() ?></p>
                            <?php if ($isLoggedIn && $_SESSION['discriminator'] === 'customer' && $_SESSION['user_id'] === $review->user_id) : ?>
                                <button type="button" class="btn edit" onclick="toggleForm('<?= $review->review_id ?>', 'review')">Edit</button>
                            <?php endif ?>
                            <?php if ($isLoggedIn && $_SESSION['discriminator'] === 'admin' && empty($review->reply_content)) : ?>
                                <button type="button" class="btn edit" onclick="toggleForm('<?= $review->review_id ?>', 'reply')">reply</button>
                            <?php endif ?>
                        </div>
                        <div class="col-span-3" id="review_<?= $review->review_id ?>">
                            <div class="review_info">
                                <div class="star_rating"><?= $review->star_rating ?></div>
                                <div class="review_content"><?= $review->review_content ?></div>
                                <div class="pictures">pictures go here...</div>
                                <div id="reply_<?= $review->review_id ?>">
                                    <div class="reply_info"><?= empty($review->reply_content) ? '' : $review->reply_content ?></div>
                                    <?php if ($isLoggedIn && $_SESSION['discriminator'] === 'admin' && empty($review->reply_content)) : ?>
                                        <div class="hidden reply_form">
                                            <form method="post">
                                                <input type="hidden" name="review_id" value="<?= $review->review_id ?>">
                                                <textarea class="w-full border border-gray-300 rounded" name="reply_content" rows="3"></textarea>
                                                <input type="submit" class="btn" name="insert" value="submit" />
                                            </form>
                                        </div>
                                    <?php endif ?>
                                </div>
                            </div>
                            <div class="hidden review_form"></div>
                        </div>
                    </li>
                <?php endforeach ?>
            </ul>
        </section>
    </main>
    <script src="./js/room.js"></script>
</body>

</html>