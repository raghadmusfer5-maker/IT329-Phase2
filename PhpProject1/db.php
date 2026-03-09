<?php

$host = "localhost";
$user = "root";
$password = "root";
$database = "it329_recipes";

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

?>