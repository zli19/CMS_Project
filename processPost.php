<?php

function handlePostFromRoomView()
{
    $result = false;

    if ($_SESSION['discriminator'] === 'admin') {
        if (filter_input(INPUT_POST, 'room_id', FILTER_VALIDATE_INT)) {
            require('./models/Room.php');
            $room = new Room();
            $room->room_id = filter_input(INPUT_POST, 'room_id', FILTER_SANITIZE_NUMBER_INT);;
            $room->room_name = filter_input(INPUT_POST, 'room_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $room->description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            if (!empty($_POST['update']) && $room->room_name && $room->description) {
                $result = $room->updateRoom();
            }
            if (!empty($_POST['delete']) && !empty($room->room_id)) {
                $result = $room->deleteRoom();
            }
        }

        if (filter_input(INPUT_POST, 'review_id', FILTER_VALIDATE_INT)) {
            require('./models/Reply.php');
            $reply = new Reply();
            $reply->review_id = filter_input(INPUT_POST, 'review_id', FILTER_SANITIZE_NUMBER_INT);
            $reply->reply_content = filter_input(INPUT_POST, 'reply_content', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            if (!empty($_POST['insert']) && $reply) {
                $result = $reply->insertReply();
            }
        }
    }

    if ($_SESSION['discriminator'] === 'customer' && filter_input(INPUT_POST, 'room_id', FILTER_VALIDATE_INT)) {
        require('./models/Review.php');
        $review = new Review();
        $review->room_id = filter_input(INPUT_POST, 'room_id', FILTER_SANITIZE_NUMBER_INT);
        if (filter_input(INPUT_POST, 'star_rating', FILTER_VALIDATE_INT)) {
            $review->star_rating = filter_input(INPUT_POST, 'star_rating', FILTER_SANITIZE_NUMBER_INT);
        }
        $review->review_content = filter_input(INPUT_POST, 'review_content', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        if (filter_input(INPUT_POST, 'review_id', FILTER_VALIDATE_INT)) {
            $review->review_id = filter_input(INPUT_POST, 'review_id', FILTER_SANITIZE_NUMBER_INT);
        }

        if (
            !empty($_POST['insert']) &&
            $review->room_id &&
            $review->star_rating &&
            $review->review_content
        ) {
            $review->user_id = $_SESSION['user_id'];
            $result = $review->insertReview();
        }

        if (
            !empty($_POST['update']) &&
            $review->review_id &&
            $review->star_rating &&
            $review->review_content
        ) {
            $result = $review->updateReview();
        }
        if (!empty($_POST['delete']) && $review->review_id) {
            $result = $review->deleteReview();
        }
    }

    return $result;
}
