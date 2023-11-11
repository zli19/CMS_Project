<?php

function handlePostFromRoomView()
{
    $result = false;

    if ($_SESSION['discriminator'] === 'admin') {
        require('./models/Room.php');
        $room_id = filter_input(INPUT_POST, 'room_id', FILTER_VALIDATE_INT);
        $room_name = filter_input(INPUT_POST, 'room_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $review_id = filter_input(INPUT_POST, 'review_id', FILTER_VALIDATE_INT);
        $reply_content = filter_input(INPUT_POST, 'reply_content', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        if (
            !empty($_POST['update']) &&
            $room_id &&
            $room_name &&
            $description
        ) {
            $keyValuePairs = ['room_name' => $room_name, 'description' => $description];
            $result = Room::updateRoomById($room_id, $keyValuePairs);
        }
        if (!empty($_POST['delete']) && $room_id) {
            $result = Room::deleteRoomById($room_id);
        }
        if (!empty($_POST['insert']) && $review_id && $reply_content) {
            require('./models/Reply.php');
            $keyValuePairs = ['review_id' => $review_id, 'reply_content' => $reply_content];
            $result = Reply::insertReplyByBindingParams($keyValuePairs);
        }
    }

    if ($_SESSION['discriminator'] === 'customer') {
        require('./models/Review.php');
        $room_id = filter_input(INPUT_POST, 'room_id', FILTER_VALIDATE_INT);
        $star_rating = filter_input(INPUT_POST, 'star_rating', FILTER_VALIDATE_INT);
        $review_content = filter_input(INPUT_POST, 'review_content', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $review_id = filter_input(INPUT_POST, 'review_id', FILTER_VALIDATE_INT);
        if (
            !empty($_POST['insert']) &&
            $room_id &&
            $star_rating &&
            $review_content
        ) {
            $keyValuePairs = ['user_id' => $_SESSION['user_id'], 'room_id' => $room_id, 'star_rating' => $star_rating, 'review_content' => $review_content];
            $result = Review::insertReviewByBindingParams($keyValuePairs);
        }
        if (
            !empty($_POST['update']) &&
            $star_rating &&
            $review_content &&
            $review_id
        ) {
            $keyValuePairs = ['star_rating' => $star_rating, 'review_content' => $review_content];
            $result = Review::updateReviewById($review_id, $keyValuePairs);
        }
        if (!empty($_POST['delete']) && $review_id) {
            $result = Review::deleteReviewById($review_id);
        }
    }

    return $result;
}
