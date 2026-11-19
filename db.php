<?php

$host = "127.0.0.1:3307";
$username = "root";
$password = "";
$database = "canteen_db";

$conn = new mysqli(
    $host,
    $username,
    $password,
    $database
);

if ($conn->connect_error) {
    die("Database Connection Failed: " .
        $conn->connect_error);
}

?>