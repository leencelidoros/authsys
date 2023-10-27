<?php

session_start();
include 'functions.php';
if (isset($_COOKIE['authid']) && !isset($_SESSION['user'])) {
    $encyptedcookie = $_COOKIE['authid'];
    $authid = decrypt($encyptedcookie, 'qwerty');

    $servername = "localhost";
        $username = "root";
        $dbpassword = "";
        $database = "auth";

        $conn = new PDO("mysql:host=$servername;dbname=$database", $username, $dbpassword);
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->bindParam(':email', $authid);
        $stmt->execute();

        $userData = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($userData) {
            $_SESSION['user'] = $userData;
        }
}


//if user is logged in (has a session) and is tryng to access a guest page, redirect to home page
if (isset($_SESSION['user']) && ($_SERVER['SCRIPT_NAME']  == '/login.php' || $_SERVER['SCRIPT_NAME']  == '/register.php' || $_SERVER['SCRIPT_NAME']  == '/index.php')) {
    header("Location: home.php");
    exit();
}

if (!isset($_SESSION['user']) && ($_SERVER['SCRIPT_NAME']  == '/home.php' || $_SERVER['SCRIPT_NAME']  == '/logout.php')) {
    header("Location: login.php");
    exit();
}


