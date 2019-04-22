<?php
function imgResize ($type, $src, $dest, $width, $height, $quality, $userText_top = null, $userText_bottom = null) {
    $tmp_image = "";

    if("png" == $type) {
        $tmp_image = imagecreatefrompng($src);
    } elseif("jpg" == $type) {
        $tmp_image = imagecreatefromjpeg($src);
    }

    $font = getcwd() . "/fonts/open-sans/OpenSans-Bold.ttf";
    if(isset($_POST['fs']) && ($_POST['fs'] > 0 && $_POST['fs'] <= 25)) {
        $font_size = $_POST['fs'];
    } else {
        $font_size = 15;
    }

    $new_tmp_image = imagecreatetruecolor($width, $height);
    $color = imagecolorallocate($new_tmp_image, 0, 0, 0);

    $tmp_text_top = "";
    $tmp_text_bottom = "";

    if(null == $userText_top || null == $userText_bottom) {
        $tmp_text_top = "Test";
        $tmp_text_bottom = "Text";
    } else {
        $tmp_text_top = trim(wordwrap($userText_top, $width - 190, "\n", true));
        $tmp_text_bottom = trim(wordwrap($userText_bottom, $width - 190, "\n", true));
    }

    $top_text = imagettfbbox($font_size, 0, $font, $tmp_text_top);
    $bottom_text = imagettfbbox($font_size, 0, $font, $tmp_text_bottom);

    $top_text_height = abs($top_text[5] - $top_text[1]) + 15;
    $bottom_text_height = abs($bottom_text[5] - $bottom_text[1]) + 15;

    $textImageTop = imagecreatetruecolor($width, $top_text_height);
    $textImageBottom = imagecreatetruecolor($width, $bottom_text_height);

    $topTextBoxBgrd = imagecolorallocate($textImageTop, 255, 255, 255);
    $bottomTextBoxBgrnd = imagecolorallocate($textImageBottom, 255, 255, 255);

    imagefill($textImageTop, 0, 0, $topTextBoxBgrd);
    imagefill($textImageBottom, 0, 0, $bottomTextBoxBgrnd);
    
    imagecopyresampled($new_tmp_image, $tmp_image, 0, 0, 0, 0, $width, $height, $width, $height);
    imagecopyresampled($new_tmp_image, $textImageTop, 0, 0, 0, 0, $width, $top_text_height, $width, $top_text_height);
    imagecopyresampled($new_tmp_image, $textImageBottom, 0, $height - $font_size * 3, 0, 0, $width, $bottom_text_height, $width, $bottom_text_height);

    imagettftext($new_tmp_image, $font_size, 0, 0, $font_size * 1.5, $color, $font, $tmp_text_top);
    imagettftext($new_tmp_image, $font_size, 0, 0, $height - $font_size * 1.5, $color, $font, $tmp_text_bottom);
    
    if("jpg" == $type) {
        imagejpeg($new_tmp_image, $dest, $quality);
    } else {
        imagepng($new_tmp_image, $dest, $quality);
    }
    
    $returnData = file_get_contents($dest);
    imagedestroy($tmp_image);
    imagedestroy($new_tmp_image);
    imagedestroy($textImageTop);
    imagedestroy($textImageBottom);
    return ('data:image/' . $type . ';base64,' . base64_encode($returnData));
}

function msgReturn ($_status = null, $_error = null, $_data = null, $_error_code = null) {

    $returnArr = json_encode(
        array(
            'status' => $_status,
            'error' => $_error,
            'data' => $_data,
            'error_code' => $_error_code
        )
    );
    return $returnArr;
}

/*=================== PROGRAM BEGIN ===================*/

$uploadDir =  getcwd() . "/uploads/";
$new_file = "";
$new_filename = "";
$new_filename_size = "";
$new_image_height = "";
$new_image_width = "";
$image_file_type = "";

//TODO: Document the code
//TODO: Update php init file to allow file uploads bigger than 1 MB, or else the size for bigger files will always be 0 as they are not uploaded.

if($_FILES['uFile']['name'] != "" && ((isset($_POST['fn']) && $_POST['fn'] != "") && (isset($_POST['ln']) && $_POST['ln'] != ""))) {

    $new_file = basename($_FILES['uFile']['name']);
    $new_filename = $uploadDir . $new_file;
    $new_filename_size = $_FILES['uFile']['size'];

    $image_file_type = strtolower(pathinfo($new_filename, PATHINFO_EXTENSION));

    if($image_file_type != "png" && $image_file_type != "jpg") {
        $msg = "You cannot upload this type of image. Only supported formats are png or jpg.";
        echo msgReturn(false, $msg, null, 500);
        exit;
    }

    if(file_exists($new_filename)) {

        $new_file = date("dmy_his") . "_" . basename($_FILES['uFile']['name']);
        $new_filename = $uploadDir . $new_file;

        if($new_filename_size > 0 && $new_filename_size < 1000000) {
            copy($_FILES['uFile']['tmp_name'], $new_filename);
        } else {
            $msg = "There seem to have been an issue with saving the file or it is over 1MB in size.";
            echo msgReturn(false, $msg, null, 500);
            exit;
        }
    } else {
        if($new_filename_size > 0 && $new_filename_size < 1000000) {
            copy($_FILES['uFile']['tmp_name'], $new_filename);
        } else {
            $msg = "There was an issue with saving the file. It might be too big. File size is " . number_format($new_filename_size / 1000000, 1) . " Megabyte/s.";
            echo msgReturn(false, $msg, null, 500);
            exit;
        }
        
    }

    $userFormText_top = filter_var($_POST['fn'], FILTER_SANITIZE_STRING);
    $userFormText_bottom = filter_var($_POST['ln'], FILTER_SANITIZE_STRING);

    if(isset($_POST['orientation_x']) && isset($_POST['orientation_y'])) {
        if(!filter_var($_POST['orientation_x'], FILTER_VALIDATE_INT === false)) {
            if((($_POST['orientation_x'] > 0 && $_POST['orientation_x'] <= 800) && ($_POST['orientation_y'] > 0 && $_POST['orientation_y'] <= 800))) {
                $new_image_height = $_POST['orientation_y'];
                $new_image_width = $_POST['orientation_x'];
            } else {
                $msg = "Out of bounds dimension parameters provided";
                echo msgReturn(false, $msg, null, 500);
                exit;
            }
        } else {
            $msg = "Invalid dimenstions data provided";
            echo msgReturn(false, $msg, null, 500);
            exit;
        }
    } else {
        $new_image_height = 800;
        $new_image_width = 800;
    }

    $sendData = imgResize($image_file_type, $new_filename, $uploadDir . date("dmy_his") . "_new" . "." . $image_file_type, 
           (int) $new_image_width, (int) $new_image_height, 9, $userFormText_top, $userFormText_bottom);

    echo msgReturn(true, null, $sendData, null);
    exit;
} else {
    $msg = "Please fill in the all the information in the fields.";
    echo msgReturn(false, $msg, null, 500);
    exit;
}
?>
