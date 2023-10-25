<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form inputs
    $enteredEmail = $_POST['email'];
    $enteredPassword = $_POST['password'];

    try {
        // Replace with your database credentials
        $servername = "localhost";
        $username = "root";
        $password = "";
        $database = "auth";

        $conn = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Prepare and execute a SELECT statement to retrieve user's information
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->bindParam(':email', $enteredEmail);
        $stmt->execute();

        // Fetch the user's data
        $userData = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($userData) {
            // Verify the password (you should use password_verify for hashed passwords)
            if (password_verify($enteredPassword, $userData['password'])) {
                // Set a session variable to indicate the user is logged in
                $_SESSION['user'] = $userData['email'];

                // Redirect the user to the home page
                header("Location: home.php");
                exit();
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
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault">
                                <label class="form-check-label" for="flexCheckDefault">
                                    Remember Me
                                </label>
                            </div>
                            <button type="submit" class="btn btn-primary btn-block ">Login</button>
                            <div class="col-sm-9 offset-sm-3">
                                    <a href="/register.php" class="link-info">Dont have an account .Click here to Register</a>
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