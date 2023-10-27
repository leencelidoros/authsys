<?php
require 'session.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirmPassword = trim($_POST['confirmPassword']);
    $normalizedPhone = normalizePhoneNumber($phone);

    $formattedPhone = "+254" . ltrim($normalizedPhone, '0');

    // Validation
    $nameError = isNotEmpty($name, 'Name');
    $emailError = isValidEmail($email);
    $passwordError = isStrongPassword($password);

    if (empty($name) || empty($email) || empty($password)) {
        $_SESSION['alert'] = "Name, email, and password are required.";
    } elseif ($nameError) {
        $_SESSION['alert'] = "Name is required.";
    } elseif ($emailError) {
        $_SESSION['alert'] = "Invalid email address.";
    } elseif ($passwordError) {
        $_SESSION['alert'] = "Password must be at least 8 characters long and contain at least one uppercase letter, one lowercase letter, one number, and one special character.";
    } elseif ($password !== $confirmPassword) {
        $_SESSION['alert'] = "Passwords do not match.";
    } elseif (!$normalizedPhone) {
        $_SESSION['alert'] = "Invalid phone number.";
    } else {
        $cleanedUserData = cleanUserTable([
            [
                'name' => $name,
                'phone' => $normalizedPhone,
                'email' => $email,
                'password' => $password
            ]
        ]);

        $cleanedUser = $cleanedUserData[0];

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
            $stmt->bindParam(':email', $cleanedUser['email'], PDO::PARAM_STR);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $_SESSION['alert'] = "Email is already taken. Please choose another email.";
            } else {
                // phone unique
                $checkPhoneSQL = "SELECT * FROM users WHERE phone = :phone";
                $stmtPhone = $conn->prepare($checkPhoneSQL);
                $stmtPhone->bindParam(':phone', $cleanedUser['phone'], PDO::PARAM_STR); // Use the cleaned phone number
                $stmtPhone->execute();

                if ($stmtPhone->rowCount() > 0) {
                    $_SESSION['alert'] = "Phone number is already taken. Please choose another phone number.";
                } else {
                    // Insert in db
                    $insertSQL = "INSERT INTO users (name, phone, email, password) VALUES (:name, :phone, :email, :password)";
                    $stmt = $conn->prepare($insertSQL);

                    $stmt->bindParam(':name', $cleanedUser['name'], PDO::PARAM_STR);
                    $stmt->bindParam(':phone', $cleanedUser['phone'], PDO::PARAM_STR);
                    $stmt->bindParam(':email', $cleanedUser['email'], PDO::PARAM_STR);
                    $stmt->bindParam(':password', $cleanedUser['password'], PDO::PARAM_STR);

                    $stmt->execute();

                    // Set a success message
                    
                    $_SESSION['success'] = "Registration successful!";
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
                        <?php
                        if (isset($_SESSION['success'])) {
                            echo '<div class="alert alert-success">' . $_SESSION['success'] . '</div>';
                            unset($_SESSION['success']);
                        }
                        ?>


                    <div class="card-body bg-body-secondary">
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

    <script src="/js/bootstrap.bundle.min.js"></script>
    
</body>
</html>