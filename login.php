<?php
require 'session.php';
require 'src/dbconnect.php'; 
require 'vendor/autoload.php';


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $enteredEmail = trim($_POST['email']);
    $enteredPassword = $_POST['password'];

    try {
        $stmt = $conn->executeQuery("SELECT * FROM users WHERE email = :email", ['email' => $enteredEmail], ['email' => 'string']);
        $userData = $stmt->fetchAssociative();
 
        if ($userData) {
            if (password_verify($enteredPassword, $userData['password'])) {
                $_SESSION['user'] = $userData;

                if (isset($_POST['remember_me'])) {
                    $user_id = $userData['id'];
                    $ip_address = $_SERVER['REMOTE_ADDR'];
                    // store in db
                    storeActivityInDatabase($user_id, $token, "Logged in remember me", $ip_address);
                    // Generate a secure token
                    setcookie('authid', encrypt($userData['email'], 'qwerty'), time() + 3600 * 24 * 30, '/', null, true, true);
                }

                // Set a success message
                $_SESSION['success_message'] = "Login successful";

                header("Location: home.php");
                exit();
            } else {
                $errorMessage = "Invalid password. Please try again.";
            }
        } else {
            $errorMessage = "User not found. Please try again.";
        }
    } catch (PDOException $e) {
        $_SESSION['alert'] = "An error occurred: " . $e->getMessage();
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link rel="stylesheet" href="./css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                     <?php
                        if (isset($_SESSION['success'])) {
                            echo '<div class="alert alert-success">' . $_SESSION['success'] . '</div>';
                            unset($_SESSION['success']);
                        }
                        ?>
                    <div class="card-header">
                        <p class="h4 card-title text-center">Login Page</p>
                    </div>
                    <div class="card-body">
                    <?php
                        if (isset($_SESSION['success'])) {
                            echo '<div id="success-alert" class="alert alert-success" role="alert">';
                            echo $_SESSION['success'];
                            echo '<button type="button" class="close" data-dismiss="alert" aria-label="Close">';
                            echo '<span aria-hidden="true">&times;</span>';
                            echo '</button>';
                            echo '</div>';
                            unset($_SESSION['success']);
                        }
                    ?>
                        <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                            <div class="form-group mb-2">
                                <label for="email">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="form-group mb-2">
                                <label for="password">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <div class="form-group mb-2">
                                <label for="rememberMe" class="col-sm-3 col-form-label">Remember Me</label>
                                <div class="col-sm-9">
                                    <input type="checkbox" class="form-check-input" id="rememberMe" name="remember_me">
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary btn-block">Login</button>
                            <div class="col-sm-9 offset-sm-3">
                                <a href="/register.php" class="link-info">Don't have an account? Click here to Register</a>
                            </div>
                        </form>
                        <?php if (isset($errorMessage)) {
                            echo '<div class="alert alert-danger" role="alert">' . $errorMessage . '</div>';
                        } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var successAlert = document.getElementById('success-alert');
            if (successAlert) {
                successAlert.addEventListener('click', function () {
                    successAlert.style.display = 'none';
                });
            }
        });
        </script>
</body>
</html>