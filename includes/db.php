<?php
// Database connection details
$database_host = 'localhost';
$database_username = 'root';
$database_password = '';
$database_name = 'car_rental';

// Create the connection
$database_connection = mysqli_connect(
    $database_host,
    $database_username,
    $database_password,
    $database_name
);

// Stop if the connection failed
if (!$database_connection) {
    die('Database connection failed: ' . mysqli_connect_error());
}

mysqli_set_charset($database_connection, 'utf8mb4');
?>

