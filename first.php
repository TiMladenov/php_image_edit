<?php

use function PHPSTORM_META\type;

/**
 * This finction is passed the type of the uploaded image, its location on the server, the height and width of the
 * edited image, it's quality (currently hardcoded), top and bottom text, provided by the user. Then it creates
 * a temporary image with the provided height and width, to which the top and bottom texts are added. If the text
 * is slightly longer than the width of the image, it will be wrapped on a new line. The texts are then added to
 * two temporary images, which are then copied to the main temporary image that would become the edited image.
 * The temporary image file is compressed the the type of file of the original image, saved on disk, and its
 * contents are returned.
 * 
 * @param type                  The file format of the original image.
 * @param src                   The location on the server of the original uploaded image.
 * @param dest                  The destination where the edited image will be saved.
 * @param width                 The width of the edited image.
 * @param height                The height of the edited image.
 * @param quality               The quality of the edited image (hardcoded)
 * @param userText_top          The top text to be applied on the edited image.
 * @param userText_bottom       The bottom text to be applied on the edited image.
 * 
 * @param tmp_image             Creates an image from the uploaded file.
 * @param font                  The font that will be used to write on the image.
 * @param font_size             The size of the font.
 * @param new_tmp_image         Creates the edited image.
 * @param color                 The background of the edited image. Usually not seen.
 * @param tmp_text_top          Stores the top text. If it's longer than the width of the edited image, it gets wrapped.
 * @param tmp_text_bottom       Stores the bottom text. If it's longer than the width of the edited image, it gets wrapped.
 * @param top_text              Bounding box in pixels for the top text. Basically so that I can calculate how big the text will be on the image.
 * @param bottom_text           Bounding box in pixels for the bottom text. Basically so that I can calculate how big the text will be on the image.
 * @param top_text_height       Calculates the height of the top_text @see top_text
 * @param bottom_text_height    Calculates the height of the bottom_text @see bottom_text
 * @param textImageTop          Temporary image to store @see top_text.
 * @param textImageBottom       Temporary image to store @see bottom_text.
 * @param topTextBoxBgrd        White background for the @see top_text to be seen easily.
 * @param bottomTextBoxBgrnd    White background for the @see bottom_text to be seen easily.
 * @param returnData            Contents of the file containing the edited image.
 * 
 * @return                      Base64 encoded URI of the edited image.
 */

