<?php

class Review
{
    public int $review_id;
    public int $user_id;
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

    static function queryReviewsByRoomIdWithOrderBy($id, array $orderBy)
    {
        require_once('./db/DBConnection.php');

        $db = new DBConnection();
        $query = 'SELECT r.review_id, r.user_id, u.user_name, r.room_id, r.review_content, r.star_rating, r.created_at, re.reply_id, re.reply_content FROM reviews r JOIN users u ON r.user_id = u.user_id LEFT JOIN replies re ON r.review_id = re.review_id WHERE r.room_id = :room_id';
        if (!empty($orderBy['name'])) {
            $query .= " ORDER BY {$orderBy['name']}";
            if (!empty($orderBy['how'])) {
                $query .= " {$orderBy['how']}";
            }
        }
        $keyValuePairs = ['room_id' => $id];
        $objs = $db->queryObjectsByBindingParams($query, $keyValuePairs, 'Review');
        return $objs;
    }
}
