/**
 * @author Tihomir Mladenov (tihomir.mladenov777@gmail.com), 23-Apr-2019
 * @version 1.0
 * 
 * Clears the imgResponse span on the webpage, if the user confirms and if
 * the span isn't already cleared. Then sends the appropriate message
 * 
 * @param {Object} imageField  References the imgResponse span in the HTML.
 * @param {string} msg          Confirm dialog to delete the image and clear the span.
 * @returns {boolean}           True if span is cleared, False if it wasn't cleared.
 */

function clearImage() {
    var imageField = document.getElementById("imgResponse");
    //If imgResponse has child nodes, ask to delete, otherwise
    //there's nothing to be deleted.
    if(imageField.childNodes.length != 0) {
        var msg = confirm("Image will be cleared.");
        if(msg == true) {
            imageField.innerHTML = "";
            return true;
        }
    } else {
        alert("Image field is clear already");
        return false;
    }
}