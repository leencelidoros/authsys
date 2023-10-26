<?php
session_start();
include 'functions.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];
    $normalizedPhone = normalizePhoneNumber($phone); // Normalize the phone number

    // Validation 
    $nameError = isNotEmpty($name, 'Name');
    $emailError = isValidEmail($email);
    $passwordError = isStrongPassword($password);
    
    if ($nameError) {
        $_SESSION['alert'] = "Name is required.";
    } elseif ($emailError) {
        $_SESSION['alert'] = "Invalid email address.";
    } elseif ($passwordError) {
        $_SESSION['alert'] = "Password must be at least 8 characters long and contain at least one uppercase letter, one lowercase letter, one number, and one special character.";
    } elseif ($password !== $confirmPassword) {
        $_SESSION['alert'] = "Passwords do not match.";
    } elseif (!$normalizedPhone) { // Check the normalized phone number
        $_SESSION['alert'] = "Invalid phone number.";
    } else {
        // Database conn
        $servername = "localhost";
        $username = "root";
        $dbpassword = "";
        $database = "auth";

        try {
            $conn = new PDO("mysql:host=$servername;dbname=$database", $username, $dbpassword);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // email unique
            $checkEmailSQL = "SELECT * FROM users WHERE email = :email";
            $stmt = $conn->prepare($checkEmailSQL);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $_SESSION['alert'] = "Email is already taken. Please choose another email.";
            } else {
                // phone unique
                $checkPhoneSQL = "SELECT * FROM users WHERE phone = :phone";
                $stmtPhone = $conn->prepare($checkPhoneSQL);
                $stmtPhone->bindParam(':phone', $normalizedPhone, PDO::PARAM_STR); // Use the normalized phone number
                $stmtPhone->execute();

                if ($stmtPhone->rowCount() > 0) {
                    $_SESSION['alert'] = "Phone number is already taken. Please choose another phone number.";
                } else {
                    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                    // Insert in db 
                    $insertSQL = "INSERT INTO users (name, phone, email, password) VALUES (:name, :phone, :email, :password)";
                    $stmt = $conn->prepare($insertSQL);

                    $stmt->bindParam(':name', $name, PDO::PARAM_STR);
                    $stmt->bindParam(':phone', $normalizedPhone, PDO::PARAM_STR);// +254
                    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
                    $stmt->bindParam(':password', $hashedPassword, PDO::PARAM_STR);

                    $stmt->execute();

                    $_SESSION['alert'] = "Registration successful!";
                    header("Location: login.php");
                    exit();
                }
            }
        } catch (PDOException $e) {
            $_SESSION['alert'] = "An error occurred: " . $e->getMessage();
        }
    }
}
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="./css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header text-center">
                        <h3>Register</h3>
                    </div>
                    <div class="card-body bg-body-secondary">
                    <div id="alertDiv" class="alert 
                    <?php echo isset($_SESSION['alert']) ? 'alert-success' : 'd-none'; ?>
                    " role="alert">
                        <?php
                        if (isset($_SESSION['alert'])) {
                            echo $_SESSION['alert'];
                            unset($_SESSION['alert']); 
                        }
                        ?>
                        
                    </div>
                    <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                            <div class="form-group row mt-2 mb-2">
                                <label for="name" class="col-sm-3 col-form-label">Name</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="name" name="name" required>
                                </div>
                            </div>
                            <div class="form-group row mt-2 mb-2">
                                <label for="phone" class="col-sm-3 col-form-label">Phone</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="phone" name="phone" required>
                                </div>
                            </div>
                            <div class="form-group row mt-2 mb-2">
                                <label for="email" class="col-sm-3 col-form-label">Email</label>
                                <div class="col-sm-9">
                                    <input type="email" class="form-control" id="email" name="email" required>
                                </div>
                            </div>
                            <div class="form-group row mt-2 mb-2">
                                <label for="password" class="col-sm-3 col-form-label">Password</label>
                                <div class="col-sm-9">
                                    <input type="password" class="form-control" id="password" name="password" required>
                                </div>
                            </div>
                            <div class="form-group row mt-2 mb-2">
                                <label for="confirmPassword" class="col-sm-3 col-form-label">Confirm Password</label>
                                <div class="col-sm-9">
                                    <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" required>
                                </div>
                            </div>
                            <div class="form-group row text-center">
                                <div class="col-sm-9 offset-sm-3">
                                    <button type="submit" class="btn btn-primary">Register</button>
                                </div>
                                <div class="col-sm-9 offset-sm-3">
                                    <a href="/login.php" class="link-info">Already have an Account .Click here to login</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="/js/bootstrap.bundle.min.js"></script>
</body>
</html>