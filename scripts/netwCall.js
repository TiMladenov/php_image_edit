/**
 * @author Tihomir Mladenov (tihomir.mladenov777@gmail.com) 23-Apr-2019
 * @version 1.0
 * 
 * Checks if all fields are completed by the user and then sends an AJAX call.
 * Else it triggers errors for the missing information.
 * 
 * @param {Element}  upFile              The file that is being uploaded.
 * @param {Element}  upFileValueCheck    Checks if there is a file that is actually selected for upload.
 * @param {string}   fn                  The top text, entered by the user.
 * @param {string}   ln                  The bottom text, entered by the user.
 * @param {integer}  fs                  The selected font size for the text on the edited image.
 * @param {integer}  ws                  The selected edited image width.
 * @param {integer}  hs                  The selected edited image height.
 * 
 * @returns {boolean}                    False if there's missing information, else true and makes an AJAX call.
 *                                       @see ajaxCall
 */

function checkFile() {
    var upFile = document.getElementById("uploadFile"); 
    var upFileValueCheck = document.getElementById("uploadFile").value;
    var fn = document.forms['submitForm'].elements['top_text'].value;
    var ln = document.forms['submitForm'].elements['bottom_text'].value;
    var fs = document.forms['submitForm'].elements['font_size'].value;
    var ws = document.forms['submitForm'].elements['orientation_x'].value;
    var hs = document.forms['submitForm'].elements['orientation_y'].value;
    
    if("" == upFileValueCheck || null == upFileValueCheck) {
        alert("Select a file.");
        return false;
    } else if("" == ln || null == ln) {
        alert("Enter the bottom text.");
        return false;
    } else if("" == fn || null == fn) {
        alert("Enter the top text.");
        return false;
    } else if(0 == fs || null == fs) {
        alert("Enter font size.");
        return false;
    } else {
        ajaxCall(upFile, fn, ln, fs, ws, hs);
    }
}

/**
 * @author Tihomir Mladenov (tihomir.mladenov777@gmail.com) 23-Apr-2019
 * @version 1.0
 * 
 * Makes an AJAX call with the data provided by @see checkFile . If the AJAX
 * response from the server is success, the image will be loaded onto the page.
 * If there was an error, it will be displayed instead.
 * 
 * @param {Object} xmlhttp              Creates a new XMLHttpRequest instance
 *                                      depending on the browser.
 * @param {Object} form                 Creates a Form object, appends all the
 *                                      data to it from the HTML form.
 * @param {Object} json                 Stores the parsed JSON string that is returned
 *                                      from the server. @see parseJson
 * @param {Object} responseHTMLField    References the imgResponse element. Dynamically
 *                                      sets its CSS and populates the error on the webpage.
 * @param {Object} tmp                  References to the load_image element. If it exists,
 *                                      it populates the src field with the Base64 encoded
 *                                      image from the BE. Else it creates and populates the IMG node.
 * 
 * @returns {*}                         The new IMG node to imgResponse span on the HTML.
 */

function ajaxCall(upFile, fn, ln, fs, ws, hs) {
    var xmlhttp = new createXMLHttpInstance();
    var form = new FormData(document.getElementById("submitForm"));
    
    xmlhttp.onreadystatechange=function(){
        
        //If the XMLHttpRequest was successful and completed, perform the actions inside.
        if (this.readyState==4 && this.status==200){
            var json = parseJson(this.responseText);
            document.getElementById("imgResponse").innerHTML = "";

            /**
             * If the JSON object doesn't exist or its status is false / failed,
             * populate the imgResponse span with the error message. Else add to
             * the imgResponse span an IMG child node and populate it with the
             * received image.
            */ 
            if(!json || json.status === false) {
                var responseHTMLField = document.getElementById("imgResponse");
                responseHTMLField.innerHTML = json.error;
                responseHTMLField.setAttribute("style", "color: red;");
                return;
            } else if(json.status === true) {
                /**
                 * If the IMG tag with it load_image already exists on the page,
                 * just populate it with the image from the last received JSON,
                 * else create the node and populate it altogether.
                 */
                if(document.getElementById("load_image")) {
                var tmp = document.getElementById('load_image');
                tmp.setAttribute("src", json.data);
                } else {
                    var tmp = document.createElement('IMG');
                    tmp.setAttribute("class", "w-100 h-100");
                    tmp.setAttribute("src", json.data);
                    tmp.setAttribute("id", "load_image");
                    tmp.setAttribute("alt", "This image has been loaded dynamically from the back end");
                    document.getElementById("imgResponse").appendChild(tmp);
                }
                document.getElementById("uploadFile").value = "";
                return;
            }
        }
    }
    //Call first.php script and send the form object's properties to it for processing.
    xmlhttp.open("POST", "first.php", true);
    form.append("fn", fn);
    form.append("ln", ln);
    form.append('fs', fs);
    form.append('ws', ws);
    form.append("hs", hs);
    form.append("uFile", upFile.files[0]);
    xmlhttp.send(form);
}

/**
 * @author Tihomir Mladenov (tihomir.mladenov777@gmail.com) 23-Apr-2019
 * @version 1.0
 * 
 * Parses the received JSON string from the server into a JSON object.
 * 
 * @param  {string} text     The JSON string that is received from the server.
 * @param  {Object} json     The JSON object that is created from @see text.
 * @returns {*}              Exception if conversion was failed or JSON object.
 *                           @see ajaxCall
 */
function parseJson(text) {
    try {
        var json = JSON.parse(text);
    } catch(e) {
        return false;
    }
    return json;
}
/**
 * @author Tihomir Mladenov (tihomir.mladenov777@gmail.com) 23-Apr-2019
 * @version 1.0
 * 
 * Tries to create a new XMLHttpRequest object and returns it to @see ajaxCall
 * 
 * @param {Object} request      Attempts to create a new XMLHttpRequest object
 * @returns {*}                 Exception or ref. to XMLHttpRequest object instance.
 */
function createXMLHttpInstance() {
    var request = null;
    if (window.XMLHttpRequest) {
        request = new XMLHttpRequest();
    } else if (window.ActiveXObject) {
        try {
            request = new ActiveXObject("Msxml2.XMLHTTP");
        } catch (e) {
            try {
                request = new ActiveXObject("Microsoft.XMLHTTP");
            } catch (e) {
                alert("XHR not created");
            }
        }
    }
    return request;
}