<?php


function isNotEmpty($value, $field) {
    if (empty($value)) {
        return "Please enter a value for $field.";
    }
    return null; 
}
function isValidEmail($email) {
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return "Invalid email address.";
    }
    return null; 
}

function isStrongPassword($password) {
    if (strlen($password) < 8) {
        return "Password must be at least 8 characters long.";
    }
    return null; 
}
function validatePhoneNumber($phone) {
    $phone = preg_replace("/[^0-9]/", "", $phone);
    if (strlen($phone) !== 10 || substr($phone, 0, 3) !== '254') {
        return "Invalid phone number format. It should start with '254' and have 10 digits.";
    }
    return null; 
}


?>