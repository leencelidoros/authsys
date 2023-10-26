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
function normalizePhoneNumber($phone) {
    $phone = preg_replace('/\D/', '', $phone);
    if (substr($phone, 0, 1) === '0') {
        $phone = '254' . substr($phone, 1);
    } elseif (substr($phone, 0, 1) === '+') {
        $phone = '254' . substr($phone, 1);
    }

    return $phone;
}

?>




