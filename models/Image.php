<?php
require_once('./db/DBConnection.php');

class Image
{
    public int $image_id;
    public ?int $user_id;
    public ?int $room_id;
    public ?int $review_id;
    public string $path;
    public ?int $parent_img_id;
    public ?int $width;


    static function getImagesByAttribute($attributeName, $value, ?int $width = null)
    {
        $db = new DBConnection();

        $keyValuePairs = [$attributeName => $value];
        $query = "SELECT * FROM images WHERE {$attributeName} = :{$attributeName}";
        if ($width) {
            $query .= ' AND width = :width';
            $keyValuePairs['width'] = $width;
        }
        $query .= ' ORDER BY image_id DESC';

        $images = $db->queryObjectsByBindingParams($query, $keyValuePairs, 'Image');
        return $images;
    }

    function getParentImages()
    {
        if (!empty($this->width) && !empty($this->parent_img_id)) {
            $result = Image::getImagesByAttribute('image_id', $this->parent_img_id);
        }
        return empty($result) ? [] :  $result;
    }

    private function getNumberOfImagesByAttribute($attributeName, $value)
    {
        $db = new DBConnection();

        $query = "SELECT COUNT(1) AS 'number' FROM Images WHERE width is null AND {$attributeName} = :{$attributeName}";
        $keyValuePairs = [$attributeName => $value];
        $result = $db->queryArrayByBindingParams($query, $keyValuePairs);
        return $result;
    }

    function insertImage()
    {
        $db = new DBConnection();

        $result = false;
        $keyValuePairs = ['path' => $this->path];

        if (!empty($this->user_id)) {
            $keyValuePairs['user_id'] = $this->user_id;
        }
        if (!empty($this->room_id)) {
            $keyValuePairs['room_id'] = $this->room_id;
        }
        if (!empty($this->review_id)) {
            $keyValuePairs['review_id'] = $this->review_id;
        }
        if (!empty($this->parent_img_id)) {
            $keyValuePairs['parent_img_id'] = $this->parent_img_id;
        }
        if (!empty($this->width)) {
            $keyValuePairs['width'] = $this->width;
        }
        if (!(empty($this->user_id) && empty($this->room_id) && empty($this->review_id))) {
            $result = $db->insertObject($keyValuePairs, 'Image');
        }
        return $result;
    }

    function deleteImage()
    {
        $db = new DBConnection();
        $result = $db->deleteObjectByAttribute('image_id', $this->image_id, 'Image');
        return $result;
    }

    function removeImageAndFile()
    {
        $result = $this->deleteImage();
        if ($result) {
            return unlink($this->path);
        }
        return false;
    }

    function uploadImageTo(string $destinationDir, array $resizeToWidthArray = [])
    {

        if (!empty($this->user_id)) {
            $className = 'user';
            $id = $this->user_id;
        }
        if (!empty($this->room_id)) {
            $className = 'room';
            $id = $this->room_id;
        }
        if (!empty($this->review_id)) {
            $className = 'review';
            $id = $this->review_id;
        }

        $upload_dir = $destinationDir . DIRECTORY_SEPARATOR . $className . 'Images';
        // make sure target upload folder exists.
        if (!is_dir($upload_dir)) mkdir($upload_dir);

        if ($this->is_valid_uploaded_files()) {
            $length = count($_FILES['image']['name']);
            $number = $this->getNumberOfImagesByAttribute($className . '_id', $id)['number'];
            for ($i = 0; $i < $length; $i++) {
                $extension = pathinfo($_FILES['image']['full_path'][$i], PATHINFO_EXTENSION);
                $path = $upload_dir . DIRECTORY_SEPARATOR . $className . $id . '_' . ($number + $i) . '_origin.' . $extension;

                if (move_uploaded_file($_FILES['image']['tmp_name'][$i], $path)) {
                    $this->path = $path;
                    $parent_img_id = $this->insertImage();

                    if ($parent_img_id && !empty($resizeToWidthArray)) {
                        $this->parent_img_id = intval($parent_img_id);

                        foreach ($resizeToWidthArray as $width) {
                            $destinationPath = $upload_dir . DIRECTORY_SEPARATOR . $className . $id . '_' . ($number + $i) . '_width' . $width . '.' . $extension;
                            if ($this->resizeImage($path, $destinationPath, $width)) {
                                $this->width = $width;
                                $this->path = $destinationPath;
                                $this->insertImage();
                                $this->parent_img_id = null;
                                $this->width = null;
                            }
                        }
                    }
                }
            }
        }
    }

    private function is_valid_uploaded_files()
    {
        $allowed_mime_types = ['image/gif', 'image/jpeg', 'image/png'];
        $allowed_file_extensions = ['gif', 'jpg', 'jpeg', 'png'];

        foreach ($_FILES['image']['tmp_name'] as $tmp_name) {
            $mime_type = mime_content_type($tmp_name);
            // check if pass the builtin function 'is_uploaded_file' and check if mime type is allowed
            if (!(is_uploaded_file($tmp_name) && in_array($mime_type, $allowed_mime_types, true))) {
                return false;
            }
        }
        foreach ($_FILES['image']['full_path'] as $full_path) {
            $extension = pathinfo($full_path, PATHINFO_EXTENSION);
            // check if file extension is allowed
            if (!in_array($extension, $allowed_file_extensions, true)) {
                return false;
            }
        }

        // check error field in $_FILES
        foreach ($_FILES['image']['error'] as $error) {
            if ($error !== 0) {
                return false;
            }
        }

        return true;
    }

    private function resizeImage($path, $destinationPath, int $resizeToWidth)
    {
        require_once 'vendor/autoload.php';
        try {
            $image = new \Gumlet\ImageResize($path);
            $image->resizeToWidth($resizeToWidth);
            $image->save($destinationPath);
            return true;
        } catch (\Gumlet\ImageResizeException) {
            return false;
        }
    }
}
