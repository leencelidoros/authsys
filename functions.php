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
        if (substr($phone, 0, 1) === '0') {
            $phone = '254' . substr($phone, 1);
        } elseif (substr($phone, 0, 1) === '+') {
            $phone = '254' . substr($phone, 1);
        }

        return $phone;
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
?>
