<?php

class Review
{
    public int $review_id;
    public ?int $user_id;
    public ?string $user_name;
    public int $room_id;
    public string $review_content;
    public int $star_rating;
    public string $created_at;
    public ?int $reply_id;
    public ?string $reply_content;
    public ?int $image_id;
    public ?string $path;

    function get_formatted_datetime()
    {
        $date = new DateTimeImmutable($this->created_at);
        return $date->format('d/M/Y, h:i A');
    }

    static function queryReviewsByRoomIdWithRatingAndOrderBy(int $id, array $options)
    {
        require_once('./db/DBConnection.php');

        $db = new DBConnection();
        $query = 'SELECT r.review_id, r.user_id, u.user_name, r.room_id, r.review_content, r.star_rating, r.created_at, re.reply_id, re.reply_content FROM reviews r LEFT JOIN users u ON r.user_id = u.user_id LEFT JOIN replies re ON r.review_id = re.review_id WHERE r.room_id = :room_id';
        $keyValuePairs = ['room_id' => $id];
        if (!empty($options['rating'])) {
            $query .= " AND r.star_rating = :star_rating";
            $keyValuePairs['star_rating'] = $options['rating'];
        }
        if (!empty($options['orderBy'])) {
            $query .= " ORDER BY {$options['orderBy']} DESC";
        }
        $objs = $db->queryObjectsByBindingParams($query, $keyValuePairs, 'Review');
        return $objs;
    }

    function insertReview()
    {
        require_once('./db/DBConnection.php');

        $db = new DBConnection();
        $keyValuePairs = ['user_id' => $this->user_id, 'room_id' => $this->room_id, 'star_rating' => $this->star_rating, 'review_content' => $this->review_content];
        $result = $db->insertObject($keyValuePairs, 'Review');
        return $result;
    }

    function deleteReview()
    {
        require_once('./db/DBConnection.php');

        $db = new DBConnection();
        $result = $db->deleteObjectByAttribute('review_id', $this->review_id, 'Review');
        return $result;
    }

    function updateReview()
    {
        require_once('./db/DBConnection.php');

        $db = new DBConnection();
        $keyValuePairs = ['room_id' => $this->room_id, 'star_rating' => $this->star_rating, 'review_content' => $this->review_content];
        $result = $db->updateObjectByAttribute('review_id', $this->review_id, $keyValuePairs, 'Review');
        return $result;
    }
}
