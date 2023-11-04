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
}
