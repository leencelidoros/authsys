<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$servername = "localhost";
$username = "root";
$dbpassword = "";
$database = "auth";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$database", $username, $dbpassword);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

$loggedInUserEmail = $_SESSION['user']['email']; 

$sql = "SELECT name, phone, email FROM users WHERE email = :email"; 
$stmt = $conn->prepare($sql);
$stmt->bindParam(':email', $loggedInUserEmail, PDO::PARAM_STR);
$stmt->execute();
$userData = $stmt->fetchAll(PDO::FETCH_ASSOC);

$filename = "user_report.csv";

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="' . $filename . '"');

$output = fopen('php://output', 'w');

fputcsv($output, array('Name', 'Phone', 'Email'));

foreach ($userData as $user) {
    fputcsv($output, $user);
}

fclose($output);
?>
