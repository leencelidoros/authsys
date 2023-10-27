<?php
require 'session.php';

session_unset();

// destroy the session
session_destroy();


setcookie("authid", "", time() - 3600);

header("Location: index.php");
exit();
?>
