<?php

/**
 * ParkFinder - Database Connection File
 * --------------------------------
 * Uses PDO for secure and reliable database connections.
 */

$config = require __DIR__ . '/config.php';

try {
    // Set timezone
    date_default_timezone_set($config['app']['timezone']);

    // Create PDO instance
    $dsn = "mysql:host={$config['db']['host']};dbname={$config['db']['name']};charset={$config['db']['charset']}";
    $pdo = new PDO($dsn, $config['db']['user'], $config['db']['pass'], [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,  // Throw exceptions on errors
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,        // Fetch associative arrays
        PDO::ATTR_EMULATE_PREPARES   => false                    // Use real prepared statements
    ]);
} catch (PDOException $e) {
    // If connection fails, stop and show message
    die("Database Connection Failed: " . htmlspecialchars($e->getMessage()));
}


$dsn = 'mysql:host=localhost;dbname=ocpms;charset=utf8mb4';
$user = 'root'; // or your MySQL username
$pass = '';    

try {
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Database connection failed: ' . $e->getMessage());
}