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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Data</title>
    <link rel="stylesheet" href="./css/bootstrap.min.css">

</head>
<body>
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <div class="card mt-5">
                        <div class="card-header text-center">
                            <h1>User Data</h1>
                        </div>
                    <div class="card-body">
                        <table class="table table-bordered border-info">
                            <tr>
                                <th>Name</th>
                                <th>Phone</th>
                                <th>Email</th>
                            </tr>
                            <?php foreach ($userData as $user): ?>
                                <tr>
                                    <td><?php echo $user['name']; ?></td>
                                    <td><?php echo $user['phone']; ?></td>
                                    <td><?php echo $user['email']; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </table>
                    </div>
                    <div class="card-footer text-center">
                        <a href="generate-csv.php">Download CSV</a>
                    </div>
                </div>
                
            </div>
        </div>
    <script src="js/bootstrap.bundle.min.js"></script>

</body>
</html>
