<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user'])) {
    // Redirect to the login page or display a message
    header("Location: login.php");
    exit();
}

// Database connection information
$servername = "localhost";
$username = "root";
$dbpassword = "";
$database = "auth";

// Create a PDO database connection
try {
    $conn = new PDO("mysql:host=$servername;dbname=$database", $username, $dbpassword);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

// Retrieve user data from the database
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
            <div class="card">
                <div class="card-body">
                <h2>All Users</h2>
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
    <script src="js/bootstrap.min.bundle.js"></script>
</body>
</html>
