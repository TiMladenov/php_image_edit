<?php

function imgResize ($type, $src, $dest, $width, $height, $quality, $userText = null) {
    $tmp_image = "";

    if($type == "png") {

        $tmp_image = imagecreatefrompng($src);
        $new_tmp_image = imagecreatetruecolor($width, $height);
        $color = imagecolorallocate($new_tmp_image, 0, 0, 0);

        $tmp_text = "";
        $box_img = null;

        $font = getcwd() . "/fonts/open-sans/OpenSans-Bold.ttf";
        $font_size = 30;

        list($image_width, $image_height) = getimagesize($src);
        $image_margin = 5;

        if(null == $userText) {
            $userText = "Test Text";
        } else {
            $userText = explode(" ", $userText);
            foreach($userText as $word) {
                $box_img = imagettfbbox($font_size, 0, $font, $tmp_text . " " . $word);
                
                if($box_img[2] > $image_width - $image_margin * 2) {
                    $tmp_text .= "\n".$word;
                } else {
                    $tmp_text .= " ".$word;
                }
            }
        }

        $tmp_text = trim($tmp_text);
        
        imagecopyresampled($new_tmp_image, $tmp_image, 0, 0, 0, 0, $width, $height, $width, $height);
        imagettftext($new_tmp_image, $font_size, 0, imagesx($src) / 2, 50, $color, $font, $tmp_text);
        imagepng($new_tmp_image, $dest, $quality);

        imagedestroy($tmp_image);
        imagedestroy($new_tmp_image);
        imagedestroy($box_img);
    } elseif($type == "jpg" || $type == "jpeg") {
        $tmp_image = imagecreatefromjpeg($src);
    }
}

$uploadDir =  getcwd() . "/uploads/";
$new_file = "";
$new_filename = "";
$new_filename_size = "";

$image_file_type = "";

if(isset($_POST['upload']) && ((isset($_POST['firstname']) && $_POST['firstname'] != "") 
                            && (isset($_POST['lastname']) && $_POST['lastname'] != ""))) {
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
            die("There was an issue with saving the file. It might be too big.");
        }
    }

    $userFormText = $_POST['firstname'] . " " . $_POST['lastname'];

    imgResize($image_file_type, $new_filename, $uploadDir . time() . "." . $image_file_type, 600, 600, 9, $userFormText);

} else {
    die("Please fill in the all the information in the fields.");
}
?>