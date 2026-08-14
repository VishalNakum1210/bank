<?php
/**
 * Database Configuration & Connection
 * NNP Online Banking System
 */

// Database credentials
$server   = "localhost";
$username = "root";
$password = "";
$database = "bank";

// Create MySQLi Connection
$conn = mysqli_connect($server, $username, $password, $database);

// Check connection
if (!$conn) {
    die("Database Connection Failed: " . mysqli_connect_error());
}

// Set character set to UTF-8
mysqli_set_charset($conn, "utf8mb4");
?>
