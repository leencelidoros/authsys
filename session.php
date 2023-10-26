<?php
include 'functions.php';
$sessionTimeout = 30 * 60; 
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > $sessionTimeout)) {
    session_unset();
    session_destroy();
}
$_SESSION['LAST_ACTIVITY'] = time();

if (isset($_COOKIE['authid']) && !isset($_SESSION['user'])) {
    $authid = $_COOKIE['authid'];
    $user = getUserByAuthId($authid);

    if ($user) {
        $_SESSION['user'] = $user['email'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_pass'] =$user['password'];
}
function setAlert($message, $type = 'info') {
    $_SESSION['alert']['message'] = $message;
    $_SESSION['alert']['type'] = $type;
}

function displayAlert() {
    if (isset($_SESSION['alert']['message'])) {
        $message = $_SESSION['alert']['message'];
        $type = $_SESSION['alert']['type'];

        echo '<div class="alert alert-' . $type . '">' . $message . '</div>';
        
        unset($_SESSION['alert']);
    }
}

function isUserLoggedIn() {
    return isset($_SESSION['user_id']);
}
}