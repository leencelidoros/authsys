<?php
session_start();
include 'functions.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $enteredEmail = $_POST['email'];
    $enteredPassword = $_POST['password'];

    try {
        $servername = "localhost";
        $username = "root";
        $dbpassword = "";
        $database = "auth";

        $conn = new PDO("mysql:host=$servername;dbname=$database", $username, $dbpassword);
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->bindParam(':email', $enteredEmail);
        $stmt->execute();

        $userData = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($userData) {
            if (password_verify($enteredPassword, $userData['password'])) {
                $_SESSION['user'] = $userData['email'];
                $_SESSION['user_name'] = $userData['name'];
                $authenticated = true; 

                if ($authenticated) {
                    $_SESSION['user_id'] = $user_id;

                    if ($_POST['remember_me']) {
                        // secure token
                        $token = bin2hex(random_bytes(32)); 
                        storeActivityInDatabase($userData['id'], 'Logged In', $_SERVER['REMOTE_ADDR']);
                        // Set the "Remember Me" cookies
                        setcookie('user_id', $user_id, time() + 3600 * 24 * 30, '/', null, true, true);
                        setcookie('token', $token, time() + 3600 * 24 * 30, '/', null, true, true);

                        // store cookie in hmac
                        $hmacKey = 'your_secret_key'; 
                        $hmac = hash_hmac('sha256', $user_id . $token, $hmacKey);
                        setcookie('auth', $hmac, time() + 3600 * 24 * 30, '/', null, true, true); 
                    }

                    header("Location: home.php");
                    exit();
                }
            } else {
                $errorMessage = "Invalid password. Please try again.";
            }
        } else {
            $errorMessage = "User not found. Please try again.";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
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
                    <div class="card-header">
                        <p class="h4 card-title text-center">Login Page</p>
                    </div>
                    <div class="card-body">
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
</body>
</html>