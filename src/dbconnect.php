<?php

require 'vendor/autoload.php';

use Doctrine\DBAL\DriverManager;

    $config = [
        'driver' => 'pdo_mysql',
        'user' => 'root',
        'password' => '',
        'dbname' => 'auth',
        'host' => 'localhost',
    ];

try {
    $conn = DriverManager::getConnection($config);

} catch (Exception $e) {
    die("Database error: " . $e->getMessage());
}
