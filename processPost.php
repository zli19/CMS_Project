<?php
require_once('./models/Image.php');

function handlePostFromRoomView()
{
    $result = false;
    $type = filter_input(INPUT_POST, 'type', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    if ($type === 'review') {
        require_once('./models/Review.php');
        $review = new Review();
        if (filter_input(INPUT_POST, 'room_id', FILTER_VALIDATE_INT)) {
            $review->room_id = filter_input(INPUT_POST, 'room_id', FILTER_SANITIZE_NUMBER_INT);
        }
        if (filter_input(INPUT_POST, 'star_rating', FILTER_VALIDATE_INT)) {
            $review->star_rating = filter_input(INPUT_POST, 'star_rating', FILTER_SANITIZE_NUMBER_INT);
        }
        $review->review_content = filter_input(INPUT_POST, 'review_content', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        if (filter_input(INPUT_POST, 'review_id', FILTER_VALIDATE_INT)) {
            $review->review_id = filter_input(INPUT_POST, 'review_id', FILTER_SANITIZE_NUMBER_INT);
        }

        if (
            !empty($_POST['insert']) &&
            !empty($review->room_id) &&
            !empty($review->star_rating) &&
            !empty($review->review_content)
        ) {
            $review->user_id = $_SESSION['user_id'];
            $result = $review->insertReview();
            if ($result && !empty($_FILES['image']['tmp_name'][0])) {
                $img = new Image;
                $img->review_id = intval($result);
                $destination = './images';
                $img->uploadImageTo($destination, [200, 1230]);
            }
        }

        if (
            !empty($_POST['update']) &&
            !empty($review->room_id) &&
            !empty($review->review_id) &&
            !empty($review->star_rating) &&
            !empty($review->review_content)
        ) {
            $result = $review->updateReview();
            if ($result && !empty($_FILES['image']['tmp_name'][0])) {
                $img = new Image;
                $img->review_id = $review->review_id;
                $destination = './images';
                $img->uploadImageTo($destination, [200, 1230]);
            }
        }
        if (!empty($_POST['delete']) && $review->review_id) {
            $result = $review->removeReviewAndItsImages();
        }
    }

    if ($type === 'room') {
        require_once('./models/Room.php');
        $room = new Room();
        if (filter_input(INPUT_POST, 'room_id', FILTER_VALIDATE_INT)) {
            $room->room_id = filter_input(INPUT_POST, 'room_id', FILTER_SANITIZE_NUMBER_INT);
        }
        $room->room_name = filter_input(INPUT_POST, 'room_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $room->description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        if (!empty($_POST['update']) && !empty($room->room_id) && !empty($room->room_name) && !empty($room->description)) {
            $result = $room->updateRoom();
            if ($result && !empty($_FILES['image']['tmp_name'][0])) {
                $img = new Image;
                $img->room_id = $room->room_id;
                $destination = './images';
                $img->uploadImageTo($destination, [128, 1230]);
            }
        }
        if (!empty($_POST['delete']) && !empty($room->room_id)) {
            $result = $room->removeRoomAndItsImages();
        }
    }

    if ($type === 'reply') {
        require_once('./models/Reply.php');
        $reply = new Reply();
        if (filter_input(INPUT_POST, 'review_id', FILTER_VALIDATE_INT)) {
            $reply->review_id = filter_input(INPUT_POST, 'review_id', FILTER_SANITIZE_NUMBER_INT);
        }
        $reply->reply_content = filter_input(INPUT_POST, 'reply_content', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        if (!empty($_POST['insert']) && $reply) {
            $result = $reply->insertReply();
        }
    }

    if ($type === 'image') {
        $image_id = filter_input(INPUT_POST, 'image_id', FILTER_VALIDATE_INT) ? filter_input(INPUT_POST, 'image_id', FILTER_SANITIZE_NUMBER_INT) : null;
        if (!empty($_POST['delete']) && $image_id) {
            $images = Image::getImagesByAttribute('image_id', $image_id);
            if (!empty($images)) {
                $result = $images[0]->removeImageAndFile();
            }
        }
    }

    return $result;
}

function handlePostFromIndex()
{
    $result = false;

    require_once('./models/Room.php');
    $room = new Room();
    $room->room_name = filter_input(INPUT_POST, 'room_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $room->description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    if (!empty($_POST['insert']) && !empty($room->room_name) && !empty($room->description)) {
        $result = $room->insertRoom();
        if ($result && !empty($_FILES['image']['tmp_name'][0])) {
            $img = new Image;
            $img->room_id = intval($result);
            $destination = './images';
            $img->uploadImageTo($destination, [128, 1230]);
        }
    }
    return $result;
}

function handlePostFromAdmin()
{
    $result = false;

    require_once('./models/User.php');
    $user = new User();

    if (filter_input(INPUT_POST, 'user_id', FILTER_VALIDATE_INT)) {
        $user->user_id = filter_input(INPUT_POST, 'user_id', FILTER_SANITIZE_NUMBER_INT);
    }
    $user_name = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    if ($user_name) {
        $user->user_name = $user_name;
    }
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    if ($email) {
        $user->email = $email;
    }
    $discriminator = filter_input(INPUT_POST, 'discriminator', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    if ($discriminator && $discriminator === 'admin') {
        $user->discriminator = 'admin';
    } else {
        $user->discriminator = 'customer';
    }

    if (
        !empty($_POST['insert']) &&
        !empty($user->user_name) &&
        !empty($user->email) &&
        !empty($_POST['password'])
    ) {
        $hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $user->password = $hash;
        $result = $user->insertUser();
    }

    if (
        !empty($_POST['delete']) &&
        !empty($user->user_id)
    ) {
        $result = $user->deleteUser();
    }

    if (
        !empty($_POST['update']) &&
        !empty($user->user_id) &&
        !empty($user->user_name) &&
        !empty($user->email)
    ) {
        $result = $user->updateUser();
    }
    return $result;
}
