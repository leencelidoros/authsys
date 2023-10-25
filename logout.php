<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

session_destroy();
header("Location: login.php?logout=1");
exit();
?>
