<?php
require 'session.php';
require 'src/dbconnect.php';
require 'vendor/autoload.php';

use Doctrine\DBAL\DriverManager;
use libphonenumber\PhoneNumberUtil;
use libphonenumber\PhoneNumberFormat;

$phoneNumberUtil = PhoneNumberUtil::getInstance();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirmPassword = trim($_POST['confirmPassword']);

    $phoneNumber = $phoneNumberUtil->parse($phone, 'KE');

    if ($phoneNumberUtil->isValidNumber($phoneNumber)) {
        $formattedPhone = $phoneNumberUtil->format($phoneNumber, PhoneNumberFormat::E164);
        $formattedPhone = ltrim($formattedPhone, '+');
    } else {
        // If the number is not from KE
        $formattedPhone = '+' . $phone;
    }
    
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
    } else {
        $cleanedUserData = cleanUserTable([
            [
                'name' => $name,
                'phone' => $formattedPhone,
                'email' => $email,
                'password' => $password,
            ],
        ]);
        try {
            // $checkEmailSQL = "SELECT * FROM users WHERE email = :email";
           $checkEmailSQL= $conn->executeQuery('SELECT * FROM users WHERE email = :email', ['email' => $email], ['email' => 'string']);

            if ($checkEmailSQL->rowCount() > 0) {
                $_SESSION['alert'] = "Email is already taken. Please choose another email.";
            } else {
                $stmtPhone = $conn->executeQuery('SELECT * FROM users WHERE phone = :phone', ['phone' => $cleanedUserData[0]['phone']], ['phone' => 'string']);
            
                if ($stmtPhone->rowCount() > 0) {
                    $_SESSION['alert'] = "Phone number is already taken. Please choose another phone number.";
                } else {
                    $insertSQL = "INSERT INTO users (name, phone, email, password) VALUES (:name, :phone, :email, :password)";
                    $stmt = $conn->executeQuery('INSERT INTO users (name, phone, email, password) VALUES (:name, :phone, :email, :password)', [
                        'name' => $cleanedUserData[0]['name'],
                        'phone' => $cleanedUserData[0]['phone'],
                        'email' => $cleanedUserData[0]['email'],
                        'password' => $cleanedUserData[0]['password'],
                    ], [
                        'name' => \Doctrine\DBAL\ParameterType::STRING,
                        'phone' => \Doctrine\DBAL\ParameterType::STRING,
                        'email' => \Doctrine\DBAL\ParameterType::STRING,
                        'password' => \Doctrine\DBAL\ParameterType::STRING,
                    ]);
                    
            
            
            
        
                    $_SESSION['success'] = "Registration successful!";
                    header("Location: login.php");
                    exit();
                }
            }
        } catch (PDOException $e) {
            $_SESSION['alert'] = "Database error: " . $e->getMessage();
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
                        if (isset($_SESSION['alert'])) {
                            echo '<div class="alert alert-danger">' . $_SESSION['alert'] . '</div>';
                            unset($_SESSION['alert']);
                        }
                        ?>

                    <div class="card-body bg-body-secondary">
                    <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                            <div class="form-group row mt-2 mb-2">
                                <label for "name" class="col-sm-3 col-form-label">Name</label>
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
                                    <a href="/login.php" class="link-info">Already have an Account. Click here to login</a>
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
