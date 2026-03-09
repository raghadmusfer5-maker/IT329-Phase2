<?php

$host = "localhost";
$user = "root";
$password = "root";
$database = "it329_recipes";
$port="8889";

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>