function imgResize ($type, $src, $dest, $width, $height, $quality, $userText_top = null, $userText_bottom = null) {
    $tmp_image = "";

    //Checks the type of the edited file so that an image of the correct type could be created.
    if("png" == $type) {
        $tmp_image = imagecreatefrompng($src);
    } elseif("jpg" == $type) {
        $tmp_image = imagecreatefromjpeg($src);
    }

    //Gets the font to be used and sets the font size. If larger value is provided, a hardcoded one would be used.
    $font = getcwd() . "/fonts/open-sans/OpenSans-Bold.ttf";
    if(isset($_POST['fs']) && ($_POST['fs'] > 0 && $_POST['fs'] <= 25)) {
        $font_size = $_POST['fs'];
    } else {
        $font_size = 15;
    }

    //Creates a temporary image that will become the edited image. Sets background color.
    $new_tmp_image = imagecreatetruecolor($width, $height);
    $color = imagecolorallocate($new_tmp_image, 0, 0, 0);

    $tmp_text_top = "";
    $tmp_text_bottom = "";

    //If no text is provided, static text is added. Else, the provided text is check if is longer than the edited image,
    //if it is, it will be wrapped on a new line to stay in image bounds.
    if(null == $userText_top || null == $userText_bottom) {
        $tmp_text_top = "Test";
        $tmp_text_bottom = "Text";
    } else {
        $limit = $width * 0.05;
        $tmp_text_top = trim(wordwrap($userText_top, $width - ($width - $limit), "\n", true));
        $tmp_text_bottom = trim(wordwrap($userText_bottom, $width - ($width - $limit), "\n", true));
    }

    //Creates pixeled boxes around the texts.
    $top_text = imagettfbbox($font_size, 0, $font, $tmp_text_top);
    $bottom_text = imagettfbbox($font_size, 0, $font, $tmp_text_bottom);

    //Calculates the height of the boxes. Basically abs(lower_left_Y - top_left_Y).
    //Adds 15 pixels vertical padding.
    $top_text_height = abs($top_text[5] - $top_text[1]) + 15;
    $bottom_text_height = abs($bottom_text[5] - $bottom_text[1]) + 15;

    //Creates temporary images to serve as background to user's text with height being the text height.
    $textImageTop = imagecreatetruecolor($width, $top_text_height);
    $textImageBottom = imagecreatetruecolor($width, $bottom_text_height);

    //Sets white background color to user text so that it could be see easily.
    $topTextBoxBgrd = imagecolorallocate($textImageTop, 255, 255, 255);
    $bottomTextBoxBgrnd = imagecolorallocate($textImageBottom, 255, 255, 255);

    //Applies the background color to the text background images.
    imagefill($textImageTop, 0, 0, $topTextBoxBgrd);
    imagefill($textImageBottom, 0, 0, $bottomTextBoxBgrnd);
    
    //Copies the uploaded image into the edited image, editing its size.
    imagecopyresampled($new_tmp_image, $tmp_image, 0, 0, 0, 0, $width, $height, $width, $height);
    /** Copies the @see textImageTop into the edited image.*/
    imagecopyresampled($new_tmp_image, $textImageTop, 0, 0, 0, 0, $width, $top_text_height, $width, $top_text_height);
    /** Copies the @see textImageBottom into the edited image.*/
    imagecopyresampled($new_tmp_image, $textImageBottom, 0, $height - $font_size * 3, 0, 0, $width, $bottom_text_height, $width, $bottom_text_height);

    /** Applies the @see tmp_text_top and @see tmp_text_bottom on top the white backround images. */
    imagettftext($new_tmp_image, $font_size, 0, 0, $font_size * 1.5, $color, $font, $tmp_text_top);
    imagettftext($new_tmp_image, $font_size, 0, 0, $height - $font_size * 1.5, $color, $font, $tmp_text_bottom);
    
    //Saves the edited image according to the type of the original image.
    if("jpg" == $type) {
        imagejpeg($new_tmp_image, $dest, $quality);
    } else {
        imagepng($new_tmp_image, $dest, $quality);
    }
    
    /** Gets the contents of the @see dest variable. */
    $returnData = file_get_contents($dest);
    //Frees memory. Removes temporary files.
    imagedestroy($tmp_image);
    imagedestroy($new_tmp_image);
    imagedestroy($textImageTop);
    imagedestroy($textImageBottom);
    
    //Returns BASE64 encoded URI to the front-end.
    return ('data:image/' . $type . ';base64,' . base64_encode($returnData));
}
/**
 * The standard XMLHttpRequest error codes didn't cut it for my app if there was a runtime error.
 * So I created this method to return more information  in JSON format to the client where it is
 * processed with JS to extract the data.
 * 
 * @param _status       Boolean:    True or False, depending on the file upload / processing status.
 * @param _error        String:     Contains a detailed error message.
 * @param _data         String:     Contains the URI to the generated image.
 * @param _error_code   Integer:    Contains a custom code for the front end to pick and decide an action.
 * @return returnArr    JSON:       Contains the JSON with either an error or a success code and generated img URI.
 */
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
/**
 * @author Tihomir Mladenov (tihomir.mladenov777@gmail.com) 23-Apr-2019
 * @version 1.0
 * 
 * This is the PHP application code of a simple dynamic web-app where the user
 * enters two strings, selects an image to which the text is to be applied,
 * selects output image height and width, selects font size for the text
 * and submits the image. The original image is saved on the server, as well
 * as the edited copy, which is then returned to the user via AJAX and loaded
 * in his browser. The application only accepts *.jpg or .*png files with
 * upload file size limit of 1MB in the app - if the server is set to lower file
 * size limit, no file will be uploaded, but the app will generate an error nevertheless.
 * 
 * @param uploadDir             file upload location.
 * @param new_file              the name of the new upload file.
 * @param new_filename          appends the name of the file to the upload directory name.
 * @param new_filename_size     the size of the upload file.
 * @param new_image_height      the desired by the user height of the output image.
 * @param new_image_width       the desired by the user width of the output image.
 * @param image_file_type       the file format of the uploaded file.
 * @param userFormText_top      the top text on the output image, entered by the user.
 * @param userFormText_bottom   the bottom text on the output image, entered by the user.
 * @param msg                   sets error message that is to be returned to the user, if needed.
 * @param sendData              returns the URI of the edited image.
 * @see imgResize
 * 
 * @return msgReturn
 * @see msgReturn
 */

