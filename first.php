<?php
//TODO: Add checks that image and names are entered

//TODO: Check image size in b, too big is to be discarted
//TODO: Add image resizing to a square
//TODO: Add users' names on top of the image

function imgResize ($type, $src, $dest, $width, $height, $quality) {
    $tmp_image = "";
    if($type == "png") {
        $tmp_image = imagecreatefrompng($src);
        $new_tmp_image = imagecreatetruecolor($width, $height);

        $color = imagecolorallocate($new_tmp_image, 0, 0, 0);
        $text = "Test text";

        
        imagecopyresampled($new_tmp_image, $tmp_image, 0, 0, 0, 0, $width, $height, $width, $height);
        //imagestring($new_tmp_image, 5, imagesx($new_tmp_image)/2, 15, $text, $color);
        imagettftext($new_tmp_image, 30, 0, 150, 50, $color, "/home/tmladenov/Desktop/php_simple_project/fonts/open-sans/OpenSans-Light.ttf", $text);
        imagepng($new_tmp_image, $dest, $quality);
        imagedestroy($tmp_image);
        imagedestroy($new_tmp_image);
    } elseif($type == "jpg" || $type == "jpeg") {
        $tmp_image = imagecreatefromjpeg($src);
    }
}

$uploadDir =  getcwd() . "/uploads/";
$new_file = "";
$new_filename = "";
$new_filename_size = "";

$image_file_type = "";

if(isset($_POST['upload']) && isset($_POST['firstname']) && isset($_POST['lastname'])) {
    $new_file = basename($_FILES['uFile']['name']);
    $new_filename = $uploadDir . $new_file;
    $new_filename_size = filesize($uploadDir . $new_file);

    $image_file_type = strtolower(pathinfo($new_filename, PATHINFO_EXTENSION));

    if($image_file_type != "png" && $image_file_type != "jpg" && $image_file_type != "jpeg" ) {
        die("You cannot upload this type of image. Only supported formats are png, jpg, jpeg.");
    }

    if(file_exists($new_filename)) {
        $new_file = time() . "_" . basename($_FILES['uFile']['name']);
        $new_filename = $uploadDir . $new_file;
        copy($_FILES['uFile']['tmp_name'], $new_filename);

        if($new_filename_size > 0) {
            echo "The file has been renamed and successfully saved on this machine!";
            echo "<br/>";
            echo "Thie file name is " . $new_file . " and file size is " . $_FILES['uFile']['size'];
        } else {
            die("There seem to have been an issue with saving the file.");
        }
    } else {
        copy($_FILES['uFile']['tmp_name'], $new_filename);
        $new_filename_size = filesize($uploadDir . $new_file);
        if($new_filename_size > 0) {
            echo "The file has been successfully saved on this machine!";
        } else {
            die("There was an issue with saving the file. It might be too big");
        }
    }
    imgResize($image_file_type, $new_filename, $uploadDir . time() . "." . $image_file_type, 600, 600, 9);
} else {
    die("Please fill in the all the information in the fields.");
}
?>