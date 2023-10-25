<?php
// Start or resume the session
session_start();

// Set session timeout (optional)
// Adjust the timeout value based on your needs
// The example sets a session timeout to 30 minutes
$sessionTimeout = 30 * 60; // 30 minutes
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > $sessionTimeout)) {
    // Session has expired, clear it
    session_unset();
    session_destroy();
}
$_SESSION['LAST_ACTIVITY'] = time();

// Function to set an alert message in the session
function setAlert($message, $type = 'info') {
    $_SESSION['alert']['message'] = $message;
    $_SESSION['alert']['type'] = $type;
}

// Function to display and clear the alert message
function displayAlert() {
    if (isset($_SESSION['alert']['message'])) {
        $message = $_SESSION['alert']['message'];
        $type = $_SESSION['alert']['type'];

        echo '<div class="alert alert-' . $type . '">' . $message . '</div>';
        
        unset($_SESSION['alert']);
    }
}

// Additional session-related functions or configurations can go here
// ...

// Example: function to check if a user is logged in
function isUserLoggedIn() {
    return isset($_SESSION['user_id']);
}
