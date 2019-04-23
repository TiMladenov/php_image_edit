/**
 * @author Tihomir Mladenov (tihomir.mladenov777@gmail.com) 23-Apr-2019
 * @version 1.0
 * 
 * Checks whether the input text fields are empty. If they are, an message
 * is displayed to their side that they need to be filled in.
 * 
 * @param {Object} field       References the field from which this function was triggered.
 * @param {Object} displayID   References the corresponding span field for the warning to be
 *                              displayed.
 * @returns {boolean}           False if form input is empty, true if it isn't.
 */

function checkRequired(field, displayID) {
    var display = document.getElementById(displayID);
    //If the input field is empty display warning in the span
    //else do nothing.
    if(null == field.value || "" == field.value) {
        display.innerHTML = "Please fill in this field!";
        return false;
    } else {
        display.innerHTML = "";
        return true;
    }
}