//Use for deployed version
$uploadDir =  "/home/ubunu/Desktop/uploads/";

//Use for test version
//$uploadDir =  getcwd() . "//uploads/";
$new_file = "";
$new_filename = "";
$new_filename_size = "";
$new_image_height = "";
$new_image_width = "";
$image_file_type = "";
$userFormText_top = "";
$userFormText_bottom = "";
$msg = "";
$sendData = "";

//TODO: Update php init file to allow file uploads bigger than 1 MB, or else the size for 
//bigger files will always be 0 as they are not uploaded.

/**
 * If the upload file field isn't empty AND there is a top text AND it isn't empty AND there is a bottom text and it isn't empty,
 * proceed to file upload.
 */
if($_FILES['uFile']['name'] != "" && ((isset($_POST['fn']) && $_POST['fn'] != "") && (isset($_POST['ln']) && $_POST['ln'] != ""))) {

    //Get the name of the uploaded file.
    $new_file = basename($_FILES['uFile']['name']);
    //Append the name of the uploaded file to the name of the upload directory.
    $new_filename = $uploadDir . $new_file;
    //Get the file size of the uploaded file.
    $new_filename_size = $_FILES['uFile']['size'];

    //Get the type of the uploaded file
    $image_file_type = strtolower(pathinfo($new_filename, PATHINFO_EXTENSION));

    /**
     * If the uploaded file's type isn't supported, return an error code and message to
     * user's client.
     */
    if($image_file_type != "png" && $image_file_type != "jpg") {
        $msg = "You cannot upload this type of image. Only supported formats are png or jpg.";
        echo msgReturn(false, $msg, null, 500);
        exit;
    }

    /**
     * If there is already an uploaded file with the same name as the file that is being uploaded,
     * get the date and hour and append it to the name of the file that is being uploaded and upload it.
     * Else just upload the file directly.
     */
    if(file_exists($new_filename)) {

        $new_file = date("dmy_his") . "_" . basename($_FILES['uFile']['name']);
        $new_filename = $uploadDir . $new_file;

        //If the file size is more than 0MB and less than 1MB - upload, else return error that the file
        //is too big.
        if($new_filename_size > 0 && $new_filename_size < 1000000) {
            copy($_FILES['uFile']['tmp_name'], $new_filename);
        } else {
            $msg = "There seem to have been an issue with saving the file or it is over 1MB in size.";
            echo msgReturn(false, $msg, null, 500);
            exit;
        }
    } else {
        //Same as the check above.
        if($new_filename_size > 0 && $new_filename_size < 1000000) {
            copy($_FILES['uFile']['tmp_name'], $new_filename);
        } else {
            $msg = "There was an issue with saving the file. It might be too big. File size is " . number_format($new_filename_size / 1000000, 1) . " Megabyte/s.";
            echo msgReturn(false, $msg, null, 500);
            exit;
        }
        
    }
    //Get the top and bottom text from the user form input in the form and sanitize it.
    $userFormText_top = filter_var($_POST['fn'], FILTER_SANITIZE_STRING);
    $userFormText_bottom = filter_var($_POST['ln'], FILTER_SANITIZE_STRING);

    // Get the output image width and height if they are set, else set a hardcoded value.
    if(isset($_POST['orientation_x']) && isset($_POST['orientation_y'])) {
        //Check whether the provided data for size is of the correct type (integer), else return error that invalid dimensions provided.
        if(!filter_var($_POST['orientation_x'], FILTER_VALIDATE_INT === false)) {
            //Check if the provided data for size is in the supported bounds by the app, if not, generate an out of bounds error.
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

    /** Contains the URI of the generated image.
     * @see imgResize
    */
    
    $sendData = imgResize($image_file_type, $new_filename, $uploadDir . date("dmy_his") . "_new" . "." . $image_file_type, 
           (int) $new_image_width, (int) $new_image_height, 90, $userFormText_top, $userFormText_bottom);

    /** Returns the URI to user's client via AJAX in JSON format.
     * @see msgReturn
     */
    
    echo msgReturn(true, null, $sendData, null);
    exit;

    //If the user has not filled in all the fields in the HTML form.
} else {
    $msg = "Please fill in the all the information in the fields.";
    echo msgReturn(false, $msg, null, 500);
    exit;
}
?>
