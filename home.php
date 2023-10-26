<?php
session_start();
require 'session.php';
include 'functions.php';

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

$sql = "SELECT name, phone ,email FROM users";
$stmt = $conn->prepare($sql);
$stmt->execute();
$userData = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home Page</title>
    
    <link rel="stylesheet" href="./css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-md-10 offset-md-2">
                <div class="user-profile text-center">
                    <img src="./images/avatar.jpg" alt="User Avatar" height=100 width=100 class="img-fluid rounded-circle">
                    <p class="h5 text-primary"><?php echo $_SESSION['user_name']; ?></h4>
                    <a href="logout.php" class="btn btn-danger btn-sm">Sign Out</a>
                </div>
            </div>
            <div class="col-md-10">
                <div class="card">
                    <div class="card-body">
                        <h2>All Users</h2>
                        <a href="report-gen.php" class="btn btn-primary">Generate User Report</a>
                        <table class="table table-bordered border-primary">
                            <tr>
                                <th>Name</th>
                                <th>Phone</th>
                                <th>Email</th>
                            </tr>
                            <?php foreach ($userData as $user) { ?>
                                <tr>
                                    <td><?php echo $user['name']; ?></td>
                                    <td><?php echo $user['phone']; ?></td>
                                    <td><?php echo $user['email']; ?></td>
                                </tr>
                            <?php } ?>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>
