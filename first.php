<?php
function imgResize ($type, $src, $dest, $width, $height, $quality, $userText = null) {
    $tmp_image = "";

    if("png" == $type) {
        $tmp_image = imagecreatefrompng($src);
    } elseif("jpg" == $type) {
        $tmp_image = imagecreatefromjpeg($src);
    }

    $font = getcwd() . "/fonts/open-sans/OpenSans-Bold.ttf";
    if(isset($_POST['font_size'])) {
        $font_size = $_POST['font_size'];
    } else {
        $font_size = 30;
    }

    $new_tmp_image = imagecreatetruecolor($width, $height);
    $color = imagecolorallocate($new_tmp_image, 0, 0, 0);
    $background_color = imagecolorallocate($new_tmp_image, 255, 255, 255);

    $uText = "";
    $tmp_text = "";
    $box_img = null;

    $image_width = imagesx($tmp_image);
    $image_height = imagesy($tmp_image);

    if(null == $userText) {
        $uText = "Test Text";
    } else {
        $uText = explode(" ", $userText);
        if($image_width < $image_height) {
            foreach($uText as $word) {
                $tmp_text .= "\n".$word;
            }
        } else {
            foreach($uText as $word) {
                $tmp_text .= " ".$word;
            }
        }
        $box_img = imagettfbbox($font_size, 0, $font, $tmp_text . " " . $word);
    }

    $tmp_text = trim($tmp_text);
    
    imagefilledrectangle($tmp_image, 0, 0, $width, $font_size * 2, $background_color);
    imagecopyresampled($new_tmp_image, $tmp_image, 0, 0, 0, 0, $width, $height, $width, $height);
    imagettftext($new_tmp_image, $font_size, 0, 0, $font_size * 1.5, $color, $font, $tmp_text);
    
    if("jpg" == $type) {
        imagejpeg($new_tmp_image, $dest, $quality);
    } else {
        imagepng($new_tmp_image, $dest, $quality);
    }
    
    $returnData = file_get_contents($dest);
    echo 'data:image/' . $type . ';base64,' . base64_encode($returnData);

    imagedestroy($tmp_image);
    imagedestroy($new_tmp_image);
    imagedestroy($box_img);
}

/*=================== PROGRAM BEGIN ===================*/

$uploadDir =  getcwd() . "/uploads/";
$new_file = "";
$new_filename = "";
$new_filename_size = "";
$new_image_height = "";
$new_image_width = "";

$image_file_type = "";

if(((isset($_POST['fn']) && $_POST['fn'] != "") && (isset($_POST['ln']) && $_POST['ln'] != ""))) {

    $new_file = basename($_FILES['uFile']['name']);
    $new_filename = $uploadDir . $new_file;

    $new_filename_size = filesize($uploadDir . $new_file);

    $image_file_type = strtolower(pathinfo($new_filename, PATHINFO_EXTENSION));

    if($image_file_type != "png" && $image_file_type != "jpg") {
        // $output['error'] = "You cannot upload this type of image. Only supported formats are png or jpg.";
        // header("Error: No file uploaded", true, 500);
        // echo json_encode($output);
        header("You cannot upload this type of image. Only supported formats are png or jpg.", true, 500);
        return 0;
    }

    if(file_exists($new_filename)) {

        $new_file = date("dmy_his") . "_" . basename($_FILES['uFile']['name']);
        $new_filename = $uploadDir . $new_file;
        copy($_FILES['uFile']['tmp_name'], $new_filename);

        if($new_filename_size > 0 && $new_filename_size < 1000000) {
            // echo "The file has been renamed and successfully saved on this machine!";
            // echo "<br/>";
            // echo "Thie file name is " . $new_file . " and file size is " . $new_filename_size / 1000 . "bytes.";
        } else {
            echo "There seem to have been an issue with saving the file or it is over 1MB in size.";
            return 0;
        }
    } else {
        copy($_FILES['uFile']['tmp_name'], $new_filename);
        $new_filename_size = filesize($uploadDir . $new_file);
        if($new_filename_size > 0 && $new_filename_size < 1000000) {
            // echo "The file has been successfully saved on this machine!";
            // echo "<br/>";
            // echo "Thie file name is " . $new_file . " and file size is " . $new_filename_size / 1000 . "bytes.";
        } else {
            echo "There was an issue with saving the file. It might be too big. File size is " . $new_filename_size / 1000 . " bytes.";
            return 0;
        }
    }

    $userFormText = $_POST['fn'] . " " . $_POST['ln'];

    if(isset($_POST['orientation_x']) && isset($_POST['orientation_y'])) {
        $new_image_height = $_POST['orientation_y'];
        $new_image_width = $_POST['orientation_x'];
    } else {
        $new_image_height = 800;
        $new_image_width = 800;
    }

    imgResize($image_file_type, $new_filename, $uploadDir . date("dmy_his") . "_new" . "." . $image_file_type, 
           (int) $new_image_width, (int) $new_image_height, 9, $userFormText);

} else {
    echo "Please fill in the all the information in the fields.";
    return 0;
}
?>