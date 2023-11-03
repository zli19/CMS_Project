<?php

class Review
{
    public int $review_id;
    public int $user_id;
    public int $room_id;
    public string $content;
    public int $star_no;
    public string $created_at;

    function get_formatted_datetime()
    {
        $date = new DateTimeImmutable($this->created_at);
        return $date->format('F d, Y, h:i A');
    }
}
