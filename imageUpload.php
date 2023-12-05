<?php


function is_valid_uploaded_files()
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

function resizeImage($path, $destinationPath, int $resizeToWidth)
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

function uploadImage(string $className, int $id, array $resizeToWidthArray = [])
{
    require_once('./models/Image.php');
    $img = new Image();
    switch ($className) {
        case 'User':
            $img->user_id = $id;
            break;
        case 'Review':
            $img->review_id = $id;
            break;
        case 'Room':
            $img->room_id = $id;
            break;
    }

    $upload_dir = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . $className . 'Images';
    // make sure target upload folder exists.
    if (!is_dir($upload_dir)) mkdir($upload_dir);

    if (is_valid_uploaded_files()) {
        $length = count($_FILES['image']['name']);
        for ($i = 0; $i < $length; $i++) {
            $path = $upload_dir . DIRECTORY_SEPARATOR . $className . $id . '_' . $i . '_origin.' . pathinfo($_FILES['image']['full_path'][$i], PATHINFO_EXTENSION);
            if (move_uploaded_file($_FILES['image']['tmp_name'][$i], $path)) {
                $img->is_resized = false;
                $img->path = $path;
                $result = $img->insertImage();

                if ($result && !empty($resizeToWidthArray)) {
                    foreach ($resizeToWidthArray as $width) {
                        $destinationPath = $upload_dir . DIRECTORY_SEPARATOR . $className . $id . '_' . $i . '_width' . $width . '.' . pathinfo($path, PATHINFO_EXTENSION);
                        if (resizeImage($path, $destinationPath, $width)) {
                            $img->path = $destinationPath;
                            $img->is_resized = true;
                            $img->parent_img_id = intval($result);
                            $img->insertImage();
                        }
                    }
                }
            }
        }
    }
}
