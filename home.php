<?php
require 'session.php';

$successMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $servername = "localhost";
    $database = "auth";
    $username = "root";
    $password = "";

    try {
        $pdo = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        die("Database connection failed: " . $e->getMessage());
    }

    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
        $user_id = $_SESSION['user']['email'];
        $upload_dir = 'images/profileimages/';
        $uploaded_file = $upload_dir . basename($_FILES['profile_image']['name']);

        $allowedExtensions = array("jpg", "jpeg", "png");
        $fileExtension = pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION);

        if (in_array(strtolower($fileExtension), $allowedExtensions)) {
            if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $uploaded_file)) {
                $sql = "UPDATE users SET profile_image_path = :profile_image_path WHERE email = :email";
                $defaultAvatar = 'images/avatar.jpg';

                // Prepare the SQL query
                $stmt = $pdo->prepare($sql);

                // Bind parameters
                $stmt->bindParam(':profile_image_path', $uploaded_file, PDO::PARAM_STR);
                $stmt->bindParam(':email', $user_id, PDO::PARAM_STR);

                // Execute the query
                if ($stmt->execute()) {
                    $_SESSION['user']['profile_image_path'] = $uploaded_file;
                    $successMessage = "Profile image updated successfully.";
                } else {
                    $successMessage = "Error updating profile image in the database.";
                }
            } else {
                $successMessage = "Failed to move the uploaded file.";
            }
        } else {
            $successMessage = "Invalid file type. Allowed extensions: " . implode(", ", $allowedExtensions);
        }
    } else {
        $successMessage = "Error during file upload. Error code: " . $_FILES['profile_image']['error'];
    }

    $_SESSION['success_message'] = $successMessage;

    if (empty($_SESSION['user']['profile_image_path'])) {
        $defaultAvatar = 'images/avatar.jpg';
        $_SESSION['user']['profile_image_path'] = $defaultAvatar;
    }
}
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
                            <img src="<?php echo $_SESSION['user']['profile_image_path']; ?>" alt="avatar" height=100 width=100 class="img-fluid rounded-circle">
                            <div class="col">
                                <form action="home.php" method="post" enctype="multipart/form-data">
                                    <input type="file" name="profile_image">
                                    <input type="submit" class="btn btn-success" value="Upload Image">
                                </form>
                            </div>
                            <h4 class="text-primary"><?php echo $_SESSION['user']['name']; ?></h4>
                            <p>Email: <?php echo $_SESSION['user']['email']; ?></p>
                            <p>Phone: <?php echo $_SESSION['user']['phone']; ?></p>
                            <a href="logout.php" class="btn btn-danger btn-sm">Sign Out</a>
                            <a href="report-gen.php" class="btn btn-primary btn-sm">Export Profile</a>
                            <?php
                            if (!empty($_SESSION['success_message'])) {
                                echo '<div class="alert alert-success">' . $_SESSION['success_message'] . '</div>';
                                unset($_SESSION['success_message']);
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>
