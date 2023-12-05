<?php
require_once('./db/DBConnection.php');
class Review
{
    public int $review_id;
    public ?int $user_id;
    public ?string $user_name;
    public int $room_id;
    public ?string $room_name;
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

    static function queryReviewsWithRoomOrRatingWithOrderBy(?string $search, array $idOrRating, array $orderBy)
    {
        $db = new DBConnection();
        $query = 'SELECT r.review_id, r.user_id, u.user_name, r.room_id, rooms.room_name, r.review_content, r.star_rating, r.created_at, re.reply_id, re.reply_content FROM reviews r LEFT JOIN users u ON r.user_id = u.user_id LEFT JOIN replies re ON r.review_id = re.review_id JOIN rooms ON r.room_id = rooms.room_id';

        $keyValuePairs = $idOrRating;

        if ($idOrRating) {
            $query .= ' WHERE ';
            $i = 0;
            foreach ($idOrRating as $key => $value) {
                $query .= "r.{$key} = :{$key}";
                if (count($idOrRating) > 1 && $i < count($idOrRating) - 1) {
                    $query .= ' AND ';
                }
                $i++;
            }
        }

        if ($search) {
            if ($idOrRating) {
                $query .= ' AND ';
            } else {
                $query .= ' WHERE ';
            }
            $query .= '(LOWER(r.review_content) LIKE LOWER(:content) OR LOWER(u.user_name) LIKE LOWER(:content) OR LOWER(rooms.room_name) LIKE LOWER(:content))';
            $keyValuePairs['content'] = '%' . $search . '%';
        }

        if ($orderBy) {
            $query .= " ORDER BY {$orderBy[0]} DESC";
        }

        $objs = $db->queryObjectsByBindingParams($query, $keyValuePairs, 'Review');
        return $objs;
    }

    function insertReview()
    {
        $db = new DBConnection();
        $keyValuePairs = ['user_id' => $this->user_id, 'room_id' => $this->room_id, 'star_rating' => $this->star_rating, 'review_content' => $this->review_content];
        $result = $db->insertObject($keyValuePairs, 'Review');
        return $result;
    }

    function deleteReview()
    {
        $db = new DBConnection();

        $result = $db->deleteObjectByAttribute('review_id', $this->review_id, 'Review');
        return $result;
    }

    function removeReviewAndItsImages()
    {
        require_once('./models/Image.php');
        $images = Image::getImagesByAttribute('review_id', $this->review_id);
        // Since images table is set to 'ON DELETE CASCADE', there's no need to delete images record individually.
        // but still need to remove image files.
        $result = $this->deleteReview();
        if ($result && !empty($images)) {
            $return = true;
            foreach ($images as $image) {
                $r = unlink(realpath($image->path));
                if (!$r && $return) {
                    $return = false;
                }
            }
            return $return;
        } else {
            return $result;
        }
    }

    function updateReview()
    {
        $db = new DBConnection();
        $keyValuePairs = ['room_id' => $this->room_id, 'star_rating' => $this->star_rating, 'review_content' => $this->review_content];
        $result = $db->updateObjectByAttribute('review_id', $this->review_id, $keyValuePairs, 'Review');
        return $result;
    }
}
