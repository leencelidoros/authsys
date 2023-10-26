<?php
require 'session.php';
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
            <div class="col-md-8 offset-md-2 mt-5">
                <div class="card">
                    <div class="card-header text-center">
                        <p class="h3">Logged In User</p>
                    </div>
                    <div class="card-body">
                    <div class="user-profile text-center">
                    <img src="./images/avatar.jpg" alt="User Avatar" height=100 width=100 class="img-fluid rounded-circle">
                    <h4 class="text-primary"><?php echo $_SESSION['user']['name']; ?></h4>
                    <p>Email: <?php echo $_SESSION['user']['email']; ?></p>
                    <p>Phone: <?php echo $_SESSION['user']['phone']; ?></p>
                    <a href="logout.php" class="btn btn-danger btn-sm">Sign Out</a>
                    <a href="report-gen.php" class="btn btn-primary btn-sm">Export Profile</a>
                </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>
