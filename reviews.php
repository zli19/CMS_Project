<?php
session_start();

require('./utils/Auth.php');
$auth = new Auth();
$isLoggedIn = $auth->isLoggedIn();

if ($_POST && $isLoggedIn) {

    require('./processPost.php');

    $result = handlePostFromRoomView();

    $_SESSION['message'] = $result ? 'Success!' : 'Failure.';

    header("Location: reviews.php");
    exit;
}

require('./models/Review.php');
$search = '';
if (!empty($_GET['search'])) {
    $search = filter_var(trim($_GET['search']), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
}

$idOrRating = [];
if (filter_input(INPUT_GET, 'room_id', FILTER_VALIDATE_INT)) {
    $idOrRating['room_id'] = filter_input(INPUT_GET, 'room_id', FILTER_SANITIZE_NUMBER_INT);
}
if (filter_input(INPUT_GET, 'rating', FILTER_VALIDATE_INT)) {
    $idOrRating['star_rating'] = filter_input(INPUT_GET, 'rating', FILTER_SANITIZE_NUMBER_INT);
}

$orderBy = ['created_at'];
if (!empty($_GET['orderBy'])) {
    $orderBy = array(filter_input(INPUT_GET, 'orderBy', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
}

$reviews = Review::queryReviewsWithRoomOrRatingWithOrderBy($search, $idOrRating, $orderBy);
require('./models/Room.php');
$rooms = Room::queryRoomsOrderBy([]);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Molijun Inn</title>
    <link rel="stylesheet" href="./main.css">

</head>

<body>
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
    </main>
    <section class="flex justify-center px-4 py-8">
        <ul class="w-4/5 max-w-5xl">
            <li class="w-full">
                <form class="flex justify-end w-full align-middle" method="get" action="./reviews.php">
                    <div class="mx-4">
                        <input id="searchInput" class="inline w-64 border py-1 border-green-800" type="text" name="search" value="<?= !empty($_GET['search']) ? $_GET['search'] : '' ?>">
                        <button type="submit" class="inline btn">Search</button>
                    </div>

                    <div class="my-1">
                        <label class="text-end text-sm" for="room_id">Room: </label>
                        <select class="border border-green-800 rounded text-sm bg-gray-300" name="room_id">
                            <option value="">All</option>
                            <?php foreach ($rooms as $room) : ?>
                                <option value="<?= $room->room_id ?>" <?= !empty($_GET['room_id']) && $_GET['room_id'] === strval($room->room_id) ? 'selected' : '' ?>><?= $room->room_name ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>
                    <div class="my-1">
                        <label class="text-end text-sm" for="rating">Rating: </label>
                        <select class="border border-green-800 rounded text-sm bg-gray-300" name="rating">
                            <option value="">All</option>
                            <option value="5" <?= !empty($_GET['rating']) && $_GET['rating'] === '5' ? 'selected' : '' ?>>5 star</option>
                            <option value="4" <?= !empty($_GET['rating']) && $_GET['rating'] === '4' ? 'selected' : '' ?>>4 star</option>
                            <option value="3" <?= !empty($_GET['rating']) && $_GET['rating'] === '3' ? 'selected' : '' ?>>3 star</option>
                            <option value="2" <?= !empty($_GET['rating']) && $_GET['rating'] === '2' ? 'selected' : '' ?>>2 star</option>
                            <option value="1" <?= !empty($_GET['rating']) && $_GET['rating'] === '1' ? 'selected' : '' ?>>1 star</option>
                        </select>
                    </div>
                    <div class="my-1">
                        <label class="text-end text-sm" for="orderBy">Order By: </label>
                        <select class="border border-green-800 rounded text-sm bg-gray-300" name="orderBy">
                            <option value="created_at" <?= !empty($_GET['orderBy']) && $_GET['orderBy'] === 'created_at' ? 'selected' : '' ?>>Most Recent</option>
                            <option value="star_rating" <?= !empty($_GET['orderBy']) && $_GET['orderBy'] === 'star_rating' ? 'selected' : '' ?>>Top Ratings</option>
                        </select>
                    </div>

                </form>
            </li>
            <!-- display only when user is a customer -->
            <li class="mb-4">
                <?php if ($isLoggedIn && $_SESSION['discriminator'] === 'customer') : ?>
                    <div class="my-4"><button id="create" class="btn edit">Create</button></div>
                <?php endif ?>
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
                                <select name="room_id" id="room_id" class="mb-2 px-2 py-1 rounded bg-white border border-gray-300">
                                    <option value="">choose your targeted room</option>
                                    <?php foreach ($rooms as $room) : ?>
                                        <option value="<?= $room->room_id ?>"><?= $room->room_name ?></option>
                                    <?php endforeach ?>
                                </select>
                                <input class="mb-2 w-full border border-gray-300 rounded" type="number" min="1" max="5" placeholder="Give a star rating..." name="star_rating">
                                <textarea class="w-full border border-gray-300 rounded" name="review_content" rows="5" placeholder="Write a comment about your stay..."></textarea>
                                <div>Pictures go here...</div>
                                <input type='file' name='images[]' id='image' multiple>
                                <input type="submit" class="btn" name="insert" value="submit" />
                                <input type="hidden" name="type" value="review">
                            </form>
                        </div>
                    </div>
                </div>
            </li>

            <?php foreach ($reviews as $review) : ?>
                <li class="grid grid-cols-4 mb-4 bg-white rounded">
                    <div class="col-span-1">
                        <div class="flex justify-start items-end">
                            <img src="./images/userIcons/default_icon_48.png" alt="">
                            <?php if (empty($review->user_name)) : ?>
                                <h4 class="ml-4 text-gray-400">deregistered user</h4>
                            <?php else : ?>
                                <h4 class="ml-4 user_name"><?= $review->user_name ?></h4>
                            <?php endif ?>
                        </div>
                        <p><?= $review->get_formatted_datetime() ?></p>
                        <!-- display only when user is a customer and review is written by this user -->
                        <?php if ($isLoggedIn && (($_SESSION['discriminator'] === 'customer' && $_SESSION['user_id'] === $review->user_id) || ($_SESSION['discriminator'] === 'admin'))) : ?>
                            <button type="button" class="btn edit w-20" onclick="toggleForm('<?= $review->review_id ?>', 'review')">Edit</button>
                        <?php endif ?>
                        <!-- display only when user is an administrator and reply is empty -->
                        <?php if ($isLoggedIn && $_SESSION['discriminator'] === 'admin' && empty($review->reply_content)) : ?>
                            <button type="button" class="btn edit w-20" onclick="toggleForm('<?= $review->review_id ?>', 'reply')">reply</button>
                        <?php endif ?>
                    </div>
                    <div class="col-span-3" id="review_<?= $review->review_id ?>">
                        <div class="review_info">
                            <div>
                                <?php for ($i = 0; $i < $review->star_rating; $i++) : ?>
                                    <img class="inline-block py-2" src="./images/star_16.png" alt="star">
                                <?php endfor ?>
                                <span class="text-sm text-gray-400"> for stay at </span>
                                <span class="font-mono font-bold text-lg room_name"> <?= $review->room_name ?></span>
                            </div>
                            <div class="hidden room_id"><?= $review->room_id ?></div>
                            <div class="hidden star_rating"><?= $review->star_rating ?></div>
                            <div class="review_content"><?= $review->review_content ?></div>
                            <div class="pictures">pictures go here...</div>
                            <div id="reply_<?= $review->review_id ?>">
                                <!-- display reply if exists -->
                                <div class="reply_info">
                                    <?php if (!empty($review->reply_content)) : ?>
                                        <div class="border border-gray-400 p-2 my-2 rounded text-sm">
                                            <p class="font-bold">Response from Molijun Inn:</p>
                                            <div><?= $review->reply_content ?></div>
                                        </div>
                                    <?php endif ?>
                                </div>
                                <!-- enable reply if user is admin and no reply exists -->
                                <?php if ($isLoggedIn && $_SESSION['discriminator'] === 'admin' && empty($review->reply_content)) : ?>
                                    <div class="hidden reply_form">
                                        <form method="post">
                                            <input type="hidden" name="review_id" value="<?= $review->review_id ?>">
                                            <textarea class="w-full border border-gray-300 rounded" name="reply_content" rows="3"></textarea>
                                            <input type="submit" class="btn" name="insert" value="submit" />
                                            <input type="hidden" name="type" value="reply">
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
    <script src="./js/room.js"></script>
    <script src="./js/highlightSearchResult.js"></script>
</body>

</html>