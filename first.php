<?php
function imgResize ($type, $src, $dest, $width, $height, $quality, $userText = null) {
    $tmp_image = "";

    if("png" == $type) {
        $tmp_image = imagecreatefrompng($src);
    } elseif("jpg" == $type) {
        $tmp_image = imagecreatefromjpeg($src);
    }

    $font = getcwd() . "/fonts/open-sans/OpenSans-Bold.ttf";
    if(isset($_POST['fs']) && $_POST['fs'] > 0) {
        $font_size = $_POST['fs'];
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
    imagedestroy($tmp_image);
    imagedestroy($new_tmp_image);
    imagedestroy($box_img);
    return ('data:image/' . $type . ';base64,' . base64_encode($returnData));
}

/*=================== PROGRAM BEGIN ===================*/

$uploadDir =  getcwd() . "/uploads/";
$new_file = "";
$new_filename = "";
$new_filename_size = "";
$new_image_height = "";
$new_image_width = "";

$image_file_type = "";

if($_FILES['uFile']['name'] != "" && ((isset($_POST['fn']) && $_POST['fn'] != "") && (isset($_POST['ln']) && $_POST['ln'] != ""))) {

    $new_file = basename($_FILES['uFile']['name']);
    $new_filename = $uploadDir . $new_file;
    $new_filename_size = $_FILES['uFile']['size'];

    //TODO: Update php init file to allow file uploads bigger than 1 MB, or else the size for bigger files will always be 0 as they are not uploaded.
    //TODO: Fix wordwrap to added image text and the dynamic text background size if the added text is longer than the image's width.

    $image_file_type = strtolower(pathinfo($new_filename, PATHINFO_EXTENSION));

    if($image_file_type != "png" && $image_file_type != "jpg") {
        $msg = "You cannot upload this type of image. Only supported formats are png or jpg.";
        echo json_encode(
            array(
                'status' => false,
                'error' => $msg,
                'data' => null,
                'error_code' => 500
            )
        );
        exit;
    }

    if(file_exists($new_filename)) {

        $new_file = date("dmy_his") . "_" . basename($_FILES['uFile']['name']);
        $new_filename = $uploadDir . $new_file;

        if($new_filename_size > 0 && $new_filename_size < 1000000) {
            copy($_FILES['uFile']['tmp_name'], $new_filename);
        } else {
            $msg = "There seem to have been an issue with saving the file or it is over 1MB in size.";
            echo json_encode(
                array(
                    'status' => false,
                    'error' => $msg,
                    'data' => null,
                    'error_code' => 500
                )
            );
            exit;
        }
    } else {
        if($new_filename_size > 0 && $new_filename_size < 1000000) {
            copy($_FILES['uFile']['tmp_name'], $new_filename);
        } else {
            $msg = "There was an issue with saving the file. It might be too big. File size is " . (int) $new_filename_size / 1000 . " Megabytes.";
            echo json_encode(
                array(
                    'status' => false,
                    'error' => $msg,
                    'data' => null,
                    'error_code' => 500
                )
            );
            exit;
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

    $sendData = imgResize($image_file_type, $new_filename, $uploadDir . date("dmy_his") . "_new" . "." . $image_file_type, 
           (int) $new_image_width, (int) $new_image_height, 9, $userFormText);

    echo json_encode(
    array(
        'status' => true,
        'error' => null,
        'data' => $sendData,
        'error_code' => null
        )
    );
    exit;
} else {
    $msg = "Please fill in the all the information in the fields.";
    echo json_encode(
        array(
            'status' => false,
            'error' => $msg,
            'data' => null,
            'error_code' => 500
        )
    );
    exit;
}
?>