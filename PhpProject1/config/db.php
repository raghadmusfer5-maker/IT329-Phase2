<?php

$host = "localhost";
$user = "root";
$password = "root";
$database = "it329_recipes-9";
$port=8889;

$conn = new mysqli($host, $user, $password, $database, $port);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>