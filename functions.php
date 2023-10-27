<?php
if (!function_exists('isNotEmpty')) {
    function isNotEmpty($value, $field) {
        if (empty($value)) {
            return "Please enter a value for $field.";
        }
        return null; 
    }
}

if (!function_exists('isValidEmail')) {
    function isValidEmail($email) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return "Invalid email address.";
        }
        return null; 
    }
}

if (!function_exists('isStrongPassword')) {
    function isStrongPassword($password) {
        if (strlen($password) < 8) {
            return "Password must be at least 8 characters long.";
        }
        return null; 
    }
}
if (!function_exists('normalizePhoneNumber')) {
    function normalizePhoneNumber($phone) {
        $phone = preg_replace('/\D/', '', $phone);

        // If the phone number starts with "254" or "+254"
        if (substr($phone, 0, 3) === '254' || substr($phone, 0, 4) === '+254') {
            return $phone;
        }
// add 254 
        return '254' . $phone;
    }
}


if (!function_exists('storeActivityInDatabase')) {
    function storeActivityInDatabase($user_id, $token, $activity, $ip_address) {
        $servername = "localhost";
        $username = "root";
        $dbpassword = "";
        $database = "auth";
        try {
            $conn = new PDO("mysql:host=$servername;dbname=$database", $username, $dbpassword);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Insert activity into audit_logs
            $insertActivitySQL = "INSERT INTO audit_logs (user_id, activity, ip_address) VALUES (:user_id, :activity, :ip_address)";
            $stmt = $conn->prepare($insertActivitySQL);
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->bindParam(':activity', $activity, PDO::PARAM_STR);
            $stmt->bindParam(':ip_address', $ip_address, PDO::PARAM_STR);
            $stmt->execute();
        } catch (PDOException $e) {
            echo "Error storing token and activity in the database: " . $e->getMessage();
        }
    }
}


function encrypt($data, $key) {
    $method = 'aes-256-cbc';
    $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($method));
    $encrypted = openssl_encrypt($data, $method, $key, 0, $iv);
    return base64_encode($iv . $encrypted);
}

// Function to decrypt data
function decrypt($data, $key) {
    $method = 'aes-256-cbc';
    $data = base64_decode($data);
    $iv = substr($data, 0, openssl_cipher_iv_length($method));
    return openssl_decrypt(substr($data, openssl_cipher_iv_length($method)), $method, $key, 0, $iv);    
}

function setAlert($message, $type = 'info') {
    $_SESSION['alert']['message'] = $message;
    $_SESSION['alert']['type'] = $type;
}

function displayAlert() {
    if (isset($_SESSION['alert']['message'])) {
        $message = $_SESSION['alert']['message'];
        $type = $_SESSION['alert']['type'];

        echo '<div class="alert alert-' . $type . '">' . $message . '</div>';
        
        unset($_SESSION['alert']);
    }
}

function isUserLoggedIn() {
    return isset($_SESSION['user_id']);
}
function cleanUserTable($userTable) {
    $cleanedData = [];

    foreach ($userTable as $user) {
        $cleanedUser = [];
        $cleanedUser['name'] = filter_var($user['name'], FILTER_SANITIZE_STRING);
        $cleanedUser['phone'] = preg_replace("/[^0-9]/", "", $user['phone']);
        $cleanedUser['email'] = filter_var($user['email'], FILTER_SANITIZE_EMAIL);
        $cleanedUser['password'] = password_hash($user['password'], PASSWORD_DEFAULT);
        $cleanedData[] = $cleanedUser;
    }

    return $cleanedData;
}


?>
